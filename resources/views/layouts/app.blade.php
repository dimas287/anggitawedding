<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') – Anggita WO</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @php
        $brandName = $brandInfo['brand_name'] ?? 'Anggita WO';
        $brandTagline = $brandInfo['tagline'] ?? 'Make Up & Wedding Service';
        $brandLogo = $brandInfo['logo_main_url'] ?? null;
        $brandIcon = $brandInfo['logo_icon_url'] ?? $brandLogo ?? asset('favicon.ico');
        $isSvgIcon = Str::endsWith($brandIcon, '.svg');
    @endphp
    @if($isSvgIcon)
        <link rel="icon" type="image/svg+xml" href="{{ $brandIcon }}">
    @else
        <link rel="icon" type="image/png" href="{{ $brandIcon }}">
    @endif
    <link rel="apple-touch-icon" href="{{ $brandIcon }}">

    <style>
        :root {
            --sidebar-w: 260px;
            --gold-1: #c9a84c;
            --gold-2: #f0c96b;
            --accent-purple: #7c5cbf;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: #0d0d0f;
            min-height: 100vh;
            color: #e8e3f0;
            overflow-x: hidden;
        }

        /* ─── BACKGROUND AMBIENT ─── */
        body::before {
            content: '';
            position: fixed; inset: 0; z-index: -1;
            background:
                radial-gradient(ellipse 60% 50% at 10% 0%, rgba(124,92,191,.18) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 90% 80%, rgba(201,168,76,.12) 0%, transparent 60%),
                linear-gradient(160deg, #0d0d14 0%, #0e0a1a 50%, #120c10 100%);
        }

        .font-playfair { font-family: 'Playfair Display', serif; }

        /* ─── SIDEBAR ─── */
        .sidebar {
            width: var(--sidebar-w);
            background: rgba(18, 14, 30, 0.92);
            backdrop-filter: blur(24px) saturate(160%);
            border-right: 1px solid rgba(255,255,255,0.06);
            display: flex;
            flex-direction: column;
            position: fixed;
            inset-y: 0;
            left: 0;
            z-index: 50;
            transform: translateX(-100%);
            transition: transform .32s cubic-bezier(.4,0,.2,1), box-shadow .32s;
        }
        .sidebar.open {
            transform: translateX(0);
            box-shadow: 24px 0 60px rgba(0,0,0,.5);
        }
        @media (min-width: 1024px) {
            .sidebar { transform: translateX(0); position: relative; flex-shrink: 0; }
            .sidebar-overlay { display: none !important; }
        }

        /* Sidebar brand */
        .sidebar-brand {
            padding: 28px 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .sidebar-brand a { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .sidebar-logo {
            width: 42px; height: 42px;
            border-radius: 12px;
            overflow: hidden;
            background: rgba(201,168,76,.15);
            border: 1px solid rgba(201,168,76,.25);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-logo img { width: 32px; height: 32px; object-fit: contain; }
        .sidebar-brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 15px; font-weight: 700;
            color: #f0e8d8;
            line-height: 1.2;
        }
        .sidebar-brand-sub {
            font-size: 9px;
            letter-spacing: .25em;
            text-transform: uppercase;
            color: var(--gold-1);
            margin-top: 2px;
        }

        /* Sidebar user */
        .sidebar-user {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; gap: 12px;
        }
        .sidebar-avatar {
            width: 42px; height: 42px;
            border-radius: 12px;
            object-fit: cover;
            border: 2px solid rgba(201,168,76,.3);
        }
        .sidebar-avatar-initial {
            width: 42px; height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--gold-1), var(--accent-purple));
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 16px; color: #fff;
            border: 2px solid rgba(201,168,76,.3);
            flex-shrink: 0;
        }
        .sidebar-user-name { font-size: 13px; font-weight: 600; color: #f0e8d8; }
        .sidebar-user-email { font-size: 11px; color: rgba(255,255,255,.4); margin-top: 1px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; }

        /* Sidebar nav */
        .sidebar-nav { flex: 1; overflow-y: auto; padding: 16px 12px; }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 2px; }

        .sidebar-section-label {
            font-size: 9px; font-weight: 600;
            letter-spacing: .2em; text-transform: uppercase;
            color: rgba(255,255,255,.3);
            padding: 4px 10px 8px;
        }

        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 13px; font-weight: 500;
            color: rgba(255,255,255,.55);
            text-decoration: none;
            transition: all .2s;
            margin-bottom: 2px;
            position: relative;
        }
        .nav-link:hover {
            background: rgba(255,255,255,.06);
            color: rgba(255,255,255,.9);
        }
        .nav-link.active {
            background: linear-gradient(135deg, rgba(201,168,76,.18), rgba(124,92,191,.18));
            color: var(--gold-2);
            border: 1px solid rgba(201,168,76,.2);
        }
        .nav-link.active::before {
            content: '';
            position: absolute; left: 0; top: 25%; bottom: 25%;
            width: 3px; border-radius: 0 3px 3px 0;
            background: linear-gradient(180deg, var(--gold-1), var(--accent-purple));
        }
        .nav-link-icon {
            width: 32px; height: 32px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
            background: rgba(255,255,255,.06);
            flex-shrink: 0;
            transition: all .2s;
        }
        .nav-link.active .nav-link-icon {
            background: linear-gradient(135deg, rgba(201,168,76,.3), rgba(124,92,191,.3));
            color: var(--gold-2);
        }
        .nav-booking-card {
            margin-left: 42px;
            padding: 6px 8px;
            border-radius: 8px;
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.06);
            margin-bottom: 4px;
            text-decoration: none;
            display: block;
            transition: all .2s;
        }
        .nav-booking-card:hover { background: rgba(255,255,255,.08); border-color: rgba(201,168,76,.2); }
        .nav-booking-card.active { border-color: rgba(201,168,76,.3); background: rgba(201,168,76,.08); }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 12px;
            border-top: 1px solid rgba(255,255,255,.06);
        }
        .btn-logout {
            display: flex; align-items: center; gap: 10px;
            width: 100%; padding: 10px 12px;
            border-radius: 10px;
            font-size: 13px; font-weight: 500;
            color: rgba(255,100,100,.7);
            border: none; background: transparent;
            cursor: pointer;
            transition: all .2s;
            text-align: left;
        }
        .btn-logout:hover { background: rgba(255,100,100,.1); color: #ff8080; }

        /* ─── TOPBAR ─── */
        .topbar {
            height: 60px;
            background: rgba(18,14,30,0.85);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,0.06);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 20px;
            position: sticky; top: 0; z-index: 30;
            flex-shrink: 0;
        }
        .topbar-title { font-size: 16px; font-weight: 600; color: #f0e8d8; }
        .topbar-subtitle { font-size: 10px; text-transform: uppercase; letter-spacing: .25em; color: rgba(255,255,255,.35); margin-bottom: 1px; }

        .topbar-btn {
            width: 36px; height: 36px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,.1);
            background: rgba(255,255,255,.06);
            color: rgba(255,255,255,.7);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all .2s;
            font-size: 14px;
        }
        .topbar-btn:hover { background: rgba(255,255,255,.12); color: #fff; }

        /* ─── MAIN CONTENT ─── */
        .main-content {
            flex: 1;
            overflow-y: auto;
            padding: 24px 20px 120px; /* extra bottom for mobile nav */
        }
        @media (min-width: 1024px) {
            .main-content { padding: 28px 32px 40px; }
        }

        /* ─── MOBILE MAGIC BOTTOM NAV ─── */
        .mobile-nav {
            position: fixed;
            bottom: 16px; left: 50%;
            transform: translateX(-50%);
            width: calc(100% - 32px);
            max-width: 420px;
            height: 62px;
            background: linear-gradient(135deg, rgba(124,92,191,.85) 0%, rgba(180,120,210,.85) 50%, rgba(201,168,76,.85) 100%);
            backdrop-filter: blur(20px) saturate(180%);
            border-radius: 31px;
            border: 1px solid rgba(255,255,255,.15);
            box-shadow: 0 8px 32px rgba(0,0,0,.5), 0 0 0 1px rgba(255,255,255,.05) inset;
            display: flex; align-items: center; justify-content: space-around;
            padding: 0 8px;
            z-index: 100;
        }
        @media (min-width: 1024px) {
            .mobile-nav { display: none; }
        }

        .mnav-item {
            display: flex; flex-direction: column; align-items: center;
            justify-content: center;
            width: 52px; height: 52px;
            border-radius: 50%;
            cursor: pointer;
            position: relative;
            transition: all .3s cubic-bezier(.34,1.56,.64,1);
            text-decoration: none;
            color: rgba(255,255,255,.65);
        }
        .mnav-item span.mnav-label {
            font-size: 9px; font-weight: 600;
            letter-spacing: .03em;
            margin-top: 2px;
            opacity: 0;
            transform: translateY(4px);
            transition: all .25s;
            position: absolute;
            bottom: -14px;
            white-space: nowrap;
            color: #fff;
        }
        .mnav-item i {
            font-size: 18px;
            transition: all .3s cubic-bezier(.34,1.56,.64,1);
        }
        .mnav-item.active {
            background: rgba(255,255,255,.95);
            color: #7c5cbf;
            transform: translateY(-14px);
            box-shadow: 0 8px 24px rgba(0,0,0,.35), 0 0 0 4px rgba(255,255,255,.15);
        }
        .mnav-item.active i { font-size: 20px; }
        .mnav-item.active span.mnav-label {
            opacity: 1;
            transform: translateY(0);
            color: rgba(255,255,255,.9);
        }

        /* ─── OVERLAY ─── */
        .sidebar-overlay {
            position: fixed; inset: 0;
            background: rgba(0,0,0,.6);
            backdrop-filter: blur(4px);
            z-index: 49;
        }

        /* ─── PAGE CONTENT CARDS ─── */
        .card {
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 16px;
            backdrop-filter: blur(10px);
        }
        .card-glass {
            background: rgba(255,255,255,.04);
            border: 1px solid rgba(255,255,255,.06);
            border-radius: 16px;
        }

        [x-cloak] { display: none !important; }

        /* ─── CURSOR ─── */
        .custom-cursor {
            width: 8px; height: 8px;
            background: #fff;
            border-radius: 50%;
            position: fixed; top: 0; left: 0;
            pointer-events: none; z-index: 999999;
            transform: translate(-50%,-50%);
            mix-blend-mode: difference;
            transition: transform .05s linear;
        }
        .custom-cursor-outline {
            width: 28px; height: 28px;
            border: 1.5px solid rgba(255,255,255,.4);
            border-radius: 50%;
            position: fixed; top: 0; left: 0;
            pointer-events: none; z-index: 999998;
            transform: translate(-50%,-50%);
            transition: transform .12s ease-out, width .2s, height .2s, border-color .2s;
        }
        @media (hover: none) and (pointer: coarse) {
            .custom-cursor, .custom-cursor-outline { display: none; }
        }

        /* ─── SCROLLBAR ─── */
        .main-content::-webkit-scrollbar { width: 4px; }
        .main-content::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 2px; }
    </style>
    @stack('head')
