<!DOCTYPE html>
<html lang="id" x-data="appShell()" :class="darkMode ? 'dark' : ''">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') – Anggita WO</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @php
        $brandName = $brandInfo['brand_name'] ?? 'Anggita WO';
        $brandTagline = $brandInfo['tagline'] ?? 'Make Up & Wedding Service';
        $brandLogo = $brandInfo['logo_main_url'] ?? null;
        $brandIcon = $brandInfo['logo_icon_url'] ?? $brandLogo ?? asset('favicon.ico');
        $isSvgIcon = Str::endsWith($brandIcon ?? '', '.svg');

        // Resolve avatar URL correctly
        $userAvatar = auth()->user()->avatar ?? null;
        if ($userAvatar) {
            $avatarUrl = Str::startsWith($userAvatar, ['http://', 'https://'])
                ? $userAvatar
                : \Illuminate\Support\Facades\Storage::url($userAvatar);
        } else {
            $avatarUrl = null;
        }
        $avatarInitial = strtoupper(substr(auth()->user()->name, 0, 1));
    @endphp
    @if($isSvgIcon)
        <link rel="icon" type="image/svg+xml" href="{{ $brandIcon }}">
    @else
        <link rel="icon" type="image/png" href="{{ $brandIcon }}">
    @endif

    <style>
        /* ── TOKENS ── */
        :root {
            --gold: #C9A84C;
            --gold-light: #F0C96B;
            --purple: #7C5CBF;
            --sidebar-w: 264px;

            /* Light mode */
            --bg: #F7F5F0;
            --bg-2: #FFFFFF;
            --bg-3: #F0EDE8;
            --surface: rgba(255,255,255,0.85);
            --surface-2: rgba(255,255,255,0.6);
            --border: rgba(0,0,0,0.07);
            --text-1: #1A1410;
            --text-2: #5C5048;
            --text-3: #9B8F84;
            --shadow: 0 2px 16px rgba(0,0,0,0.08);
            --shadow-lg: 0 8px 32px rgba(0,0,0,0.12);
        }
        .dark {
            --bg: #0D0B12;
            --bg-2: #130F1E;
            --bg-3: #1A1528;
            --surface: rgba(255,255,255,0.05);
            --surface-2: rgba(255,255,255,0.03);
            --border: rgba(255,255,255,0.08);
            --text-1: #F0EAD6;
            --text-2: #9D8FA8;
            --text-3: #5C5270;
            --shadow: 0 2px 16px rgba(0,0,0,0.4);
            --shadow-lg: 0 8px 40px rgba(0,0,0,0.5);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text-1);
            min-height: 100vh;
            transition: background 0.3s ease, color 0.3s ease;
        }

        /* Ambient bg */
        body::before {
            content: '';
            position: fixed; inset: 0; z-index: -1; pointer-events: none;
            background:
                radial-gradient(ellipse 70% 60% at 5% 0%, rgba(201,168,76,.07) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 95% 80%, rgba(124,92,191,.06) 0%, transparent 60%);
            transition: opacity 0.3s;
        }
        .dark body::before {
            background:
                radial-gradient(ellipse 70% 60% at 5% 0%, rgba(201,168,76,.12) 0%, transparent 60%),
                radial-gradient(ellipse 50% 40% at 95% 80%, rgba(124,92,191,.12) 0%, transparent 60%);
        }

        .font-playfair { font-family: 'Playfair Display', serif; }
        [x-cloak] { display: none !important; }

        /* ── LAYOUT SHELL ── */
        .app-shell { display: flex; height: 100vh; overflow: hidden; }

        /* ── SIDEBAR (desktop only) ── */
        .sidebar {
            width: var(--sidebar-w);
            flex-shrink: 0;
            background: var(--surface);
            backdrop-filter: blur(20px) saturate(150%);
            border-right: 1px solid var(--border);
            display: none;
            flex-direction: column;
            overflow: hidden;
        }
        @media (min-width: 1024px) { .sidebar { display: flex; } }

        .sb-brand { padding: 24px 20px 16px; border-bottom: 1px solid var(--border); }
        .sb-brand a { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .sb-logo {
            width: 40px; height: 40px; border-radius: 12px; flex-shrink: 0;
            background: rgba(201,168,76,.12); border: 1px solid rgba(201,168,76,.2);
            display: flex; align-items: center; justify-content: center; overflow: hidden;
        }
        .sb-logo img { width: 28px; height: 28px; object-fit: contain; }
        .sb-brand-name {
            font-family: 'Playfair Display', serif;
            font-size: 14px; font-weight: 700; color: var(--text-1); line-height: 1.2;
        }
        .sb-brand-sub { font-size: 9px; letter-spacing: .22em; text-transform: uppercase; color: var(--gold); margin-top: 2px; }

        .sb-user { padding: 14px 20px; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 10px; }
        .sb-avatar-wrap { position: relative; flex-shrink: 0; }
        .sb-avatar {
            width: 40px; height: 40px; border-radius: 12px;
            object-fit: cover; border: 2px solid rgba(201,168,76,.3);
        }
        .sb-avatar-initial {
            width: 40px; height: 40px; border-radius: 12px;
            background: linear-gradient(135deg, var(--gold), var(--purple));
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 15px; color: #fff;
            border: 2px solid rgba(201,168,76,.3); flex-shrink: 0;
        }
        .sb-user-name { font-size: 13px; font-weight: 600; color: var(--text-1); }
        .sb-user-email { font-size: 11px; color: var(--text-3); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 155px; }

        .sb-nav { flex: 1; overflow-y: auto; padding: 12px; scrollbar-width: thin; scrollbar-color: var(--border) transparent; }
        .sb-section { font-size: 9px; font-weight: 600; letter-spacing: .2em; text-transform: uppercase; color: var(--text-3); padding: 10px 8px 6px; }

        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 10px; border-radius: 10px;
            font-size: 13px; font-weight: 500; color: var(--text-2);
            text-decoration: none; transition: all .18s; margin-bottom: 2px;
            border: 1px solid transparent; position: relative;
        }
        .nav-link:hover { background: var(--surface-2); color: var(--text-1); }
        .nav-link.active {
            background: linear-gradient(135deg, rgba(201,168,76,.15), rgba(124,92,191,.12));
            color: var(--gold); border-color: rgba(201,168,76,.2);
            font-weight: 600;
        }
        .nav-link.active::before {
            content: ''; position: absolute; left: 0; top: 25%; bottom: 25%;
            width: 3px; border-radius: 0 2px 2px 0;
            background: linear-gradient(180deg, var(--gold), var(--purple));
        }
        .nav-icon {
            width: 30px; height: 30px; border-radius: 8px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; background: var(--surface-2);
            transition: all .18s;
        }
        .nav-link.active .nav-icon { background: rgba(201,168,76,.2); color: var(--gold); }

        .sb-footer { padding: 12px; border-top: 1px solid var(--border); }
        .btn-logout {
            display: flex; align-items: center; gap: 10px; width: 100%;
            padding: 9px 10px; border-radius: 10px; font-size: 13px; font-weight: 500;
            color: #ef4444; border: none; background: transparent; cursor: pointer; transition: all .18s;
        }
        .btn-logout:hover { background: rgba(239,68,68,.08); }

        /* ── TOPBAR ── */
        .topbar {
            height: 58px; flex-shrink: 0;
            background: var(--surface); backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 20px; position: sticky; top: 0; z-index: 40;
            transition: background 0.3s;
        }
        .topbar-title { font-size: 16px; font-weight: 600; color: var(--text-1); }
        .topbar-sub { font-size: 10px; letter-spacing: .2em; text-transform: uppercase; color: var(--text-3); margin-bottom: 1px; }
        .icon-btn {
            width: 36px; height: 36px; border-radius: 10px; border: 1px solid var(--border);
            background: var(--surface-2); color: var(--text-2);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all .18s; font-size: 14px; text-decoration: none;
        }
        .icon-btn:hover { background: var(--surface); color: var(--text-1); border-color: rgba(201,168,76,.3); }
        .top-avatar {
            width: 34px; height: 34px; border-radius: 10px;
            object-fit: cover; border: 2px solid rgba(201,168,76,.3);
        }
        .top-avatar-initial {
            width: 34px; height: 34px; border-radius: 10px;
            background: linear-gradient(135deg, var(--gold), var(--purple));
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 12px; color: #fff;
            border: 2px solid rgba(201,168,76,.3);
        }

        /* ── MAIN ── */
        .main-area { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
        .main-content {
            flex: 1; overflow-y: auto;
            padding: 20px 16px 100px;
        }
        @media (min-width: 1024px) { .main-content { padding: 28px 32px 40px; } }
        .main-content::-webkit-scrollbar { width: 4px; }
        .main-content::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }

        /* ── MAGIC BOTTOM NAV ── */
        .bottom-nav {
            position: fixed;
            bottom: 14px; left: 50%;
            transform: translateX(-50%);
            /* Fit exactly 4 items */
            width: auto;
            min-width: 260px;
            max-width: calc(100vw - 32px);
            height: 58px;
            background: linear-gradient(135deg, rgba(100,70,180,.92) 0%, rgba(160,90,210,.92) 50%, rgba(185,140,50,.92) 100%);
            backdrop-filter: blur(24px) saturate(200%);
            border-radius: 29px;
            border: 1px solid rgba(255,255,255,.22);
            box-shadow: 0 10px 40px rgba(0,0,0,.4), inset 0 1px 0 rgba(255,255,255,.18);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0;
            padding: 0 8px;
            z-index: 200;
        }
        @media (min-width: 1024px) { .bottom-nav { display: none; } }

        .bn-item {
            display: flex; flex-direction: column; align-items: center;
            justify-content: center;
            width: 58px; height: 54px;
            border-radius: 50%;
            cursor: pointer; text-decoration: none; border: none; background: none;
            color: rgba(255,255,255,.6); position: relative;
            transition: transform .38s cubic-bezier(.34,1.56,.64,1), color .2s;
            flex-shrink: 0;
        }
        .bn-item i { font-size: 18px; transition: font-size .3s cubic-bezier(.34,1.56,.64,1); }
        .bn-label {
            font-size: 9px; font-weight: 700; letter-spacing: .04em;
            position: absolute; bottom: -15px; white-space: nowrap;
            color: rgba(255,255,255,.9); opacity: 0;
            transform: translateY(5px); transition: all .25s ease;
        }
        .bn-item.active {
            background: rgba(255,255,255,.95);
            color: var(--purple);
            transform: translateY(-14px);
            box-shadow: 0 8px 20px rgba(0,0,0,.3), 0 0 0 5px rgba(255,255,255,.14);
        }
        .bn-item.active i { font-size: 20px; }
        .bn-item.active .bn-label { opacity: 1; transform: translateY(0); }
        .bn-item.chat-active {
            background: rgba(255,255,255,.95);
            color: var(--purple);
            transform: translateY(-14px);
            box-shadow: 0 8px 20px rgba(0,0,0,.3), 0 0 0 5px rgba(255,255,255,.14);
        }
        .bn-item.chat-active i { font-size: 20px; }
        .bn-item.chat-active .bn-label { opacity: 1; transform: translateY(0); }

        /* ── THEME TOGGLE ── */
        .theme-toggle {
            position: relative; width: 42px; height: 24px; cursor: pointer;
        }
        .theme-toggle input { opacity: 0; width: 0; height: 0; }
        .toggle-track {
            position: absolute; inset: 0; border-radius: 12px;
            background: var(--bg-3); border: 1px solid var(--border);
            transition: all .3s;
        }
        .dark .toggle-track { background: rgba(201,168,76,.2); border-color: rgba(201,168,76,.3); }
        .toggle-thumb {
            position: absolute; top: 3px; left: 3px;
            width: 18px; height: 18px; border-radius: 50%;
            background: var(--text-3); transition: all .3s;
            display: flex; align-items: center; justify-content: center;
            font-size: 9px; color: white;
        }
        .dark .toggle-thumb { left: 21px; background: var(--gold); }

        /* ── CURSOR ── */
        .cursor-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--gold); position: fixed; top: 0; left: 0;
            pointer-events: none; z-index: 99999;
            transform: translate(-50%,-50%);
        }
        .cursor-ring {
            width: 26px; height: 26px; border-radius: 50%;
            border: 1.5px solid rgba(201,168,76,.5); position: fixed; top: 0; left: 0;
            pointer-events: none; z-index: 99998;
            transform: translate(-50%,-50%);
            transition: width .2s, height .2s, border-color .2s;
        }
        @media (hover: none) and (pointer: coarse) { .cursor-dot, .cursor-ring { display: none; } }
    </style>
    @stack('head')
