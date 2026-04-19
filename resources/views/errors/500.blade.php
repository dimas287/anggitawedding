@extends('layouts.guest')

@section('title', 'Terjadi Kesalahan – Anggita Wedding')

@section('content')
<div class="min-h-[70vh] flex flex-col items-center justify-center px-6 text-center">
    <div class="relative mb-8">
        <h1 class="text-[12rem] md:text-[18rem] font-bold text-gray-50 leading-none select-none">500</h1>
        <div class="absolute inset-0 flex flex-col items-center justify-center mt-8 md:mt-12">
            <h2 class="text-3xl md:text-4xl font-playfair font-bold text-gray-900 mb-4">Ups, Ada Gangguan Kecil</h2>
            <p class="text-gray-500 max-w-md mx-auto leading-relaxed">Terjadi kesalahan pada server kami. Jangan khawatir, tim teknis kami sedang memperbaikinya. Silakan coba muat ulang halaman ini.</p>
        </div>
    </div>
    
    <div class="flex flex-col sm:flex-row gap-4 mt-8">
        <button onclick="window.location.reload()" class="gold-gradient text-white px-10 py-4 rounded-full font-bold font-poppins shadow-xl hover:opacity-90 transition-all flex items-center justify-center gap-2">
            <i class="fas fa-sync-alt"></i> Muat Ulang Halaman
        </button>
        <a href="{{ route('landing') }}" class="px-10 py-4 rounded-full border border-gray-200 text-gray-800 font-bold font-poppins hover:bg-gray-50 transition-all">
            Kembali ke Beranda
        </a>
    </div>
</div>
@endsection
