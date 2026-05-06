@php
    $layout = Auth::check() && !Auth::user()->isAdmin() ? 'layouts.app' : 'layouts.guest';
    $isApp = $layout === 'layouts.app';
@endphp
@extends($layout)
@section('title', 'Pilih Layanan – Anggita WO')

@section('content')
<div class="{{ $isApp ? 'py-6 px-4' : 'pt-28 pb-16 px-4 min-h-screen bg-[#0f0f11]' }}">
    <div class="max-w-md mx-auto bg-gradient-to-b from-[#5b21b6] to-[#7e22ce] rounded-[32px] p-6 sm:p-8 shadow-2xl relative overflow-hidden">
        
        <div class="text-center relative z-10 pt-2">
            <span class="text-yellow-400 text-[10px] font-bold uppercase tracking-[0.3em]">M U L A I &nbsp; B O O K I N G</span>
            <h1 class="font-playfair text-4xl font-bold mt-3 mb-4 text-white">Pilih Layanan</h1>
            <p class="text-white/90 text-sm leading-relaxed px-2">Pilih alur yang sesuai kebutuhan Anda. Anda bisa booking paket wedding, atau order undangan digital saja.</p>
        </div>

        <div class="mt-8 space-y-5 relative z-10">
            {{-- Wedding Organizer Card --}}
            <a href="{{ route('booking.select-package') }}" class="block bg-white/10 backdrop-blur-md rounded-[24px] p-6 border border-white/20 shadow-lg hover:bg-white/15 transition-all">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.2em] text-yellow-300 font-bold">Wedding Organizer</p>
                        <h3 class="text-xl font-bold mt-1 text-white">Booking Paket Wedding</h3>
                        <p class="text-white/80 mt-2 text-xs leading-relaxed">Pilih paket WO (Silver/Gold/dll). Jika paket include undangan digital, undangan akan otomatis aktif di dashboard.</p>
                    </div>
                    <div class="w-10 h-10 shrink-0 rounded-full bg-white/20 border border-white/30 flex items-center justify-center text-sm shadow-inner ml-3 text-white">
                        <i class="fas fa-ring"></i>
                    </div>
                </div>
                <div class="mt-5 inline-flex items-center gap-2 text-xs font-bold text-white">
                    Mulai booking <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            @php
                $maintenanceMode = (bool) \App\Models\SiteSetting::getValue('invitation_maintenance_mode', false);
            @endphp

            @if($maintenanceMode)
            <div class="block bg-white rounded-[24px] p-6 shadow-xl relative overflow-hidden">
                <div class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-10 flex items-center justify-center">
                    <div class="bg-red-600 text-white text-[10px] font-bold uppercase tracking-wider py-1.5 px-4 rounded-full shadow-lg transform -rotate-6 border-2 border-white">
                        Sedang Maintenance
                    </div>
                </div>
                <div class="flex items-start justify-between opacity-40">
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-bold">Digital Invitation</p>
                        <h3 class="text-xl font-bold mt-1 text-gray-800">Undangan Digital Saja</h3>
                        <p class="text-gray-500 mt-2 text-xs leading-relaxed">Pilih template, buat undangan draft di dashboard, lalu lengkapi data & file.</p>
                    </div>
                    <div class="w-10 h-10 shrink-0 rounded-full bg-gray-100 flex items-center justify-center text-sm ml-3 grayscale text-gray-400">
                        <i class="fas fa-heart"></i>
                    </div>
                </div>
            </div>
            @else
            <a href="{{ route('invitation-order.start') }}" class="block bg-white rounded-[24px] p-6 shadow-xl hover:shadow-2xl hover:-translate-y-0.5 transition-all">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-bold">Digital Invitation</p>
                        <h3 class="text-xl font-bold mt-1 text-gray-800">Undangan Digital Saja</h3>
                        <p class="text-gray-500 mt-2 text-xs leading-relaxed">Pilih template, buat undangan draft di dashboard, lalu lengkapi data & file.</p>
                    </div>
                    <div class="w-10 h-10 shrink-0 rounded-full bg-gray-100 flex items-center justify-center text-sm ml-3 text-gray-400">
                        <i class="fas fa-heart"></i>
                    </div>
                </div>
                <div class="mt-5 inline-flex items-center gap-2 text-xs font-bold text-gray-400">
                    Checkout undangan <i class="fas fa-arrow-right"></i>
                </div>
            </a>
            @endif
        </div>
    </div>
</div>

@endsection