</head>

<body>
<div class="cursor-dot" id="cdot"></div>
<div class="cursor-ring" id="cring"></div>

@php
    $userBookings = auth()->user()->bookings()->with(['package','invitation'])->latest()->get();
    $firstBooking = $userBookings->first();
@endphp

<div class="app-shell" x-data="appShell()">

    {{-- ─── SIDEBAR (desktop only) ─── --}}
    <aside class="sidebar">
        {{-- Brand --}}
        <div class="sb-brand">
            <a href="{{ route('landing') }}">
                <div class="sb-logo">
                    @if($brandLogo)
                        <img src="{{ $brandLogo }}" alt="{{ $brandName }}">
                    @else
                        <i class="fas fa-rings-wedding" style="color: var(--gold); font-size: 15px;"></i>
                    @endif
                </div>
                <div>
                    <div class="sb-brand-name">{{ $brandName }}</div>
                    <div class="sb-brand-sub">{{ $brandTagline }}</div>
                </div>
            </a>
        </div>

        {{-- User --}}
        <div class="sb-user">
            @if($avatarUrl)
                <img src="{{ $avatarUrl }}" alt="{{ auth()->user()->name }}" class="sb-avatar"
                     onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                <div class="sb-avatar-initial" style="display:none;">{{ $avatarInitial }}</div>
            @else
                <div class="sb-avatar-initial">{{ $avatarInitial }}</div>
            @endif
            <div style="overflow:hidden; flex:1;">
                <div class="sb-user-name">{{ auth()->user()->name }}</div>
                <div class="sb-user-email">{{ auth()->user()->email }}</div>
            </div>
        </div>

        {{-- Nav --}}
        <nav class="sb-nav">
            <div class="sb-section">Navigasi</div>
            <a href="{{ route('user.dashboard') }}" class="nav-link {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <div class="nav-icon"><i class="fas fa-house"></i></div>
                <span>Dashboard</span>
            </a>

            @if($userBookings->isNotEmpty())
                <div class="sb-section" style="margin-top: 8px;">Booking Saya</div>
                @foreach($userBookings as $b)
                @php $bActive = request()->routeIs('user.booking.show') && optional(request()->route('booking'))->id == $b->id; @endphp
                <a href="{{ route('user.booking.show', $b->id) }}" class="nav-link {{ $bActive ? 'active' : '' }}" style="align-items: flex-start;">
                    <div class="nav-icon" style="margin-top:2px;">
                        <i class="fas {{ $b->is_invitation_only ? 'fa-envelope-open-text' : 'fa-calendar-heart' }}"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <p style="font-size:12px;font-weight:600;color:var(--text-1);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                            {{ $b->groom_name }} & {{ $b->bride_name }}
                        </p>
                        <p style="font-size:10px;color:var(--text-3);text-transform:uppercase;letter-spacing:.12em;margin-top:1px;">
                            {{ $b->is_invitation_only ? 'Undangan' : 'Paket Wedding' }}
                        </p>
                    </div>
                </a>
                @endforeach
            @endif

            <div class="sb-section" style="margin-top: 8px;">Akun</div>
            <a href="{{ route('user.profile') }}" class="nav-link {{ request()->routeIs('user.profile') ? 'active' : '' }}">
                <div class="nav-icon"><i class="fas fa-user-circle"></i></div>
                <span>Profil Saya</span>
            </a>
            <a href="{{ route('consultation.form') }}" class="nav-link">
                <div class="nav-icon"><i class="fas fa-comments"></i></div>
                <span>Konsultasi</span>
            </a>
            <a href="{{ route('landing') }}" class="nav-link">
                <div class="nav-icon"><i class="fas fa-globe"></i></div>
                <span>Lihat Website</span>
            </a>
        </nav>

        {{-- Theme + Logout --}}
        <div class="sb-footer">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 10px 12px;">
                <span style="font-size:12px;color:var(--text-3);">
                    <i class="fas fa-moon" style="margin-right:5px;"></i>Mode Gelap
                </span>
                <label class="theme-toggle">
                    <input type="checkbox" :checked="darkMode" @change="toggleTheme()">
                    <div class="toggle-track"></div>
                    <div class="toggle-thumb">
                        <i :class="darkMode ? 'fas fa-moon' : 'fas fa-sun'" style="font-size:8px;"></i>
                    </div>
                </label>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout">
                    <i class="fas fa-arrow-right-from-bracket"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    {{-- ─── MAIN AREA ─── --}}
    <div class="main-area">
        <header class="topbar">
            <div>
                <div class="topbar-sub">@yield('page-subtitle', 'Klien Area')</div>
                <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                {{-- Theme toggle (mobile) --}}
                <button @click="toggleTheme()" class="icon-btn lg:hidden">
                    <i :class="darkMode ? 'fas fa-sun' : 'fas fa-moon'"></i>
                </button>
                <a href="{{ route('consultation.form') }}" class="icon-btn" title="Konsultasi">
                    <i class="fas fa-headset"></i>
                </a>
                {{-- Avatar topbar --}}
                @if($avatarUrl)
                    <img src="{{ $avatarUrl }}" alt="" class="top-avatar"
                         onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <div class="top-avatar-initial" style="display:none;">{{ $avatarInitial }}</div>
                @else
                    <div class="top-avatar-initial">{{ $avatarInitial }}</div>
                @endif
            </div>
        </header>

        <main class="main-content">
            @yield('content')
        </main>
    </div>
