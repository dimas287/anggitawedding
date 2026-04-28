<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PortfolioImage;
use App\Models\Review;
use App\Models\InvitationTemplate;
use App\Models\Booking;
use App\Models\HeroSlide;
use App\Models\DreamHighlightCard;
use App\Models\SiteSetting;
use App\Models\Post;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Collection;

class LandingController extends Controller
{
    public function index()
    {
        $packages = $this->activePackagesWithPopularity();
        $popularPackageId = $packages->sortByDesc('popular_score')->first()?->id;
        $packagesByCategory = $packages->groupBy('category');
        $popularPackageIdsByCategory = $packagesByCategory->map(function ($group) {
            return $group->sortByDesc('popular_score')->first()?->id;
        });
        $categoryLabels = Package::CATEGORY_LABELS;

        $reviews = Review::where('is_published', true)->with('user', 'booking')->latest()->take(6)->get();
        $templates = InvitationTemplate::where('is_active', true)->orderBy('sort_order')->take(6)->get();
        $statDefaults = [
            'events' => Booking::where('status', 'completed')->count(),
            'clients' => Booking::distinct('user_id')->count(),
            'templates' => InvitationTemplate::where('is_active', true)->count(),
            'years' => max(1, date('Y') - 2018),
        ];
        $statsSetting = SiteSetting::getJson('landing_stats', []);
        $landingStats = array_merge($statDefaults, $statsSetting ?? []);

        $heroDefaults = [
            'badge' => 'Dipercaya lebih dari 200+ pasangan',
            'fallback_subtitle' => 'Kami hadir untuk menjadikan hari terindah dalam hidup Anda menjadi kenangan yang tak terlupakan. Dari konsultasi hingga pelaksanaan, kami tangani semuanya.',
            'primary_cta_label' => 'Pesan Sekarang',
            'primary_cta_url' => route('booking.select-package'),
            'secondary_cta_label' => 'Jadwalkan Konsultasi',
            'secondary_cta_url' => route('consultation.form'),
        ];
        $heroCopy = array_merge($heroDefaults, SiteSetting::getJson('landing_hero_copy', []) ?? []);

        $dreamDefaults = [
            'eyebrow' => 'Wujudkan Pernikahan Impian Anda',
            'heading' => 'Satu tim penuh cinta untuk setiap detail hari bahagia Anda.',
            'description' => 'Mulai dari brainstorming konsep, mengecek ketersediaan vendor, hingga memastikan prosesi berjalan mulus, tim Anggita siap mengawal perjalanan cinta Anda.',
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
            'highlights' => [
                ['icon' => 'fa-heart', 'title' => 'Tim Profesional', 'desc' => 'Wedding planner, MC, dan tim dekorasi berpengalaman.'],
                ['icon' => 'fa-magic', 'title' => 'Konsep Personal', 'desc' => 'Moodboard eksklusif sesuai tema favorit Anda.'],
                ['icon' => 'fa-shield-heart', 'title' => 'Kontrol Steril', 'desc' => 'SOP detail untuk memastikan hari-H bebas panik.'],
                ['icon' => 'fa-camera-retro', 'title' => 'Dokumentasi Premium', 'desc' => 'Foto & video sinematik untuk mengabadikan momen.'],
            ],
        ];
        $dreamSetting = SiteSetting::getJson('landing_dream_section', []) ?? [];
        $dreamSection = array_merge(Arr::except($dreamDefaults, ['highlights', 'highlight_card']), Arr::except($dreamSetting, ['highlights', 'highlight_card']));
        $dreamSection['highlights'] = $dreamSetting['highlights'] ?? $dreamDefaults['highlights'];
        $dreamSection['highlight_card'] = array_merge($dreamDefaults['highlight_card'], $dreamSetting['highlight_card'] ?? []);
        $heroSlides = HeroSlide::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $posts = Post::published()->latest()->take(6)->get();

        $processDefaults = [
            'eyebrow' => 'The Process',
            'heading' => 'Harmoni Pelayanan',
            'items' => [
                ['icon' => 'fa-calendar-check', 'title' => 'Reservasi Seamless', 'desc' => 'Pesan paket wedding kapan saja. Proses elegan dengan konfirmasi eksklusif.'],
                ['icon' => 'fa-credit-card', 'title' => 'Transaksi Privasi', 'desc' => 'Sistem pembayaran aman dengan opsi termin yang mengedepankan privasi Anda.'],
                ['icon' => 'fa-envelope-open-text', 'title' => 'Undangan Interaktif', 'desc' => 'Sentuhan digital modern untuk RSVP dan e-invitation yang mudah dibagikan.'],
                ['icon' => 'fa-comments', 'title' => 'Diskusi Personal', 'desc' => 'Wedding planner dedikatif siap merespons setiap detail impian pernikahan Anda.'],
                ['icon' => 'fa-file-pdf', 'title' => 'Dokumentasi Rapi', 'desc' => 'Seluruh rundown dan arsip dokumen tertata indah dalam format PDF profesional.'],
            ]
        ];
        $processSetting = SiteSetting::getJson('landing_process_section', []) ?? [];
        $processSection = array_merge(Arr::except($processDefaults, ['items']), Arr::except($processSetting, ['items']));
        $processSection['items'] = $processSetting['items'] ?? $processDefaults['items'];

        return view('landing', compact(
            'packages',
            'packagesByCategory',
            'reviews',
            'templates',
            'landingStats',
            'popularPackageId',
            'popularPackageIdsByCategory',
            'categoryLabels',
            'heroSlides',
            'heroCopy',
            'dreamSection',
            'processSection',
            'posts',
            'highlightCards'
        ));
    }

