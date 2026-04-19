<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Models\AdminActivity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        // Define Global Rate Limiters (Operation Flash Flood)
        \Illuminate\Support\Facades\RateLimiter::for('global', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(100)->by($request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('chat', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(20)->by($request->user()?->id ?: $request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('booking', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->ip());
        });

        \Illuminate\Support\Facades\RateLimiter::for('api_heavy', function (\Illuminate\Http\Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(30)->by($request->ip());
        });

        if (config('app.force_https', false)) {
            URL::forceScheme('https');
        }

        AdminActivity::creating(function (AdminActivity $activity) {
            if (!$activity->route) {
                $activity->route = optional(request()->route())->getName();
            }

            if (!$activity->method) {
                $activity->method = request()->getMethod();
            }
        });

        $defaultBrand = [
            'brand_name' => 'Anggita Wedding',
            'tagline' => 'Make Up & Wedding Service',
            'logo_main' => null,
            'logo_light' => null,
            'logo_icon' => null,
            'social_links' => [
                'instagram' => 'https://instagram.com/anggita_wedding',
                'whatsapp' => 'https://wa.me/6281231122057',
                'facebook' => 'https://facebook.com/anggitawedding',
                'tiktok' => 'https://tiktok.com/@anggitawedding',
            ],
        ];

        View::composer(['layouts.guest', 'layouts.app', 'layouts.admin'], function ($view) use ($defaultBrand) {
            $brand = SiteSetting::getJson('brand_assets', $defaultBrand);
            $fallbackLogos = [
                'logo_main' => asset('images/brand/anggita-logo-main.svg'),
                'logo_light' => asset('images/brand/anggita-logo-main.svg'),
                'logo_icon' => asset('images/brand/anggita-logo-main.svg'),
            ];

            foreach (['logo_main', 'logo_light', 'logo_icon'] as $key) {
                $brand[$key . '_url'] = !empty($brand[$key])
                    ? Storage::url($brand[$key])
                    : ($fallbackLogos[$key] ?? null);
            }

            $view->with('brandInfo', $brand);
        });

        View::composer('layouts.guest', function ($view) {
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

            $view->with('footerInfo', $footer);
        });
    }
}
