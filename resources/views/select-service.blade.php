@php
    $layout = Auth::check() && !Auth::user()->isAdmin() ? 'layouts.app' : 'layouts.guest';
    $isApp = $layout === 'layouts.app';
@endphp

@extends($layout)

@section('title', 'Pilih Layanan – Anggita WO')

@section('content')
<div class="{{ $isApp ? 'py-8 px-4' : 'pt-24 pb-16 px-4 min-h-screen bg-gray-50 dark:bg-[#0A0A0A]' }}">
    <div class="max-w-xl mx-auto">
        {{-- Header Section --}}
        <div class="text-center mb-10">
            <div class="inline-block px-4 py-1.5 rounded-full bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-800/30 mb-4">
                <span class="text-yellow-700 dark:text-yellow-500 text-[10px] font-bold uppercase tracking-[0.2em]">Service Selection</span>
            </div>
            <h1 class="font-playfair text-4xl font-bold text-gray-900 dark:text-white mb-4">Pilih Layanan Anda</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm max-w-sm mx-auto leading-relaxed">
                Silakan pilih jenis layanan yang Anda butuhkan untuk memulai perjalanan bersama Anggita Wedding Organizer.
            </p>
        </div>

        <div class="grid gap-6">
            {{-- Wedding Organizer Card --}}
            <a href="{{ route('booking.select-package') }}" 
               class="group relative block bg-white dark:bg-white/5 rounded-[32px] p-8 border border-gray-100 dark:border-white/10 shadow-sm hover:shadow-xl hover:border-yellow-400/50 dark:hover:border-yellow-500/30 transition-all duration-300 overflow-hidden">
                {{-- Decorative background element --}}
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-yellow-400/10 dark:bg-yellow-500/5 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                
                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center gap-6">
                    <div class="w-16 h-16 shrink-0 rounded-2xl bg-yellow-50 dark:bg-yellow-900/20 flex items-center justify-center text-2xl text-yellow-600 dark:text-yellow-500 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-ring"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-yellow-600 dark:text-yellow-500">Full Service</span>
                            <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-700"></span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Wedding Organizer</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-yellow-600 dark:group-hover:text-yellow-500 transition-colors">Booking Paket Wedding</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                            Pilih paket WO eksklusif kami. Sudah termasuk perencanaan lengkap dan undangan digital otomatis aktif di dashboard Anda.
                        </p>
                    </div>
                    <div class="hidden sm:flex w-10 h-10 items-center justify-center rounded-full bg-gray-50 dark:bg-white/5 text-gray-400 group-hover:bg-yellow-500 group-hover:text-white transition-all">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </div>
                </div>

                <div class="mt-6 sm:hidden flex items-center gap-2 text-xs font-bold text-yellow-600 dark:text-yellow-500">
                    Mulai Booking Sekarang <i class="fas fa-arrow-right ml-1"></i>
                </div>
            </a>

            @php
                $maintenanceMode = (bool) \App\Models\SiteSetting::getValue('invitation_maintenance_mode', false);
            @endphp

            @if($maintenanceMode)
            <div class="relative block bg-gray-50 dark:bg-white/[0.02] rounded-[32px] p-8 border border-gray-200 dark:border-white/5 overflow-hidden">
                <div class="absolute inset-0 bg-white/40 dark:bg-black/40 backdrop-blur-[1px] z-10 flex items-center justify-center">
                    <div class="bg-red-600 text-white text-[10px] font-bold uppercase tracking-widest py-2 px-6 rounded-full shadow-lg flex items-center gap-2">
                        <i class="fas fa-tools text-xs"></i> Sedang Maintenance
                    </div>
                </div>
                
                <div class="relative z-0 flex flex-col sm:flex-row sm:items-center gap-6 opacity-40 grayscale">
                    <div class="w-16 h-16 shrink-0 rounded-2xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-2xl text-purple-600 dark:text-purple-400">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-purple-600 dark:text-purple-400">Digital Product</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Undangan Digital Saja</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                            Bagi Anda yang hanya membutuhkan website undangan pernikahan premium dengan berbagai pilihan template estetik.
                        </p>
                    </div>
                </div>
            </div>
            @else
            <a href="{{ route('invitation-order.start') }}" 
               class="group relative block bg-white dark:bg-white/5 rounded-[32px] p-8 border border-gray-100 dark:border-white/10 shadow-sm hover:shadow-xl hover:border-purple-400/50 dark:hover:border-purple-500/30 transition-all duration-300 overflow-hidden">
                {{-- Decorative background element --}}
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-purple-400/10 dark:bg-purple-500/5 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>

                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center gap-6">
                    <div class="w-16 h-16 shrink-0 rounded-2xl bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center text-2xl text-purple-600 dark:text-purple-400 group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1.5">
                            <span class="text-[10px] font-bold uppercase tracking-widest text-purple-600 dark:text-purple-400">Digital Product</span>
                            <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-700"></span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Self Service</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">Undangan Digital Saja</h3>
                        <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                            Website undangan pernikahan premium dengan template modern. Mudah diatur dan aktif seketika setelah pembayaran.
                        </p>
                    </div>
                    <div class="hidden sm:flex w-10 h-10 items-center justify-center rounded-full bg-gray-50 dark:bg-white/5 text-gray-400 group-hover:bg-purple-500 group-hover:text-white transition-all">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </div>
                </div>

                <div class="mt-6 sm:hidden flex items-center gap-2 text-xs font-bold text-purple-600 dark:text-purple-400">
                    Lihat Template Undangan <i class="fas fa-arrow-right ml-1"></i>
                </div>
            </a>
            @endif
        </div>

        {{-- Footer/Back link --}}
        <div class="mt-12 text-center">
            <a href="{{ $isApp ? route('user.dashboard') : route('landing') }}" class="text-sm font-medium text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left text-xs"></i> 
                {{ $isApp ? 'Kembali ke Dashboard' : 'Kembali ke Beranda' }}
            </a>
        </div>
    </div>
</div>
@endsection
