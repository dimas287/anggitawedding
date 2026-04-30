@extends('layouts.guest')
@section('title', 'Wedding Organizer Surabaya – Anggita Wedding Organizer')
@section('meta_description', 'Anggita Wedding Organizer Surabaya mewujudkan pernikahan impian Anda. Layanan lengkap mulai dari dekorasi, rias pengantin, dokumentasi, hingga undangan digital premium.')

@push('head')
<style>
    [x-cloak] {
        display: none !important;
    }

    .hero-minimalist {
        position: relative;
        isolation: isolate;
    }

    .hero-headline-fallback {
        display: block;
    }

    .hero-typing {
        display: flex;
        flex-direction: column;
        gap: 6px;
        align-items: center;
    }

    .hero-typing-line {
        display: inline-flex;
        align-items: flex-end;
        gap: 6px;
        min-height: 1.2em;
        white-space: nowrap;
    }

    @media (max-width: 640px) {
        .hero-typing-line {
            white-space: normal;
            text-align: center;
            justify-content: center;
        }
    }

    .typewriter-caret {
        width: 2px;
        height: 1.2em;
        background: currentColor;
        display: inline-block;
        box-shadow: 0 0 14px rgba(255, 255, 255, 0.65);
        animation: caretBlink 1s steps(1) infinite;
    }

        [data-reveal] {
        opacity: 0;
        transform: var(--reveal-transform, translateY(40px));
        transition: opacity 0.85s ease, transform 0.85s ease;
        transition-delay: var(--reveal-delay, 0s);
    }

    [data-reveal][data-reveal-direction="up"] {
        --reveal-transform: translateY(40px);
    }

    [data-reveal][data-reveal-direction="down"] {
        --reveal-transform: translateY(-40px);
    }

    [data-reveal][data-reveal-direction="left"] {
        --reveal-transform: translateX(-45px);
    }

    [data-reveal][data-reveal-direction="right"] {
        --reveal-transform: translateX(45px);
    }

    [data-reveal][data-reveal-direction="zoom"] {
        --reveal-transform: scale(0.92);
    }

    [data-reveal].is-visible {
        opacity: 1;
        transform: none;
    }

    .btn-pulse {
        position: relative;
        overflow: hidden;
    }

    .btn-pulse::after {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.4);
        transform: scale(0.92);
        opacity: 0;
        animation: pulseRing 2.8s ease-out infinite;
    }

    .btn-pulse:hover::after {
        opacity: 1;
    }

    .section-glow {
        position: relative;
        z-index: 0;
    }

    .section-glow::before {
        content: '';
        position: absolute;
        inset: -60px;
        border-radius: 48px;
        background: radial-gradient(circle, rgba(250, 216, 137, 0.15), transparent 65%);
        opacity: 0;
        transition: opacity 0.8s ease;
        z-index: -1;
    }

    .section-glow.is-visible::before {
        opacity: 1;
    }

    @keyframes caretBlink {
        0%, 60% { opacity: 1; }
        61%, 100% { opacity: 0; }
    }

    @keyframes modernFadeUp {
        0% { transform: translateY(20px); opacity: 0; }
        100% { transform: translateY(0); opacity: 1; }
    }

    @keyframes pulseRing {
        0% { transform: scale(0.9); opacity: 0.6; }
        70% { transform: scale(1.2); opacity: 0; }
        100% { opacity: 0; }
    }

    .mouse-scroll {
        width: 24px;
        height: 40px;
        border: 2px solid rgba(255, 255, 255, 0.4);
        border-radius: 12px;
        position: relative;
    }

    .mouse-scroll::after {
        content: '';
        position: absolute;
        top: 6px;
        left: 50%;
        transform: translateX(-50%);
        width: 4px;
        height: 8px;
        background: white;
        border-radius: 2px;
        animation: mouseWheel 2s ease-out infinite;
    }

    @keyframes mouseWheel {
        0% { transform: translateX(-50%) translateY(0); opacity: 0; }
        20% { transform: translateX(-50%) translateY(0); opacity: 1; }
        80% { transform: translateX(-50%) translateY(16px); opacity: 0; }
        100% { transform: translateX(-50%) translateY(16px); opacity: 0; }
    }
</style>
@endpush

@php
    $heroSlidesPayload = $heroSlides->map(function ($slide) {
        return [
            'id' => $slide->id,
            'type' => $slide->media_type,
            'src' => $slide->resolved_media_url,
            'title' => $slide->title,
            'subtitle' => $slide->subtitle,
            'cta_label' => $slide->cta_label,
            'cta_url' => $slide->cta_url,
        ];
    })->values();
    $maintenanceMode = (bool) \App\Models\SiteSetting::getValue('invitation_maintenance_mode', false);
    $firstHeroSlide = $heroSlides->first();
@endphp

@push('head')
{{-- Preload the first hero image/video for LCP --}}
@if($firstHeroSlide)
    @if($firstHeroSlide->media_type === 'image')
        <link rel="preload" as="image" href="{{ $firstHeroSlide->resolved_media_url }}" fetchpriority="high">
    @elseif($firstHeroSlide->media_type === 'video')
        <link rel="preload" as="video" href="{{ $firstHeroSlide->resolved_media_url }}">
    @endif
@endif
@endpush

