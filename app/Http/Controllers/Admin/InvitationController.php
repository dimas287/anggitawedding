<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationTemplate;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InvitationController extends Controller
{
    public function templates()
    {
        $templates = InvitationTemplate::orderBy('sort_order')->paginate(15);
        return view('admin.invitations.templates', compact('templates'));
    }

    public function storeTemplate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|unique:invitation_templates,slug',
            'theme' => 'required|in:classic,modern,rustic,floral,minimalist,royal,bohemian,garden',
            'primary_color' => 'nullable|string|max:10',
            'secondary_color' => 'nullable|string|max:10',
            'font_family' => 'nullable|string|max:50',
            'demo_slug' => 'nullable|string|max:150',
            'price' => 'nullable|numeric|min:0',
            'promo_label' => 'nullable|string|max:100',
            'promo_description' => 'nullable|string',
            'promo_discount_percent' => 'nullable|numeric|min:0|max:100',
            'promo_expires_at' => 'nullable|date',
            'sort_order' => 'nullable|integer|min:1',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'preview_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'is_premium' => 'nullable|boolean',
            'demo_groom_name' => 'nullable|string|max:100',
            'demo_bride_name' => 'nullable|string|max:100',
            'demo_groom_father' => 'nullable|string|max:150',
            'demo_groom_mother' => 'nullable|string|max:150',
            'demo_bride_father' => 'nullable|string|max:150',
            'demo_bride_mother' => 'nullable|string|max:150',
            'demo_akad_datetime' => 'nullable|date',
            'demo_akad_venue' => 'nullable|string|max:150',
            'demo_akad_address' => 'nullable|string|max:255',
            'demo_reception_datetime' => 'nullable|date',
            'demo_reception_venue' => 'nullable|string|max:150',
            'demo_reception_address' => 'nullable|string|max:255',
            'demo_maps_link' => 'nullable|string|max:255',
            'demo_love_story' => 'nullable|string',
            'demo_opening_quote' => 'nullable|string',
            'demo_closing_message' => 'nullable|string',
            'demo_hashtag' => 'nullable|string|max:100',
            'demo_music_file' => 'nullable|mimetypes:audio/mpeg,audio/mp3,audio/wav,audio/x-wav,audio/aac|max:15360',
            'demo_photo_prewedding' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'demo_gallery' => 'nullable|array',
            'demo_gallery.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'demo_clear_gallery' => 'nullable|boolean',
            'demo_clear_photo' => 'nullable|boolean',
            'demo_clear_music' => 'nullable|boolean',
        ]);

        $data = $request->only([
            'name', 'slug', 'theme', 'primary_color', 'secondary_color',
            'font_family', 'demo_slug', 'price', 'promo_label',
            'promo_description', 'promo_discount_percent', 'promo_expires_at',
            'is_active', 'sort_order', 'is_premium'
        ]);

        if ($request->has('media_slots')) {
            $slots = json_decode($request->input('media_slots'), true);
            $data['media_slots'] = is_array($slots) ? $slots : null;
        }
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('templates/thumbnails', 'public');
        }
        if ($request->hasFile('preview_image')) {
            $data['preview_image'] = $request->file('preview_image')->store('templates/previews', 'public');
        }

        $data['price'] = $request->input('price', 0);
        $data['promo_discount_percent'] = $request->input('promo_discount_percent', 0);
        $data['promo_expires_at'] = $request->input('promo_expires_at');
        $data['is_active'] = $request->boolean('is_active', true);
        $data['is_premium'] = $request->boolean('is_premium', false);
        $data['slug'] = $this->generateUniqueSlug($request->input('slug', $request->name));
        $demoPayload = $this->prepareDemoContent($request);
        $data['demo_content'] = $demoPayload['content'];
        $data['demo_gallery'] = $demoPayload['gallery'];

        InvitationTemplate::create($data);
        return back()->with('success', 'Template undangan berhasil ditambahkan.');
    }

    public function updateTemplate(Request $request, InvitationTemplate $template)
    {
        $request->validate([
            'name' => 'sometimes|string|max:100',
            'slug' => 'sometimes|string|unique:invitation_templates,slug,' . $template->id,
            'theme' => 'sometimes|in:classic,modern,rustic,floral,minimalist,royal,bohemian,garden',
            'primary_color' => 'nullable|string|max:10',
            'secondary_color' => 'nullable|string|max:10',
            'font_family' => 'nullable|string|max:50',
            'demo_slug' => 'nullable|string|max:150',
            'price' => 'nullable|numeric|min:0',
            'promo_label' => 'nullable|string|max:100',
            'promo_description' => 'nullable|string',
            'promo_discount_percent' => 'nullable|numeric|min:0|max:100',
            'promo_expires_at' => 'nullable|date',
            'sort_order' => 'nullable|integer|min:1',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'preview_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'demo_groom_name' => 'nullable|string|max:100',
            'demo_bride_name' => 'nullable|string|max:100',
            'demo_groom_father' => 'nullable|string|max:150',
            'demo_groom_mother' => 'nullable|string|max:150',
            'demo_bride_father' => 'nullable|string|max:150',
            'demo_bride_mother' => 'nullable|string|max:150',
            'demo_akad_datetime' => 'nullable|date',
            'demo_akad_venue' => 'nullable|string|max:150',
            'demo_akad_address' => 'nullable|string|max:255',
            'demo_reception_datetime' => 'nullable|date',
            'demo_reception_venue' => 'nullable|string|max:150',
            'demo_reception_address' => 'nullable|string|max:255',
            'demo_maps_link' => 'nullable|string|max:255',
            'demo_love_story' => 'nullable|string',
            'demo_opening_quote' => 'nullable|string',
            'demo_closing_message' => 'nullable|string',
            'demo_hashtag' => 'nullable|string|max:100',
            'demo_music_file' => 'nullable|mimetypes:audio/mpeg,audio/mp3,audio/wav,audio/x-wav,audio/aac|max:15360',
            'demo_photo_prewedding' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'demo_gallery' => 'nullable|array',
            'demo_gallery.*' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'demo_clear_gallery' => 'nullable|boolean',
            'demo_clear_photo' => 'nullable|boolean',
            'demo_clear_music' => 'nullable|boolean',
        ]);

        $data = $request->only([
            'name', 'slug', 'theme', 'primary_color', 'secondary_color',
            'font_family', 'demo_slug', 'price', 'promo_label',
            'promo_description', 'promo_discount_percent', 'promo_expires_at',
            'is_active', 'sort_order', 'is_premium'
        ]);

        if ($request->has('media_slots')) {
            $slots = json_decode($request->input('media_slots'), true);
            $data['media_slots'] = is_array($slots) ? $slots : null;
        }
        if ($request->hasFile('thumbnail')) {
            if ($template->thumbnail) {
                Storage::disk('public')->delete($template->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('templates/thumbnails', 'public');
        }
        if ($request->hasFile('preview_image')) {
            if ($template->preview_image) {
                Storage::disk('public')->delete($template->preview_image);
            }
            $data['preview_image'] = $request->file('preview_image')->store('templates/previews', 'public');
        }

        if ($request->has('price')) {
            $data['price'] = $request->input('price', 0);
        }
        if ($request->has('demo_slug')) {
            $data['demo_slug'] = $request->input('demo_slug');
        }
        if ($request->has('promo_discount_percent')) {
            $data['promo_discount_percent'] = $request->input('promo_discount_percent', 0);
        }
        if ($request->has('promo_expires_at')) {
            $data['promo_expires_at'] = $request->input('promo_expires_at');
        }
        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }
        if ($request->has('is_premium')) {
            $data['is_premium'] = $request->boolean('is_premium');
        }

        if (array_key_exists('slug', $data)) {
            $data['slug'] = $this->generateUniqueSlug($data['slug'] ?? $template->name, $template->id);
        }

        $demoPayload = $this->prepareDemoContent($request, $template);
        $data['demo_content'] = $demoPayload['content'];
        $data['demo_gallery'] = $demoPayload['gallery'];

        $template->update($data);
        return back()->with('success', 'Template berhasil diperbarui.');
    }

    public function destroyTemplate(InvitationTemplate $template)
    {
        $template->update(['is_active' => false]);
        return back()->with('success', 'Template dinonaktifkan.');
    }

    public function clientInvitation(Booking $booking)
    {
        $booking->load('invitation.template', 'invitation.rsvps', 'user');
        $invitation = $booking->invitation;
        $templates = InvitationTemplate::where('is_active', true)->get();
        return view('admin.invitations.client', compact('booking', 'invitation', 'templates'));
    }

    public function updateClientInvitation(Request $request, Booking $booking)
    {
        $invitation = $booking->invitation;
        if (!$invitation) {
            return back()->with('error', 'Undangan tidak ditemukan.');
        }
        
        $data = $request->only([
            'template_id', 'slug', 'groom_name', 'groom_short_name', 'bride_name', 'bride_short_name', 'groom_father', 'groom_mother',
            'bride_father', 'bride_mother', 'akad_datetime', 'akad_venue', 'akad_address',
            'reception_datetime', 'reception_venue', 'reception_address', 'maps_link',
            'love_story', 'opening_quote', 'closing_message', 'hashtag',
            'rsvp_enabled', 'view_count'
        ]);

        // Handle is_published checkbox (unchecked = not sent)
        $data['is_published'] = $request->has('is_published') ? 1 : 0;

        // Handle bank account (single account from form)
        if ($request->filled('bank_name') || $request->filled('bank_account')) {
            $data['bank_accounts'] = [[
                'bank_name'      => $request->input('bank_name', ''),
                'account_number' => $request->input('bank_account', ''),
                'account_name'   => $request->input('bank_owner', ''),
            ]];
        }

        // Handle QRIS image
        if ($request->hasFile('qris_image')) {
            if ($invitation->qris_image) {
                Storage::disk('local')->delete($invitation->qris_image);
            }
            $data['qris_image'] = $request->file('qris_image')->store('invitations/qris', 'local');
        }
        if ($request->has('clear_qris')) {
            if ($invitation->qris_image) {
                Storage::disk('local')->delete($invitation->qris_image);
            }
            $data['qris_image'] = null;
        }

        if ($request->hasFile('photo_prewedding')) {
            $data['photo_prewedding'] = $request->file('photo_prewedding')->store('invitations/prewedding', 'public');
        }

        if ($request->hasFile('music_file')) {
            $request->validate(['music_file' => 'file|mimes:mp3,ogg,wav|max:10240']);
            $data['music_file'] = $request->file('music_file')->store('invitations/music', 'public');
        }

        if ($request->hasFile('gallery_photos')) {
            $gallery = [];
            foreach ($request->file('gallery_photos') as $photo) {
                $gallery[] = $photo->store('invitations/gallery', 'public');
            }
            $data['gallery_photos'] = array_merge($invitation->gallery_photos ?? [], $gallery);
        }

        // Handle dynamic media slots
        if ($request->has('media_slots')) {
            $slotsData = $invitation->media_files ?? [];
            foreach ($request->file('media_slots', []) as $key => $fileOrFiles) {
                if (is_array($fileOrFiles)) {
                    $urls = [];
                    foreach ($fileOrFiles as $f) {
                        $urls[] = $f->store('invitations/dynamic', 'public');
                    }
                    $slotsData[$key] = $urls;
                } else {
                    $slotsData[$key] = $fileOrFiles->store('invitations/dynamic', 'public');
                }
            }
            $data['media_files'] = $slotsData;
        }

        $invitation->update($data);
        return back()->with('success', 'Data undangan berhasil diperbarui.');
    }

    public function resetLink(Booking $booking)
    {
        $invitation = $booking->invitation;
        if ($invitation) {
            $newSlug = \Illuminate\Support\Str::slug($booking->groom_name . '-' . $booking->bride_name . '-' . time());
            $invitation->update(['slug' => $newSlug]);
        }
        return back()->with('success', 'Link undangan berhasil direset.');
    }

    private function generateUniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $base = Str::slug($source) ?: Str::random(8);
        $slug = $base;
        $counter = 1;

        while (InvitationTemplate::where('slug', $slug)
            ->when($ignoreId, fn($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }

    private function prepareDemoContent(Request $request, ?InvitationTemplate $template = null): array
    {
        $existingContent = $template?->demo_content ?? [];
        $existingGallery = $template?->demo_gallery ?? [];

        $content = [
            'groom_name' => $this->nullIfEmpty($request->input('demo_groom_name', $existingContent['groom_name'] ?? null)),
            'bride_name' => $this->nullIfEmpty($request->input('demo_bride_name', $existingContent['bride_name'] ?? null)),
            'groom_father' => $this->nullIfEmpty($request->input('demo_groom_father', $existingContent['groom_father'] ?? null)),
            'groom_mother' => $this->nullIfEmpty($request->input('demo_groom_mother', $existingContent['groom_mother'] ?? null)),
            'bride_father' => $this->nullIfEmpty($request->input('demo_bride_father', $existingContent['bride_father'] ?? null)),
            'bride_mother' => $this->nullIfEmpty($request->input('demo_bride_mother', $existingContent['bride_mother'] ?? null)),
            'akad_datetime' => $this->normalizeDemoDate($request->input('demo_akad_datetime', $existingContent['akad_datetime'] ?? null)),
            'akad_venue' => $this->nullIfEmpty($request->input('demo_akad_venue', $existingContent['akad_venue'] ?? null)),
            'akad_address' => $this->nullIfEmpty($request->input('demo_akad_address', $existingContent['akad_address'] ?? null)),
            'reception_datetime' => $this->normalizeDemoDate($request->input('demo_reception_datetime', $existingContent['reception_datetime'] ?? null)),
            'reception_venue' => $this->nullIfEmpty($request->input('demo_reception_venue', $existingContent['reception_venue'] ?? null)),
            'reception_address' => $this->nullIfEmpty($request->input('demo_reception_address', $existingContent['reception_address'] ?? null)),
            'maps_link' => $this->nullIfEmpty($request->input('demo_maps_link', $existingContent['maps_link'] ?? null)),
            'love_story' => $this->nullIfEmpty($request->input('demo_love_story', $existingContent['love_story'] ?? null)),
            'opening_quote' => $this->nullIfEmpty($request->input('demo_opening_quote', $existingContent['opening_quote'] ?? null)),
            'closing_message' => $this->nullIfEmpty($request->input('demo_closing_message', $existingContent['closing_message'] ?? null)),
            'hashtag' => $this->nullIfEmpty($request->input('demo_hashtag', $existingContent['hashtag'] ?? null)),
            'rsvp_enabled' => $request->boolean('demo_rsvp_enabled', (bool)($existingContent['rsvp_enabled'] ?? false)),
        ];

        // Bank Account inside Demo Content
        $bankName = $request->input('demo_bank_name');
        $bankAccount = $request->input('demo_bank_account');
        $bankOwner = $request->input('demo_bank_owner');
        if ($bankName || $bankAccount || $bankOwner) {
            $content['bank_accounts'] = [
                [
                    'bank_name' => $bankName,
                    'account_number' => $bankAccount,
                    'account_name' => $bankOwner,
                ]
            ];
        } else {
            $content['bank_accounts'] = $existingContent['bank_accounts'] ?? [];
        }

        // QRIS
        $content['qris_image'] = $this->handleDemoSingleFile(
            $request, 'demo_qris', $existingContent['qris_image'] ?? null, 'templates/demo/qris', (bool)$request->boolean('demo_clear_qris')
        );

        $content['photo_prewedding_url'] = $this->handleDemoSingleFile(
            $request,
            'demo_photo_prewedding',
            $existingContent['photo_prewedding_url'] ?? null,
            'templates/demo/photos',
            (bool)$request->boolean('demo_clear_photo')
        );

        $content['music_file_url'] = $this->handleDemoSingleFile(
            $request,
            'demo_music_file',
            $existingContent['music_file_url'] ?? null,
            'templates/demo/music',
            (bool)$request->boolean('demo_clear_music')
        );

        $gallery = $existingGallery;

        if ($request->boolean('demo_clear_gallery')) {
            if (!empty($gallery)) {
                Storage::disk('public')->delete($gallery);
            }
            $gallery = [];
        }

        if ($request->hasFile('demo_gallery')) {
            if (!empty($gallery)) {
                Storage::disk('public')->delete($gallery);
            }
            $gallery = [];
            foreach ($request->file('demo_gallery') as $photo) {
                if ($photo) {
                    $gallery[] = $photo->store('templates/demo/gallery', 'public');
                }
            }
        }

        // Dynamic Media Files for Demo
        $demoMedia = $existingContent['media_files'] ?? [];
        
        // Handle clear actions
        foreach ($request->input('demo_clear_media', []) as $key => $clearValue) {
            if ($clearValue && isset($demoMedia[$key])) {
                $filesToDelete = is_array($demoMedia[$key]) ? $demoMedia[$key] : [$demoMedia[$key]];
                foreach ($filesToDelete as $fileUrl) {
                    Storage::disk('public')->delete($fileUrl);
                }
                unset($demoMedia[$key]);
            }
        }

        // Handle uploads
        if ($request->has('demo_media_files')) {
            foreach ($request->file('demo_media_files', []) as $key => $fileOrFiles) {
                if (is_array($fileOrFiles)) {
                    $urls = [];
                    foreach ($fileOrFiles as $f) {
                        $urls[] = $f->store('templates/demo/dynamic', 'public');
                    }
                    $demoMedia[$key] = $urls;
                } else {
                    $demoMedia[$key] = $fileOrFiles->store('templates/demo/dynamic', 'public');
                }
            }
        }
        $content['media_files'] = $demoMedia;

        return [
            'content' => $content,
            'gallery' => array_values(array_filter($gallery)),
        ];
    }

    private function normalizeDemoDate(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->toIso8601String();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function nullIfEmpty($value)
    {
        return $value === '' ? null : $value;
    }

    private function handleDemoSingleFile(Request $request, string $field, ?string $existingPath, string $directory, bool $clear): ?string
    {
        if ($clear) {
            if ($existingPath) {
                Storage::disk('public')->delete($existingPath);
            }
            $existingPath = null;
        }

        if ($request->hasFile($field)) {
            if ($existingPath) {
                Storage::disk('public')->delete($existingPath);
            }
            $existingPath = $request->file($field)->store($directory, 'public');
        }

        return $existingPath;
    }
}