</head>
<body x-data="{ sidebarOpen: false }">

<div class="custom-cursor" id="app-cursor"></div>
<div class="custom-cursor-outline" id="app-cursor-outline"></div>

<div class="flex h-screen overflow-hidden">

    {{-- ─── SIDEBAR ─── --}}
    <aside class="sidebar" :class="sidebarOpen ? 'open' : ''">

        {{-- Brand --}}
        <div class="sidebar-brand">
            <a href="{{ route('landing') }}">
                <div class="sidebar-logo">
                    @if($brandLogo)
                        <img src="{{ $brandLogo }}" alt="{{ $brandName }}">
                    @else
                        <i class="fas fa-rings-wedding" style="color: var(--gold-1); font-size: 16px;"></i>
                    @endif
                </div>
                <div>
                    <div class="sidebar-brand-name">{{ $brandName }}</div>
                    <div class="sidebar-brand-sub">{{ $brandTagline }}</div>
                </div>
            </a>
        </div>

        {{-- User --}}
        <div class="sidebar-user">
            @if(auth()->user()->avatar)
                <img src="{{ auth()->user()->avatar }}" class="sidebar-avatar" alt="{{ auth()->user()->name }}">
            @else
                <div class="sidebar-avatar-initial">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            @endif
            <div style="overflow: hidden;">
                <div class="sidebar-user-name">{{ auth()->user()->name }}</div>
                <div class="sidebar-user-email">{{ auth()->user()->email }}</div>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Menu Utama</div>

            <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <div class="nav-link-icon"><i class="fas fa-house-chimney"></i></div>
                <span>Dashboard</span>
            </a>

            @php
                $userBookings = auth()->user()->bookings()->with(['package','invitation'])->latest()->get();
            @endphp

            @if($userBookings->isNotEmpty())
                <div class="sidebar-section-label" style="margin-top: 12px;">Booking Saya</div>
                @foreach($userBookings as $b)
                    @php
                        $isActive = request()->routeIs('user.booking.show') && optional(request()->route('booking'))->id == $b->id;
                        $isInvOnly = $b->is_invitation_only;
                    @endphp
                    <a href="{{ route('user.booking.show', $b->id) }}"
                       class="nav-link {{ $isActive ? 'active' : '' }}"
                       style="align-items: flex-start; padding: 10px 12px;">
                        <div class="nav-link-icon" style="margin-top: 2px;">
                            <i class="fas {{ $isInvOnly ? 'fa-envelope-open-text' : 'fa-calendar-heart' }}"
                               style="{{ $isInvOnly ? 'color: #c084fc;' : '' }}"></i>
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <p style="font-size: 12px; font-weight: 600; color: rgba(255,255,255,.8); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $b->groom_name }} & {{ $b->bride_name }}
                            </p>
                            <p style="font-size: 10px; color: rgba(255,255,255,.35); text-transform: uppercase; letter-spacing: .15em; margin-top: 1px;">
                                {{ $isInvOnly ? 'Undangan' : 'Paket Wedding' }}
                            </p>
                        </div>
                    </a>
                @endforeach
            @endif

            <div class="sidebar-section-label" style="margin-top: 12px;">Akun</div>
            <a href="{{ route('user.profile') }}" class="nav-link {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                <div class="nav-link-icon"><i class="fas fa-user-circle"></i></div>
                <span>Profil Saya</span>
            </a>
            <a href="{{ route('consultation.form') }}" class="nav-link">
                <div class="nav-link-icon"><i class="fas fa-comments"></i></div>
                <span>Konsultasi</span>
            </a>
            <a href="{{ route('landing') }}" class="nav-link">
                <div class="nav-link-icon"><i class="fas fa-globe"></i></div>
                <span>Lihat Website</span>
            </a>
        </nav>

        {{-- Footer --}}
        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-arrow-right-from-bracket"></i>
                    <span>Keluar</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Overlay --}}
    <div class="sidebar-overlay" x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak></div>

    {{-- ─── MAIN AREA ─── --}}
    <div style="flex: 1; display: flex; flex-direction: column; overflow: hidden;">

        {{-- Topbar --}}
        <header class="topbar">
            <div style="display: flex; align-items: center; gap: 12px;">
                <button @click="sidebarOpen = true" class="topbar-btn lg:hidden">
                    <i class="fas fa-bars"></i>
                </button>
                <div>
                    <div class="topbar-subtitle">@yield('page-subtitle', 'Klien Area')</div>
                    <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 8px;">
                <a href="{{ route('consultation.form') }}" class="topbar-btn hidden sm:flex" title="Konsultasi">
                    <i class="fas fa-headset"></i>
                </a>
                @if(auth()->user()->avatar)
                    <img src="{{ auth()->user()->avatar }}" style="width:34px;height:34px;border-radius:10px;object-fit:cover;border:2px solid rgba(201,168,76,.3);" alt="">
                @else
                    <div style="width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,var(--gold-1),var(--accent-purple));display:flex;align-items:center;justify-content:center;font-weight:700;font-size:13px;color:#fff;border:2px solid rgba(201,168,76,.3);">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
            </div>
        </header>

        {{-- Content --}}
        <main class="main-content">
            @yield('content')
        </main>
    </div>
