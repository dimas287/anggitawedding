@extends('layouts.guest')
@section('title', 'Pilih Layanan – Anggita WO')

@section('content')
<div class="pt-28 bg-gradient-to-b from-purple-900 via-purple-800 to-white text-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="text-center max-w-3xl mx-auto">
            <span class="text-yellow-300 text-xs font-semibold uppercase tracking-[0.5em]">Mulai Booking</span>
            <h1 class="font-playfair text-4xl md:text-5xl font-bold mt-4 mb-4">Pilih Layanan</h1>
            <p class="text-white/80 text-base md:text-lg">Pilih alur yang sesuai kebutuhan Anda. Anda bisa booking paket wedding, atau order undangan digital saja.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-12">
            <a href="{{ route('booking.select-package') }}" class="group bg-white/15 backdrop-blur rounded-3xl p-7 border border-white/20 shadow-lg hover:shadow-2xl transition-all">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em] text-yellow-200 font-semibold">Wedding Organizer</p>
                        <h3 class="text-2xl font-bold mt-2">Booking Paket Wedding</h3>
                        <p class="text-white/80 mt-3 text-sm leading-relaxed">Pilih paket WO (Silver/Gold/dll). Jika paket include undangan digital, undangan akan otomatis aktif di dashboard.</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center text-2xl">💍</div>
                </div>
                <div class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-white">
                    Mulai booking <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                </div>
            </a>

            @php
                $maintenanceMode = (bool) \App\Models\SiteSetting::getValue('invitation_maintenance_mode', false);
            @endphp

            @if($maintenanceMode)
            <div class="relative group bg-gray-50 rounded-3xl p-7 border border-gray-100 shadow-md text-gray-400 overflow-hidden">
                <div class="absolute inset-0 bg-white/40 backdrop-blur-[1px] z-10 flex items-center justify-center">
                    <div class="bg-red-600 text-white text-[10px] font-bold uppercase tracking-wider py-1 px-3 rounded-full shadow-lg transform -rotate-12 border-2 border-white">
                        Sedang Maintenance
                    </div>
                </div>
                <div class="flex items-start justify-between opacity-50">
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em] text-gray-400 font-semibold">Digital Invitation</p>
                        <h3 class="text-2xl font-bold mt-2 text-gray-500">Undangan Digital Saja</h3>
                        <p class="text-gray-400 mt-3 text-sm leading-relaxed">Fitur ini sementara tidak tersedia karena sedang dalam pemeliharaan sistem.</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center text-2xl grayscale">💌</div>
                </div>
                <div class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-gray-400">
                    Sistem dalam perbaikan <i class="fas fa-tools ml-1 text-xs"></i>
                </div>
            </div>
            @else
            <a href="{{ route('invitation-order.start') }}" class="group bg-white rounded-3xl p-7 border border-gray-100 shadow-lg hover:shadow-2xl transition-all text-gray-900">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em] text-purple-600 font-semibold">Digital Invitation</p>
                        <h3 class="text-2xl font-bold mt-2 text-gray-500">Undangan Digital Saja</h3>
                        <p class="text-gray-600 mt-3 text-sm leading-relaxed">Pilih template, buat undangan draft di dashboard, lalu lengkapi data & file. Publish hanya bisa setelah pembayaran selesai.</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center text-2xl">💌</div>
                </div>
                <div class="mt-6 inline-flex items-center gap-2 text-sm font-semibold text-purple-700">
                    Checkout undangan <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i>
                </div>
            </a>
            @endif
        </div>
    </div>
</div>

@endsection