@section('content')
{{-- HERO --}}
<section class="hero-minimalist min-h-screen min-h-[100dvh] flex items-center justify-center relative overflow-hidden" x-data='heroSlider({ slides: @json($heroSlidesPayload) })'>
    <template x-for="(slide, index) in slides" :key="slide.id">
        <div class="absolute inset-0 transition-opacity duration-[1200ms]" :class="current === index ? 'opacity-100' : 'opacity-0'">
            <template x-if="slide.type === 'video'">
                <video :src="slide.src" autoplay muted loop playsinline :preload="index === 0 ? 'auto' : 'metadata'" class="w-full h-full object-cover"></video>
            </template>
            <template x-if="slide.type === 'image'">
                <img :src="slide.src" :alt="slide.title || 'Anggita Wedding'" class="w-full h-full object-cover" :fetchpriority="index === 0 ? 'high' : 'low'" :loading="index === 0 ? 'eager' : 'lazy'">
            </template>
            <div class="absolute inset-0 bg-gradient-to-b from-black/70 via-black/55 to-black/70 transition-opacity duration-700"></div>
        </div>
    </template>

    <div class="relative z-10 w-full px-6 md:px-12 lg:px-20 pt-28 pb-40 md:pb-32 lg:pb-28 min-h-full flex flex-col justify-center">
        <div class="max-w-4xl" data-reveal data-reveal-direction="up" style="--reveal-delay:.1s;">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-black/20 backdrop-blur-md text-white/90 text-[10px] tracking-[0.3em] uppercase mb-6 border border-white/20">
                {{ $heroCopy['badge'] ?? 'Elegance in Every Detail' }}
            </div>
            <h1 class="font-playfair text-5xl md:text-7xl lg:text-[5.5rem] text-white leading-[1.05] tracking-tight drop-shadow-md mb-6 relative">
                <span class="sr-only">Wedding Organizer Surabaya Terbaik</span>
                <span class="hero-headline-fallback block" x-data="{ hide: false }" x-init="hide = true" x-show="!hide">
                    Wujudkan Pernikahan <br><span class="italic text-white/90">Impian</span>
                </span>
            <span class="hero-typing block" x-data="heroHeadlineTypewriter({
                lines: [
                    { text: 'Wujudkan Pernikahan', classes: '' },
                    { text: 'Impian', classes: 'italic text-white/90 font-light' }
                ],
                speed: 75,
                lineDelay: 260,
                restartDelay: 4600,
                loop: false
            })" x-init="start()" x-cloak>
                <template x-for="(line, idx) in lines" :key="`hero-line-${idx}`">
                    <span class="hero-typing-line block text-left" :class="line.classes">
                        <span x-text="displayLines[idx]"></span>
                        <span class="typewriter-caret" x-show="caretShouldShow(idx)"></span>
                    </span>
                </template>
            </span>
        </h1>
        
        <template x-if="activeSlide()?.title">
            <p class="text-white/90 text-xl font-light mb-2 tracking-wide" x-text="activeSlide().title"></p>
        </template>
        
        <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-8 mt-6 lg:mt-10">
            <div class="max-w-md">
                <p class="text-white/80 text-sm md:text-base font-light tracking-wide leading-relaxed border-l border-white/30 pl-4 mb-6" data-reveal data-reveal-direction="right" style="--reveal-delay:.18s;"
                   x-text="activeSlide()?.subtitle ?? @js($heroCopy['fallback_subtitle'] ?? 'Kami mengkurasi setiap momen menjadi karya seni tak terlupakan. Elegan, rapi, dan esensial.')">
                    {{ $heroCopy['fallback_subtitle'] ?? 'Kami mengkurasi setiap momen menjadi karya seni tak terlupakan. Elegan, rapi, dan esensial.' }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 items-start" data-reveal data-reveal-direction="up" style="--reveal-delay:.25s;">
                    <a href="{{ $heroCopy['primary_cta_url'] ?? route('booking.select-package') }}"
                       class="bg-white text-gray-900 font-semibold px-8 py-3 hover:bg-gray-100 transition-colors text-xs w-full sm:w-auto text-center tracking-[0.2em] uppercase">
                        {{ $heroCopy['primary_cta_label'] ?? 'Eksplorasi Paket' }}
                    </a>
                    <a href="{{ $heroCopy['secondary_cta_url'] ?? route('consultation.form') }}"
                       class="bg-transparent border border-white/40 text-white font-medium px-8 py-3 hover:bg-white/10 transition-colors text-xs w-full sm:w-auto text-center tracking-[0.2em] uppercase">
                        {{ $heroCopy['secondary_cta_label'] ?? 'Konsultasi' }}
                    </a>
                </div>
            </div>

            {{-- Date Check --}}
            <div x-data="dateChecker()" class="bg-black/40 backdrop-blur-md border border-white/10 p-5 lg:p-6 w-full lg:w-[380px] shrink-0" data-reveal data-reveal-direction="left" style="--reveal-delay:.27s;">
                <p class="text-white/70 text-[10px] uppercase tracking-[0.25em] font-medium mb-4">Cek Ketersediaan Kami</p>
                <div class="flex gap-2">
                    <input type="text" x-model="date" data-flatpickr data-min-date="{{ now()->addDay()->toDateString() }}"
                           placeholder="Pilih tanggal"
                           class="flex-1 bg-transparent border-b border-white/20 text-white rounded-none px-2 py-2 text-sm placeholder-white/40 focus:outline-none focus:border-white transition-colors">
                    <button @click="checkDate()" :disabled="!date || loading"
                            class="bg-white text-black px-6 py-2 text-[10px] font-bold tracking-widest uppercase hover:bg-gray-200 transition-all disabled:opacity-50">
                        <span x-show="!loading">Cek</span>
                        <span x-show="loading"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
                <div x-show="result" x-cloak class="mt-4 p-3 text-xs"
                     :class="result?.status === 'available' ? 'bg-green-500/10 text-green-300 border-l-2 border-green-400/50' :
                             result?.status === 'tentative' ? 'bg-yellow-500/10 text-yellow-300 border-l-2 border-yellow-400/50' :
                             'bg-red-500/10 text-red-300 border-l-2 border-red-400/50'">
                    <p><i :class="result?.status === 'available' ? 'fa-check' : result?.status === 'tentative' ? 'fa-clock' : 'fa-times'" class="fas mr-2"></i>
                    <strong x-text="result?.label"></strong> – <span x-text="result?.message"></span></p>
                </div>
            </div>
        </div>
        
        <template x-if="activeSlide()?.cta_label && activeSlide()?.cta_url">
            <a :href="activeSlide().cta_url" class="inline-flex items-center gap-2 text-white/60 hover:text-white text-[10px] tracking-[0.2em] uppercase mt-8 transition-colors">
                <span x-text="activeSlide().cta_label"></span> <i class="fas fa-arrow-right"></i>
            </a>
        </template>
        <div class="flex justify-start gap-3 mt-8" data-reveal data-reveal-direction="up" style="--reveal-delay:.42s;">
            <template x-for="(slide, index) in slides" :key="'dot-' + slide.id">
                <button type="button" class="w-2 h-2 rounded-full border border-white/40 transition-all"
                        :class="current === index ? 'bg-white w-6' : 'bg-transparent'" @click="goTo(index)"></button>
            </template>
        </div>
    </div>

    {{-- Scroll Indicator --}}
    <div class="absolute bottom-12 sm:bottom-10 left-0 right-0 flex justify-center z-30 transition-all duration-700 pointer-events-none" 
         x-show="showScrollIndicator" 
         x-transition:leave="transition ease-in duration-500" 
         x-transition:leave-start="opacity-100 translate-y-0" 
         x-transition:leave-end="opacity-0 translate-y-4">
        <button type="button" @click="scrollToDream()" class="flex flex-col items-center gap-4 group pointer-events-auto">
            <div class="mouse-scroll group-hover:border-white transition-colors"></div>
            <span class="text-[9px] tracking-[0.4em] text-white/40 group-hover:text-white transition-all uppercase font-bold">Scroll Down</span>
        </button>
    </div>
</section>

{{-- DREAM SECTION — Editorial Layout --}}
<section id="dream-section" class="pt-16 lg:pt-36 pb-12 lg:pb-16 bg-[#FAF9F6] dark:bg-[#0A0A0A] relative overflow-hidden transition-colors duration-500 z-10">
    {{-- 3D Paper Tear Overlay (Represents the DARK Hero section being ripped away to reveal the LIGHT Dream section) --}}
    <div id="paper-rip-container" class="absolute inset-x-0 top-0 h-[350px] z-[60] pointer-events-none overflow-visible" style="perspective: 2000px;">
        <div id="paper-rip-layer" class="absolute inset-x-0 top-0 h-full bg-[#111111] dark:bg-[#050505] origin-top">
            {{-- Jagged Edge SVG --}}
            <svg class="absolute bottom-0 left-0 w-full h-16 translate-y-[98%] fill-[#111111] dark:fill-[#050505] drop-shadow-[0_20px_40px_rgba(0,0,0,0.6)]" 
                 preserveAspectRatio="none" viewBox="0 0 1200 100">
                <path d="M0,0 L1200,0 L1200,20 C1150,35 1100,10 1050,25 C1000,40 950,15 900,30 C850,45 800,20 750,35 C700,50 650,25 600,40 C550,55 500,30 450,45 C400,60 350,35 300,50 C250,65 200,40 150,55 C100,70 50,45 0,60 Z" />
            </svg>
            {{-- Back side shadow effect for 3D feel --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent opacity-0" id="paper-rip-back-shadow"></div>
        </div>
    </div>
    {{-- Subtle grain texture overlay --}}
    <div class="absolute inset-0 opacity-[0.03] dark:opacity-[0.06] pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg viewBox=%220 0 256 256%22 xmlns=%22http://www.w3.org/2000/svg%22%3E%3Cfilter id=%22noise%22%3E%3CfeTurbulence type=%22fractalNoise%22 baseFrequency=%220.9%22 numOctaves=%224%22 stitchTiles=%22stitch%22/%3E%3C/filter%3E%3Crect width=%22100%25%22 height=%22100%25%22 filter=%22url(%23noise)%22/%3E%3C/svg%3E');"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {{-- Editorial Header --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-0 mb-20 lg:mb-28">
            <div class="lg:col-span-5" data-reveal data-reveal-direction="left">
                <p class="text-gray-400 dark:text-gray-500 text-[10px] font-bold uppercase tracking-[0.35em] mb-5 flex items-center gap-3">
                    <span class="w-8 h-px bg-gray-300 dark:bg-gray-600"></span>
                    {{ $dreamSection['eyebrow'] ?? 'The Experience' }}
                </p>
                <h2 class="font-playfair text-4xl md:text-5xl lg:text-[3.5rem] text-gray-900 dark:text-white leading-[1.1] tracking-tight">
                    {{ $dreamSection['heading'] ?? 'Merangkai Kisah Dalam Nuansa Berkelas' }}
                </h2>
            </div>
            <div class="lg:col-span-2"></div>
            <div class="lg:col-span-5 flex items-end" data-reveal data-reveal-direction="right" style="--reveal-delay:.1s;">
                <p class="text-gray-500 dark:text-gray-400 text-base lg:text-lg font-light leading-relaxed border-l-2 border-yellow-500/40 dark:border-yellow-600/40 pl-6">
                    {{ $dreamSection['description'] ?? 'Kami mengedepankan kualitas, estetika, dan ketenangan pikiran Anda. Biarkan kami mengatur simpati dari setiap detail kecil, sehingga Anda cukup menikmati momen yang berlalu sekali seumur hidup.' }}
                </p>
            </div>
        </div>

        {{-- Editorial Asymmetric Image + Stacking Highlight Cards Grid --}}
        <div id="highlight-experience-row" class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-6 items-start">
            {{-- Right: Features + CTA (First in Source for Mobile) --}}
            <div class="lg:col-span-5 flex flex-col gap-6 lg:pt-8 order-1 lg:order-2">
                @foreach($dreamSection['highlights'] ?? [] as $index => $dream)
                <div class="group flex gap-5 p-5 rounded-2xl border border-transparent hover:border-gray-200 dark:hover:border-white/10 hover:bg-white/60 dark:hover:bg-white/[0.03] transition-all duration-300" data-reveal data-reveal-direction="right" style="--reveal-delay: {{ 0.1 + $index * 0.06 }}s;">
                    <div class="w-12 h-12 rounded-xl bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-400 flex items-center justify-center text-lg shrink-0 group-hover:bg-yellow-100 dark:group-hover:bg-yellow-900/30 transition-colors">
                        <i class="fas {{ $dream['icon'] ?? 'fa-heart' }}"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900 dark:text-white text-sm mb-1">{{ $dream['title'] ?? '' }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 font-light leading-relaxed">{{ $dream['desc'] ?? '' }}</p>
                    </div>
                </div>
                @endforeach

                {{-- CTAs --}}
                <div class="flex flex-col sm:flex-row gap-4 mt-4 pl-0 sm:pl-5" data-reveal data-reveal-direction="up" style="--reveal-delay:.3s;">
                    <a href="{{ $dreamSection['primary_cta_url'] ?? '#paket' }}" class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium px-8 py-3.5 rounded-lg hover:bg-black dark:hover:bg-gray-100 transition-colors text-xs tracking-[0.2em] uppercase text-center">
                        {{ $dreamSection['primary_cta_label'] ?? 'Lihat Koleksi' }}
                    </a>
                    <a href="{{ $dreamSection['secondary_cta_url'] ?? route('consultation.form') }}" class="px-8 py-3.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium text-xs hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition-colors tracking-[0.2em] uppercase text-center">
                        {{ $dreamSection['secondary_cta_label'] ?? 'Hubungi Kami' }}
                    </a>
                </div>
            </div>

            {{-- Left: Stacking Highlight Cards (Second in Source for Mobile) --}}
            <div class="lg:col-span-7 relative order-2 lg:order-1">
                @if($highlightCards->count() > 1)
                    {{-- Scroll-pinned stacking cards --}}
                    <div id="highlight-stack-pin" class="relative mx-auto" style="perspective: 1200px;">
                        <div id="highlight-stack-container" class="relative w-full">
                            {{-- Container for cards --}}
                            <div class="relative w-full h-[65vh] overflow-hidden rounded-2xl relative">
                                @foreach($highlightCards as $i => $card)
                                <div class="highlight-card absolute inset-0 rounded-2xl lg:rounded-3xl overflow-hidden shadow-2xl dark:shadow-black/40"
                                     data-card-index="{{ $i }}"
                                     style="z-index: {{ 50 - $i }}; transform-origin: top right; backface-visibility: hidden;">
                                    <img src="{{ $card->resolved_image_url }}" loading="lazy" decoding="async"
                                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.02]" 
                                         alt="{{ $card->title }}">
                                    {{-- Gradient overlay --}}
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                                    {{-- Floating highlight badge --}}
                                    <div class="absolute bottom-4 left-4 right-4 sm:bottom-6 sm:left-6 sm:right-auto bg-white/95 dark:bg-[#161616]/95 backdrop-blur-md rounded-2xl shadow-xl dark:shadow-black/50 px-5 py-3.5 border border-gray-100 dark:border-white/10 max-w-xs">
                                        <p class="text-[10px] uppercase text-yellow-600 dark:text-yellow-400 font-bold tracking-[0.25em] mb-1">Highlight</p>
                                        <p class="font-playfair text-base sm:text-lg text-gray-900 dark:text-white font-semibold leading-tight">{{ $card->title }}</p>
                                        @if($card->subtitle)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $card->subtitle }}</p>
                                        @endif
                                        @if($card->quote)
                                            <div class="mt-2 flex items-center gap-1.5 text-yellow-500 text-xs">
                                                <i class="fas fa-star"></i>
                                                <span class="line-clamp-1">{{ $card->quote }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @elseif($highlightCards->count() === 1)
                    {{-- Single card: no animation --}}
                    @php $singleCard = $highlightCards->first(); @endphp
                    <div class="relative group">
                        <div class="relative rounded-2xl overflow-hidden aspect-[4/5] shadow-md group">
                            <img src="{{ $singleCard->resolved_image_url }}" loading="lazy" decoding="async"
                                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.02]" alt="{{ $singleCard->title }}">
                        </div>
                        <div class="absolute bottom-5 left-5 bg-white dark:bg-[#161616] rounded-2xl shadow-xl dark:shadow-black/50 px-6 py-4 border border-gray-100 dark:border-white/10 max-w-[calc(100%-40px)]">
                            <p class="text-[10px] uppercase text-yellow-600 dark:text-yellow-400 font-bold tracking-[0.25em] mb-1">Highlight</p>
                            <p class="font-playfair text-lg text-gray-900 dark:text-white font-semibold">{{ $singleCard->title }}</p>
                            @if($singleCard->subtitle)<p class="text-xs text-gray-500 dark:text-gray-400">{{ $singleCard->subtitle }}</p>@endif
                            @if($singleCard->quote)
                            <div class="mt-2 flex items-center gap-1.5 text-yellow-500 text-xs">
                                <i class="fas fa-star"></i>
                                <span>{{ $singleCard->quote }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                @else
                    {{-- Fallback: Original static image from dream section --}}
                    @php
                        $dreamHeroImage = trim($dreamSection['hero_image'] ?? '') ?: 'https://images.unsplash.com/photo-1520854221050-0f4caff449fb?w=1200';
                    @endphp
                    <div class="relative group">
                        <div class="relative w-full h-[70vh] rounded-3xl overflow-hidden shadow-xl group border border-gray-100 dark:border-white/5">
                            <img src="{{ $dreamHeroImage }}" loading="lazy" decoding="async" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-[1.02]" alt="Wedding highlight">
                        </div>
                        <div class="absolute bottom-5 left-5 bg-white dark:bg-[#161616] rounded-2xl shadow-xl dark:shadow-black/50 px-6 py-4 border border-gray-100 dark:border-white/10 max-w-[calc(100%-40px)]">
                            <p class="text-[10px] uppercase text-yellow-600 dark:text-yellow-400 font-bold tracking-[0.25em] mb-1">Highlight</p>
                            <p class="font-playfair text-lg text-gray-900 dark:text-white font-semibold">{{ $dreamSection['highlight_card']['title'] ?? 'Momen Terbaik' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $dreamSection['highlight_card']['subtitle'] ?? '' }}</p>
                            <div class="mt-2 flex items-center gap-1.5 text-yellow-500 text-xs">
                                <i class="fas fa-star"></i>
                                <span>{{ $dreamSection['highlight_card']['quote'] ?? '' }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- STATS --}}
<section class="relative z-20 bg-[#FAF9F6] dark:bg-[#0A0A0A]" data-reveal data-reveal-direction="up">
    <!-- Background blend - tighter transition -->
    <div class="absolute inset-0 bg-gradient-to-b from-[#FAF9F6] via-[#111111] to-[#FAF9F6] dark:from-[#0A0A0A] dark:via-[#111111] dark:to-[#0A0A0A]"></div>
    <!-- Dark core for stats -->
    <div class="absolute inset-x-0 top-0 bottom-0 bg-[#111111] border-y border-white/5"></div>
    
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-30 py-16 lg:py-20">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center" data-reveal data-reveal-direction="right" style="--reveal-delay:.1s;">
            <div class="space-y-2" data-reveal data-reveal-direction="left" style="--reveal-delay:.12s;">
                <div class="text-4xl lg:text-5xl font-playfair text-white tracking-wide">
                    <span data-countup data-target="{{ (int) ($landingStats['events'] ?? 0) }}" data-suffix="+">0</span>
                </div>
                <div class="text-white/40 text-[10px] uppercase tracking-[0.2em] font-medium mt-2">Event Sukses</div>
            </div>
            <div class="space-y-2" data-reveal data-reveal-direction="up" style="--reveal-delay:.16s;">
                <div class="text-4xl lg:text-5xl font-playfair text-white tracking-wide">
                    <span data-countup data-target="{{ (int) ($landingStats['clients'] ?? 0) }}" data-suffix="+">0</span>
                </div>
                <div class="text-white/40 text-[10px] uppercase tracking-[0.2em] font-medium mt-2">Pasangan Bahagia</div>
            </div>
            <div class="space-y-2" data-reveal data-reveal-direction="right" style="--reveal-delay:.2s;">
                <div class="text-4xl lg:text-5xl font-playfair text-white tracking-wide">
                    <span data-countup data-target="{{ (int) ($landingStats['templates'] ?? 0) }}">0</span>
                </div>
                <div class="text-white/40 text-[10px] uppercase tracking-[0.2em] font-medium mt-2">Desain Mewah</div>
            </div>
            <div class="space-y-2" data-reveal data-reveal-direction="left" style="--reveal-delay:.24s;">
                <div class="text-4xl lg:text-5xl font-playfair text-white tracking-wide">
                    <span data-countup data-target="{{ (int) ($landingStats['years'] ?? 1) }}" data-suffix="+">0</span>
                </div>
                <div class="text-white/40 text-[10px] uppercase tracking-[0.2em] font-medium mt-2">Tahun Keahlian</div>
            </div>
        </div>
    </div>
</section>

{{-- PAKET --}}
<section class="py-24 bg-white dark:bg-[#0A0A0A] section-glow transition-colors duration-500 relative z-20" id="paket" data-reveal data-reveal-direction="up">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
         x-data="{
            tab: '{{ $packagesByCategory->keys()->first() ?? 'rumahan' }}',
            detailModal: false,
            detailPackage: null,
            detailMedia: [],
            detailIndex: 0,
            openDetailModal(pkg) {
                this.detailPackage = pkg;
                this.detailMedia = Array.isArray(pkg.media) ? pkg.media : [];
                this.detailIndex = 0;
                this.detailModal = true;
            },
            closeDetailModal() {
                this.detailModal = false;
                this.detailPackage = null;
                this.detailMedia = [];
                this.detailIndex = 0;
            },
            nextDetail() {
                if (!this.detailMedia.length) return;
                this.detailIndex = (this.detailIndex + 1) % this.detailMedia.length;
            },
            prevDetail() {
                if (!this.detailMedia.length) return;
                this.detailIndex = (this.detailIndex - 1 + this.detailMedia.length) % this.detailMedia.length;
            }
         }">
        <div class="text-center mb-16" data-reveal data-reveal-direction="down">
            <span class="text-gray-400 dark:text-gray-500 text-xs font-semibold uppercase tracking-[0.3em] mb-4 block">Curated Services</span>
            <h2 class="font-playfair text-4xl lg:text-5xl font-light text-gray-900 dark:text-white leading-tight">Layanan Eksklusif</h2>
        </div>

        <div class="flex flex-wrap gap-6 justify-center mb-16" data-reveal data-reveal-direction="right" style="--reveal-delay:.08s;">
            @foreach($categoryLabels as $key => $label)
                @if(isset($packagesByCategory[$key]) && $packagesByCategory[$key]->isNotEmpty())
                    <button @click="tab='{{ $key }}'" type="button"
                        :class="tab === '{{ $key }}' ? 'border-gray-900 dark:border-white text-gray-900 dark:text-white' : 'border-transparent text-gray-400 dark:text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'"
                        class="pb-1 text-sm font-medium tracking-widest uppercase transition-all border-b-2">
                        {{ $label }}
                    </button>
                @endif
            @endforeach
        </div>

        {{-- Detail Modal --}}
        <template x-teleport="body">
            <div x-show="detailModal" x-cloak
                 x-transition.opacity
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
                <div class="bg-white rounded-[28px] shadow-2xl max-w-3xl w-full overflow-hidden"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     @click.outside="closeDetailModal()">
                    <div class="flex items-center justify-between px-6 py-4 border-b">
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.3em] text-gray-400">Detail Paket</p>
                            <h3 class="font-semibold text-gray-900" x-text="detailPackage?.name"></h3>
                        </div>
                        <button class="text-gray-400 hover:text-gray-600" @click="closeDetailModal()"><i class="fas fa-times"></i></button>
                    </div>
                    <div class="p-6">
                        <div class="relative rounded-3xl overflow-hidden bg-gray-100 h-[360px] sm:h-[420px]">
                            <template x-for="(item, index) in detailMedia" :key="item.id">
                                <div x-show="detailIndex === index" x-transition.opacity.duration.500ms class="absolute inset-0">
                                    <template x-if="item.type === 'image'">
                                        <img :src="item.url" class="w-full h-full object-cover" alt="">
                                    </template>
                                    <template x-if="item.type === 'video' && item.embed">
                                        <iframe :src="item.embed" class="w-full h-full" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>
                                    </template>
                                    <template x-if="item.type === 'video' && !item.embed">
                                        <video :src="item.url" class="w-full h-full object-cover" autoplay muted loop playsinline controls></video>
                                    </template>
                                </div>
                            </template>
                            <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent pointer-events-none"></div>
                            <div class="absolute inset-y-0 left-3 flex items-center" x-show="detailMedia.length > 1">
                                <button type="button" class="w-10 h-10 rounded-full bg-white/80 hover:bg-white text-gray-700 shadow" @click="prevDetail()">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                            </div>
                            <div class="absolute inset-y-0 right-3 flex items-center" x-show="detailMedia.length > 1">
                                <button type="button" class="w-10 h-10 rounded-full bg-white/80 hover:bg-white text-gray-700 shadow" @click="nextDetail()">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-center gap-2 mt-4" x-show="detailMedia.length > 1">
                            <template x-for="(item, index) in detailMedia" :key="'dot-' + item.id">
                                <button type="button" class="w-2.5 h-2.5 rounded-full"
                                        :class="detailIndex === index ? 'bg-yellow-500' : 'bg-gray-300'"
                                        @click="detailIndex = index"></button>
                            </template>
                        </div>
                        <div class="flex justify-center gap-2 mt-4 flex-wrap" x-show="detailMedia.length > 1">
                            <template x-for="(item, index) in detailMedia" :key="'thumb-' + item.id">
                                <button type="button" class="w-14 h-14 rounded-xl border overflow-hidden transition"
                                        :class="detailIndex === index ? 'border-yellow-500 ring-2 ring-yellow-200' : 'border-gray-200 hover:border-yellow-300'"
                                        @click="detailIndex = index">
                                    <template x-if="item.type === 'image'">
                                        <img :src="item.url" class="w-full h-full object-cover" alt="">
                                    </template>
                                    <template x-if="item.type === 'video'">
                                        <div class="w-full h-full bg-gray-900/80 flex items-center justify-center">
                                            <i class="fas fa-play text-white text-xs"></i>
                                        </div>
                                    </template>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        @foreach($packagesByCategory as $category => $list)
            <div x-show="tab === '{{ $category }}'" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @php $cardDirections = ['left', 'up', 'right']; @endphp
                    @foreach($list as $package)
                        @php
                            $categoryPopularId = $popularPackageIdsByCategory[$category] ?? null;
                            $isPopular = $categoryPopularId === $package->id;
                            $detailMediaPayload = $package->mediaItems->map(function ($m) {
                                return [
                                    'id' => $m->id,
                                    'type' => $m->media_type,
                                    'url' => $m->url,
                                    'embed' => $m->embed_url,
                                ];
                            })->values()->toArray();
                        @endphp
                        <div class="bg-[#FAF9F6] dark:bg-[#111111] rounded-xl hover:shadow-xl dark:hover:shadow-black/30 transition-all overflow-hidden relative border border-gray-100/50 dark:border-white/10" data-reveal data-reveal-direction="{{ $cardDirections[$loop->index % count($cardDirections)] }}" style="--reveal-delay: {{ $loop->index * 0.08 }}s;">
                            @if($isPopular)
                                <div class="bg-gray-900 text-white text-center py-2 text-[10px] font-medium uppercase tracking-[0.2em]">
                                    Signature Choice
                                </div>
                            @endif
                            <div class="p-8 pb-10 flex flex-col h-full">
                                <div class="text-center mb-6 space-y-3">
                                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-semibold uppercase bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300">
                                        <i class="fas fa-map-marker-alt text-yellow-500"></i> {{ $package->category_label }}
                                    </div>
                                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold uppercase
                                        {{ $package->tier === 'silver' ? 'bg-gray-100 text-gray-600' : ($package->tier === 'gold' ? 'bg-yellow-100 text-yellow-700' : 'bg-purple-100 text-purple-700') }}">
                                        <i class="fas {{ $package->tier === 'gold' ? 'fa-crown' : ($package->tier === 'silver' ? 'fa-medal' : 'fa-gem') }}"></i> {{ ucfirst($package->tier ?? 'Premium') }}
                                    </div>
                                    @if($package->hasActivePromo())
                                        <span class="inline-flex items-center gap-2 px-4 py-1 rounded-full text-xs font-semibold uppercase bg-pink-50 text-pink-600">
                                            <i class="fas fa-bolt"></i> {{ $package->promo_label ?? 'Promo Spesial' }}
                                        </span>
                                    @endif
                                    <h3 class="font-playfair text-3xl text-gray-900 dark:text-white tracking-wide">{{ $package->name }}</h3>
                                    <div class="mt-4 space-y-1">
                                        @if($package->hasActivePromo())
                                            <div class="text-xs text-gray-400 line-through">{{ $package->formatted_price }}</div>
                                            <div class="text-3xl font-light text-gray-900 dark:text-white">{{ $package->formattedEffectivePrice }}</div>
                                        @else
                                            <div class="text-3xl font-light text-gray-900 dark:text-white">{{ $package->formatted_price }}</div>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">DP 30%: Rp {{ number_format($package->dp_amount, 0, ',', '.') }}</p>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 text-center">{{ $package->description }}</p>

                                @if($package->has_digital_invitation)
                                <div class="flex items-center justify-center gap-2 text-yellow-700 bg-yellow-50 border border-yellow-100 rounded-xl py-2 text-xs font-semibold mb-4 shadow-sm">
                                    <i class="fas fa-envelope-open-text"></i>
                                    Termasuk Undangan Digital
                                </div>
                                @endif

                                @php $sections = $package->feature_sections; @endphp
                                <div class="grid grid-cols-1 gap-4 mb-8 flex-1 items-start auto-rows-min">
                                    @forelse($sections as $section)
                                        <div class="rounded-2xl border border-gray-100 dark:border-white/10 bg-white/60 dark:bg-white/5 p-4 flex flex-col gap-2">
                                            @if($section['title'])
                                                <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-300 font-semibold border-b border-gray-100 dark:border-gray-700/50 pb-1">{{ $section['title'] }}</p>
                                            @endif
                                            <ul class="space-y-1.5 text-[12px] text-gray-700 dark:text-gray-300 leading-tight">
                                                @foreach($section['items'] as $item)
                                                    @if(str_starts_with(trim($item), '##'))
                                                        <li class="pt-2 first:pt-0">
                                                            <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide border-b border-gray-100 dark:border-gray-700/50 pb-1 mb-0.5">{{ ltrim(trim($item), '# ') }}</p>
                                                        </li>
                                                    @else
                                                        <li class="flex items-start gap-2">
                                                            <span class="w-5 h-5 rounded-full bg-yellow-100 dark:bg-yellow-900/40 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                                <i class="fas fa-check text-yellow-600 dark:text-yellow-500 text-[10px]"></i>
                                                            </span>
                                                            <span>{{ $item }}</span>
                                                        </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    @empty
                                        <div class="rounded-2xl border border-dashed border-gray-200 dark:border-white/10 p-4 text-center text-xs text-gray-400">
                                            Belum ada fitur yang ditambahkan.
                                        </div>
                                    @endforelse
                                </div>
                                <div class="space-y-3 mt-4">
                                    <a href="{{ route('booking.select-package') }}?package_id={{ $package->id }}"
                                       class="block w-full text-center py-3.5 rounded bg-gray-900 dark:bg-yellow-600 text-white font-medium text-[11px] tracking-[0.2em] uppercase hover:bg-black dark:hover:bg-yellow-700 transition-colors border border-gray-900 dark:border-yellow-600">
                                        Reservasi
                                    </a>
                                    <a href="{{ route('consultation.form') }}?package={{ $package->slug }}"
                                       class="block w-full text-center py-3 rounded-2xl font-semibold text-sm border border-gray-200 dark:border-white/10 text-gray-600 dark:text-gray-300 hover:text-yellow-600 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-all">
                                        Konsultasi Dulu
                                    </a>
                                    <div class="flex justify-center pt-2">
                                        <button type="button"
                                                class="w-9 h-9 rounded-full border border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-all flex items-center justify-center"
                                                @click="openDetailModal({{ Js::from([
                                                    'id' => $package->id,
                                                    'name' => $package->name,
                                                    'media' => $detailMediaPayload,
                                                ]) }})">
                                            <i class="fas fa-images text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>

