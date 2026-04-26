@extends('layouts.guest')

@section('meta_description', $post->meta_description ?? $post->excerpt ?? Str::limit(strip_tags($post->content), 160))

@push('head')
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "{{ url()->current() }}"
  },
  "headline": "{{ $post->title }}",
  "description": "{{ $post->meta_description ?? $post->excerpt ?? Str::limit(strip_tags($post->content), 160) }}",
  "image": "{{ $post->resolved_image_url }}",  
  "author": {
    "@type": "Organization",
    "name": "Anggita Wedding Organizer"
  },  
  "publisher": {
    "@type": "Organization",
    "name": "Anggita Wedding Organizer",
    "logo": {
      "@type": "ImageObject",
      "url": "{{ asset('images/logo.png') }}"
    }
  },
  "datePublished": "{{ $post->published_at ? $post->published_at->tz('UTC')->toAtomString() : $post->created_at->tz('UTC')->toAtomString() }}",
  "dateModified": "{{ $post->updated_at->tz('UTC')->toAtomString() }}"
}
</script>
@endpush

@section('content')
<main class="pt-32 pb-20">
    <article class="max-w-4xl mx-auto px-6">
        {{-- Breadcrumb & Category --}}
        <div class="flex items-center gap-3 mb-8">
            <a href="{{ route('blog.index') }}" class="text-xs font-bold text-gray-400 hover:text-yellow-600 transition-colors uppercase tracking-widest">Blog</a>
            <span class="text-gray-300">/</span>
            <span class="px-4 py-1.5 bg-yellow-50 dark:bg-yellow-900/20 rounded-full text-[10px] font-bold text-yellow-700 dark:text-yellow-500 uppercase tracking-widest">{{ $post->category ?? 'Tips' }}</span>
        </div>

        {{-- Title --}}
        <h1 class="text-3xl md:text-5xl font-playfair font-bold text-gray-900 dark:text-white mb-8 leading-tight">
            {{ $post->title }}
        </h1>

        {{-- Meta --}}
        <div class="flex items-center justify-between py-6 border-y border-gray-100 mb-10">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-white/10 flex items-center justify-center overflow-hidden">
                    <img src="https://ui-avatars.com/api/?name=Admin+Anggita&background=fef3c7&color=b45309" loading="lazy" decoding="async" alt="Admin">
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-900 dark:text-white">Admin Anggita</div>
                    <div class="flex items-center gap-3 text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                        <span>{{ $post->published_at ? $post->published_at->format('d M Y') : $post->created_at->format('d M Y') }}</span>
                        <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-white/10"></span>
                        <span class="flex items-center gap-1.5" title="Dilihat {{ number_format($post->views ?? 0, 0, ',', '.') }} kali">
                            <i class="far fa-eye text-[11px]"></i> {{ number_format($post->views ?? 0, 0, ',', '.') }} Views
                        </span>
                    </div>
                </div>
            </div>
            
            {{-- Social Share --}}
            <div class="flex items-center gap-3">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest hidden sm:inline">Share:</span>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="w-8 h-8 rounded-full bg-gray-50 dark:bg-white/5 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-blue-600 hover:text-white dark:hover:text-white transition-all">
                    <i class="fab fa-facebook-f text-xs"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($post->title) }}" target="_blank" class="w-8 h-8 rounded-full bg-gray-50 dark:bg-white/5 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-black hover:text-white dark:hover:text-white transition-all">
                    <i class="fab fa-x-twitter text-xs"></i>
                </a>
                <a href="https://wa.me/?text={{ urlencode($post->title . ' ' . url()->current()) }}" target="_blank" class="w-8 h-8 rounded-full bg-gray-50 dark:bg-white/5 flex items-center justify-center text-gray-500 dark:text-gray-400 hover:bg-green-500 hover:text-white dark:hover:text-white transition-all">
                    <i class="fab fa-whatsapp text-xs"></i>
                </a>
            </div>
        </div>

        {{-- Thumbnail --}}
        @if($post->thumbnail)
        <div class="mb-12 rounded-3xl overflow-hidden shadow-lg aspect-[16/9]">
            <img src="{{ asset('storage/' . $post->thumbnail) }}" loading="lazy" decoding="async" alt="{{ $post->title }}" class="w-full h-full object-cover">
        </div>
        @endif

        {{-- Content --}}
        <div class="prose prose-lg dark:prose-invert max-w-none mt-10" data-reveal data-reveal-direction="up" style="--reveal-delay:.3s;">
            {!! $post->content !!}
        </div>

        {{-- Related Posts --}}
        @if($relatedPosts->count() > 0)
        <div class="pt-16 border-t border-gray-100 dark:border-white/10">
            <h3 class="text-2xl font-playfair font-bold text-gray-900 dark:text-white mb-8">Artikel Terkait</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($relatedPosts as $related)
                <a href="{{ route('blog.show', $related->slug) }}" class="group block">
                    <div class="aspect-[16/10] rounded-2xl overflow-hidden mb-4 bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-white/5 shadow-sm">
                        @if($related->thumbnail)
                        <img src="{{ asset('storage/' . $related->thumbnail) }}" loading="lazy" decoding="async" alt="{{ $related->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        @else
                        <div class="w-full h-full flex items-center justify-center"><i class="fas fa-image text-gray-200 dark:text-gray-700 text-2xl"></i></div>
                        @endif
                    </div>
                    <h4 class="text-base font-playfair font-bold text-gray-900 dark:text-white group-hover:text-yellow-600 dark:group-hover:text-yellow-500 transition-colors line-clamp-2">{{ $related->title }}</h4>
                </a>
                @endforeach
            </div>
        </div>
        @endif
        
        <div class="mt-20 text-center">
            <a href="{{ route('blog.index') }}" class="inline-flex items-center gap-2 px-8 py-3 rounded-full border border-gray-200 dark:border-white/10 text-sm font-bold text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5 transition-all uppercase tracking-widest">
                <i class="fas fa-arrow-left"></i> Kembali ke Blog
            </a>
        </div>
    </article>
</main>

<style>
    .article-content {
        line-height: 1.8;
        font-size: 1.125rem;
    }
    .article-content p {
        margin-bottom: 1.5rem;
    }
    .article-content h2 {
        font-family: 'Playfair Display', serif;
        font-size: 2rem;
        font-weight: 700;
        margin-top: 3.5rem;
        margin-bottom: 1.5rem;
    }
    .article-content h3 {
        font-family: 'Playfair Display', serif;
        font-size: 1.75rem;
        font-weight: 700;
        margin-top: 3rem;
        margin-bottom: 1.25rem;
    }
    .dark .article-content h2,
    .dark .article-content h3 {
        color: #f3f4f6;
    }
    .article-content img {
        max-width: 100%;
        height: auto;
        border-radius: 1.5rem;
        margin: 2.5rem 0;
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.08);
    }
    .article-content ul, .article-content ol {
        margin-bottom: 1.5rem;
        padding-left: 1.5rem;
    }
    .article-content ul {
        list-style-type: disc;
    }
    .article-content ol {
        list-style-type: decimal;
    }
    .article-content li {
        margin-bottom: 0.5rem;
    }
</style>
@endsection
