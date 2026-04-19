<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\PackageMediaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with(['mediaItems'])
            ->withCount([
                'bookings as popular_score' => function ($query) {
                $query->whereIn('status', ['pending', 'dp_paid', 'in_progress', 'completed']);
            },
                'bookings'
            ])
            ->orderBy('sort_order')
            ->get();

        $popularPackageId = $packages->sortByDesc('popular_score')->first()?->id;
        $nextSortOrder = ($packages->max('sort_order') ?? 0) + 1;

        return view('admin.packages.index', compact('packages', 'popularPackageId', 'nextSortOrder'));
    }

    public function create()
    {
        $package = new Package(['is_active' => true]);
        return view('admin.packages.create', compact('package'));
    }

    public function store(Request $request)
    {
        $data = $this->validatePackage($request);

        try {
            if (!array_key_exists('is_active', $data)) {
                $data['is_active'] = true;
            }
            $package = Package::create($data);
            $this->handleMediaUploads($request, $package);
            Log::info('Package created', ['id' => $package->id]);
        } catch (\Throwable $e) {
            Log::error('Package create failed', ['message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Gagal menyimpan paket.');
        }

        return redirect()->route('admin.packages.index')->with('success', 'Paket berhasil dibuat.');
    }

    public function edit(Package $package)
    {
        return view('admin.packages.edit', compact('package'));
    }

    public function update(Request $request, Package $package)
    {
        $data = $this->validatePackage($request, $package->id);
        Log::info('Update package request received', ['id' => $package->id]);

        if (!$request->has('is_active')) {
            unset($data['is_active']);
        }

        try {
            $package->update($data);
            $this->handleMediaUploads($request, $package);
            Log::info('Package updated', ['id' => $package->id]);
        } catch (\Throwable $e) {
            Log::error('Package update failed', ['id' => $package->id, 'message' => $e->getMessage()]);
            return back()->withInput()->with('error', 'Gagal memperbarui paket.');
        }

        return redirect()->route('admin.packages.index')->with('success', 'Paket berhasil diperbarui.');
    }

    public function destroy(Package $package)
    {
        if ($package->bookings()->exists()) {
            return back()->with('error', 'Paket tidak dapat dihapus karena sudah memiliki booking.');
        }

        $package->delete();
        return back()->with('success', 'Paket berhasil dihapus.');
    }

    public function deleteMedia(Package $package, PackageMediaItem $media)
    {
        if ($media->package_id !== $package->id) {
            abort(404);
        }

        if ($media->media_path) {
            Storage::disk('public')->delete($media->media_path);
        }

        $media->delete();

        return back()->with('success', 'Media berhasil dihapus.');
    }

    public function reorderMedia(Request $request, Package $package)
    {
        $data = $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer',
        ]);

        $currentIds = $package->mediaItems()->orderBy('sort_order')->orderBy('id')->pluck('id')->all();
        $requestedIds = array_values(array_unique($data['order']));
        $filteredIds = array_values(array_intersect($requestedIds, $currentIds));

        if (count($filteredIds) !== count($currentIds)) {
            return response()->json(['message' => 'Urutan media tidak valid.'], 422);
        }

        foreach ($filteredIds as $index => $mediaId) {
            PackageMediaItem::whereKey($mediaId)->update(['sort_order' => $index]);
        }

        return response()->json(['message' => 'Urutan media diperbarui.']);
    }

    private function validatePackage(Request $request, ?int $packageId = null): array
    {
        $maxSort = Package::max('sort_order') ?? 0;

        if ($request->has('is_active')) {
            $request->merge(['is_active' => $request->boolean('is_active')]);
        }

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'slug' => ['nullable', 'string', 'max:150', Rule::unique('packages', 'slug')->ignore($packageId, 'id')],
            'tier' => 'nullable|string|max:50',
            'category' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'description' => 'required|string',
            'features_input' => 'nullable|string',
            'features_payload' => 'nullable|string',
            'image' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'includes_digital_invitation' => 'nullable|boolean',
            'promo_label' => 'nullable|string|max:80',
            'promo_description' => 'nullable|string|max:255',
            'promo_discount_percent' => 'nullable|numeric|min:0|max:100',
            'promo_expires_at' => 'nullable|date|after:today',
        ], [
            'slug.unique' => 'Slug sudah digunakan. Silakan gunakan slug lain.',
            'is_active.boolean' => 'Status aktif tidak valid.',
            'promo_discount_percent.max' => 'Diskon maks 100%.',
            'promo_expires_at.after' => 'Tanggal promo harus di masa depan.',
        ]);

        $data['slug'] = Str::slug($data['slug'] ?? $data['name']);
        $data['tier'] = $data['tier'] ? Str::lower($data['tier']) : null;
        $data['features'] = $this->resolveFeatures(
            $data['features_payload'] ?? null,
            $data['features_input'] ?? null
        );
        unset($data['features_input'], $data['features_payload']);

        if (!isset($data['sort_order'])) {
            $data['sort_order'] = $maxSort + 1;
        }

        $data['category'] = $data['category'] ?: 'lainnya';
        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $data['includes_digital_invitation'] = array_key_exists('includes_digital_invitation', $data)
            ? (bool) $data['includes_digital_invitation']
            : true;

        return $data;
    }

    private function resolveFeatures(?string $payload, ?string $legacy): array
    {
        $decoded = $this->parseFeatureSections($payload);
        if (!empty($decoded)) {
            return $decoded;
        }

        return $this->parseLegacyFeatures($legacy ?? '');
    }

    private function handleMediaUploads(Request $request, Package $package): void
    {
        if ($request->hasFile('image_file')) {
            $request->validate([
                'image_file' => 'image|max:2048',
            ]);

            if ($package->image && !str_starts_with($package->image, 'http')) {
                Storage::disk('public')->delete($package->image);
            }

            $path = $request->file('image_file')->store('packages', 'public');
            $package->update(['image' => $path]);
        }

        if ($request->hasFile('media_uploads')) {
            $request->validate([
                'media_uploads.*' => 'file|max:51200|mimes:jpg,jpeg,png,webp,mp4,webm,mov',
            ]);
        }

        $package->loadMissing('mediaItems');
        $existingMedia = $package->mediaItems;
        $existingCount = $existingMedia->count();

        $mediaFiles = array_values(array_filter($request->file('media_uploads', [])));
        $rawVideoUrls = preg_split('/\r?\n/', (string) $request->input('media_video_urls', ''), -1, PREG_SPLIT_NO_EMPTY);
        $validVideoUrls = [];
        foreach ($rawVideoUrls as $url) {
            $url = trim($url);
            if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
                $validVideoUrls[] = $url;
            }
        }

        $newItemsCount = count($mediaFiles) + count($validVideoUrls);
        if ($newItemsCount > 0 && ($existingCount + $newItemsCount) > PackageMediaItem::MAX_ITEMS_PER_PACKAGE) {
            $remainingSlots = max(0, PackageMediaItem::MAX_ITEMS_PER_PACKAGE - $existingCount);
            throw ValidationException::withMessages([
                'media_uploads' => 'Maksimal ' . PackageMediaItem::MAX_ITEMS_PER_PACKAGE . ' media per paket. Slot tersisa ' . $remainingSlots . '.',
            ]);
        }

        $nextSortOrder = ($existingMedia->max('sort_order') ?? -1) + 1;

        foreach ($mediaFiles as $media) {
            $mime = $media->getMimeType();
            $isVideo = $mime && str_starts_with($mime, 'video/');
            $path = $media->store('package-media', 'public');
            $package->mediaItems()->create([
                'media_type' => $isVideo ? 'video' : 'image',
                'media_path' => $path,
                'sort_order' => $nextSortOrder++,
            ]);
        }

        foreach ($validVideoUrls as $url) {
            $package->mediaItems()->create([
                'media_type' => 'video',
                'video_url' => $url,
                'sort_order' => $nextSortOrder++,
            ]);
        }
    }

    private function parseLegacyFeatures(?string $raw): array
    {
        if (!$raw) {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split("/(\r\n|\r|\n)/", $raw))));
    }

    private function parseFeatureSections(?string $payload): array
    {
        if (!$payload) {
            return [];
        }

        $decoded = json_decode($payload, true);
        if (!is_array($decoded)) {
            return [];
        }

        return collect($decoded)
            ->map(function ($section) {
                $items = $this->sanitizeFeatureItems($section['items'] ?? []);
                $title = isset($section['title']) && $section['title'] !== ''
                    ? trim((string) $section['title'])
                    : null;

                if (!$title && empty($items)) {
                    return null;
                }

                return [
                    'title' => $title,
                    'items' => $items,
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    private function sanitizeFeatureItems($items): array
    {
        if (is_string($items)) {
            $items = preg_split("/(\r\n|\r|\n)/", $items);
        }

        return collect($items)
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }
}