{{-- THE PROCESS (Harmoni Pelayanan) --}}
<section class="bg-white dark:bg-[#0A0A0A] section-glow transition-colors duration-500 relative z-10" id="layanan">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-16 lg:py-24">
        {{-- Header: sticky, will scroll up synchronously with cards via JS --}}
        <div id="harmoni-header" class="text-center pb-8 lg:pb-12 sticky top-28 lg:top-32 z-30 pointer-events-none">
            <span class="text-gray-400 dark:text-gray-500 text-xs font-semibold uppercase tracking-[0.3em] mb-4 block">{{ $processSection['eyebrow'] ?? 'The Process' }}</span>
            <h2 class="font-playfair text-4xl lg:text-5xl font-light text-gray-900 dark:text-white mt-2">{{ $processSection['heading'] ?? 'Harmoni Pelayanan' }}</h2>
        </div>

        {{-- Stack Area React Mount Point --}}
        <div id="harmoni-pelayanan-root" data-items="{{ json_encode($processSection['items']) }}" class="w-full h-full relative z-20"></div>
    </div>
</section>

{{-- UNDANGAN DIGITAL --}}
<section class="relative z-30 bg-white dark:bg-[#0A0A0A]" id="undangan" data-reveal data-reveal-direction="up">
    <!-- Background bridge -->
    <div class="absolute inset-x-0 top-0 h-24 pointer-events-none">
        <div class="h-10 bg-white dark:bg-[#0A0A0A]"></div>
        <svg class="w-full h-14 text-[#111]" viewBox="0 0 1440 320" preserveAspectRatio="none">
            <path fill="currentColor" d="M0,256L48,213.3C96,171,192,85,288,74.7C384,64,480,128,576,165.3C672,203,768,213,864,213.3C960,213,1056,203,1152,176C1248,149,1344,107,1392,85.3L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>
    <div class="absolute inset-0 bg-[#111] top-[95px]"></div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 py-24 text-white">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            <div data-reveal data-reveal-direction="left">
                <h2 class="font-playfair text-4xl lg:text-5xl font-light mb-8 leading-tight flex items-center gap-4 flex-wrap">
                    Keintiman yang<br>Dibagikan
                    @if($maintenanceMode)
                        <span class="inline-flex items-center gap-2 px-3 py-1 bg-red-500/20 text-red-300 text-[10px] font-bold uppercase tracking-widest rounded-full border border-red-500/30">
                            <i class="fas fa-tools text-[8px] animate-pulse"></i> Maintenance
                        </span>
                    @endif
                </h2>
                <p class="text-white/60 font-light leading-relaxed mb-10 text-base">Sebuah mahakarya undangan digital dengan desain tipografi mewah, galeri estetis, RSVP terintegrasi, serta penunjuk lokasi akurat yang memuliakan tamu Anda.</p>
                <ul class="space-y-4 mb-10">
                    @foreach(['Template berkelas dari desainer kami', 'Manajemen tamu & kursi interaktif', 'Galeri potret romansa', 'Simfoni musik pengiring', 'Peta lokasi presisi tinggi', 'Eksklusif namun personalisasi mudah'] as $f)
                    <li class="flex items-center gap-4 text-sm font-light text-white/80"><span class="w-1.5 h-1.5 rounded-full bg-white/40"></span> {{ $f }}</li>
                    @endforeach
                </ul>
                <a href="{{ route('login', ['redirect' => route('user.dashboard')]) }}" class="border border-white/40 text-white font-medium px-8 py-3.5 rounded hover:bg-white hover:text-black transition-colors text-xs tracking-widest uppercase inline-block">
                    Eksplorasi Galeri
                </a>
            </div>

            <div x-data="{
                    activeIndex: 0,
                    total: {{ $templates->take(12)->count() }},
                    normalize(index) {
                        return ((index % this.total) + this.total) % this.total;
                    },
                    distance(index) {
                        let d = index - this.activeIndex;
                        if (d > this.total / 2) d -= this.total;
                        if (d < -this.total / 2) d += this.total;
                        return d;
                    },
                    isSide(index) {
                        return Math.abs(this.distance(index)) === 1;
                    },
                    focus(index) {
                        if (this.isSide(index)) this.activeIndex = index;
                    },
                    prev() {
                        this.activeIndex = this.normalize(this.activeIndex - 1);
                    },
                    next() {
                        this.activeIndex = this.normalize(this.activeIndex + 1);
                    },
                    cardStyle(index) {
                        const d = this.distance(index);
                        const abs = Math.abs(d);
                        if (abs > 1) {
                            const off = d < 0 ? -120 : 120;
                            return `transform: translate(-50%, -50%) translateX(${off}%) scale(0.74); opacity: 0; filter: blur(8px); z-index: 1; pointer-events:none;`;
                        }
                        const translate = d * 50;
                        const scale = d === 0 ? 1 : 0.84;
                        const blur = d === 0 ? 0 : 3.5;
                        const opacity = d === 0 ? 1 : 0.56;
                        const z = d === 0 ? 30 : 20;
                        return `transform: translate(-50%, -50%) translateX(${translate}%) scale(${scale}); opacity:${opacity}; filter: blur(${blur}px); z-index:${z};`;
                    }
                }" class="relative" data-reveal data-reveal-direction="right">
                <div class="flex items-center justify-between mb-4">
                </div>

                <div class="relative h-[500px] sm:h-[540px]">
                    <button type="button" @click="prev()" class="flex items-center justify-center absolute left-2 sm:left-0 top-1/2 -translate-y-1/2 sm:-translate-x-6 w-12 h-12 rounded-full bg-white/20 backdrop-blur-lg border border-white/30 text-white shadow-2xl hover:bg-white/30 hover:scale-110 active:scale-95 transition-all duration-300 z-40" aria-label="Sebelumnya">
                        <i class="fas fa-chevron-left text-sm"></i>
                    </button>
                    <button type="button" @click="next()" class="flex items-center justify-center absolute right-2 sm:right-0 top-1/2 -translate-y-1/2 sm:translate-x-6 w-12 h-12 rounded-full bg-white/20 backdrop-blur-lg border border-white/30 text-white shadow-2xl hover:bg-white/30 hover:scale-110 active:scale-95 transition-all duration-300 z-40" aria-label="Berikutnya">
                        <i class="fas fa-chevron-right text-sm"></i>
                    </button>

                    <div class="absolute inset-0">
                        @foreach($templates->take(12) as $tpl)
                        <article @click="focus({{ $loop->index }})"
                                class="group absolute left-1/2 top-1/2 w-[62vw] sm:w-[52vw] lg:w-[40vw] max-w-[315px] rounded-3xl border border-white/15 bg-white/5 backdrop-blur-sm overflow-hidden shadow-lg transition-all duration-500 ease-out transform-gpu"
                                :class="isSide({{ $loop->index }}) ? 'cursor-pointer' : 'cursor-default'"
                                :style="cardStyle({{ $loop->index }})">
                            <div class="aspect-[3/4] rounded-2xl overflow-hidden relative group-hover:shadow-lg transition-all border border-gray-100 dark:border-white/5">
                                @php
                                    $mainImage = $tpl->preview_image ? asset('storage/'.$tpl->preview_image) : asset('storage/'.$tpl->thumbnail);
                                @endphp
                                <img src="{{ $mainImage }}" loading="lazy" decoding="async" alt="Preview {{ $tpl->name }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center p-4 backdrop-blur-[2px]">
                                    <img src="{{ asset('storage/'.$tpl->thumbnail) }}" loading="lazy" decoding="async" alt="Preview {{ $tpl->name }}" class="w-full h-full object-cover">
                                </div>
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
                                <div class="absolute bottom-3 left-3 px-3 py-1 rounded-full text-[10px] uppercase tracking-[0.2em] font-semibold text-white/90 bg-white/15 backdrop-blur" style="border-color: {{ $tpl->primary_color ?? '#fff' }};">
                                    {{ strtoupper($tpl->theme ?? 'Elegan') }}
                                </div>
                            </div>
                            <div class="p-5 space-y-3 bg-[#0f0f0f]/90 flex flex-col">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-white/60 text-[11px] uppercase tracking-[0.25em]">{{ ucfirst($tpl->theme ?? 'Elegan') }}</p>
                                        <h4 class="text-white text-lg font-semibold mt-1">{{ $tpl->name }}</h4>
                                    </div>
                                    <span class="px-3 py-1 rounded-full text-[10px] font-semibold {{ $tpl->is_premium ? 'bg-purple-500/20 text-purple-200' : 'bg-green-500/15 text-green-200' }}">
                                        {{ $tpl->is_premium ? 'Premium' : 'Standar' }}
                                    </span>
                                </div>
                                <div>
                                    @if($tpl->has_active_promo)
                                        <p class="text-xs text-white/40 line-through">{{ $tpl->formatted_price }}</p>
                                        <p class="text-xl font-bold text-yellow-300">{{ $tpl->formatted_effective_price }}</p>
                                    @else
                                        <p class="text-xl font-bold text-white">{{ $tpl->formatted_price }}</p>
                                    @endif
                                </div>
                                <div class="space-y-1.5 text-xs text-white/70 flex-1">
                                    <p><i class="fas fa-palette text-yellow-300/80 mr-2"></i>{{ $tpl->primary_color ?? '#D4AF37' }}</p>
                                    <p><i class="fas fa-font text-yellow-300/80 mr-2"></i>{{ $tpl->font_family ?? 'Playfair Display' }}</p>
                                </div>
                                <div class="mt-2 grid grid-cols-2 gap-3">
                                    @php $demoUrl = $tpl->demo_url; @endphp
                                    <a @click.stop href="{{ $demoUrl ?? '#' }}" target="{{ $demoUrl ? '_blank' : '_self' }}"
                                       class="text-xs font-semibold px-3 py-2 rounded-full border {{ $demoUrl ? 'border-white/30 text-white hover:border-yellow-300 hover:text-yellow-200' : 'border-dashed border-white/20 text-white/40 cursor-not-allowed' }} transition-colors flex items-center justify-center gap-2"
                                       {{ $demoUrl ? '' : 'aria-disabled=true' }}>
                                        <i class="fas fa-eye"></i> Preview
                                    </a>
                                    @if($maintenanceMode)
                                    <button disabled
                                       class="text-xs font-semibold px-3 py-2 rounded-full bg-white/10 text-white/40 cursor-not-allowed flex items-center justify-center gap-2">
                                        <i class="fas fa-tools"></i> Maintenance
                                    </button>
                                    @else
                                    <a @click.stop href="{{ route('invitation-order.start') }}?template_slug={{ $tpl->slug }}"
                                       class="text-xs font-semibold px-3 py-2 rounded-full gold-gradient text-white shadow hover:shadow-lg transition-all flex items-center justify-center gap-2">
                                        <i class="fas fa-calendar-check"></i> Booking
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- BLOG / ARTICLES 3D SLIDER --}}
@if(isset($posts) && $posts->count() > 0)
<section class="py-24 bg-white dark:bg-[#0A0A0A] overflow-hidden transition-colors duration-500" data-reveal data-reveal-direction="up">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-end mb-10 gap-6">
            <div data-reveal data-reveal-direction="left">
                <span class="text-gray-400 dark:text-gray-500 text-xs font-semibold uppercase tracking-[0.3em] mb-3 block">Wedding Tips & Inspiration</span>
                <h2 class="font-playfair text-4xl lg:text-5xl font-light text-gray-900 dark:text-white leading-tight">Wawasan & Inspirasi</h2>
            </div>
            <a href="{{ route('blog.index') }}" class="text-xs font-bold text-gray-900 dark:text-white uppercase tracking-widest hover:text-yellow-600 dark:hover:text-yellow-400 transition-all flex items-center gap-2" data-reveal data-reveal-direction="right">
                Lihat Semua Artikel <i class="fas fa-arrow-right-long text-yellow-600 dark:hover:text-yellow-400"></i>
            </a>
        </div>

        <div x-data="{
                activeIndex: 0,
                total: {{ $posts->count() }},
                normalize(index) {
                    return ((index % this.total) + this.total) % this.total;
                },
                distance(index) {
                    let d = index - this.activeIndex;
                    if (d > this.total / 2) d -= this.total;
                    if (d < -this.total / 2) d += this.total;
                    return d;
                },
                isSide(index) {
                    return Math.abs(this.distance(index)) === 1;
                },
                focus(index) {
                    if (this.isSide(index)) this.activeIndex = index;
                },
                prev() {
                    this.activeIndex = this.normalize(this.activeIndex - 1);
                },
                next() {
                    this.activeIndex = this.normalize(this.activeIndex + 1);
                },
                cardStyle(index) {
                    const d = this.distance(index);
                    const abs = Math.abs(d);
                    if (abs > 1) {
                        const off = d < 0 ? -120 : 120;
                        return `transform: translate(-50%, -50%) translateX(${off}%) scale(0.74); opacity: 0; filter: blur(8px); z-index: 1; pointer-events:none;`;
                    }
                    const translate = d * 50;
                    const scale = d === 0 ? 1 : 0.84;
                    const blur = d === 0 ? 0 : 3.5;
                    const opacity = d === 0 ? 1 : 0.56;
                    const z = d === 0 ? 30 : 20;
                    return `transform: translate(-50%, -50%) translateX(${translate}%) scale(${scale}); opacity:${opacity}; filter: blur(${blur}px); z-index:${z};`;
                }
            }" class="relative select-none">
            
            <div class="relative h-[500px]">
                {{-- Navigation Buttons --}}
                <button type="button" @click="prev()" class="flex items-center justify-center absolute left-2 sm:left-0 top-1/2 -translate-y-1/2 sm:-translate-x-6 w-12 h-12 rounded-full bg-white/40 backdrop-blur-md border border-white/60 text-gray-900 shadow-xl hover:bg-white/60 hover:scale-110 active:scale-95 transition-all duration-300 z-40" aria-label="Sebelumnya">
                    <i class="fas fa-chevron-left text-sm"></i>
                </button>
                <button type="button" @click="next()" class="flex items-center justify-center absolute right-2 sm:right-0 top-1/2 -translate-y-1/2 sm:translate-x-6 w-12 h-12 rounded-full bg-white/40 backdrop-blur-md border border-white/60 text-gray-900 shadow-xl hover:bg-white/60 hover:scale-110 active:scale-95 transition-all duration-300 z-40" aria-label="Berikutnya">
                    <i class="fas fa-chevron-right text-sm"></i>
                </button>

                {{-- Carousel Items --}}
                <div class="absolute inset-0">
                    @foreach($posts as $post)
                    <article @click="focus({{ $loop->index }})"
                            class="group absolute left-1/2 top-1/2 w-[75vw] sm:w-[55vw] lg:w-[45vw] max-w-[340px] rounded-3xl border border-gray-100 dark:border-white/10 bg-white dark:bg-[#111111] shadow-[0_32px_64px_-16px_rgba(0,0,0,0.1)] dark:shadow-[0_32px_64px_-16px_rgba(0,0,0,0.4)] transition-all duration-500 ease-out transform-gpu overflow-hidden"
                            :class="isSide({{ $loop->index }}) ? 'cursor-pointer' : 'cursor-default'"
                            :style="cardStyle({{ $loop->index }})">
                        
                        {{-- Image Header --}}
                        <div class="aspect-[4/3] w-full overflow-hidden relative">
                            <img src="{{ asset('storage/'.$post->thumbnail) }}" loading="lazy" decoding="async" alt="{{ $post->title }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/40 to-transparent"></div>
                            <div class="absolute bottom-4 left-4 flex gap-2">
                                <span class="px-3 py-1 bg-white/90 backdrop-blur rounded-full text-[9px] font-bold text-yellow-700 uppercase tracking-widest shadow-sm">
                                    {{ $post->category ?? 'Wawasan' }}
                                </span>
                            </div>
                        </div>

                        {{-- Details --}}
                        <div class="p-6 space-y-4">
                            <div>
                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-[0.2em] mb-2 flex items-center gap-2">
                                    <i class="far fa-calendar text-[8px]"></i> {{ $post->published_at ? $post->published_at->format('d M Y') : $post->created_at->format('d M Y') }}
                                </div>
                                <h4 class="text-gray-900 dark:text-white text-lg font-playfair font-bold leading-snug line-clamp-2 group-hover:text-yellow-600 dark:group-hover:text-yellow-400 transition-colors">
                                    <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                                </h4>
                            </div>

                            <p class="text-gray-500 text-xs leading-relaxed line-clamp-2 font-light">
                                {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 80) }}
                            </p>

                            <div class="pt-4 border-t border-white/5">
                                <a href="{{ route('blog.show', $post->slug) }}" class="w-full text-[10px] font-bold py-3 rounded-full bg-yellow-500 text-black shadow-lg shadow-yellow-500/20 hover:bg-yellow-400 transition-all flex items-center justify-center gap-2 uppercase tracking-widest">
                                    Baca Selengkapnya <i class="fas fa-arrow-right-long text-[9px]"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endif

