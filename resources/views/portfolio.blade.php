@extends('layouts.guest')
@section('title', 'Portofolio – Anggita WO')

@section('content')
<div class="min-h-screen bg-white dark:bg-[#0A0A0A] pt-32 pb-24 transition-colors duration-500">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20" data-reveal>
            <span class="text-gray-400 dark:text-gray-500 text-xs font-semibold uppercase tracking-[0.3em] mb-4 block">Our Legacy</span>
            <h1 class="font-playfair text-5xl lg:text-7xl font-light text-gray-900 dark:text-white leading-tight">Portofolio</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-6 max-w-xl mx-auto font-light leading-relaxed">Setiap pernikahan adalah mahakarya yang unik. Kami merangkai setiap detail menjadi simfoni momen yang tak terlupakan.</p>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-24 border-y border-gray-100 dark:border-white/10 py-16">
            @foreach($portfolioStats as $stat)
            <div class="text-center px-4" data-reveal style="--reveal-delay: {{ $loop->index * 0.1 }}s;">
                <p class="text-4xl lg:text-5xl font-playfair text-gray-900 dark:text-white tracking-tight">
                    <span data-countup
                          data-target="{{ $stat['value'] }}"
                          data-suffix="{{ $stat['suffix'] ?? '' }}"
                          data-decimals="{{ $stat['decimals'] ?? 0 }}">
                          0
                    </span>
                </p>
                <p class="text-gray-400 dark:text-gray-500 text-[10px] uppercase tracking-[0.2em] font-medium mt-3">{{ $stat['label'] }}</p>
            </div>
            @endforeach
        </div>

        @if(isset($portfolioImages) && $portfolioImages->count() > 0)
        <div class="mb-32">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($portfolioImages as $img)
                    @php
                        $coverPath = $img->image_path;
                        $coverUrl = $coverPath
                            ? (\Illuminate\Support\Str::startsWith($coverPath, ['http://', 'https://', '/storage/'])
                                ? $coverPath
                                : Storage::url($coverPath))
                            : null;
                        $mediaPayload = collect([
                            [
                                'id' => 'cover-'.$img->id,
                                'type' => 'image',
                                'url' => $coverUrl,
                                'embed' => null,
                            ],
                        ])->merge(
                            $img->mediaItems->map(function ($media) {
                                return [
                                    'id' => $media->id,
                                    'type' => $media->media_type,
                                    'url' => $media->url,
                                    'embed' => $media->embed_url,
                                ];
                            })
                        );
                    
                        $aspectRatio = match($img->aspect) {
                            'portrait' => '3 / 4',
                            'landscape' => '4 / 3',
                            'wide' => '16 / 9',
                            'tall' => '3 / 4',
                            default => '1 / 1',
                        };

                        $spanClass = match($img->aspect) {
                            'wide' => 'md:col-span-2',
                            'tall' => 'md:row-span-2',
                            default => '',
                        };
                    @endphp
                    <div class="portfolio-card group relative overflow-hidden rounded-2xl bg-gray-100 dark:bg-gray-800 transition-all {{ $spanClass }}"
                         style="aspect-ratio: {{ $aspectRatio }}; --reveal-delay: {{ $loop->index * 0.05 }}s;"
                         x-data="portfolioSlider(@js($mediaPayload))"
                         x-init="init()"
                         @mouseenter="revealUi()"
                         @mouseleave="hideUi()"
                         @touchstart.passive="revealUi()"
                         @focusin="revealUi()"
                         @focusout="hideUi()"
                         data-reveal
                         tabindex="0">
                        <div class="absolute inset-0">
                            <template x-for="(item, index) in media" :key="item.id">
                                <div x-show="current == index" class="absolute inset-0 transition-opacity duration-500" :class="current == index ? 'opacity-100' : 'opacity-0 pointer-events-none'">
                                    <template x-if="item.type === 'image'">
                                        <img :src="item.url" :alt="item.title ?? 'Galeri'" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="item.type === 'video' && item.embed">
                                        <iframe :src="item.embed" class="w-full h-full" frameborder="0" allow="autoplay; fullscreen" allowfullscreen loading="lazy"></iframe>
                                    </template>
                                    <template x-if="item.type === 'video' && !item.embed">
                                        <video :src="item.url" class="w-full h-full object-cover" autoplay muted loop playsinline controls></video>
                                    </template>
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/0 to-black/0"></div>
                                </div>
                            </template>
                        </div>

                        <div x-show="media.length > 1 && showUi" x-transition.opacity x-cloak class="absolute inset-0 pointer-events-none">
                            <button type="button" @click="prev()" class="absolute left-3 top-1/2 -translate-y-1/2 text-white hover:text-yellow-200 transition focus-visible:outline-none pointer-events-auto flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M15 5l-7 7 7 7" />
                                </svg>
                            </button>
                            <button type="button" @click="next()" class="absolute right-3 top-1/2 -translate-y-1/2 text-white hover:text-yellow-200 transition focus-visible:outline-none pointer-events-auto flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            <div class="absolute bottom-3 left-0 right-0 flex justify-center gap-1">
                                <template x-for="(item, index) in media" :key="'dot-'+item.id">
                                    <button type="button" @click="goTo(index)" class="w-2.5 h-2.5 rounded-full border border-white/70"
                                            :class="current == index ? 'bg-white' : 'bg-transparent'"></button>
                                </template>
                            </div>
                        </div>

                        @if($img->title || $img->caption)
                        <div class="absolute left-0 right-0 bottom-0 p-4 text-white" x-show="showUi" x-transition.opacity x-cloak>
                            @if($img->title)
                                <div class="font-semibold">{{ $img->title }}</div>
                            @endif
                            @if($img->caption)
                                <div class="text-xs text-white/80 mt-1">{{ $img->caption }}</div>
                            @endif
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Testimonials --}}
        @if($reviews->count() > 0)
        <div class="mb-32">
            <div class="text-center mb-16" data-reveal>
                <span class="text-gray-400 dark:text-gray-500 text-xs font-semibold uppercase tracking-[0.3em] mb-4 block">Testimonials</span>
                <h2 class="font-playfair text-4xl lg:text-5xl font-light text-gray-900 dark:text-white mt-2">Kata Klien Kami</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($reviews as $review)
                <div class="bg-white dark:bg-gray-800/10 border border-gray-100 dark:border-white/5 p-10 hover:border-gray-300 dark:hover:border-white/20 transition-colors" data-reveal style="--reveal-delay: {{ $loop->index * 0.1 }}s;">
                    <div class="flex gap-1 mb-6">
                        @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star text-[10px] {{ $i <= $review->rating ? 'text-gray-800 dark:text-yellow-500' : 'text-gray-200 dark:text-gray-700' }}"></i>
                        @endfor
                    </div>
                    @if($review->title)
                    <h4 class="font-playfair text-lg text-gray-900 dark:text-white mb-4">{{ $review->title }}</h4>
                    @endif
                    <p class="text-sm text-gray-500 dark:text-gray-400 font-light leading-relaxed mb-8 italic">"{{ Str::limit($review->review, 200) }}"</p>
                    <div class="flex items-center gap-4 pt-6 border-t border-gray-100 dark:border-white/10">
                        <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-600 dark:text-gray-400 font-medium text-xs">
                            {{ strtoupper(substr($review->user->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-widest font-medium text-gray-900 dark:text-white">{{ $review->user->name }}</p>
                            @if($review->booking)
                            <p class="text-[10px] uppercase tracking-widest text-gray-400 dark:text-gray-500 mt-1">
                                {{ $review->booking->package->name }} • {{ $review->booking->event_date->isoFormat('MMMM Y') }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- CTA --}}
        <div class="text-center py-20 bg-gray-900 rounded-3xl relative overflow-hidden px-6" data-reveal>
             <!-- Dekorasi background tipis -->
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-10 mix-blend-overlay pointer-events-none"></div>
            <div class="relative z-10">
                <h2 class="font-playfair text-4xl lg:text-5xl font-light text-white mb-6">Wujudkan Impian Anda</h2>
                <p class="text-white/60 mb-12 font-light max-w-xl mx-auto">Bergabung dengan ratusan pasangan yang telah mengabadikan cinta mereka bersama kami.</p>
                <div class="flex flex-col sm:flex-row gap-5 justify-center">
                    <a href="{{ route('booking.select-package') }}" class="bg-white text-black font-medium px-10 py-4 rounded hover:bg-gray-100 transition-colors text-xs tracking-[0.2em] uppercase">
                        Mulai Perjalanan
                    </a>
                    <a href="{{ route('consultation.form') }}" class="bg-transparent border border-white/40 text-white font-medium px-10 py-4 rounded hover:bg-white/10 hover:border-white transition-colors text-xs tracking-[0.2em] uppercase">
                        Konsultasi Gratis
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function portfolioSlider(mediaItems) {
    return {
        media: mediaItems,
        current: 0,
        timer: null,
        showUi: false,
        uiTimer: null,
        init() {
            this.resume();
        },
        next() {
            this.current = (this.current + 1) % this.media.length;
            this.restart();
            this.revealUi();
        },
        prev() {
            this.current = (this.current - 1 + this.media.length) % this.media.length;
            this.restart();
            this.revealUi();
        },
        goTo(index) {
            this.current = index;
            this.restart();
            this.revealUi();
        },
        restart() {
            this.pause();
            this.resume();
        },
        pause() {
            if (this.timer) {
                clearInterval(this.timer);
                this.timer = null;
            }
        },
        resume() {
            if (this.media.length <= 1 || this.timer) return;
            this.timer = setInterval(() => {
                this.current = (this.current + 1) % this.media.length;
            }, 6000);
        },
        revealUi() {
            this.showUi = true;
            this.pause();
            this.resume();
            if (this.uiTimer) {
                clearTimeout(this.uiTimer);
            }
            this.uiTimer = setTimeout(() => {
                this.showUi = false;
                this.uiTimer = null;
            }, 4000);
        },
        hideUi() {
            if (this.uiTimer) {
                clearTimeout(this.uiTimer);
                this.uiTimer = null;
            }
        }
    };
}

document.addEventListener('DOMContentLoaded', () => {
    const isMobile = window.matchMedia('(max-width: 768px)').matches;
    // Reveal Observer
    const revealEls = document.querySelectorAll('[data-reveal]');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
            }
        });
    }, { threshold: 0.1 });
    revealEls.forEach(el => observer.observe(el));

    // Stats Countup
    const countups = document.querySelectorAll('[data-countup]');
    const countupObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const el = entry.target;
                const target = parseFloat(el.getAttribute('data-target'));
                const suffix = el.getAttribute('data-suffix') || '';
                const decimals = parseInt(el.getAttribute('data-decimals') || '0');
                let start = 0;
                const duration = 2000;
                const startTime = performance.now();

                function update(currentTime) {
                    const elapsed = currentTime - startTime;
                    const progress = Math.min(elapsed / duration, 1);
                    const current = progress * target;
                    el.innerText = current.toFixed(decimals).toLocaleString() + suffix;
                    if (progress < 1) requestAnimationFrame(update);
                }
                requestAnimationFrame(update);
                countupObserver.unobserve(el);
            }
        });
    });
    countups.forEach(c => countupObserver.observe(c));

    // GSAP 3D Animations (disable on mobile to prevent image blur)
    if (window.gsap && window.ScrollTrigger && !isMobile) {
        gsap.registerPlugin(ScrollTrigger);

        // Portfolio Cards 3D Entrance
        gsap.utils.toArray('.portfolio-card').forEach((card, i) => {
            const direction = i % 2 === 0 ? -1 : 1;
            gsap.from(card, {
                scrollTrigger: {
                    trigger: card,
                    start: "top 90%",
                    toggleActions: "play none none none"
                },
                x: direction * 140,
                y: 50,
                opacity: 0,
                rotateX: -10,
                rotateY: direction * 14,
                scale: 0.94,
                duration: 1.1,
                ease: "power3.out"
            });

            // Hover 3D Tilt Effect
            card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                const rotateX = (y - centerY) / 20;
                const rotateY = (centerX - x) / 20;

                gsap.to(card, {
                    rotateX: rotateX,
                    rotateY: rotateY,
                    scale: 1.02,
                    duration: 0.5,
                    ease: "power2.out"
                });
            });

            card.addEventListener('mouseleave', () => {
                gsap.to(card, {
                    rotateX: 0,
                    rotateY: 0,
                    scale: 1,
                    duration: 0.8,
                    ease: "elastic.out(1, 0.5)"
                });
            });
        });
    }
});
</script>

<style>
    [data-reveal] {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 1s cubic-bezier(0.2, 0.8, 0.2, 1), 
                    transform 1s cubic-bezier(0.2, 0.8, 0.2, 1);
        transition-delay: var(--reveal-delay, 0s);
    }
    [data-reveal].revealed {
        opacity: 1;
        transform: translateY(0);
    }
    .portfolio-card {
        perspective: 1000px;
        transform-style: preserve-3d;
        will-change: transform, opacity;
    }
    .portfolio-card img,
    .portfolio-card video,
    .portfolio-card iframe {
        backface-visibility: hidden;
        transform: translateZ(0);
    }
    @media (max-width: 768px) {
        .portfolio-card {
            perspective: none;
            transform-style: flat;
            will-change: auto;
        }
        .portfolio-card img,
        .portfolio-card video,
        .portfolio-card iframe {
            transform: none !important;
            filter: none !important;
        }
    }
</style>
@endpush
