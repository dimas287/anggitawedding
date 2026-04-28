@extends('layouts.guest')

@section('title', 'Sedang Pembaruan – Anggita Wedding Organizer')

@section('content')
<div class="min-h-screen pt-24 pb-12 flex items-center justify-center relative overflow-hidden bg-[#FAF9F6] dark:bg-[#0A0A0A]">
    {{-- Decorative Background Elements --}}
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-purple-100/40 rounded-full blur-[120px]"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-amber-100/40 rounded-full blur-[120px]"></div>
    
    <div class="noise-surface absolute inset-0 opacity-[0.03] pointer-events-none"></div>

    <div class="max-w-3xl w-full px-6 py-12 relative z-10 text-center">
        <div class="space-y-12" data-reveal>
            {{-- Aesthetic Icon Holder --}}
            <div class="relative inline-block group">
                <div class="w-32 h-32 md:w-40 md:h-40 rounded-[2.5rem] bg-white dark:bg-gray-800 shadow-[0_20px_50px_rgba(0,0,0,0.05)] flex items-center justify-center transform group-hover:rotate-6 transition-transform duration-500 border border-white/50 dark:border-white/10 backdrop-blur-sm">
                    <i class="fas fa-magic text-4xl md:text-5xl text-gray-800 dark:text-gray-200"></i>
                </div>
                <div class="absolute -top-4 -right-4 w-12 h-12 rounded-2xl bg-gray-900 flex items-center justify-center text-white shadow-xl animate-pulse">
                    <i class="fas fa-sparkles text-sm"></i>
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex items-center justify-center gap-3">
                    <span class="w-12 h-[1px] bg-gray-300 dark:bg-white/20"></span>
                    <span class="text-[11px] font-bold uppercase tracking-[0.4em] text-gray-400 dark:text-gray-500">Restoring Elegance</span>
                    <span class="w-12 h-[1px] bg-gray-300 dark:bg-white/20"></span>
                </div>
                
                <h1 class="font-playfair text-5xl md:text-7xl font-bold text-gray-900 dark:text-white leading-tight">
                    Sedang Kami <br>
                    <span class="italic text-gray-400 dark:text-gray-500 font-medium">Sempurnakan</span>
                </h1>
                
                <p class="text-gray-500 dark:text-gray-400 text-lg md:text-xl leading-relaxed max-w-xl mx-auto font-medium">
                    {{ session('maintenance_message') ?? 'Kami sedang melakukan pemeliharaan rutin pada sistem undangan digital kami untuk memastikan setiap detail hari spesial Anda tampil sempurna.' }}
                </p>
            </div>

            <div class="flex flex-col sm:flex-row gap-5 justify-center items-center pt-4">
                <a href="{{ route('landing') }}" class="w-full sm:w-auto px-10 py-5 rounded-2xl bg-white dark:bg-white/5 border border-gray-100 dark:border-white/10 text-gray-800 dark:text-gray-200 font-semibold shadow-sm hover:shadow-md hover:-translate-y-1 transition-all flex items-center justify-center gap-3">
                    <i class="fas fa-arrow-left text-xs"></i> Kembali ke Beranda
                </a>
                <a href="https://wa.me/6281234567890" target="_blank" class="w-full sm:w-auto px-12 py-5 rounded-2xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-semibold shadow-2xl shadow-gray-900/20 dark:shadow-none hover:bg-black dark:hover:bg-gray-100 hover:-translate-y-1 transition-all flex items-center justify-center gap-3">
                    <i class="fab fa-whatsapp"></i> Hubungi Concierge
                </a>
            </div>

            <div class="pt-16">
                <p class="text-[11px] text-gray-400 font-medium uppercase tracking-[0.2em] opacity-60">
                    Expected Completion: Soon • Anggita Wedding Organizer
                </p>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.8; }
    }
    .animate-pulse {
        animation: pulse 3s ease-in-out infinite;
    }
</style>
@endsection