{{-- TESTIMONI --}}
@if($reviews->count() > 0)
<section class="py-24 bg-[#FAF9F6] dark:bg-[#0A0A0A] section-glow transition-colors duration-500" data-reveal data-reveal-direction="up">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16" data-reveal data-reveal-direction="down">
            <span class="text-gray-400 dark:text-gray-500 text-xs font-semibold uppercase tracking-[0.3em] mb-4 block">Testimoni</span>
            <h2 class="font-playfair text-4xl lg:text-5xl font-light text-gray-900 dark:text-white leading-tight">Jejak Kebahagiaan</h2>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @php $testimonialDirections = ['left','right','up']; @endphp
            @foreach($reviews as $review)
            <div class="bg-white dark:bg-[#111111] border border-gray-100 dark:border-white/10 p-8 hover:border-gray-300 dark:hover:border-white/20 transition-colors section-glow" data-reveal data-reveal-direction="{{ $testimonialDirections[$loop->index % count($testimonialDirections)] }}" style="--reveal-delay: {{ $loop->index * 0.08 }}s;">
                <div class="flex items-center gap-1 mb-5">
                    @for($i = 1; $i <= 5; $i++)<i class="fas fa-star text-[10px] {{ $i <= $review->rating ? 'text-gray-800 dark:text-yellow-400' : 'text-gray-200 dark:text-gray-600' }}"></i>@endfor
                </div>
                @if($review->title)<h4 class="font-playfair text-lg text-gray-900 dark:text-white mb-3">{{ $review->title }}</h4>@endif
                <p class="text-sm text-gray-500 dark:text-gray-400 font-light leading-relaxed mb-6">"{{ Str::limit($review->review, 150) }}"</p>
                <div class="flex items-center gap-3 pt-5 border-t border-gray-100 dark:border-white/10">
                    <div class="w-8 h-8 rounded-full border border-gray-200 dark:border-white/15 flex items-center justify-center text-gray-600 dark:text-gray-300 font-medium text-[10px]">
                        {{ strtoupper(substr($review->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-xs uppercase tracking-widest font-medium text-gray-900 dark:text-white">{{ $review->user->name }}</div>
                        @if($review->booking)<div class="text-[10px] uppercase tracking-widest text-gray-400 dark:text-gray-500 mt-0.5">{{ $review->booking->groom_name }} & {{ $review->booking->bride_name }}</div>@endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
<section class="py-28 bg-[#111111] relative overflow-hidden" data-reveal data-reveal-direction="up">
    <!-- Dekorasi background tipis -->
    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-10 mix-blend-overlay pointer-events-none"></div>
    <div class="max-w-3xl mx-auto px-4 text-center relative z-10">
        <h2 class="font-playfair text-4xl lg:text-5xl font-light text-white mb-6" data-reveal data-reveal-direction="down">Sebuah Awal Keabadian</h2>
        <p class="text-white/60 text-base font-light mb-12" data-reveal data-reveal-direction="right" style="--reveal-delay:.08s;">Kami di sini siap mendengarkan rencana besar Anda. Temukan simfoni sempurna untuk hari bahagia tanpa hambatan.</p>
        <div class="flex flex-col sm:flex-row gap-5 justify-center items-center" data-reveal data-reveal-direction="up" style="--reveal-delay:.16s;">
            <a href="{{ route('booking.select-package') }}" class="bg-white text-black font-medium px-8 py-3.5 rounded hover:bg-gray-100 transition-colors text-sm tracking-widest uppercase w-full sm:w-auto text-center">
                Mulai Perjalanan
            </a>
            <a href="{{ route('consultation.form') }}" class="bg-transparent border border-white/40 text-white font-medium px-8 py-3.5 rounded hover:bg-white/10 hover:border-white transition-colors text-sm tracking-widest uppercase w-full sm:w-auto text-center">
                Sapa Perencana Kami
            </a>
        </div>
    </div>
</section>

@push('scripts')
<script>
function dateChecker() {
    return {
        date: '',
        loading: false,
        result: null,
        minDate: '{{ now()->addDay()->toDateString() }}',
        checkDate() {
            if (!this.date) return;
            this.loading = true;
            this.result = null;
            fetch(`/booking/check-date?date=${this.date}`)
                .then(r => r.json())
                .then(data => { this.result = data; this.loading = false; })
                .catch(() => { this.loading = false; });
        }
    };
}

document.addEventListener('DOMContentLoaded', () => {
    const revealEls = document.querySelectorAll('[data-reveal]');
    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                obs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.15 });

    revealEls.forEach(el => observer.observe(el));

    initCountupElements();

    // GSAP 3D Scroll Animations (hero parallax only to avoid hiding content)
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);

        // Advanced 3D Parallax for Hero Elements
        const heroBg = document.querySelector('.hero-minimalist > div:first-child');
        const homeSection = document.querySelector('#home') || document.querySelector('.hero-minimalist');
        if (heroBg && homeSection) {
            gsap.to(heroBg, {
                yPercent: 20,
                scale: 1.05,
                opacity: 0.8,
                ease: 'none',
                scrollTrigger: {
                    trigger: homeSection,
                    start: 'top top',
                    end: 'bottom top',
                    scrub: true
                }
            });
        }

        // Stacking Highlight Cards Animation (Responsive with MatchMedia)
        const experienceRow = document.getElementById('highlight-experience-row');
        const highlightStackPinMobile = document.getElementById('highlight-stack-pin');
        const highlightCards = document.querySelectorAll('.highlight-card');
        
        if (highlightCards.length > 1) {
            const totalCards = highlightCards.length;
            let mm = gsap.matchMedia();

            // Function to generate the card stacking animations inside a timeline
            // TRUE STICKER PEEL: 3D fold from top-right corner
            const createStackingAnimation = (tl) => {
                highlightCards.forEach((card, i) => {
                    if (i === 0) return; 
                    
                    // Peel the top card (i-1) from top-right corner using 3D rotation
                    tl.to(highlightCards[i - 1], {
                        rotateY: -120,  // Fold the card backwards like a page turn
                        rotateZ: -8,    // Slight diagonal tilt for realism
                        opacity: 0,     // Fade as it peels away
                        ease: "power3.inOut", 
                        duration: 1.2
                    }, (i - 1) * 1.2);

                    // The card underneath brightens as it gets revealed
                    tl.fromTo(card, {
                        filter: "brightness(0.5)"
                    }, {
                        filter: "brightness(1)", 
                        ease: "power2.out", 
                        duration: 0.8
                    }, (i - 1) * 1.2 + 0.4);
                });
            };

            // 3D PAPER TEAR: Initial reveal transition (Desktop & Mobile)
            const paperRip = document.getElementById('paper-rip-layer');
            const paperRipBackShadow = document.getElementById('paper-rip-back-shadow');
            if (paperRip) {
                gsap.to(paperRip, {
                    scrollTrigger: {
                        trigger: "#dream-section",
                        start: "top bottom", 
                        end: "top 10%",     
                        scrub: 1, // Tighter scrub for more 'felt' rip
                    },
                    yPercent: -180,   
                    rotateX: -80,     
                    rotateZ: -10,      
                    skewY: -15,       
                    scale: 1.25,      
                    opacity: 0,       
                    transformOrigin: "top center",
                    ease: "power2.in"
                });
                
                if (paperRipBackShadow) {
                    gsap.to(paperRipBackShadow, {
                        scrollTrigger: {
                            trigger: "#dream-section",
                            start: "top bottom",
                            end: "top 10%",
                            scrub: 1.2
                        },
                        opacity: 1
                    });
                }
            }

            // Desktop Strategy: Pin the experience row (features + cards)
            mm.add("(min-width: 1024px)", () => {
                if (experienceRow) {
                    const tl = gsap.timeline({
                        scrollTrigger: {
                            trigger: experienceRow,
                            start: "top 110px", // Slightly lower than before
                            end: () => `+=${totalCards * 550}`, 
                            pin: true, 
                            pinSpacing: true,
                            scrub: 1, 
                            anticipatePin: 1,
                            invalidateOnRefresh: true
                        }
                    });
                    createStackingAnimation(tl);
                }
            });

            // Mobile Strategy: Pin ONLY the cards container perfectly balanced but slightly lower
            mm.add("(max-width: 1023px)", () => {
                // 1. WhatsApp Float Reveal Logic (Mobile Only)
                const waButton = document.getElementById('floating-whatsapp-container');
                if (waButton) {
                    gsap.set(waButton, { autoAlpha: 0 });
                    ScrollTrigger.create({
                        trigger: "#dream-section",
                        start: "bottom center",
                        onEnter: () => gsap.to(waButton, { autoAlpha: 1, duration: 0.5 }),
                        onLeaveBack: () => gsap.to(waButton, { autoAlpha: 0, duration: 0.5 }),
                    });
                }

                // 2. Stacking Cards Logic
                if (highlightStackPinMobile) {
                    const tl = gsap.timeline({
                        scrollTrigger: {
                            trigger: highlightStackPinMobile,
                            start: "center 55%", // Pushed slightly below center
                            end: () => `+=${totalCards * 350}`, // Tighter scroll for mobile
                            pin: true, 
                            pinSpacing: true,
                            scrub: 0.8, 
                            anticipatePin: 1,
                            invalidateOnRefresh: true
                        }
                    });
                    createStackingAnimation(tl);
                }
            });
        }
    }
});

