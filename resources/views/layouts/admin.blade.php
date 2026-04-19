<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') – Anggita WO</title>
    <script defer src="/js/alpine.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .gold-gradient { background: linear-gradient(135deg, #D4AF37, #B8960C); }
        .sidebar-active { background: linear-gradient(135deg, #D4AF37, #B8960C); color: white; }
        .sidebar-active i { color: white; }
        [x-cloak] { display: none !important; }

        .brand-logo {
            width: 2.5rem;
            height: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .admin-sidebar-scroll { scrollbar-width: thin; scrollbar-color: rgba(212,175,55,.65) rgba(255,255,255,.06); }
        .admin-sidebar-scroll::-webkit-scrollbar { width: 10px; }
        .admin-sidebar-scroll::-webkit-scrollbar-track { background: rgba(255,255,255,.06); border-radius: 9999px; }
        .admin-sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(212,175,55,.65); border-radius: 9999px; border: 2px solid rgba(17,24,39,1); }
        .admin-sidebar-scroll::-webkit-scrollbar-thumb:hover { background: rgba(212,175,55,.85); }

    </style>
    @stack('head')
</head>
@php
    $brandName = $brandInfo['brand_name'] ?? 'Anggita WO';
    $brandTagline = $brandInfo['tagline'] ?? 'Wedding Organizer';
    $brandLogo = $brandInfo['logo_main_url'] ?? null;
    $brandIcon = $brandInfo['logo_icon_url'] ?? $brandLogo;
@endphp

<body class="bg-gray-100" x-data="{ sidebarOpen: window.innerWidth >= 1024, sidebarCollapsed: false }" x-init="window.addEventListener('resize', () => { if (window.innerWidth < 1024) { sidebarOpen = false; sidebarCollapsed = false; } else { sidebarOpen = true; } })">

<div class="flex h-screen overflow-hidden">
    {{-- Admin Sidebar --}}
    <aside
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed lg:relative inset-y-0 left-0 z-50 bg-gray-900 text-gray-300 transform transition-transform duration-300 flex flex-col w-64 lg:translate-x-0 lg:transform-none"
           :style="(window.innerWidth >= 1024 && sidebarCollapsed) ? 'width: 5rem;' : ''">

        <div class="p-5 border-b border-gray-800" :class="sidebarCollapsed ? 'px-3' : ''">
            <div class="flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'justify-between'">
                <div class="flex items-center gap-3" x-show="!sidebarCollapsed" x-cloak>
                    <div class="brand-logo">
                        @if($brandIcon)
                            <img src="{{ $brandIcon }}" alt="{{ $brandName }}">
                        @else
                            <i class="fas fa-rings-wedding text-white text-sm"></i>
                        @endif
                    </div>
                    <div>
                        <div class="text-white font-bold text-sm">{{ $brandName }}</div>
                        <div class="text-yellow-400 text-xs">{{ $brandTagline }}</div>
                    </div>
                </div>
                <div class="flex items-center" x-show="sidebarCollapsed" x-cloak>
                    <div class="brand-logo">
                        @if($brandIcon)
                            <img src="{{ $brandIcon }}" alt="{{ $brandName }}">
                        @else
                            <i class="fas fa-rings-wedding text-white text-sm"></i>
                        @endif
                    </div>
                </div>

                <button type="button" @click="sidebarCollapsed = !sidebarCollapsed" class="w-10 h-10 rounded-full gold-gradient flex items-center justify-center hover:opacity-90 hidden lg:flex" :class="sidebarCollapsed ? 'mx-auto' : ''">
                    <i class="fas" :class="sidebarCollapsed ? 'fa-bars' : 'fa-bars'"></i>
                </button>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 admin-sidebar-scroll">
            @php
            $navGroups = [
                'Utama' => [
                    ['route' => 'admin.dashboard', 'icon' => 'fa-chart-pie', 'label' => 'Dashboard'],
                    ['route' => 'admin.calendar', 'icon' => 'fa-calendar-days', 'label' => 'Kalender Event'],
                ],
                'Manajemen' => [
                    ['route' => 'admin.bookings.index', 'icon' => 'fa-book', 'label' => 'Booking Paket'],
                    ['route' => 'admin.invitation-bookings.index', 'icon' => 'fa-envelope-open', 'label' => 'Booking Undangan'],
                    ['route' => 'admin.consultations.index', 'icon' => 'fa-comments', 'label' => 'Konsultasi'],
                    ['route' => 'admin.vendors.index', 'icon' => 'fa-store', 'label' => 'Vendor'],
                    ['route' => 'admin.packages.index', 'icon' => 'fa-gift', 'label' => 'Paket Wedding'],
                    ['route' => 'admin.clients.index', 'icon' => 'fa-users', 'label' => 'Klien'],
                    ['route' => 'admin.site-content.edit', 'icon' => 'fa-pen-to-square', 'label' => 'Konten Landing & Footer'],
                    ['route' => 'admin.portfolio.index', 'icon' => 'fa-images', 'label' => 'Portofolio'],
                    ['route' => 'admin.invitation.templates', 'icon' => 'fa-envelope-open-text', 'label' => 'Template Undangan'],
                    ['route' => 'admin.posts.index', 'icon' => 'fa-newspaper', 'label' => 'Blog / Artikel'],
                ],
                'Keuangan' => [
                    ['route' => 'admin.financial.index', 'icon' => 'fa-coins', 'label' => 'Laporan Keuangan'],
                ],
                'Komunikasi' => [
                    ['route' => 'admin.chat.inbox', 'icon' => 'fa-inbox', 'label' => 'Kotak Masuk'],
                    ['route' => 'admin.activities.index', 'icon' => 'fa-history', 'label' => 'Riwayat Aktivitas'],
                ],
                'Akun' => [
                    ['route' => 'admin.account.settings', 'icon' => 'fa-user-gear', 'label' => 'Pengaturan Akun'],
                ],
            ];
            @endphp

            @foreach($navGroups as $group => $items)
            <div class="px-4 mb-1" :class="sidebarCollapsed ? 'px-2' : ''">
                <p class="text-xs uppercase tracking-widest text-gray-600 mb-2 mt-3" x-show="!sidebarCollapsed" x-cloak>{{ $group }}</p>
                @foreach($items as $item)
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm mb-0.5 transition-all
                          {{ request()->routeIs($item['route'].'*') ? 'sidebar-active font-semibold' : 'hover:bg-gray-800 text-gray-400 hover:text-white' }}">
                    <i class="fas {{ $item['icon'] }} w-4 text-center"></i>
                    <span x-show="!sidebarCollapsed" x-cloak>{{ $item['label'] }}</span>
                </a>
                @endforeach
            </div>
            @endforeach
        </nav>

        <div class="p-4 border-t border-gray-800" :class="sidebarCollapsed ? 'px-2' : ''">
            <div class="text-xs text-gray-500 mb-3" x-show="!sidebarCollapsed" x-cloak>Masuk sebagai: <span class="text-yellow-400">{{ auth()->user()->name }}</span></div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 rounded-lg text-sm text-red-400 hover:bg-gray-800 transition-colors" :class="sidebarCollapsed ? 'justify-center px-0' : ''">
                    <i class="fas fa-sign-out-alt w-4"></i>
                    <span x-show="!sidebarCollapsed" x-cloak>Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden" x-cloak></div>

    {{-- Main Content --}}
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b px-4 py-3 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-lg hover:bg-gray-100 lg:hidden">
                    <i class="fas fa-bars text-gray-600"></i>
                </button>
                <div>
                    <h1 class="text-base font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h1>
                    @hasSection('breadcrumb')
                    <p class="text-xs text-gray-500">@yield('breadcrumb')</p>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.chat.inbox') }}" class="relative p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                    <i class="fas fa-bell"></i>
                </a>
                <a href="{{ route('landing') }}" target="_blank" class="text-xs text-gray-500 hover:text-yellow-600 flex items-center gap-1">
                    <i class="fas fa-external-link-alt"></i> View Site
                </a>
            </div>
        </header>

        @include('components.auth-status-modal')

        <main class="flex-1 overflow-y-auto p-4 md:p-6">
            @yield('content')
        </main>
    </div>
</div>

@include('components.global-loader')
@include('components.lazyload-script')
@include('components.chat-widget-admin')

@stack('scripts')

<script>
    document.addEventListener('alpine:initialized', () => {
        // placeholder to keep stack structure consistent
    });
</script>
</body>
</html>
