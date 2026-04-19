<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DreamHighlightCard;
use App\Models\HeroSlide;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SiteContentController extends Controller
{
    public function edit()
    {
        $hero = SiteSetting::getJson('landing_hero_copy', [
            'badge' => 'Dipercaya lebih dari 200+ pasangan',
            'fallback_subtitle' => 'Kami hadir untuk menjadikan hari terindah Anda menjadi kenangan tak terlupakan.',
            'primary_cta_label' => 'Pesan Sekarang',
            'primary_cta_url' => route('booking.select-package'),
            'secondary_cta_label' => 'Jadwalkan Konsultasi',
            'secondary_cta_url' => route('consultation.form'),
        ]);

        $dream = SiteSetting::getJson('landing_dream_section', [
            'eyebrow' => 'Wujudkan Pernikahan Impian Anda',
            'heading' => 'Satu tim penuh cinta untuk setiap detail hari bahagia Anda.',
            'description' => 'Mulai dari brainstorming konsep hingga memastikan prosesi berjalan mulus, tim Anggita siap mengawal perjalanan cinta Anda.',
            'highlights' => [
                ['icon' => 'fa-heart', 'title' => 'Tim Profesional', 'desc' => 'Wedding planner, MC, dan tim dekorasi berpengalaman.'],
                ['icon' => 'fa-magic', 'title' => 'Konsep Personal', 'desc' => 'Moodboard eksklusif sesuai tema favorit Anda.'],
                ['icon' => 'fa-shield-heart', 'title' => 'Kontrol Steril', 'desc' => 'SOP detail memastikan hari-H bebas panik.'],
                ['icon' => 'fa-camera-retro', 'title' => 'Dokumentasi Premium', 'desc' => 'Foto & video sinematik untuk mengabadikan momen.'],
            ],
            'primary_cta_label' => 'Lihat Paket',
            'primary_cta_url' => '#paket',
            'secondary_cta_label' => 'Konsultasi Dengan Planner',
            'secondary_cta_url' => route('consultation.form'),
            'hero_image' => 'https://images.unsplash.com/photo-1520854221050-0f4caff449fb?w=1200',
            'highlight_card' => [
                'title' => 'Anisa & Rizky',
                'subtitle' => 'Intimate wedding • Bandung',
                'quote' => '“Semua detail dieksekusi sempurna.”',
            ],
        ]);

        $stats = SiteSetting::getJson('landing_stats', [
            'events' => 0,
            'clients' => 0,
            'templates' => 0,
            'years' => 1,
        ]);

        $portfolioStatsDefaults = SiteSetting::portfolioStatsDefaults();
        $portfolioStatsSetting = SiteSetting::getJson('portfolio_stats', []);
        $portfolioStats = collect($portfolioStatsDefaults)->map(function ($stat, $key) use ($portfolioStatsSetting) {
            return array_merge($stat, $portfolioStatsSetting[$key] ?? []);
        })->all();

        $footer = SiteSetting::getJson('footer_info', [
            'description' => 'Wujudkan pernikahan impian Anda bersama kami.',
            'address' => 'Jl. Bulak Setro Indah 2 Blok C No. 5, Surabaya',
            'address_url' => 'https://maps.app.goo.gl/rnYQB2kmWPEj1XZ7A',
            'email' => 'anggitaweddingsurabaya@gmail.com',
            'phone_display' => '+62 812-3112-2057',
            'phone_link' => 'https://wa.me/6281231122057',
            'socials' => [
                'instagram' => 'https://instagram.com/anggita_wedding',
                'whatsapp' => 'https://wa.me/6281231122057',
                'facebook' => 'https://facebook.com/anggitawedding',
                'tiktok' => 'https://tiktok.com/@anggitawedding',
            ],
        ]);
        $brand = SiteSetting::getJson('brand_assets', [
            'brand_name' => 'Anggita Wedding',
            'tagline' => 'Make Up & Wedding Service',
            'logo_main' => null,
            'logo_light' => null,
            'logo_icon' => null,
        ]);
        $consultationSettings = SiteSetting::getJson('consultation_settings', [
            'admin_email' => config('mail.from.address'),
        ]);

        $maintenanceMode = (bool) SiteSetting::getValue('invitation_maintenance_mode', false);
        $maintenanceMessage = SiteSetting::getValue('invitation_maintenance_message', 'Fitur undangan digital sedang dalam perbaikan dan akan segera kembali. Terima kasih atas kesabarannya!');

        $globalMaintenanceMode = (bool) SiteSetting::getValue('global_maintenance_mode', false);
        $globalMaintenanceMessage = SiteSetting::getValue('global_maintenance_message', 'Website sedang dalam pemeliharaan rutin untuk meningkatkan kualitas layanan kami.');

        $slides = HeroSlide::orderBy('sort_order')->orderBy('id')->get();
        $highlightCards = DreamHighlightCard::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.site-content.edit', compact(
            'hero', 'dream', 'stats', 'portfolioStats', 'footer', 'slides', 'brand', 'consultationSettings',
            'maintenanceMode', 'maintenanceMessage', 'globalMaintenanceMode', 'globalMaintenanceMessage',
            'highlightCards'
        ));
    }

    public function updateMaintenance(Request $request)
    {
        $validated = $request->validate([
            'invitation_maintenance_mode' => 'required|boolean',
            'invitation_maintenance_message' => 'nullable|string|max:500',
            'global_maintenance_mode' => 'required|boolean',
            'global_maintenance_message' => 'nullable|string|max:500',
        ]);

        SiteSetting::setValue('invitation_maintenance_mode', $validated['invitation_maintenance_mode']);
        SiteSetting::setValue('invitation_maintenance_message', $validated['invitation_maintenance_message'] ?? '');
        
        SiteSetting::setValue('global_maintenance_mode', $validated['global_maintenance_mode']);
        SiteSetting::setValue('global_maintenance_message', $validated['global_maintenance_message'] ?? '');

        return redirect()->route('admin.site-content.edit', ['section' => 'maintenance-section'])
            ->with('success', 'Pengaturan mode maintenance berhasil diperbarui.');
    }

    public function updateHero(Request $request)
    {
        $data = $request->validate([
            'hero' => 'required|array',
            'hero.badge' => 'nullable|string|max:150',
            'hero.fallback_subtitle' => 'nullable|string|max:500',
            'hero.primary_cta_label' => 'nullable|string|max:120',
            'hero.primary_cta_url' => 'nullable|url|max:255',
            'hero.secondary_cta_label' => 'nullable|string|max:120',
            'hero.secondary_cta_url' => 'nullable|url|max:255',
        ]);

        SiteSetting::setJson('landing_hero_copy', $data['hero']);

        return redirect()->route('admin.site-content.edit', ['section' => 'hero-copy'])
            ->with('success', 'Hero section berhasil diperbarui.');
    }

    public function updateDream(Request $request)
    {
        $data = $request->validate([
            'dream' => 'required|array',
            'dream.eyebrow' => 'nullable|string|max:150',
            'dream.heading' => 'nullable|string|max:255',
            'dream.description' => 'nullable|string',
            'dream.highlights' => 'nullable|array|max:6',
            'dream.highlights.*.icon' => 'nullable|string|max:50',
            'dream.highlights.*.title' => 'nullable|string|max:120',
            'dream.highlights.*.desc' => 'nullable|string|max:255',
            'dream.primary_cta_label' => 'nullable|string|max:120',
            'dream.primary_cta_url' => 'nullable|string|max:255',
            'dream.secondary_cta_label' => 'nullable|string|max:120',
            'dream.secondary_cta_url' => 'nullable|string|max:255',
            'dream.hero_image' => 'nullable|string|max:500',
            'dream.hero_image_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'dream.highlight_card.title' => 'nullable|string|max:120',
            'dream.highlight_card.subtitle' => 'nullable|string|max:160',
            'dream.highlight_card.quote' => 'nullable|string|max:255',
        ]);

        $dreamSetting = SiteSetting::getJson('landing_dream_section', []);
        $dreamData = $data['dream'];

        if ($request->hasFile('dream.hero_image_file')) {
            if (!empty($dreamSetting['hero_image'])) {
                $publicPrefix = rtrim(Storage::url(''), '/');
                if (Str::startsWith($dreamSetting['hero_image'], $publicPrefix)) {
                    $relative = ltrim(Str::after($dreamSetting['hero_image'], $publicPrefix), '/');
                    Storage::disk('public')->delete($relative);
                }
            }

            $path = $request->file('dream.hero_image_file')->store('landing/dream', 'public');
            $dreamData['hero_image'] = Storage::url($path);
        }

        unset($dreamData['hero_image_file']);

        SiteSetting::setJson('landing_dream_section', $dreamData);

        return redirect()->route('admin.site-content.edit', ['section' => 'dream-section'])
            ->with('success', 'Section "Wujudkan Pernikahan" berhasil diperbarui.');
    }

    public function updateStats(Request $request)
    {
        $data = $request->validate([
            'stats' => 'required|array',
            'stats.events' => 'required|integer|min:0',
            'stats.clients' => 'required|integer|min:0',
            'stats.templates' => 'required|integer|min:0',
            'stats.years' => 'required|integer|min:0',
        ]);

        SiteSetting::setJson('landing_stats', $data['stats']);

        return redirect()->route('admin.site-content.edit', ['section' => 'landing-stats'])
            ->with('success', 'Statistik landing berhasil diperbarui.');
    }

    public function updatePortfolioStats(Request $request)
    {
        $defaults = SiteSetting::portfolioStatsDefaults();
        $rules = ['stats' => 'required|array'];

        foreach ($defaults as $key => $stat) {
            $rules["stats.$key.value"] = 'required|numeric|min:0';
            $rules["stats.$key.label"] = 'nullable|string|max:120';
            $rules["stats.$key.suffix"] = 'nullable|string|max:5';
            $rules["stats.$key.decimals"] = 'nullable|integer|min:0|max:2';
        }

        $validated = $request->validate($rules);

        $payload = [];
        foreach ($defaults as $key => $stat) {
            $payload[$key] = [
                'label' => $validated['stats'][$key]['label'] ?? $stat['label'],
                'value' => $validated['stats'][$key]['value'],
                'suffix' => $validated['stats'][$key]['suffix'] ?? ($stat['suffix'] ?? ''),
                'decimals' => isset($validated['stats'][$key]['decimals'])
                    ? (int) $validated['stats'][$key]['decimals']
                    : ($stat['decimals'] ?? 0),
            ];
        }

        SiteSetting::setJson('portfolio_stats', $payload);

        return redirect()->route('admin.site-content.edit', ['section' => 'portfolio-stats'])
            ->with('success', 'Statistik portofolio berhasil diperbarui.');
    }

    public function updateFooter(Request $request)
    {
        $data = $request->validate([
            'footer' => 'required|array',
            'footer.description' => 'nullable|string',
            'footer.address' => 'nullable|string',
            'footer.address_url' => 'nullable|url|max:255',
            'footer.email' => 'nullable|email|max:150',
            'footer.phone_display' => 'nullable|string|max:50',
            'footer.phone_link' => 'nullable|url|max:255',
            'footer.socials.instagram' => 'nullable|url|max:255',
            'footer.socials.whatsapp' => 'nullable|url|max:255',
            'footer.socials.facebook' => 'nullable|url|max:255',
            'footer.socials.tiktok' => 'nullable|url|max:255',
        ]);

        SiteSetting::setJson('footer_info', $data['footer']);

        return redirect()->route('admin.site-content.edit', ['section' => 'footer'])
            ->with('success', 'Konten footer berhasil diperbarui.');
    }

    public function updateBrand(Request $request)
    {
        $validated = $request->validate([
            'brand_name' => 'nullable|string|max:120',
            'tagline' => 'nullable|string|max:160',
            'logo_main' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'logo_light' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'logo_icon' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:1024',
        ]);

        $brand = SiteSetting::getJson('brand_assets', [
            'brand_name' => 'Anggita',
            'tagline' => 'Wedding Organizer',
            'logo_main' => null,
            'logo_light' => null,
            'logo_icon' => null,
        ]);

        foreach (['logo_main', 'logo_light', 'logo_icon'] as $key) {
            if ($request->hasFile($key)) {
                if (!empty($brand[$key])) {
                    Storage::disk('public')->delete($brand[$key]);
                }
                $brand[$key] = $request->file($key)->store('brand', 'public');
            }
        }

        $brand['brand_name'] = $validated['brand_name'] ?? $brand['brand_name'];
        $brand['tagline'] = $validated['tagline'] ?? $brand['tagline'];

        SiteSetting::setJson('brand_assets', $brand);

        return redirect()->route('admin.site-content.edit', ['section' => 'brand'])
            ->with('success', 'Branding berhasil diperbarui.');
    }

    public function updateConsultationSettings(Request $request)
    {
        $data = $request->validate([
            'consultation_settings' => 'required|array',
            'consultation_settings.admin_email' => 'nullable|string|max:500',
        ]);

        SiteSetting::setJson('consultation_settings', $data['consultation_settings']);

        return redirect()->route('admin.site-content.edit', ['section' => 'consultation'])
            ->with('success', 'Pengaturan notifikasi konsultasi berhasil diperbarui.');
    }

    public function storeHighlightCard(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:120',
            'subtitle' => 'nullable|string|max:160',
            'quote' => 'nullable|string|max:255',
            'image_url' => 'nullable|url|max:500',
            'image_upload' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('image_upload')) {
            $imagePath = $request->file('image_upload')->store('landing/highlights', 'public');
        }

        DreamHighlightCard::create([
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'quote' => $validated['quote'] ?? null,
            'image_path' => $imagePath,
            'image_url' => $imagePath ? null : ($validated['image_url'] ?? null),
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->route('admin.site-content.edit', ['section' => 'highlight-cards'])
            ->with('success', 'Highlight card berhasil ditambahkan.');
    }

    public function updateHighlightCard(Request $request, DreamHighlightCard $card)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:120',
            'subtitle' => 'nullable|string|max:160',
            'quote' => 'nullable|string|max:255',
            'image_url' => 'nullable|url|max:500',
            'image_upload' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $data = [
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?? null,
            'quote' => $validated['quote'] ?? null,
            'sort_order' => $validated['sort_order'] ?? 0,
            'is_active' => $validated['is_active'],
        ];

        if ($request->hasFile('image_upload')) {
            if ($card->image_path) {
                Storage::disk('public')->delete($card->image_path);
            }
            $data['image_path'] = $request->file('image_upload')->store('landing/highlights', 'public');
            $data['image_url'] = null;
        } elseif (!empty($validated['image_url'])) {
            if ($card->image_path) {
                Storage::disk('public')->delete($card->image_path);
                $data['image_path'] = null;
            }
            $data['image_url'] = $validated['image_url'];
        }

        $card->update($data);

        return redirect()->route('admin.site-content.edit', ['section' => 'highlight-cards'])
            ->with('success', 'Highlight card berhasil diperbarui.');
    }

    public function destroyHighlightCard(DreamHighlightCard $card)
    {
        if ($card->image_path) {
            Storage::disk('public')->delete($card->image_path);
        }
        $card->delete();

        return redirect()->route('admin.site-content.edit', ['section' => 'highlight-cards'])
            ->with('success', 'Highlight card berhasil dihapus.');
    }
}
