<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class HeroSlideController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.site-content.edit', ['section' => 'hero-media']);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $this->handleUpload($request, $data);

        HeroSlide::create($data);

        return redirect()->route('admin.site-content.edit', ['section' => 'hero-media'])
            ->with('success', 'Slide hero berhasil ditambahkan.');
    }

    public function update(Request $request, HeroSlide $heroSlide)
    {
        $data = $this->validatedData($request, $heroSlide);
        $this->handleUpload($request, $data, $heroSlide);

        $heroSlide->update($data);

        return redirect()->route('admin.site-content.edit', ['section' => 'hero-media'])
            ->with('success', 'Slide hero berhasil diperbarui.');
    }

    public function destroy(HeroSlide $heroSlide)
    {
        if ($heroSlide->media_path) {
            Storage::disk('public')->delete($heroSlide->media_path);
        }

        $heroSlide->delete();

        return redirect()->route('admin.site-content.edit', ['section' => 'hero-media'])
            ->with('success', 'Slide hero berhasil dihapus.');
    }

    private function validatedData(Request $request, ?HeroSlide $slide = null): array
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:150'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'media_type' => ['required', Rule::in(['image', 'video'])],
            'media_url' => ['nullable', 'url', 'max:500'],
            'media_upload' => ['nullable', 'file', 'max:51200'], // 50MB
            'cta_label' => ['nullable', 'string', 'max:100'],
            'cta_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (!$request->hasFile('media_upload') && empty($data['media_url']) && empty($slide?->media_path) && empty($slide?->media_url)) {
            throw ValidationException::withMessages([
                'media_url' => 'Unggah file media atau isi URL media.',
            ]);
        }

        if ($request->hasFile('media_upload')) {
            $mimeRule = $data['media_type'] === 'video'
                ? ['mimetypes:video/mp4,video/webm']
                : ['mimes:jpg,jpeg,png,webp'];
            $request->validate(['media_upload' => $mimeRule]);
        }

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        unset($data['media_upload']);

        return $data;
    }

    private function handleUpload(Request $request, array &$data, ?HeroSlide $existing = null): void
    {
        if ($request->hasFile('media_upload')) {
            if ($existing?->media_path) {
                Storage::disk('public')->delete($existing->media_path);
            }

            $path = $request->file('media_upload')->store('hero-slides', 'public');
            $data['media_path'] = $path;
            $data['media_url'] = null;
        }
    }
}
