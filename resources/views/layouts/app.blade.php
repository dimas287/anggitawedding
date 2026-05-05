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

        /* ── BOTTOM SHEET ── */
        .sheet-overlay {
            position: fixed; inset: 0; z-index: 300;
            background: rgba(0,0,0,.55);
            backdrop-filter: blur(4px);
            opacity: 0; pointer-events: none;
            transition: opacity .3s;
        }
        .sheet-overlay.open { opacity: 1; pointer-events: all; }

        .bottom-sheet {
            position: fixed; bottom: 0; left: 0; right: 0; z-index: 310;
            background: var(--bg-2);
            border-radius: 24px 24px 0 0;
            border-top: 1px solid var(--border);
            box-shadow: 0 -8px 40px rgba(0,0,0,.2);
            max-height: 92vh;
            display: flex; flex-direction: column;
            transform: translateY(100%);
            transition: transform .38s cubic-bezier(.32,0,.67,0);
        }
        .dark .bottom-sheet { background: var(--bg-3); }
        .bottom-sheet.open { transform: translateY(0); transition-timing-function: cubic-bezier(.33,1,.68,1); }

        @media (min-width: 1024px) {
            .bottom-sheet {
                left: var(--sidebar-w); max-width: 680px;
                margin: 0 auto; right: 0;
                border-radius: 24px 24px 0 0;
            }
        }

        .sheet-handle {
            width: 36px; height: 4px; border-radius: 2px;
            background: var(--border); margin: 12px auto 0;
            flex-shrink: 0;
        }
        .sheet-header {
            padding: 16px 20px 12px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid var(--border);
            flex-shrink: 0;
        }
        .sheet-title {
            font-family: 'Playfair Display', serif;
            font-size: 18px; font-weight: 700; color: var(--text-1);
        }
        .sheet-body { flex: 1; overflow-y: auto; padding: 20px; }
        .sheet-body::-webkit-scrollbar { width: 3px; }
        .sheet-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }
        .sheet-close {
            width: 32px; height: 32px; border-radius: 8px;
            background: var(--surface-2); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: var(--text-2); font-size: 13px;
            transition: all .18s;
        }
        .sheet-close:hover { background: var(--surface); color: var(--text-1); }

        /* Sheet form styles */
        .sf-label {
            display: block; font-size: 11px; font-weight: 600;
            letter-spacing: .1em; text-transform: uppercase;
            color: var(--text-3); margin-bottom: 7px;
        }
        .sf-input {
            width: 100%; background: var(--surface-2);
            border: 1px solid var(--border); border-radius: 12px;
            padding: 11px 14px; font-size: 14px; color: var(--text-1);
            font-family: 'Inter', sans-serif; outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .sf-input:focus { border-color: rgba(201,168,76,.5); box-shadow: 0 0 0 3px rgba(201,168,76,.08); }
        .sf-input::placeholder { color: var(--text-3); }
        .sf-group { margin-bottom: 16px; }
        .sf-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        @media (max-width: 480px) { .sf-row { grid-template-columns: 1fr; } }

        .sf-radio-group { display: flex; gap: 8px; }
        .sf-radio {
            flex: 1; padding: 10px 14px;
            background: var(--surface-2); border: 1px solid var(--border);
            border-radius: 10px; text-align: center; cursor: pointer;
            font-size: 13px; font-weight: 500; color: var(--text-2);
            transition: all .18s;
        }
        .sf-radio:has(input:checked) {
            background: rgba(201,168,76,.12);
            border-color: rgba(201,168,76,.35); color: var(--gold);
        }
        .sf-radio input { display: none; }

        .sf-submit {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, var(--gold), var(--purple));
            color: #fff; border: none; border-radius: 12px;
            font-size: 14px; font-weight: 600; font-family: 'Inter', sans-serif;
            cursor: pointer; transition: all .2s;
            box-shadow: 0 4px 16px rgba(201,168,76,.25);
            margin-top: 8px;
        }
        .sf-submit:hover { opacity: .92; transform: translateY(-1px); }

        /* Package grid in sheet */
        .pkg-tabs { display: flex; gap: 6px; flex-wrap: wrap; margin-bottom: 16px; }
        .pkg-tab {
            padding: 6px 14px; border-radius: 20px;
            border: 1px solid var(--border); background: var(--surface-2);
            font-size: 12px; font-weight: 600; color: var(--text-2);
            cursor: pointer; transition: all .18s;
        }
        .pkg-tab.active {
            background: linear-gradient(135deg, var(--gold), var(--purple));
            border-color: transparent; color: #fff;
        }
        .pkg-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: 14px; overflow: hidden; cursor: pointer;
            transition: all .22s; margin-bottom: 10px;
        }
        .pkg-card:hover { border-color: rgba(201,168,76,.35); transform: translateY(-1px); box-shadow: var(--shadow-lg); }
        .pkg-card.selected { border-color: var(--gold); box-shadow: 0 0 0 2px rgba(201,168,76,.2); }
        .pkg-card-body { padding: 14px 16px; }
        .pkg-card-name { font-family: 'Playfair Display', serif; font-size: 16px; font-weight: 700; color: var(--text-1); margin-bottom: 4px; }
        .pkg-card-price { font-size: 15px; font-weight: 700; color: var(--gold); }
        .pkg-card-desc { font-size: 12px; color: var(--text-3); margin-top: 4px; line-height: 1.5; }

        /* Step indicator */
        .step-bar { display: flex; align-items: center; gap: 8px; margin-bottom: 20px; }
        .step-dot {
            width: 28px; height: 28px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 700; flex-shrink: 0;
            background: var(--surface-2); border: 1px solid var(--border);
            color: var(--text-3); transition: all .3s;
        }
        .step-dot.active { background: linear-gradient(135deg, var(--gold), var(--purple)); border-color: transparent; color: #fff; }
        .step-dot.done { background: rgba(34,197,94,.15); border-color: rgba(34,197,94,.3); color: #4ade80; }
        .step-line { flex: 1; height: 1px; background: var(--border); }
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
            <button type="button" onclick="openSheet('konsultasi')" class="nav-link" style="width: 100%; border: none; background: transparent; text-align: left;">
                <div class="nav-icon"><i class="fas fa-comments"></i></div>
                <span>Konsultasi</span>
            </button>
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
                <button type="button" onclick="openSheet('konsultasi')" class="icon-btn" title="Konsultasi">
                    <i class="fas fa-headset"></i>
                </button>
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

{{-- ─── MAGIC BOTTOM NAV (mobile only, 5 items) ─── --}}
<nav class="bottom-nav" id="bottom-nav">
    <a href="{{ route('user.dashboard') }}"
       class="bn-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
        <i class="fas fa-house"></i>
        <span class="bn-label">Home</span>
    </a>
    <button type="button" class="bn-item" id="bn-paket"
            onclick="openSheet('paket')">
        <i class="fas fa-gem"></i>
        <span class="bn-label">Paket</span>
    </button>
    <button type="button" class="bn-item" id="bn-konsultasi"
            onclick="openSheet('konsultasi')">
        <i class="fas fa-headset"></i>
        <span class="bn-label">Konsultasi</span>
    </button>
    <button type="button" class="bn-item" id="bn-chat-btn"
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

{{-- ─── SHEET OVERLAY ─── --}}
<div class="sheet-overlay" id="sheet-overlay" onclick="closeSheet()"></div>

{{-- ─── SHEET: KONSULTASI ─── --}}
<div class="bottom-sheet" id="sheet-konsultasi">
    <div class="sheet-handle"></div>
    <div class="sheet-header">
        <div>
            <p style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);margin-bottom:3px;">Jadwalkan</p>
            <div class="sheet-title">Konsultasi Gratis</div>
        </div>
        <button class="sheet-close" onclick="closeSheet()"><i class="fas fa-times"></i></button>
    </div>
    <div class="sheet-body">
        @if(session('consultation_success'))
        <div style="padding:14px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.25);border-radius:12px;color:#4ade80;font-size:13px;margin-bottom:16px;">
            <i class="fas fa-check-circle mr-2"></i>{{ session('consultation_success') }}
        </div>
        @endif

        <form action="{{ route('consultation.store') }}" method="POST">
            @csrf
            <div class="sf-row">
                <div class="sf-group">
                    <label class="sf-label">Nama Lengkap</label>
                    <input type="text" name="name" class="sf-input" required
                           value="{{ old('name', auth()->user()->name) }}" placeholder="Nama Anda">
                </div>
                <div class="sf-group">
                    <label class="sf-label">WhatsApp</label>
                    <input type="tel" name="phone" class="sf-input" required
                           value="{{ old('phone', auth()->user()->phone) }}" placeholder="08xx">
                </div>
            </div>
            <div class="sf-group">
                <label class="sf-label">Email</label>
                <input type="email" name="email" class="sf-input" required
                       value="{{ old('email', auth()->user()->email) }}" placeholder="email@domain.com">
            </div>
            <div class="sf-row">
                <div class="sf-group">
                    <label class="sf-label">Tanggal Konsultasi</label>
                    <input type="date" name="preferred_date" class="sf-input" required
                           min="{{ date('Y-m-d') }}" value="{{ old('preferred_date') }}">
                </div>
                <div class="sf-group">
                    <label class="sf-label">Jam</label>
                    <select name="preferred_time" class="sf-input" required>
                        <option value="">Pilih jam</option>
                        @foreach(['09:00','10:00','11:00','13:00','14:00','15:00','16:00'] as $t)
                        <option value="{{ $t }}" {{ old('preferred_time') == $t ? 'selected' : '' }}>{{ $t }} WIB</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="sf-group">
                <label class="sf-label">Tanggal Acara (opsional)</label>
                <input type="date" name="event_date" class="sf-input"
                       min="{{ date('Y-m-d', strtotime('+1 day')) }}" value="{{ old('event_date') }}">
            </div>
            <div class="sf-group">
                <label class="sf-label">Tipe Konsultasi</label>
                <div class="sf-radio-group">
                    <label class="sf-radio">
                        <input type="radio" name="consultation_type" value="online" {{ old('consultation_type','online') == 'online' ? 'checked' : '' }}>
                        <i class="fas fa-video" style="margin-right:5px;"></i> Online
                    </label>
                    <label class="sf-radio">
                        <input type="radio" name="consultation_type" value="offline" {{ old('consultation_type') == 'offline' ? 'checked' : '' }}>
                        <i class="fas fa-store" style="margin-right:5px;"></i> Offline
                    </label>
                </div>
            </div>
            <div class="sf-group">
                <label class="sf-label">Pesan / Pertanyaan (opsional)</label>
                <textarea name="message" class="sf-input" rows="3"
                          placeholder="Ceritakan rencana pernikahan Anda...">{{ old('message') }}</textarea>
            </div>
            <button type="submit" class="sf-submit">
                <i class="fas fa-paper-plane" style="margin-right:6px;"></i> Kirim Permintaan Konsultasi
            </button>
        </form>
    </div>
</div>

{{-- ─── SHEET: PAKET & BOOKING ─── --}}
<div class="bottom-sheet" id="sheet-paket"
     x-data="paketSheet()"
     @open-paket-sheet.window="openPaketSheet()">
    <div class="sheet-handle"></div>
    <div class="sheet-header">
        <div>
            <p style="font-size:10px;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);margin-bottom:3px;">Mulai Perjalanan</p>
            <div class="sheet-title" x-text="stepTitle"></div>
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
            <button x-show="step > 1" @click="step--" class="sheet-close" style="color:var(--gold);">
                <i class="fas fa-arrow-left"></i>
            </button>
            <button class="sheet-close" onclick="closeSheet()"><i class="fas fa-times"></i></button>
        </div>
    </div>

    <div class="sheet-body">
        {{-- Step indicator --}}
        <div class="step-bar">
            <div class="step-dot" :class="step >= 1 ? (step > 1 ? 'done' : 'active') : ''">
                <i x-show="step > 1" class="fas fa-check" style="font-size:9px;"></i>
                <span x-show="step <= 1">1</span>
            </div>
            <div class="step-line"></div>
            <div class="step-dot" :class="step >= 2 ? (step > 2 ? 'done' : 'active') : ''">
                <i x-show="step > 2" class="fas fa-check" style="font-size:9px;"></i>
                <span x-show="step <= 2">2</span>
            </div>
            <div class="step-line"></div>
            <div class="step-dot" :class="step >= 3 ? 'active' : ''">3</div>
        </div>

        {{-- Step 1: Tanggal --}}
        <div x-show="step === 1">
            <p style="font-size:13px;color:var(--text-2);margin-bottom:20px;">
                Pilih tanggal akad / resepsi Anda. Kami akan cek ketersediaan hari tersebut.
            </p>
            <div class="sf-group">
                <label class="sf-label">Tanggal Acara</label>
                <input type="date" x-model="eventDate" class="sf-input"
                       :min="minDate" @change="checkDate()">
            </div>
            <div x-show="dateStatus" x-cloak style="margin-bottom:16px;">
                <div style="padding:12px 14px;border-radius:10px;font-size:13px;"
                     :style="dateStatus === 'available' ? 'background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.25);color:#4ade80;' :
                             dateStatus === 'limited' ? 'background:rgba(234,179,8,.1);border:1px solid rgba(234,179,8,.25);color:#d97706;' :
                             'background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.25);color:#f87171;'">
                    <i class="fas" :class="dateStatus === 'available' ? 'fa-check-circle' : dateStatus === 'limited' ? 'fa-exclamation-circle' : 'fa-times-circle'" style="margin-right:6px;"></i>
                    <span x-text="dateMessage"></span>
                </div>
            </div>
            <button class="sf-submit" :disabled="!eventDate || dateStatus === 'full'"
                    @click="step = 2" style="opacity:1;" :style="(!eventDate || dateStatus === 'full') ? 'opacity:.5;cursor:not-allowed;' : ''">
                Lanjut Pilih Paket <i class="fas fa-arrow-right" style="margin-left:6px;"></i>
            </button>
        </div>

        {{-- Step 2: Pilih Paket --}}
        <div x-show="step === 2" x-cloak>
            <div class="pkg-tabs">
                @foreach($categoryLabels as $catKey => $catLabel)
                    @if(isset($packagesByCategory[$catKey]) && $packagesByCategory[$catKey]->isNotEmpty())
                    <button class="pkg-tab" :class="pkgTab === '{{ $catKey }}' ? 'active' : ''"
                            @click="pkgTab = '{{ $catKey }}'">{{ $catLabel }}</button>
                    @endif
                @endforeach
            </div>

            @foreach($packagesByCategory as $cat => $pkgList)
            <div x-show="pkgTab === '{{ $cat }}'">
                @foreach($pkgList as $pkg)
                <div class="pkg-card" :class="selectedPackageId === {{ $pkg->id }} ? 'selected' : ''"
                     @click="selectPackage({{ $pkg->id }}, @js($pkg->name), {{ $pkg->effective_price }})">
                    <div class="pkg-card-body" style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
                        <div style="flex:1;min-width:0;">
                            <div class="pkg-card-name">{{ $pkg->name }}</div>
                            <div class="pkg-card-price">Rp {{ number_format($pkg->effective_price, 0, ',', '.') }}</div>
                            @if($pkg->short_description)
                            <div class="pkg-card-desc">{{ Str::limit($pkg->short_description, 80) }}</div>
                            @endif
                        </div>
                        <div x-show="selectedPackageId === {{ $pkg->id }}" style="flex-shrink:0;">
                            <div style="width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,var(--gold),var(--purple));display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;">
                                <i class="fas fa-check"></i>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach

            <button class="sf-submit" :disabled="!selectedPackageId" @click="step = 3"
                    :style="!selectedPackageId ? 'opacity:.5;cursor:not-allowed;' : ''">
                Lanjut Isi Data <i class="fas fa-arrow-right" style="margin-left:6px;"></i>
            </button>
        </div>

        {{-- Step 3: Form Booking --}}
        <div x-show="step === 3" x-cloak>
            <div style="padding:12px 14px;background:rgba(201,168,76,.08);border:1px solid rgba(201,168,76,.2);border-radius:10px;margin-bottom:18px;font-size:13px;color:var(--gold);">
                <i class="fas fa-gem" style="margin-right:6px;"></i>
                <span x-text="selectedPackageName"></span> —
                Rp <span x-text="selectedPackagePrice.toLocaleString('id-ID')"></span>
            </div>
            <form action="{{ route('booking.store') }}" method="POST">
                @csrf
                <input type="hidden" name="package_id" :value="selectedPackageId">
                <input type="hidden" name="event_date" :value="eventDate">

                <div class="sf-row">
                    <div class="sf-group">
                        <label class="sf-label">Nama Mempelai Pria</label>
                        <input type="text" name="groom_name" class="sf-input" required placeholder="Nama lengkap">
                    </div>
                    <div class="sf-group">
                        <label class="sf-label">Panggilan Pria</label>
                        <input type="text" name="groom_short_name" class="sf-input" required placeholder="Mis: Budi">
                    </div>
                </div>
                <div class="sf-row">
                    <div class="sf-group">
                        <label class="sf-label">Nama Mempelai Wanita</label>
                        <input type="text" name="bride_name" class="sf-input" required placeholder="Nama lengkap">
                    </div>
                    <div class="sf-group">
                        <label class="sf-label">Panggilan Wanita</label>
                        <input type="text" name="bride_short_name" class="sf-input" required placeholder="Mis: Sari">
                    </div>
                </div>
                <div class="sf-group">
                    <label class="sf-label">Venue / Gedung</label>
                    <input type="text" name="venue" class="sf-input" required placeholder="Nama gedung atau tempat">
                </div>
                <div class="sf-group">
                    <label class="sf-label">Alamat Venue</label>
                    <input type="text" name="venue_address" class="sf-input" placeholder="Alamat lengkap">
                </div>
                <div class="sf-row">
                    <div class="sf-group">
                        <label class="sf-label">No. WhatsApp</label>
                        <input type="tel" name="phone" class="sf-input" required
                               value="{{ auth()->user()->phone }}" placeholder="08xx">
                    </div>
                    <div class="sf-group">
                        <label class="sf-label">Estimasi Tamu</label>
                        <input type="number" name="estimated_guests" class="sf-input" min="10" placeholder="mis: 200">
                    </div>
                </div>
                <div class="sf-group">
                    <label class="sf-label">Catatan Tambahan</label>
                    <textarea name="notes" class="sf-input" rows="2" placeholder="Permintaan khusus, tema warna, dll."></textarea>
                </div>
                <div style="padding:12px;background:var(--surface-2);border-radius:10px;font-size:12px;color:var(--text-3);margin-bottom:12px;border:1px solid var(--border);">
                    <i class="fas fa-info-circle" style="color:var(--gold);margin-right:4px;"></i>
                    Setelah submit, Anda akan diarahkan ke halaman pembayaran DP. DP minimum 30% dari total paket.
                </div>
                <button type="submit" class="sf-submit">
                    <i class="fas fa-calendar-check" style="margin-right:6px;"></i> Buat Booking & Lanjut Bayar DP
                </button>
            </form>
        </div>
    </div>
</div>

@include('components.global-loader')
@include('components.auth-status-modal')
@include('components.lazyload-script')

<style>
    @media (max-width: 1023px) {
        [x-data*="chatWidgetUser"] > button:last-child { display: none !important; }
        [x-data*="chatWidgetUser"] { bottom: 80px !important; right: 12px !important; }
    }
</style>
@include('components.chat-widget-user')

@stack('scripts')
<script>
// ── Bottom Sheet System ──
function openSheet(name) {
    document.getElementById('sheet-overlay').classList.add('open');
    document.getElementById('sheet-' + name).classList.add('open');
    document.body.style.overflow = 'hidden';
    // update active state on bn items
    document.querySelectorAll('.bn-item').forEach(el => el.classList.remove('bn-sheet-active'));
    const btn = document.getElementById('bn-' + name);
    if (btn) btn.classList.add('active');
}
function closeSheet() {
    document.getElementById('sheet-overlay').classList.remove('open');
    document.querySelectorAll('.bottom-sheet').forEach(s => s.classList.remove('open'));
    document.body.style.overflow = '';
    document.querySelectorAll('#bn-paket, #bn-konsultasi').forEach(b => b.classList.remove('active'));
}
window.addEventListener('open-sheet', e => openSheet(e.detail));

// ── Paket Sheet Alpine Component ──
function paketSheet() {
    return {
        step: 1,
        eventDate: '',
        minDate: new Date(Date.now() + 86400000).toISOString().split('T')[0],
        dateStatus: null,
        dateMessage: '',
        pkgTab: '{{ $packagesByCategory->keys()->first() ?? "rumahan" }}',
        selectedPackageId: null,
        selectedPackageName: '',
        selectedPackagePrice: 0,
        get stepTitle() {
            return ['Pilih Tanggal Acara', 'Pilih Paket', 'Data Booking'][this.step - 1];
        },
        openPaketSheet() { openSheet('paket'); },
        selectPackage(id, name, price) {
            this.selectedPackageId = id;
            this.selectedPackageName = name;
            this.selectedPackagePrice = price;
        },
        async checkDate() {
            if (!this.eventDate) return;
            try {
                const res = await fetch(`{{ route('booking.check-date') }}?date=${this.eventDate}`);
                const data = await res.json();
                this.dateStatus = data.status;
                this.dateMessage = data.message ?? (data.status === 'available' ? 'Tanggal tersedia!' : data.status === 'limited' ? 'Tersisa slot terbatas.' : 'Tanggal penuh, pilih lain.');
            } catch { this.dateStatus = null; }
        }
    };
}

// ── AppShell ──
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

// ── Custom cursor ──
(function() {
    const dot = document.getElementById('cdot');
    const ring = document.getElementById('cring');
    if (!dot || !ring) return;
    let rx = 0, ry = 0;
    document.addEventListener('mousemove', e => {
        dot.style.left = e.clientX + 'px'; dot.style.top = e.clientY + 'px';
        rx += (e.clientX - rx) * 0.14; ry += (e.clientY - ry) * 0.14;
    });
    setInterval(() => { ring.style.left = rx + 'px'; ring.style.top = ry + 'px'; }, 16);
    document.querySelectorAll('a,button,[role=button]').forEach(el => {
        el.addEventListener('mouseenter', () => { ring.style.width='40px'; ring.style.height='40px'; ring.style.borderColor='rgba(201,168,76,.7)'; });
        el.addEventListener('mouseleave', () => { ring.style.width='26px'; ring.style.height='26px'; ring.style.borderColor='rgba(201,168,76,.5)'; });
    });
})();
</script>
</body>
</html>