</div>

{{-- ─── MAGIC BOTTOM NAV (mobile only) ─── --}}
<nav class="bottom-nav">
    <a href="{{ route('user.dashboard') }}"
       class="bn-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
        <i class="fas fa-house"></i>
        <span class="bn-label">Home</span>
    </a>

    <a href="{{ $firstBooking ? route('user.booking.show', $firstBooking->id) : route('booking.start') }}"
       class="bn-item {{ request()->routeIs('user.booking.show') ? 'active' : '' }}">
        <i class="fas fa-calendar-heart"></i>
        <span class="bn-label">Booking</span>
    </a>

    {{-- Chat button — triggers the chat widget via custom event --}}
    <button type="button" id="bn-chat-btn"
            class="bn-item"
            onclick="window.dispatchEvent(new CustomEvent('toggle-chat'))">
        <i class="fas fa-comment-dots"></i>
        <span class="bn-label">Chat</span>
    </button>

    <a href="{{ route('user.profile') }}"
       class="bn-item {{ request()->routeIs('user.profile') ? 'active' : '' }}">
        <i class="fas fa-user"></i>
        <span class="bn-label">Profil</span>
    </a>
</nav>

@include('components.global-loader')
@include('components.auth-status-modal')
@include('components.lazyload-script')

{{-- Chat widget: floating button hidden on mobile, panel still accessible via bottom nav --}}
<style>
    /* Hide only the floating trigger button on mobile, keep the chat panel */
    @media (max-width: 1023px) {
        [x-data*="chatWidgetUser"] > button:last-child {
            display: none !important;
        }
        /* Keep chat panel accessible but anchored to screen edge */
        [x-data*="chatWidgetUser"] {
            bottom: 80px !important;
            right: 12px !important;
        }
    }