    public function packages()
    {
        $packages = $this->activePackagesWithPopularity();
        $popularPackageId = $packages->sortByDesc('popular_score')->first()?->id;
        $packagesByCategory = $packages->groupBy('category');
        $popularPackageIdsByCategory = $packagesByCategory->map(function ($group) {
            return $group->sortByDesc('popular_score')->first()?->id;
        });
        $categoryLabels = Package::CATEGORY_LABELS;
        return view('packages', compact('packages', 'packagesByCategory', 'popularPackageId', 'popularPackageIdsByCategory', 'categoryLabels'));
    }

    public function faq()
    {
        return view('faq');
    }

    public function digitalInvitations()
    {
        $templates = InvitationTemplate::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $statDefaults = [
            'events' => Booking::where('status', 'completed')->count(),
            'clients' => Booking::distinct('user_id')->count(),
            'templates' => InvitationTemplate::where('is_active', true)->count(),
            'years' => max(1, date('Y') - 2018),
        ];
        $statsSetting = SiteSetting::getJson('landing_stats', []);
        $landingStats = array_merge($statDefaults, $statsSetting ?? []);

        return view('digital-invitations', compact('templates', 'landingStats'));
    }

    public function portfolio()
    {
        $reviews = Review::where('is_published', true)->with('user', 'booking')->latest()->get();
        $completedBookings = Booking::where('status', 'completed')->with('package', 'user')->latest()->take(12)->get();
        $portfolioImages = PortfolioImage::with('mediaItems')
            ->where(function ($query) {
                $query->where('is_active', true)->orWhereNull('is_active');
            })
            ->orderBy('sort_order')
            ->latest('id')
            ->get();

        $portfolioStatsDefaults = SiteSetting::portfolioStatsDefaults();
        $portfolioStatsSetting = SiteSetting::getJson('portfolio_stats', []);
        $portfolioStats = collect($portfolioStatsDefaults)->map(function ($stat, $key) use ($portfolioStatsSetting) {
            return array_merge($stat, $portfolioStatsSetting[$key] ?? []);
        })->all();

        return view('portfolio', compact('reviews', 'completedBookings', 'portfolioImages', 'portfolioStats'));
    }

    private function activePackagesWithPopularity(): Collection
    {
        return Package::where('is_active', true)
            ->with('mediaItems')
            ->withCount(['bookings as popular_score' => function ($query) {
                $query->whereIn('status', ['pending', 'dp_paid', 'in_progress', 'completed']);
            }])
            ->orderBy('sort_order')
            ->get();
    }
}