function heroSlider(config = {}) {
    const fallbackSlides = [
        { id: 'fallback-1', type: 'video', src: 'https://cdn.pixabay.com/video/2022/12/08/142343-781582013_large.mp4' },
        { id: 'fallback-2', type: 'image', src: 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?w=1600' },
        { id: 'fallback-3', type: 'image', src: 'https://images.unsplash.com/photo-1508672019048-805c876b67e2?w=1600' },
    ];

    const providedSlides = Array.isArray(config.slides) && config.slides.length ? config.slides : fallbackSlides;
    const normalizedSlides = providedSlides.map((slide, idx) => ({
        id: slide.id ?? `slide-${idx + 1}`,
        type: slide.type ?? 'image',
        src: slide.src,
        title: slide.title ?? null,
        subtitle: slide.subtitle ?? null,
        cta_label: slide.cta_label ?? null,
        cta_url: slide.cta_url ?? null,
    })).filter(slide => !!slide.src);

    return {
        slides: normalizedSlides.length ? normalizedSlides : fallbackSlides,
        current: 0,
        interval: null,
        showScrollIndicator: true,
        init() {
            if (this.slides.length > 1) {
                this.start();
            }
            window.addEventListener('scroll', () => {
                this.showScrollIndicator = window.scrollY < 100;
            });
        },
        start() {
            if (this.interval || this.slides.length <= 1) return;
            this.interval = setInterval(() => {
                this.next();
            }, 6000);
        },
        next() {
            this.current = (this.current + 1) % this.slides.length;
        },
        goTo(index) {
            if (index < 0 || index >= this.slides.length) return;
            this.current = index;
            this.restart();
        },
        restart() {
            if (this.interval) {
                clearInterval(this.interval);
                this.interval = null;
            }
            this.start();
        },
        activeSlide() {
            return this.slides[this.current] ?? null;
        },
        scrollToDream() {
            const target = document.querySelector('#dream-section');
            if (target) target.scrollIntoView({ behavior: 'smooth' });
        }
    };
}

function heroHeadlineTypewriter(config = {}) {
    const defaults = {
        lines: [
            { text: 'Wujudkan Pernikahan', classes: '' },
            { text: 'Impian Anda', classes: 'italic text-white/90' }
        ],
        speed: 90,
        lineDelay: 300,
        restartDelay: 5000,
        loop: true,
    };

    const merged = { ...defaults, ...config };
    const normalizedLines = Array.isArray(merged.lines) && merged.lines.length ? merged.lines : defaults.lines;

    return {
        lines: normalizedLines,
        speed: merged.speed,
        lineDelay: merged.lineDelay,
        restartDelay: merged.restartDelay,
        loop: merged.loop,
        displayLines: normalizedLines.map(() => ''),
        caretLine: 0,
        started: false,
        _timeouts: [],
        start() {
            if (this.started || !this.lines.length) return;
            this.started = true;
            this.displayLines = this.lines.map(() => '');
            this.caretLine = 0;
            this._clearFallback();
            this._clearTimers();
            this._typeLine(0);
        },
        caretShouldShow(idx) {
            return this.started && this.caretLine === idx;
        },
        _typeLine(lineIndex) {
            if (lineIndex >= this.lines.length) {
                this._handleCompletion();
                return;
            }

            const targetText = this.lines[lineIndex]?.text ?? '';
            let cursor = 0;
            const tick = () => {
                if (cursor < targetText.length) {
                    this.displayLines[lineIndex] = targetText.slice(0, cursor + 1);
                    this.caretLine = lineIndex;
                    cursor += 1;
                    this._schedule(tick, this.speed);
                } else {
                    this.displayLines[lineIndex] = targetText;
                    this._schedule(() => this._typeLine(lineIndex + 1), this.lineDelay);
                }
            };

            tick();
        },
        _handleCompletion() {
            if (this.loop) {
                this._schedule(() => {
                    this.displayLines = this.lines.map(() => '');
                    this.caretLine = 0;
                    this._typeLine(0);
                }, this.restartDelay);
            } else {
                this.caretLine = -1;
            }
        },
        _schedule(callback, delay) {
            const id = setTimeout(callback, delay);
            this._timeouts.push(id);
            return id;
        },
        _clearTimers() {
            this._timeouts.forEach(clearTimeout);
            this._timeouts = [];
        },
        _clearFallback() {
            if (this.$el && this.$el.previousElementSibling?.classList.contains('hero-headline-fallback')) {
                this.$el.previousElementSibling.style.display = 'none';
            }
        }
    };
}

function initCountupElements() {
    const counters = document.querySelectorAll('[data-countup]');
    if (!counters.length) {
        return;
    }

    const animate = (el) => {
        const target = parseFloat(el.dataset.target || '0');
        const duration = parseInt(el.dataset.duration || '1200', 10);
        const suffix = el.dataset.suffix || '';
        const decimals = parseInt(el.dataset.decimals || '0', 10);
        let start = null;

        const step = (timestamp) => {
            if (!start) start = timestamp;
            const progress = Math.min((timestamp - start) / duration, 1);
            const value = target * progress;
            el.textContent = (decimals > 0 ? value.toFixed(decimals) : Math.round(value).toString()) + suffix;
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                el.textContent = (decimals > 0 ? target.toFixed(decimals) : Math.round(target).toString()) + suffix;
            }
        };

        requestAnimationFrame(step);
    };

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animate(entry.target);
                obs.unobserve(entry.target);
            }
        });
    }, { threshold: 0.6 });

    counters.forEach(counter => observer.observe(counter));
}
</script>
@endpush
@endsection