</style>
@include('components.chat-widget-user')

@stack('scripts')
<script>
function appShell() {
    return {
        darkMode: localStorage.getItem('appTheme') === 'dark'
            || (!localStorage.getItem('appTheme') && window.matchMedia('(prefers-color-scheme: dark)').matches),
        init() {
            this.$watch('darkMode', v => {
                document.documentElement.classList.toggle('dark', v);
                localStorage.setItem('appTheme', v ? 'dark' : 'light');
            });
            document.documentElement.classList.toggle('dark', this.darkMode);
        },
        toggleTheme() { this.darkMode = !this.darkMode; }
    };
}

// Custom cursor
(function() {
    const dot = document.getElementById('cdot');
    const ring = document.getElementById('cring');
    if (!dot || !ring) return;
    let rx = 0, ry = 0;
    document.addEventListener('mousemove', e => {
        dot.style.left = e.clientX + 'px';
        dot.style.top = e.clientY + 'px';
        rx += (e.clientX - rx) * 0.14;
        ry += (e.clientY - ry) * 0.14;
        ring.style.left = rx + 'px';
        ring.style.top = ry + 'px';
        requestAnimationFrame(() => {});
    });
    setInterval(() => {
        ring.style.left = rx + 'px';
        ring.style.top = ry + 'px';
    }, 16);
    document.querySelectorAll('a,button,[role=button]').forEach(el => {
        el.addEventListener('mouseenter', () => { ring.style.width = '40px'; ring.style.height = '40px'; ring.style.borderColor = 'rgba(201,168,76,.7)'; });
        el.addEventListener('mouseleave', () => { ring.style.width = '26px'; ring.style.height = '26px'; ring.style.borderColor = 'rgba(201,168,76,.5)'; });
    });
})();
</script>
</body>
</html>
