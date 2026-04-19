@extends('layouts.guest')

@section('title', 'Sedang Pemeliharaan – Anggita Wedding')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center px-6 text-center py-20 bg-gray-50">
    <div class="mb-10 animate-fade-in">
        <div class="w-32 h-32 rounded-full bg-white shadow-2xl flex items-center justify-center mx-auto mb-8 border border-yellow-100">
            <i class="fas fa-tools text-4xl text-yellow-500"></i>
        </div>
        <h1 class="text-4xl md:text-5xl font-playfair font-bold text-gray-900 mb-6">Mempersiapkan Hal Besar...</h1>
        <div class="max-w-xl mx-auto">
            <p class="text-lg text-gray-600 leading-relaxed italic mb-8">
                "{{ $exception->getMessage() ?: 'Website sedang dalam pemeliharaan rutin untuk meningkatkan kualitas layanan kami.' }}"
            </p>
            <div class="h-1 w-24 bg-yellow-500 mx-auto rounded-full mb-8"></div>
            <p class="text-sm text-gray-400 font-medium uppercase tracking-widest">Akan Segera Kembali</p>
        </div>
    </div>
    
    <div class="flex flex-col sm:flex-row gap-6">
        @php
            $waUrl = $brandInfo['social_links']['whatsapp'] ?? 'https://wa.me/6281231122057';
            $igUrl = $brandInfo['social_links']['instagram'] ?? 'https://instagram.com/anggita_wedding';
        @endphp
        <a href="{{ $waUrl }}" class="gold-gradient text-white px-10 py-4 rounded-full font-bold font-poppins shadow-xl hover:opacity-90 transition-all flex items-center justify-center gap-3">
            <i class="fab fa-whatsapp"></i> Hubungi via WhatsApp
        </a>
        <a href="{{ $igUrl }}" class="px-10 py-4 rounded-full border border-gray-200 bg-white text-gray-800 font-bold font-poppins hover:bg-gray-50 transition-all flex items-center justify-center gap-3">
            <i class="fab fa-instagram"></i> Pantau Instagram
        </a>
    </div>

    <div class="mt-20 flex items-center gap-3 text-gray-400 group">
        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center group-hover:bg-yellow-100 transition-colors">
            <i class="fas fa-rings-wedding text-xs group-hover:text-yellow-600 transition-colors"></i>
        </div>
        <span class="text-xs font-bold uppercase tracking-[0.3em] font-playfair">{{ $brandInfo['brand_name'] ?? 'Anggita Wedding' }}</span>
    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 1.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    /* Hide nav and footer on maintenance mode */
    nav, footer, .custom-cursor, .custom-cursor-outline, .back-to-top { display: none !important; }
</style>
@endsection