</div>

{{-- ─── MOBILE BOTTOM NAV (Magic Indicator) ─── --}}
<nav class="mobile-nav">
    <a href="{{ route('user.dashboard') }}"
       class="mnav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}"
       title="Dashboard">
        <i class="fas fa-house"></i>
        <span class="mnav-label">Home</span>
    </a>

    @php $firstBooking = $userBookings->first() ?? null; @endphp
    <a href="{{ $firstBooking ? route('user.booking.show', $firstBooking->id) : '#' }}"
       class="mnav-item {{ request()->routeIs('user.booking.show') ? 'active' : '' }}"
       title="Booking">
        <i class="fas fa-calendar-heart"></i>
        <span class="mnav-label">Booking</span>
    </a>

    <a href="{{ route('consultation.form') }}"
       class="mnav-item"
       title="Konsultasi">
        <i class="fas fa-comments"></i>
        <span class="mnav-label">Chat</span>
    </a>

    <a href="{{ route('user.profile') }}"
       class="mnav-item {{ request()->routeIs('user.profile') ? 'active' : '' }}"
       title="Profil">
        <i class="fas fa-user"></i>
        <span class="mnav-label">Profil</span>
    </a>
</nav>

@include('components.global-loader')
@include('components.auth-status-modal')
@include('components.lazyload-script')
@include('components.chat-widget-user')

@stack('scripts')

<script>
    // Custom cursor
    const cur = document.getElementById('app-cursor');
    const curO = document.getElementById('app-cursor-outline');
    let mx = 0, my = 0, ox = 0, oy = 0;

    document.addEventListener('mousemove', e => {
        mx = e.clientX; my = e.clientY;
        if (cur) cur.style.transform = `translate(${mx}px, ${my}px) translate(-50%,-50%)`;
    });

    function lerpCursor() {
        ox += (mx - ox) * 0.12;
        oy += (my - oy) * 0.12;
        if (curO) curO.style.transform = `translate(${ox}px, ${oy}px) translate(-50%,-50%)`;
        requestAnimationFrame(lerpCursor);
    }
    lerpCursor();

    document.querySelectorAll('a, button, [role="button"], input, select, textarea').forEach(el => {
        el.addEventListener('mouseenter', () => {
            if (curO) { curO.style.width = '44px'; curO.style.height = '44px'; curO.style.borderColor = 'rgba(201,168,76,.7)'; }
        });
        el.addEventListener('mouseleave', () => {
            if (curO) { curO.style.width = '28px'; curO.style.height = '28px'; curO.style.borderColor = 'rgba(255,255,255,.4)'; }
        });
    });
</script>
</body>
</html>
