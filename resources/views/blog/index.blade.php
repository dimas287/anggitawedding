@extends('layouts.guest')

@section('title', 'Wedding blog & Inspirasi – Anggita WO')
@section('meta_description', 'Temukan berbagai tips pernikahan, inspirasi dekorasi, dan panduan persiapan pernikahan dari Anggita Wedding Organizer.')

@section('content')
<main class="pt-32 pb-20 overflow-hidden bg-white dark:bg-[#0A0A0A] transition-colors duration-500">
    {{-- Header --}}
    <section class="max-w-7xl mx-auto px-6 mb-16 relative">
        <div class="absolute -top-10 -left-10 w-64 h-64 bg-yellow-500/5 rounded-full blur-3xl -z-10"></div>
        <div class="max-w-3xl">
            <h1 class="text-4xl md:text-5xl font-playfair font-bold text-gray-900 dark:text-white mb-6 leading-tight">
                Wedding Blog <span class="text-yellow-600 dark:text-yellow-500">&</span> Inspirasi
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 leading-relaxed font-space">
                Temukan panduan lengkap, tips cerdas, dan inspirasi terkini untuk mewujudkan pernikahan impian Anda bersama Anggita Wedding Organizer.
            </p>
        </div>
    </section>

    {{-- Blog Grid --}}
    <section class="max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-10">
            @forelse($posts as $post)
            <article class="group bg-white dark:bg-gray-800/10 rounded-3xl overflow-hidden border border-gray-100 dark:border-white/5 shadow-sm hover:shadow-xl transition-all duration-500 scale-hover">
                {{-- Image --}}
                <a href="{{ route('blog.show', $post->slug) }}" class="block relative aspect-[16/10] overflow-hidden">
                    @if($post->thumbnail)
                    <img src="{{ asset('storage/' . $post->thumbnail) }}" loading="lazy" decoding="async" alt="{{ $post->title }}" 
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    @else
                    <div class="w-full h-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center">
                        <i class="fas fa-image text-gray-200 dark:text-gray-700 text-4xl"></i>
                    </div>
                    @endif
                    <div class="absolute top-4 left-4">
                        <span class="px-4 py-1.5 bg-white/90 dark:bg-black/80 backdrop-blur-md rounded-full text-[10px] font-bold text-yellow-700 dark:text-yellow-500 uppercase tracking-widest shadow-sm">
                            {{ $post->category ?? 'Tips' }}
                        </span>
                    </div>
                </a>

                {{-- Content --}}
                <div class="p-8">
                    <div class="flex items-center gap-4 text-[10px] text-gray-400 dark:text-gray-500 font-bold uppercase tracking-widest mb-4">
                        <span class="flex items-center gap-1.5"><i class="far fa-calendar"></i> {{ $post->published_at ? $post->published_at->format('d M Y') : $post->created_at->format('d M Y') }}</span>
                        <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-white/10"></span>
                        <span class="flex items-center gap-1.5"><i class="far fa-eye"></i> {{ number_format($post->views ?? 0, 0, ',', '.') }} Views</span>
                    </div>
                    
                    <h2 class="text-xl font-playfair font-bold text-gray-900 dark:text-white mb-4 group-hover:text-yellow-600 dark:group-hover:text-yellow-500 transition-colors line-clamp-2">
                        <a href="{{ route('blog.show', $post->slug) }}">{{ $post->title }}</a>
                    </h2>
                    
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed mb-6 line-clamp-3">
                        {{ $post->excerpt ?? Str::limit(strip_tags($post->content), 120) }}
                    </p>

                    <a href="{{ route('blog.show', $post->slug) }}" class="inline-flex items-center gap-2 text-xs font-bold text-gray-900 dark:text-gray-200 uppercase tracking-widest group-hover:gap-3 transition-all hover:text-yellow-600 dark:hover:text-yellow-500">
                        Baca Selengkapnya 
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </a>
                </div>
            </article>
            @empty
            <div class="col-span-full py-20 text-center">
                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-newspaper text-gray-200 dark:text-gray-700 text-3xl"></i>
                </div>
                <h3 class="text-xl font-playfair font-bold text-gray-900 dark:text-white mb-2">Belum Ada Artikel</h3>
                <p class="text-gray-500 dark:text-gray-400">Kami sedang menyiapkan inspirasi menarik untuk Anda. Kembali lagi segera!</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($posts->hasPages())
        <div class="mt-20 flex justify-center">
            {{ $posts->links() }}
        </div>
        @endif
    </section>
</main>

<style>
    .scale-hover:hover {
        transform: translateY(-8px);
    }
</style>
@endsection
