@php
    $layout = Auth::check() && !Auth::user()->isAdmin() ? 'layouts.app' : 'layouts.guest';
    $isApp = $layout === 'layouts.app';
@endphp
@extends($layout)
@section('title', 'Pilih Layanan – Anggita WO')
@section('content')
<div class="{{ $isApp ? 'py-4 px-4' : 'pt-24 pb-16 px-4 min-h-screen bg-gray-50 dark:bg-[#0f0f11]' }}">
    <div class="max-w-md mx-auto bg-white dark:bg-[#1a1a1a] border border-gray-100 dark:border-white/10 rounded-[32px] p-6 sm:p-8 shadow-xl dark:shadow-2xl relative overflow-hidden">
        
        <div class="text-center relative z-10 pt-2">
            <span class="text-yellow-600 dark:text-yellow-500 text-[10px] font-bold uppercase tracking-[0.3em]">M U L A I &nbsp; B O O K I N G</span>
            <h1 class="font-playfair text-3xl sm:text-4xl font-bold mt-3 mb-4 text-gray-900 dark:text-white">Pilih Layanan</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm leading-relaxed px-2">Pilih alur yang sesuai kebutuhan Anda. Anda bisa booking paket wedding, atau order undangan digital saja.</p>
        </div>

        <div class="mt-8 space-y-4 relative z-10">
            {{-- Wedding Organizer Card --}}
            <a href="{{ route('booking.select-package') }}" class="block bg-gray-50 dark:bg-[#222] rounded-[24px] p-6 border border-gray-200 dark:border-white/5 shadow-sm hover:shadow-md dark:shadow-none hover:dark:bg-[#2a2a2a] transition-all">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.2em] text-yellow-600 dark:text-yellow-500 font-bold">Wedding Organizer</p>
                        <h3 class="text-lg font-bold mt-1 text-gray-800 dark:text-white">Booking Paket Wedding</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-2 text-xs leading-relaxed">Pilih paket WO (Silver/Gold/dll). Jika paket include undangan digital, undangan akan otomatis aktif di dashboard.</p>
                    </div>
                    <div class="w-10 h-10 shrink-0 rounded-full bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 flex items-center justify-center text-sm shadow-sm ml-3 text-yellow-600 dark:text-yellow-500">
                        <i class="fas fa-ring"></i>
                    </div>
                </div>
                <div class="mt-5 inline-flex items-center gap-2 text-xs font-bold text-gray-600 dark:text-gray-300">
                    Mulai booking <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            @php
                $maintenanceMode = (bool) \App\Models\SiteSetting::getValue('invitation_maintenance_mode', false);
            @endphp

            @if($maintenanceMode)
            <div class="block bg-gray-50 dark:bg-[#222] rounded-[24px] p-6 border border-gray-200 dark:border-white/5 relative overflow-hidden">
                <div class="absolute inset-0 bg-white/60 dark:bg-black/60 backdrop-blur-[2px] z-10 flex items-center justify-center">
                    <div class="bg-red-600 text-white text-[10px] font-bold uppercase tracking-wider py-1.5 px-4 rounded-full shadow-lg transform -rotate-6 border-2 border-white dark:border-red-800">
                        Sedang Maintenance
                    </div>
                </div>
                <div class="flex items-start justify-between opacity-50">
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.2em] text-purple-600 dark:text-purple-400 font-bold">Digital Invitation</p>
                        <h3 class="text-lg font-bold mt-1 text-gray-800 dark:text-white">Undangan Digital Saja</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-2 text-xs leading-relaxed">Pilih template, buat undangan draft di dashboard, lalu lengkapi data & file.</p>
                    </div>
                    <div class="w-10 h-10 shrink-0 rounded-full bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 flex items-center justify-center text-sm shadow-sm ml-3 text-purple-600 dark:text-purple-400 grayscale">
                        <i class="fas fa-heart"></i>
                    </div>
                </div>
            </div>
            @else
            <a href="{{ route('invitation-order.start') }}" class="block bg-gray-50 dark:bg-[#222] rounded-[24px] p-6 border border-gray-200 dark:border-white/5 shadow-sm hover:shadow-md dark:shadow-none hover:dark:bg-[#2a2a2a] transition-all">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.2em] text-purple-600 dark:text-purple-400 font-bold">Digital Invitation</p>
                        <h3 class="text-lg font-bold mt-1 text-gray-800 dark:text-white">Undangan Digital Saja</h3>
                        <p class="text-gray-500 dark:text-gray-400 mt-2 text-xs leading-relaxed">Pilih template, buat undangan draft di dashboard, lalu lengkapi data & file.</p>
                    </div>
                    <div class="w-10 h-10 shrink-0 rounded-full bg-white dark:bg-[#1a1a1a] border border-gray-200 dark:border-white/10 flex items-center justify-center text-sm shadow-sm ml-3 text-purple-600 dark:text-purple-400">
                        <i class="fas fa-heart"></i>
                    </div>
                </div>
                <div class="mt-5 inline-flex items-center gap-2 text-xs font-bold text-gray-600 dark:text-gray-300">
                    Checkout undangan <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
