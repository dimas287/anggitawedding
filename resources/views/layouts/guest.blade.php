<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Anggita Wedding Organizer')</title>
    <meta name="description" content="@yield('meta_description', 'Wedding organizer Surabaya dengan layanan dekorasi, rias, dokumentasi, dan undangan digital. Konsultasi gratis bersama Anggita Wedding Organizer.')">
    <meta name="robots" content="index,follow,max-image-preview:large">
    <link rel="canonical" href="{{ url()->current() }}">
    <script>
        // Set initial dark mode state to prevent flash
        // Default to dark mode if no preference is saved
        if (localStorage.getItem('theme') === 'dark' || !localStorage.getItem('theme')) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                mode: localStorage.getItem('theme') || 'dark',
                toggle() {
                    this.mode = this.mode === 'dark' ? 'light' : 'dark';
                    localStorage.setItem('theme', this.mode);
                    document.documentElement.classList.toggle('dark', this.mode === 'dark');
                }
            });
        });
    </script>

    @php
        $defaultOgImage = $brandInfo['logo_main_url'] ?? asset('images/og-default.jpg');
        $appIcon = $brandInfo['logo_icon_url'] ?? asset('favicon.ico');
        $isSvgIcon = Str::endsWith($appIcon, '.svg');
    @endphp

    {{-- Favicon --}}
    @if($isSvgIcon)
        <link rel="icon" type="image/svg+xml" href="{{ $appIcon }}">
    @else
        <link rel="icon" type="image/png" href="{{ $appIcon }}">
    @endif
    <link rel="apple-touch-icon" href="{{ $appIcon }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $brandInfo['brand_name'] ?? 'Anggita Wedding Organizer' }}">
    <meta property="og:title" content="@yield('title', 'Anggita Wedding Organizer')">
    <meta property="og:description" content="@yield('meta_description', 'Wedding organizer Surabaya dengan layanan dekorasi, rias, dokumentasi, dan undangan digital.')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', $defaultOgImage)">

    {{-- Twitter --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Anggita Wedding Organizer')">
    <meta name="twitter:description" content="@yield('meta_description', 'Wedding organizer Surabaya dengan layanan dekorasi, rias, dokumentasi, dan undangan digital.')">
    <meta name="twitter:image" content="@yield('og_image', $defaultOgImage)">

    {{-- PWA --}}
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#111111">

    {{-- JSON-LD LocalBusiness --}}
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "LocalBusiness",
      "name": "Anggita Wedding Organizer - Wedding Organizer Surabaya",
      "image": "{{ asset('images/logo.png') }}",
      "@id": "{{ url('/') }}",
      "url": "{{ url('/') }}",
      "telephone": "+628123456789",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Surabaya",
        "addressLocality": "Surabaya",
        "addressRegion": "Jawa Timur",
        "addressCountry": "ID"
      },
      "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": [
          "Monday",
          "Tuesday",
          "Wednesday",
          "Thursday",
          "Friday",
          "Saturday",
          "Sunday"
        ],
        "opens": "08:00",
        "closes": "20:00"
      }
    }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>


        /* Global Styles */
        :root {
            --color-ink: #111111;
            --color-muted: #888888;
            --color-soft: #FAF9F6;
        }

        ::selection { background: rgba(0, 0, 0, 0.1); color: #111; }

        html {
            overflow-x: clip;
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Space Grotesk', 'Poppins', sans-serif;
            background-color: #FFFFFF;
            color: var(--color-ink);
            position: relative;
            min-height: 100vh;
            margin: 0;
            overflow-x: clip;
            -webkit-font-smoothing: antialiased;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1 0 auto;
            width: 100%;
        }

        .font-playfair { font-family: 'Playfair Display', serif; }
        .hero-minimalist { background-color: #000; }
        .gold-gradient { background: #111; color: #fff; }
        html.dark .gold-gradient { background: #1a1a1a !important; color: #fff !important; }
        .text-gold { color: #8C7B62; }
        .border-gold { border-color: #8C7B62; }
        .bg-gold { background-color: #8C7B62; }
        .hover\:bg-gold:hover { background-color: #7A6952; }
        .glass-card { background: rgba(255, 255, 255, 0.92); box-shadow: 0 15px 40px rgba(20, 16, 38, 0.08); backdrop-filter: blur(20px); }
        .noise-surface { position: relative; }
        .noise-surface::after { content: ''; position: absolute; inset: 0; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='160' height='160' viewBox='0 0 160 160'%3E%3Cg fill-opacity='0.04'%3E%3Cpolygon fill='%23000' points='0,0 40,0 80,40 40,80 0,40'/%3E%3C/g%3E%3C/svg%3E"); opacity: .6; mix-blend-mode: soft-light; pointer-events: none; border-radius: inherit; }
        [x-cloak] { display: none !important; }

        .back-to-top {
            position: fixed;
            bottom: 30px;
            left: 30px;
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            mix-blend-mode: difference;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            z-index: 9998;
            transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
        }

        .back-to-top.is-visible {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .back-to-top:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-5px);
        }

        .flatpickr-calendar.anggita-picker {
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(31, 27, 46, 0.15);
            border: none;
            padding: 0 0 18px;
        }
        .flatpickr-calendar.anggita-picker .flatpickr-months {
            border-bottom: none;
            padding: 14px 18px 6px;
        }
        .flatpickr-calendar.anggita-picker .flatpickr-current-month {
            font-weight: 600;
            color: #2d1f44;
        }
        .flatpickr-calendar.anggita-picker .flatpickr-day {
            border-radius: 12px;
            font-weight: 500;
            color: #51486d;
        }
        .flatpickr-calendar.anggita-picker .flatpickr-day:hover,
        .flatpickr-calendar.anggita-picker .flatpickr-day:focus {
            background: rgba(244, 199, 119, 0.2);
        }
        .flatpickr-calendar.anggita-picker .flatpickr-day.selected,
        .flatpickr-calendar.anggita-picker .flatpickr-day.startRange,
        .flatpickr-calendar.anggita-picker .flatpickr-day.endRange {
            background: linear-gradient(135deg, #F4C76A, #E7A650, #F68787);
            color: #fff;
            box-shadow: 0 8px 16px rgba(244, 199, 106, 0.35);
        }
        .flatpickr-calendar.anggita-picker .flatpickr-day.today {
            border-color: rgba(244, 199, 106, 0.6);
        }
        .flatpickr-calendar.anggita-picker .flatpickr-weekday {
            color: #a29abd;
            font-weight: 600;
        }

        .brand-logo {
            width: 2.75rem;
            height: 2.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        /* Day/Night Sky Toggle */
        .sky-toggle {
            position: relative;
            width: 58px;
            height: 28px;
            border-radius: 999px;
            padding: 3px;
            cursor: pointer;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            display: flex;
            align-items: center;
        }
        
        .sky-toggle-light {
            background: #f8f6f2; /* Soft Brand Cream */
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
        }
        
        .sky-toggle-dark {
            background: #111111; /* Brand Ink */
            box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.1);
        }

        .sky-thumb {
            position: relative;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .sun-icon {
            width: 100%;
            height: 100%;
            background: #C5A059; /* Brand Gold */
            box-shadow: 0 0 10px rgba(197, 160, 89, 0.4);
        }

        .moon-icon {
            width: 100%;
            height: 100%;
            background: #FAF9F6; /* Soft White */
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
            position: relative;
        }

        .moon-crater {
            position: absolute;
            background: #e5e1d8;
            border-radius: 50%;
        }

        /* Clouds */
        .cloud {
            position: absolute;
            background: #ffffff;
            border-radius: 50%;
            transition: all 0.5s ease;
            z-index: 1;
            opacity: 0.9;
        }

        /* Stars */
        .star {
            position: absolute;
            background: #C5A059; /* Gold Stars */
            border-radius: 50%;
            transition: all 0.5s ease;
            z-index: 1;
        }

        /* Custom Modern Cursor */
        .custom-cursor {
            width: 8px;
            height: 8px;
            background: #111;
            border-radius: 50%;
            position: fixed;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 999999;
            transition: transform 0.1s ease-out;
            mix-blend-mode: exclusion;
        }

        .custom-cursor-outline {
            width: 30px;
            height: 30px;
            border: 1px solid rgba(17, 17, 17, 0.3);
            border-radius: 50%;
            position: fixed;
            top: -11px;
            left: -11px;
            pointer-events: none;
            z-index: 999998;
            transition: transform 0.15s ease-out;
        }

        @media (hover: none) and (pointer: coarse) {
            .custom-cursor, .custom-cursor-outline { display: none; }
        }

        a:hover ~ .custom-cursor,
        button:hover ~ .custom-cursor {
            transform: scale(2.5);
            background: #C5A059;
        }

        a:hover ~ .custom-cursor-outline,
        button:hover ~ .custom-cursor-outline {
            transform: scale(1.5);
            border-color: #C5A059;
        }
    </style>
    @stack('head')
</head>
<body class="font-poppins text-gray-800 dark:text-gray-200 antialiased bg-[#FAF9F6] dark:bg-[#0A0A0A] selection:bg-yellow-200 dark:selection:bg-yellow-900 selection:text-yellow-900 dark:selection:text-yellow-100 min-h-screen flex flex-col relative">

    <div class="custom-cursor"></div>
    <div class="custom-cursor-outline"></div>

{{-- Navbar --}}
@php
    $brandName = $brandInfo['brand_name'] ?? 'Anggita';
    $brandTagline = $brandInfo['tagline'] ?? 'Wedding Organizer';
    $brandLogo = $brandInfo['logo_main_url'] ?? null;
    $brandLogoLight = $brandInfo['logo_light_url'] ?? $brandLogo;
    $brandIcon = $brandInfo['logo_icon_url'] ?? $brandLogo ?? $brandLogoLight;

    $lightNavRoutes = ['landing', 'login', 'register', 'password.request', 'password.reset'];
    $prefersDarkNavText = !request()->routeIs($lightNavRoutes);
    $compactFooterRoutes = ['login', 'register', 'password.request', 'password.reset'];
    $footerPaddingClass = request()->routeIs($compactFooterRoutes) ? 'py-10 lg:py-12' : 'py-16';

    $navItems = [
        ['route' => 'landing', 'label' => 'Beranda'],
        [
            'label' => 'Paket',
            'is_dropdown' => true,
            'active_routes' => ['packages', 'digital-invitations', 'booking.select-package', 'booking.form', 'invitation.maintenance', 'invitation-order.start'],
            'children' => [
                ['route' => 'packages', 'active_routes' => ['packages', 'booking.select-package', 'booking.form'], 'label' => 'Paket Wedding', 'icon' => 'fa-ring', 'desc' => 'Perencanaan momen bersejarah Anda'],
                ['route' => 'digital-invitations', 'active_routes' => ['digital-invitations', 'invitation.maintenance', 'invitation-order.start'], 'label' => 'Undangan Digital', 'icon' => 'fa-envelope-open-text', 'desc' => 'Sebarkan kabar bahagia secara elegan'],
            ]
        ],
        ['route' => 'portfolio', 'label' => 'Portofolio'],
        ['route' => 'blog.index', 'label' => 'Blog'],
        ['route' => 'faq', 'label' => 'FAQ']
    ];
@endphp

<nav x-data="{ open: false, scrolled: false, prefersDarkText: @json($prefersDarkNavText) }" @scroll.window="scrolled = window.scrollY > 30"
     :class="scrolled ? 'bg-white shadow-sm border-b border-gray-100 dark:bg-[#0A0A0A] dark:border-white/10' : 'bg-transparent border-b border-transparent'"
     class="fixed w-full top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <a href="{{ route('landing') }}" class="h-full flex items-center gap-3 group">
                <div class="brand-logo">
                    @if($brandLogo)
                        <img src="{{ $brandLogo }}" alt="{{ $brandName }} logo">
                    @else
                        <i class="fas fa-rings-wedding text-white text-base"></i>
                    @endif
                </div>
                <div>
                    <div :class="(scrolled || prefersDarkText) ? 'text-gray-900 dark:text-white' : 'text-white'" class="font-playfair font-semibold text-xl tracking-wide group-hover:text-gray-500 transition-colors">{{ $brandName }}</div>
                    <div :class="(scrolled || prefersDarkText) ? 'text-gray-500 dark:text-gray-400' : 'text-white/80'" class="text-[9px] tracking-[0.4em] uppercase font-medium">{{ $brandTagline }}</div>
                </div>
            </a>

            <div class="hidden lg:flex items-center gap-8 h-full">
                @foreach($navItems as $item)
                    @if(isset($item['is_dropdown']) && $item['is_dropdown'])
                        <div class="relative group h-full flex items-center" x-data="{ dropdownOpen: false }" @mouseenter="dropdownOpen = true" @mouseleave="dropdownOpen = false">
                            <button type="button"
                               :class="scrolled ? 'text-gray-800 hover:text-black dark:text-gray-300 dark:hover:text-white' : (prefersDarkText ? 'text-gray-800 hover:text-black dark:text-gray-200 dark:hover:text-white' : 'text-white/90 hover:text-white')"
                               class="text-[11px] uppercase tracking-[0.2em] font-medium transition-colors relative flex items-center gap-1.5 focus:outline-none h-full">
                                <span class="{{ request()->routeIs($item['active_routes']) ? 'text-yellow-600 dark:text-yellow-400' : '' }}">{{ $item['label'] }}</span>
                                <i class="fas fa-chevron-down text-[9px] opacity-80 transition-transform duration-300" :class="dropdownOpen ? 'rotate-180' : ''"></i>
                                @if(request()->routeIs($item['active_routes']))
                                    <span class="absolute bottom-5 left-1/2 -translate-x-1/2 w-5 h-0.5 bg-yellow-600 dark:bg-yellow-400 rounded-full"></span>
                                @endif
                            </button>

                            {{-- Dropdown Panel --}}
                            <div x-show="dropdownOpen"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 pointer-events-none"
                                 x-transition:enter-end="opacity-100 pointer-events-auto"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 pointer-events-auto"
                                 x-transition:leave-end="opacity-0 pointer-events-none"
                                 class="absolute top-full left-1/2 -translate-x-1/2 pt-4 w-72 z-50" style="display: none;">
                                 
                                <div class="bg-white/95 dark:bg-[#111111]/95 backdrop-blur-2xl border border-gray-100 dark:border-white/10 shadow-[0_20px_40px_rgba(0,0,0,0.08)] rounded-2xl overflow-hidden p-2">
                                    @foreach($item['children'] as $child)
                                    @php $isChildActive = request()->routeIs($child['active_routes'] ?? $child['route']); @endphp
                                    <a href="{{ route($child['route']) }}" class="flex items-start gap-4 p-4 rounded-xl {{ $isChildActive ? 'bg-yellow-50/50 dark:bg-yellow-900/10' : 'hover:bg-gray-50/80 dark:hover:bg-white/5' }} transition-all group/link">
                                        <div class="w-10 h-10 rounded-full {{ $isChildActive ? 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-600' : 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-500' }} flex items-center justify-center shrink-0 group-hover/link:scale-110 group-hover/link:bg-yellow-100 dark:group-hover/link:bg-yellow-900/40 transition-all duration-300">
                                            <i class="fas {{ $child['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold {{ $isChildActive ? 'text-yellow-600 dark:text-yellow-500' : 'text-gray-900 dark:text-white' }} mb-0.5 group-hover/link:text-yellow-600 dark:group-hover/link:text-yellow-500 transition-colors">{{ $child['label'] }}</p>
                                            <p class="text-[11px] {{ $isChildActive ? 'text-yellow-700/70 dark:text-yellow-400/70' : 'text-gray-500 dark:text-gray-400' }} leading-relaxed normal-case tracking-normal">{{ $child['desc'] }}</p>
                                        </div>
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route($item['route']) }}"
                           :class="scrolled ? 'text-gray-800 hover:text-black dark:text-gray-300 dark:hover:text-white' : (prefersDarkText ? 'text-gray-800 hover:text-black dark:text-gray-200 dark:hover:text-white' : 'text-white/90 hover:text-white')"
                           class="text-[11px] uppercase tracking-[0.2em] font-medium transition-colors relative h-full flex items-center">
                            <span class="{{ request()->routeIs($item['route']) ? 'text-yellow-600 dark:text-yellow-400' : '' }}">{{ $item['label'] }}</span>
                            @if(request()->routeIs($item['route']))
                                <span class="absolute bottom-5 left-1/2 -translate-x-1/2 w-5 h-0.5 bg-yellow-600 dark:bg-yellow-400 rounded-full"></span>
                            @endif
                        </a>
                    @endif
                @endforeach
            </div>

            <div class="hidden lg:flex items-center gap-4 h-full">
                <button type="button" @click="$store.theme.toggle()"
                        class="sky-toggle group/toggle"
                        :class="$store.theme.mode === 'light' ? 'sky-toggle-light' : 'sky-toggle-dark'">
                    
                    {{-- Sky Elements --}}
                    <div class="absolute inset-0 overflow-hidden pointer-events-none">
                        {{-- Day: Clouds --}}
                        <template x-if="$store.theme.mode === 'light'">
                            <div class="contents">
                                <div class="cloud w-6 h-3 -right-1 top-1 opacity-80" style="box-shadow: 8px 4px 0 -2px #fff, -4px 6px 0 -1px #fff;"></div>
                                <div class="cloud w-4 h-2 right-4 bottom-1 opacity-60"></div>
                            </div>
                        </template>
                        
                        {{-- Night: Stars --}}
                        <template x-if="$store.theme.mode === 'dark'">
                            <div class="contents">
                                <div class="star w-0.5 h-0.5 left-4 top-2 animate-pulse"></div>
                                <div class="star w-0.5 h-0.5 left-8 top-1 opacity-70"></div>
                                <div class="star w-0.5 h-0.5 left-6 bottom-2"></div>
                            </div>
                        </template>
                    </div>

                    {{-- Thumb: Sun / Moon --}}
                    <div class="sky-thumb shadow-lg" 
                         :style="$store.theme.mode === 'light' ? 'transform: translateX(0)' : 'transform: translateX(30px)'">
                        <div x-show="$store.theme.mode === 'light'" class="sun-icon"></div>
                        <div x-show="$store.theme.mode === 'dark'" class="moon-icon" x-cloak>
                            <div class="moon-crater w-1 h-1 top-1 left-2"></div>
                            <div class="moon-crater w-1.5 h-1.5 bottom-1 right-2"></div>
                        </div>
                    </div>
                </button>
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}"
                       class="text-[11px] uppercase tracking-wider font-bold px-5 py-2.5 rounded-full border transition-all"
                       :class="scrolled ? 'border-yellow-600 text-yellow-600 hover:bg-yellow-600 hover:text-white' : (prefersDarkText ? 'border-gray-900 text-gray-900 hover:bg-gray-900 hover:text-white' : 'border-white text-white hover:bg-white hover:text-black')">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       :class="scrolled ? 'text-gray-800 dark:text-gray-300 hover:text-black dark:hover:text-white' : (prefersDarkText ? 'text-gray-800 dark:text-gray-300 hover:text-black dark:hover:text-white' : 'text-white/90 hover:text-white')"
                       class="text-[11px] uppercase tracking-[0.2em] font-medium transition-colors h-full flex items-center">Masuk</a>
                    <a href="{{ route('consultation.form') }}"
                       :class="scrolled ? 'bg-gray-900 text-white hover:bg-black' : (prefersDarkText ? 'bg-gray-900 text-white hover:bg-black' : 'bg-white text-black hover:bg-gray-100')"
                       class="text-[11px] uppercase tracking-[0.15em] font-bold px-6 py-2.5 rounded shadow-sm transition-all">
                        Konsultasi
                    </a>
                @endauth
            </div>

            <button @click="open = !open" class="lg:hidden p-2" :class="(scrolled || prefersDarkText) ? 'text-gray-700 dark:text-gray-300' : 'text-white'">
                <i :class="open ? 'fa-xmark' : 'fa-bars'" class="fas text-xl"></i>
            </button>
        </div>
    </div>

    <div x-show="open" x-cloak class="lg:hidden">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="open = false"></div>
        <div class="absolute inset-x-0 top-0 mx-4 mt-4 rounded-3xl bg-white/95 dark:bg-black/95 backdrop-blur-xl shadow-2xl border border-white/60 dark:border-white/10 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 dark:border-white/10">
                <div class="flex items-center gap-3">
                    <div class="brand-logo">
                        @if($brandLogo)
                            <img src="{{ $brandLogo }}" alt="{{ $brandName }} logo">
                        @else
                            <i class="fas fa-rings-wedding text-yellow-500 text-base"></i>
                        @endif
                    </div>
                    <div>
                        <div class="font-playfair font-semibold text-gray-900 dark:text-white text-lg tracking-tight">{{ $brandName }}</div>
                        <div class="text-yellow-500 text-xs tracking-widest uppercase">{{ $brandTagline }}</div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" @click="$store.theme.toggle()"
                            class="sky-toggle group/toggle scale-90 origin-right"
                            :class="$store.theme.mode === 'light' ? 'sky-toggle-light' : 'sky-toggle-dark'">
                        
                        {{-- Sky Elements --}}
                        <div class="absolute inset-0 overflow-hidden pointer-events-none">
                            {{-- Day: Clouds --}}
                            <template x-if="$store.theme.mode === 'light'">
                                <div class="contents">
                                    <div class="cloud w-6 h-3 -right-1 top-1 opacity-80" style="box-shadow: 8px 4px 0 -2px #fff, -4px 6px 0 -1px #fff;"></div>
                                    <div class="cloud w-4 h-2 right-4 bottom-1 opacity-60"></div>
                                </div>
                            </template>
                            
                            {{-- Night: Stars --}}
                            <template x-if="$store.theme.mode === 'dark'">
                                <div class="contents">
                                    <div class="star w-0.5 h-0.5 left-4 top-2 animate-pulse"></div>
                                    <div class="star w-0.5 h-0.5 left-8 top-1 opacity-70"></div>
                                    <div class="star w-0.5 h-0.5 left-6 bottom-2"></div>
                                </div>
                            </template>
                        </div>

                        {{-- Thumb: Sun / Moon --}}
                        <div class="sky-thumb shadow-lg" 
                             :style="$store.theme.mode === 'light' ? 'transform: translateX(0)' : 'transform: translateX(30px)'">
                            <div x-show="$store.theme.mode === 'light'" class="sun-icon"></div>
                            <div x-show="$store.theme.mode === 'dark'" class="moon-icon" x-cloak>
                                <div class="moon-crater w-1 h-1 top-1 left-2"></div>
                                <div class="moon-crater w-1.5 h-1.5 bottom-1 right-2"></div>
                            </div>
                        </div>
                    </button>
                    <button @click="open = false" class="w-10 h-10 rounded-2xl bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="px-5 py-4 space-y-1">
                @foreach($navItems as $item)
                    @if(isset($item['is_dropdown']) && $item['is_dropdown'])
                        <div x-data="{ submenuOpen: {{ request()->routeIs($item['active_routes']) ? 'true' : 'false' }} }" class="rounded-2xl overflow-hidden transition-colors" :class="submenuOpen ? 'bg-gray-50 border border-gray-100 dark:bg-white/5 dark:border-white/5' : 'border border-transparent'">
                            <button @click="submenuOpen = !submenuOpen" class="w-full flex items-center justify-between py-3 px-3 text-gray-800 dark:text-gray-200 font-medium hover:bg-gray-50 dark:hover:bg-white/5 transition-colors focus:outline-none">
                                <span class="{{ request()->routeIs($item['active_routes']) ? 'text-yellow-600 dark:text-yellow-500' : '' }}">{{ $item['label'] }}</span>
                                <i class="fas fa-chevron-down text-[10px] transition-transform duration-300" :class="submenuOpen ? 'rotate-180 text-yellow-600 dark:text-yellow-500' : 'text-gray-400 dark:text-gray-600'"></i>
                            </button>
                            <div x-show="submenuOpen" x-collapse>
                                <div class="px-3 pb-3 space-y-1 pt-1">
                                    @foreach($item['children'] as $child)
                                        @php $isChildActive = request()->routeIs($child['active_routes'] ?? $child['route']); @endphp
                                        <a href="{{ route($child['route']) }}" class="flex items-center gap-3 py-2.5 px-4 rounded-xl text-sm {{ $isChildActive ? 'text-yellow-600 dark:text-yellow-500 bg-white dark:bg-black/20 border-gray-100 dark:border-white/5 shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400 hover:bg-white dark:hover:bg-black/20 border-transparent hover:border-gray-100 dark:hover:border-white/5' }} transition-all border">
                                            <i class="fas {{ $child['icon'] }} w-5 text-center text-xs opacity-70"></i>
                                            {{ $child['label'] }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route($item['route']) }}" class="flex items-center justify-between py-3 px-3 text-gray-800 dark:text-gray-200 font-medium rounded-2xl hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <span class="{{ request()->routeIs($item['route']) ? 'text-yellow-600 dark:text-yellow-500' : '' }}">{{ $item['label'] }}</span>
                            <i class="fas fa-chevron-right text-[10px] text-gray-400 dark:text-gray-600 opacity-50"></i>
                        </a>
                    @endif
                @endforeach
            </div>
            <div class="px-5 pb-5">
                <div class="h-px bg-gray-100 dark:bg-white/10 mb-4"></div>
                @auth
                    <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}" class="flex items-center justify-between py-3 px-3 rounded-2xl border border-gray-200 dark:border-white/10 text-gray-800 dark:text-white font-semibold hover:border-gray-300 dark:hover:border-white/20 transition-all">
                        Dashboard
                        <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                    </a>
                @else
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('login') }}" class="flex items-center justify-center py-3.5 rounded-2xl border border-gray-100 dark:border-white/10 text-gray-800 dark:text-white font-medium text-xs uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-white/5 transition-all">
                            Masuk
                        </a>
                        <a href="{{ route('consultation.form') }}" class="flex items-center justify-center py-3.5 rounded-2xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold text-xs uppercase tracking-widest hover:bg-black dark:hover:bg-gray-100 transition-all shadow-lg shadow-gray-200 dark:shadow-none">
                            Konsultasi
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</nav>

<div class="flex-1 flex flex-col w-full">
    <main>
        @yield('content')
    </main>

{{-- Footer --}}
<footer class="bg-[#111111] text-gray-400 mt-auto">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 {{ $footerPaddingClass }}">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="md:col-span-2">
                <div class="flex items-center gap-2 mb-4">
                    <div class="brand-logo">
                        @if($brandLogo)
                            <img src="{{ $brandLogo }}" alt="{{ $brandName }} logo" class="opacity-50 grayscale">
                        @else
                            <i class="fas fa-rings-wedding text-gray-500 text-sm"></i>
                        @endif
                    </div>
                    <div>
                        <div class="font-playfair text-white text-lg tracking-wide">{{ $brandName }}</div>
                        <div class="text-gray-500 text-[10px] uppercase tracking-[0.3em] mt-0.5">Wedding Organizer</div>
                    </div>
                </div>
                <p class="text-sm leading-relaxed text-gray-400 mb-4">{{ $footerInfo['description'] ?? 'Wujudkan pernikahan impian Anda bersama kami.' }}</p>
                <div class="flex gap-3">
                    @foreach([
                        ['key' => 'instagram', 'icon' => 'fa-instagram', 'hover' => 'hover:bg-yellow-600'],
                        ['key' => 'whatsapp', 'icon' => 'fa-whatsapp', 'hover' => 'hover:bg-green-600'],
                        ['key' => 'facebook', 'icon' => 'fa-facebook', 'hover' => 'hover:bg-blue-600'],
                        ['key' => 'tiktok', 'icon' => 'fa-tiktok', 'hover' => 'hover:bg-red-600'],
                    ] as $social)
                        @php $url = $footerInfo['socials'][$social['key']] ?? null; @endphp
                        @if($url)
                        <a href="{{ $url }}" target="_blank" rel="noopener" class="w-9 h-9 rounded-full bg-gray-800 {{ $social['hover'] }} flex items-center justify-center transition-colors"><i class="fab {{ $social['icon'] }} text-sm"></i></a>
                        @endif
                    @endforeach
                </div>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Layanan</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('packages') }}" class="hover:text-gold transition-colors">Paket Silver</a></li>
                    <li><a href="{{ route('packages') }}" class="hover:text-gold transition-colors">Paket Gold</a></li>
                    <li><a href="{{ route('packages') }}" class="hover:text-gold transition-colors">Paket Premium</a></li>
                    <li><a href="{{ route('consultation.form') }}" class="hover:text-gold transition-colors">Konsultasi Gratis</a></li>
                    <li><a href="{{ route('landing') }}#undangan" class="hover:text-gold transition-colors">Undangan Digital</a></li>
                    <li><a href="{{ route('blog.index') }}" class="hover:text-gold transition-colors">Wedding Blog</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white font-semibold mb-4">Kontak</h4>
                <ul class="space-y-2 text-sm">
                    @php $addressUrl = $footerInfo['address_url'] ?? null; @endphp
                    <li class="flex items-start gap-2">
                        <i class="fas fa-map-marker-alt text-gold mt-1 text-xs"></i>
                        @if($addressUrl)
                            <a href="{{ $addressUrl }}" target="_blank" rel="noopener" class="hover:text-gold">
                                {{ $footerInfo['address'] ?? 'Jl. Bulak Setro Indah 2 Blok C No. 5, Surabaya' }}
                            </a>
                        @else
                            <span>{{ $footerInfo['address'] ?? 'Jl. Bulak Setro Indah 2 Blok C No. 5, Surabaya' }}</span>
                        @endif
                    </li>
                    <li class="flex items-center gap-2"><i class="fab fa-whatsapp text-gold text-xs"></i><a href="{{ $footerInfo['phone_link'] ?? '#' }}" class="hover:text-gold">{{ $footerInfo['phone_display'] ?? '+62 812-3112-2057' }}</a></li>
                    <li class="flex items-center gap-2"><i class="fas fa-envelope text-gold text-xs"></i><a href="mailto:{{ $footerInfo['email'] ?? 'anggitaweddingsurabaya@gmail.com' }}" class="hover:text-gold">{{ $footerInfo['email'] ?? 'anggitaweddingsurabaya@gmail.com' }}</a></li>
                </ul>
            </div>
        </div>
        <div class="border-t border-white/5 mt-10 pt-6 flex flex-col md:flex-row justify-between items-center text-xs text-gray-500">
            <p>© {{ date('Y') }} Anggita Wedding Organizer. All rights reserved.</p>
            <p class="mt-2 md:mt-0">Made with <span class="text-red-400">♥</span> for beautiful weddings</p>
        </div>
    </div>
</footer>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (!window.flatpickr) return;

        window.initAnggitaPickers = function (root = document) {
            root.querySelectorAll('[data-flatpickr]').forEach(function (el) {
                if (el._flatpickr) return;
                flatpickr(el, {
                    dateFormat: 'Y-m-d',
                    minDate: el.dataset.minDate || el.getAttribute('min') || 'today',
                    defaultDate: el.value || null,
                    disableMobile: true,
                    locale: {
                        firstDayOfWeek: 1
                    },
                    onReady: function (_, __, fp) {
                        fp.calendarContainer.classList.add('anggita-picker');
                    },
                    onOpen: function (_, __, fp) {
                        fp.calendarContainer.classList.add('anggita-picker');
                    }
                });
            });
        };

        initAnggitaPickers();
    });
