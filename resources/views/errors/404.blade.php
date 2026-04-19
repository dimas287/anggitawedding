@extends('layouts.guest')

@section('title', 'Halaman Tidak Ditemukan – Anggita Wedding')

@section('content')
<div class="min-h-[70vh] flex flex-col items-center justify-center px-6 text-center">
    <div class="relative mb-8">
        <h1 class="text-[12rem] md:text-[18rem] font-bold text-gray-50 dark:text-white/5 leading-none select-none">404</h1>
        <div class="absolute inset-0 flex flex-col items-center justify-center mt-8 md:mt-12">
            <h2 class="text-3xl md:text-4xl font-playfair font-bold text-gray-900 dark:text-white mb-4">Mungkin Terlalu Asik Bermimpi...</h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto leading-relaxed">Halaman yang Anda cari tidak ditemukan. Mari kembali ke beranda untuk merencanakan pernikahan impian Anda.</p>
        </div>
    </div>
    
    <div class="flex flex-col sm:flex-row gap-4 mt-8">
        <a href="{{ route('landing') }}" class="gold-gradient text-white px-10 py-4 rounded-full font-bold font-poppins shadow-xl hover:opacity-90 transition-all flex items-center justify-center gap-2">
            <i class="fas fa-home"></i> Kembali ke Beranda
        </a>
        <a href="{{ route('consultation.form') }}" class="px-10 py-4 rounded-full border border-gray-200 dark:border-white/10 text-gray-800 dark:text-gray-200 font-bold font-poppins hover:bg-gray-50 dark:hover:bg-white/5 transition-all">
            Konsultasi Gratis
        </a>
    </div>
</div>
@endsection
