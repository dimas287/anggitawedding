<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') – Anggita WO</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Space Grotesk', sans-serif;
            background: radial-gradient(120% 120% at 0% 0%, #fef5ee 0%, #f9f1ff 35%, #eef2ff 100%);
            min-height: 100vh;
            color: #1f1b2e;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: radial-gradient(circle at 20% 20%, rgba(255, 198, 125, .18), transparent 45%),
                              radial-gradient(circle at 75% 0%, rgba(146, 116, 255, .15), transparent 40%),
                              radial-gradient(circle at 85% 70%, rgba(255, 133, 173, .18), transparent 35%);
            z-index: -2;
        }
        .font-playfair { font-family: 'Playfair Display', serif; }
        .gold-gradient { background: linear-gradient(135deg, #F7C977, #F39BC0, #8F82FF); }
        .sidebar-link.active {
            background: linear-gradient(135deg, rgba(247,201,119,.25), rgba(143,130,255,.25));
            color: #3c2b58;
            border: 1px solid rgba(247,201,119,.4);
            box-shadow: 0 8px 16px rgba(20,16,38,0.08);
        }
        .sidebar-shell { background: rgba(255,255,255,0.9); backdrop-filter: blur(18px); border-right: 1px solid rgba(255,255,255,0.6); }
        .badge-soft { background: rgba(255,255,255,0.6); border: 1px solid rgba(255,255,255,0.5); }
        .brand-logo {
            width: 2.75rem;
            height: 2.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .brand-logo img { width: 100%; height: 100%; object-fit: contain; }
        [x-cloak] { display: none !important; }
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
    </style>
    @stack('head')
</head>
@php
    $brandName = $brandInfo['brand_name'] ?? 'Anggita WO';
    $brandTagline = $brandInfo['tagline'] ?? 'Make Up & Wedding Service';
    $brandLogo = $brandInfo['logo_main_url'] ?? null;
    $brandLogoLight = $brandInfo['logo_light_url'] ?? $brandLogo;
    $brandIcon = $brandInfo['logo_icon_url'] ?? $brandLogo ?? $brandLogoLight;
@endphp

<body class="bg-transparent" x-data="{ sidebarOpen: false }">
<div class="custom-cursor"></div>
<div class="custom-cursor-outline"></div>

<div class="flex h-screen overflow-hidden">
    {{-- Sidebar --}}
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-50 w-68 max-w-[280px] sidebar-shell shadow-[0_20px_60px_rgba(30,10,60,0.12)] rounded-r-3xl transform transition-transform duration-300 md:relative md:translate-x-0 flex flex-col">
        <div class="p-6 border-b border-white/40">
            <a href="{{ route('landing') }}" class="flex items-center gap-3">
                <div class="brand-logo">
                    @if($brandLogo)
                        <img src="{{ $brandLogo }}" alt="{{ $brandName }}">
                    @else
                        <i class="fas fa-rings-wedding text-white text-sm"></i>
                    @endif
                </div>
                <div>
                    <div class="font-playfair font-bold text-gray-900 text-base tracking-tight">{{ $brandName }}</div>
                    <div class="text-xs uppercase tracking-[0.3em] text-yellow-600">{{ $brandTagline }}</div>
                </div>
            </a>
        </div>

        <div class="p-5 border-b border-white/40 bg-white/60">
            <div class="flex items-center gap-3">
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" class="w-12 h-12 rounded-2xl object-cover border-2 border-white">
                @else
                    <div class="w-12 h-12 rounded-2xl gold-gradient flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <div class="font-semibold text-gray-900 text-sm leading-tight">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-gray-500">{{ auth()->user()->email }}</div>
                </div>
            </div>
        </div>

        <nav class="flex-1 p-5 space-y-2 overflow-y-auto">
            <a href="{{ route('user.dashboard') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-gray-700 hover:bg-white/60 transition-colors {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home w-4"></i> Dashboard
            </a>
            @foreach(auth()->user()->bookings()->with(['package','invitation'])->latest()->get() as $b)
            @php
                $isInvitationOnly = $b->is_invitation_only;
                $statusLabel = $b->status_label;
                $paymentStatusMap = [
                    'unpaid' => 'Belum Bayar',
                    'dp_paid' => 'DP Terbayar',
                    'partially_paid' => 'Cicilan',
                    'paid_full' => 'Lunas',
                ];
                $paymentLabel = $paymentStatusMap[$b->payment_status] ?? ucwords(str_replace('_',' ', $b->payment_status));
                $typeLabel = $isInvitationOnly ? 'Undangan Digital' : 'Paket Wedding';
            @endphp
            <a href="{{ route('user.booking.show', $b->id) }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-white/60 transition-colors {{ request()->routeIs('user.booking.show') && optional(request()->route('booking'))->id == $b->id ? 'active' : '' }}">
                <div class="w-8 h-8 rounded-xl flex items-center justify-center {{ $isInvitationOnly ? 'bg-purple-50 text-purple-600' : 'bg-yellow-50 text-yellow-600' }}">
                    <i class="fas {{ $isInvitationOnly ? 'fa-envelope-open-text' : 'fa-calendar-heart' }} text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="truncate text-sm font-semibold text-gray-800">{{ $b->groom_name }} & {{ $b->bride_name }}</p>
                    <p class="text-[11px] text-gray-500 uppercase tracking-[0.2em]">{{ $typeLabel }}</p>
                </div>
            </a>
            @endforeach
            <hr class="my-2">
            <a href="{{ route('user.profile') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-white/60 transition-colors {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                <i class="fas fa-user w-4"></i> Profil Saya
            </a>
            <a href="{{ route('landing') }}" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-white/60 transition-colors">
                <i class="fas fa-globe w-4"></i> Lihat Website
            </a>
        </nav>

        <div class="p-5 border-t border-white/40">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold text-red-600 hover:bg-red-50/70 transition-colors">
                    <i class="fas fa-sign-out-alt w-4"></i> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- Overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 md:hidden" x-cloak></div>

    {{-- Main --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white/90 backdrop-blur border-b border-white/60 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="md:hidden p-1.5 rounded-lg hover:bg-gray-100">
                    <i class="fas fa-bars text-gray-600"></i>
                </button>
                <div>
                    <p class="text-[11px] uppercase tracking-[0.4em] text-gray-400">@yield('page-subtitle', 'Klien')</p>
                    <h1 class="text-xl font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('consultation.form') }}" class="hidden sm:flex items-center gap-2 text-xs font-semibold px-3 py-1.5 rounded-full badge-soft text-gray-700 hover:bg-white">
                    <i class="fas fa-comments"></i> Konsultasi
                </a>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-4 md:p-8">
            @yield('content')
        </main>
    </div>
</div>

@include('components.global-loader')
@include('components.auth-status-modal')
@include('components.lazyload-script')
@include('components.chat-widget-user')

@stack('scripts')
<script>
    const cursor = document.querySelector('.custom-cursor');
    const outline = document.querySelector('.custom-cursor-outline');
    document.addEventListener('mousemove', (e) => {
        if (!cursor || !outline) return;
        const { clientX: x, clientY: y } = e;
        cursor.style.transform = `translate3d(${x}px, ${y}px, 0)`;
        requestAnimationFrame(() => {
            outline.style.transform = `translate3d(${x}px, ${y}px, 0)`;
        });
        createSparkle(x, y);
    });

    function createSparkle(x, y) {
        if (window.matchMedia('(hover: none) and (pointer: coarse)').matches) return;
        if (Math.random() > 0.35) return;
        const sparkle = document.createElement('span');
        sparkle.textContent = Math.random() > 0.5 ? '✦' : '✧';
        sparkle.style.position = 'fixed';
        sparkle.style.left = x + 'px';
        sparkle.style.top = y + 'px';
        sparkle.style.fontSize = (Math.random() * 10 + 8) + 'px';
        sparkle.style.color = Math.random() > 0.5 ? '#f7c977' : '#8f82ff';
        sparkle.style.pointerEvents = 'none';
        sparkle.style.zIndex = '999997';
        sparkle.style.opacity = '0.8';
        sparkle.style.transition = 'all 1s cubic-bezier(0.16, 1, 0.3, 1)';
        sparkle.style.transform = 'translate(-50%, -50%) scale(0.6)';
        document.body.appendChild(sparkle);
        requestAnimationFrame(() => {
            const angle = Math.random() * Math.PI * 2;
            const dist = Math.random() * 60 + 20;
            sparkle.style.transform = `translate(${Math.cos(angle) * dist}px, ${Math.sin(angle) * dist}px) scale(1.2)`;
            sparkle.style.opacity = '0';
        });
        setTimeout(() => sparkle.remove(), 900);
    }

    document.querySelectorAll('a, button, [role="button"], input, select, textarea').forEach(el => {
        el.addEventListener('mouseenter', () => {
            cursor?.classList.add('cursor-hover');
            outline?.classList.add('cursor-hover');
        });
        el.addEventListener('mouseleave', () => {
            cursor?.classList.remove('cursor-hover');
            outline?.classList.remove('cursor-hover');
        });
    });
</script>
</body>
</html>