</script>

    @include('components.global-loader')
    @include('components.auth-status-modal')
    @include('components.lazyload-script')

    @stack('scripts')
    
    <script>
        // Custom Cursor Logic
        const cursor = document.querySelector('.custom-cursor');
        const outline = document.querySelector('.custom-cursor-outline');
        
        document.addEventListener('mousemove', (e) => {
            if (cursor && outline) {
                const posX = e.clientX;
                const posY = e.clientY;
                
                cursor.style.transform = `translate3d(${posX}px, ${posY}px, 0)`;
                // Outline with a slight delay for trailing effect
                requestAnimationFrame(() => {
                    outline.style.transform = `translate3d(${posX}px, ${posY}px, 0)`;
                });

                // Trailing Hearts Logic
                createHeart(posX, posY);
            }
        });

        let activeHearts = 0;
        const maxHearts = 12;

        function createHeart(x, y) {
            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
            if (activeHearts >= maxHearts) return;
            if (Math.random() > 0.07) return;

            const heart = document.createElement('div');
            // Randomly pick between black and gold heart
            const isGold = Math.random() > 0.7;
            heart.innerHTML = isGold ? '💛' : '🖤';
            heart.style.position = 'fixed';
            heart.style.left = x + 'px';
            heart.style.top = y + 'px';
            heart.style.fontSize = (Math.random() * 15 + 8) + 'px';
            heart.style.pointerEvents = 'none';
            heart.style.zIndex = '999997';
            heart.style.opacity = '0.8';
            heart.style.transition = 'all 1.2s cubic-bezier(0.16, 1, 0.3, 1)';
            heart.style.transform = 'translate(-50%, -50%) scale(0.5)';
            
            document.body.appendChild(heart);
            activeHearts++;

            // Animate and remove
            requestAnimationFrame(() => {
                const angle = Math.random() * Math.PI * 2;
                const dist = Math.random() * 80 + 30;
                heart.style.transform = `translate(${-50 + Math.cos(angle) * dist}%, ${-50 + Math.sin(angle) * dist}%) rotate(${Math.random() * 720}deg) scale(1.2)`;
                heart.style.opacity = '0';
            });

            setTimeout(() => {
                heart.remove();
                activeHearts = Math.max(0, activeHearts - 1);
            }, 1200);
        }

        // Hover effect for all interactive elements
        const interactiveElements = document.querySelectorAll('a, button, [role="button"], input, select, textarea');
        interactiveElements.forEach(el => {
            el.addEventListener('mouseenter', () => {
                cursor?.classList.add('cursor-hover');
                outline?.classList.add('cursor-hover');
            });
            el.addEventListener('mouseleave', () => {
                cursor?.classList.remove('cursor-hover');
                outline?.classList.remove('cursor-hover');
            });
        });
        const btt = document.getElementById('backToTop');
        const footer = document.querySelector('footer');
        if (btt && footer) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        btt.classList.add('is-visible');
                    } else {
                        btt.classList.remove('is-visible');
                    }
                });
            }, { threshold: 0.1 });
            observer.observe(footer);
        }


    </script>
    {{-- Back to Top --}}
    <button type="button" 
            class="back-to-top" 
            id="backToTop" 
            onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
            aria-label="Back to Top">
        <i class="fas fa-arrow-up"></i>
    </button>

    @include('components.whatsapp-float')
</body>
</html>
