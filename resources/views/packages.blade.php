@extends('layouts.guest')
@section('title', 'Paket Wedding – Anggita WO')
@section('meta_description', 'Eksplorasi pilihan paket wedding eksklusif dari Anggita Wedding Organizer. Kami menawarkan paket rumahan dan gedung dengan harga transparan dan fasilitas lengkap.')

@push('head')
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "ItemList",
  "itemListElement": [
    @foreach($packages as $index => $package)
    {
      "@type": "ListItem",
      "position": {{ $index + 1 }},
      "item": {
        "@type": "Product",
        "name": "{{ $package->name }}",
        "description": "{{ $package->description }}",
        "image": "{{ $package->resolved_poster_url ?? asset('images/logo.png') }}",
        "offers": {
          "@type": "Offer",
          "priceCurrency": "IDR",
          "price": "{{ $package->hasActivePromo() ? $package->effective_price : $package->price }}",
          "availability": "https://schema.org/InStock",
          "url": "{{ url()->current() }}#paket"
        }
      }
    }{{ !$loop->last ? ',' : '' }}
    @endforeach
  ]
}
</script>
@endpush

@section('content')
<div class="min-h-screen pt-28 pb-16 bg-transparent">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="packageExplorer('{{ $packagesByCategory->keys()->first() ?? 'rumahan' }}')">
        <div class="text-center mb-12">
            <span class="text-yellow-600 dark:text-yellow-500 text-xs font-semibold uppercase tracking-[0.4em]">Paket & Harga</span>
            <h1 class="font-playfair text-5xl font-bold text-gray-900 dark:text-white mt-4">Pilih Paket Wedding Anda</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-4 max-w-2xl mx-auto">Paket curated untuk setiap gaya perayaan — dari intimate rumahan hingga ballroom megah dan layanan rias eksklusif.</p>
        </div>

        <div class="flex flex-wrap gap-3 justify-center mb-6">
            @foreach($categoryLabels as $key => $label)
                @if(isset($packagesByCategory[$key]) && $packagesByCategory[$key]->isNotEmpty())
                    <button @click="tab='{{ $key }}'" type="button"
                        :class="tab === '{{ $key }}' ? 'gold-gradient text-white shadow-lg' : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border dark:border-gray-700'"
                        class="px-5 py-2 rounded-full text-sm font-semibold transition-all border border-transparent">
                        {{ $label }}
                    </button>
                @endif
            @endforeach
        </div>

        <div class="glass-card dark:bg-[#111] dark:border-[#333] rounded-3xl p-6 mb-12 border border-white/60 shadow-xl">
            <div class="flex flex-col md:flex-row items-center gap-6">
                <div class="w-full md:w-1/2">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-[0.3em]">Rentang Harga</label>
                    <select x-model="price" class="mt-2 w-full border border-gray-200 dark:border-gray-800 rounded-2xl px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-[#1A1A1A] focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                        <option value="all">Semua harga</option>
                        <option value="low">Di bawah Rp 20 juta</option>
                        <option value="mid">Rp 20 - 40 juta</option>
                        <option value="high">Di atas Rp 40 juta</option>
                    </select>
                </div>
                <div class="w-full md:w-1/2">
                    <label class="text-xs font-semibold text-gray-700 dark:text-gray-400 uppercase tracking-[0.3em]">Kapasitas Tamu</label>
                    <select x-model="guests" class="mt-2 w-full border border-gray-200 dark:border-gray-800 rounded-2xl px-4 py-3 text-sm font-semibold text-gray-700 dark:text-gray-300 bg-white dark:bg-[#1A1A1A] focus:ring-2 focus:ring-yellow-400 focus:border-yellow-400 shadow-sm">
                        <option value="all">Semua kapasitas</option>
                        <option value="small">Hingga 200 tamu</option>
                        <option value="medium">201 - 500 tamu</option>
                        <option value="large">Di atas 500 tamu</option>
                        <option value="service">Layanan tanpa batas tamu (Rias, dll)</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Detail Modal --}}
        <template x-teleport="body">
            <div x-show="detailModal" x-cloak
                 x-transition.opacity
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4">
                    <div class="bg-white dark:bg-[#111] rounded-[28px] shadow-2xl max-w-3xl w-full overflow-hidden border border-transparent dark:border-white/10"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         @click.outside="closeDetailModal()">
                        <div class="flex items-center justify-between px-6 py-4 border-b dark:border-white/10">
                            <div>
                                <p class="text-[11px] uppercase tracking-[0.3em] text-gray-400 dark:text-gray-500">Detail Paket</p>
                                <h3 class="font-semibold text-gray-900 dark:text-white" x-text="detailPackage?.name"></h3>
                            </div>
                            <button class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" @click="closeDetailModal()"><i class="fas fa-times"></i></button>
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

        {{-- Packages --}}
        @foreach($packagesByCategory as $category => $list)
        <div x-show="tab === '{{ $category }}'" x-cloak class="mb-16">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
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
                    <div x-show="matches({ price: {{ $package->price }}, guests: {{ $package->max_guests ?? 'null' }} })" x-cloak
                     class="bg-white/90 dark:bg-[#111]/90 backdrop-blur rounded-3xl shadow-lg hover:shadow-2xl transition-all overflow-hidden flex flex-col relative border border-white/60 dark:border-gray-800">
                        @if($isPopular)
                        <div class="gold-gradient text-white text-center py-2.5 text-sm font-bold uppercase tracking-wider flex items-center justify-center gap-2"><i class="fas fa-star"></i> Paket Terfavorit</div>
                        @endif
                        <div class="p-8 pb-10 flex-1 flex flex-col gap-6">
                            <div class="text-center space-y-4">
                                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-semibold uppercase bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300">
                                    <i class="fas fa-map-marker-alt text-yellow-500"></i> {{ $package->category_label }}
                                </div>
                                <div class="w-16 h-16 mx-auto rounded-2xl flex items-center justify-center
                                    {{ $package->tier === 'silver' ? 'bg-gray-100 dark:bg-gray-800' : ($package->tier === 'gold' ? 'gold-gradient' : 'bg-purple-100 dark:bg-purple-900/40') }}">
                                    <i class="fas {{ $package->tier === 'silver' ? 'fa-medal text-gray-500 dark:text-gray-400' : ($package->tier === 'gold' ? 'fa-crown text-white' : 'fa-gem text-purple-600 dark:text-purple-400') }} text-2xl"></i>
                                </div>
                                <div class="inline-flex items-center gap-2 px-4 py-1 rounded-full text-xs font-bold uppercase
                                    {{ $package->tier === 'silver' ? 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300' : ($package->tier === 'gold' ? 'bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-500' : 'bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-400') }}">
                                    {{ ucfirst($package->tier ?? 'Premium') }}
                                </div>
                                @if($package->hasActivePromo())
                                    <div class="inline-flex items-center gap-2 px-4 py-1 rounded-full text-xs font-semibold uppercase bg-pink-50 dark:bg-pink-900/40 text-pink-600 dark:text-pink-400">
                                        <i class="fas fa-bolt"></i> {{ $package->promo_label ?? 'Promo Spesial' }}
                                    </div>
                                @endif
                                <h2 class="font-playfair text-3xl font-bold text-gray-900 dark:text-white">{{ $package->name }}</h2>
                                <div class="space-y-1">
                                    @if($package->hasActivePromo())
                                        <div class="text-sm text-gray-400 dark:text-gray-500 line-through">{{ $package->formatted_price }}</div>
                                        <div class="text-4xl font-bold text-yellow-600 dark:text-yellow-500">{{ $package->formattedEffectivePrice }}</div>
                                    @else
                                        <div class="text-4xl font-bold text-yellow-600 dark:text-yellow-500">{{ $package->formatted_price }}</div>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">DP 30%: Rp {{ number_format($package->dp_amount, 0, ',', '.') }}</p>
                            </div>

                            <p class="text-sm text-gray-600 dark:text-gray-400 text-center">{{ $package->description }}</p>

                            @if($package->has_digital_invitation)
                            <div class="flex items-center justify-center gap-2 text-yellow-700 dark:text-yellow-500 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-900/50 rounded-xl py-2 text-xs font-semibold shadow-sm">
                                <i class="fas fa-envelope-open-text"></i>
                                Termasuk Undangan Digital
                            </div>
                            @endif

                            @php $sections = $package->feature_sections; @endphp
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8 flex-1 items-start auto-rows-min">
                                @forelse($sections as $section)
                                    <div class="rounded-2xl border border-gray-100 dark:border-gray-800 bg-white/60 dark:bg-[#1A1A1A]/60 p-4 flex flex-col gap-2 min-h-[180px] relative group">
                                        @if($section['title'])
                                            <p class="text-xs uppercase tracking-wide text-gray-600 dark:text-gray-400 font-semibold sticky top-0 bg-white/90 dark:bg-[#1A1A1A]/90 backdrop-blur-sm z-10 pb-1">{{ $section['title'] }}</p>
                                        @endif
                                        <div class="relative flex-1">
                                            <ul class="space-y-1.5 text-[12px] text-gray-700 dark:text-gray-300 leading-snug max-h-56 overflow-y-auto pr-2 feature-scroll-area">
                                                @foreach($section['items'] as $item)
                                                <li class="flex items-start gap-2">
                                                    <span class="w-5 h-5 rounded-full bg-yellow-100 dark:bg-yellow-900/40 flex items-center justify-center flex-shrink-0">
                                                        <i class="fas fa-check text-yellow-600 dark:text-yellow-500 text-[10px]"></i>
                                                    </span>
                                                    <span class="break-words">{{ $item }}</span>
                                                </li>
                                                @endforeach
                                            </ul>
                                            @if(count($section['items']) > 6)
                                            <div class="feature-scroll-fade pointer-events-none absolute bottom-0 left-0 right-2 h-12 bg-gradient-to-t from-white/95 dark:from-[#1A1A1A]/95 to-transparent rounded-b-lg flex items-end justify-center pb-1 transition-opacity">
                                                <div class="flex items-center gap-1 text-[10px] text-gray-400 dark:text-gray-500 animate-bounce">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                                    <span>scroll</span>
                                                </div>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="rounded-2xl border border-dashed border-gray-200 dark:border-gray-800 p-4 text-center text-xs text-gray-400">
                                        Belum ada fitur yang ditambahkan.
                                    </div>
                                @endforelse
                            </div>

                        <div class="space-y-2">
                            <button type="button"
                                class="block w-full text-center py-3.5 rounded-2xl font-bold text-sm transition-all gold-gradient text-white hover:shadow-2xl"
                                @click="openBookingModal({ id: {{ $package->id }}, name: '{{ addslashes($package->name) }}' })">
                                Pilih Paket Ini
                            </button>
                            <a href="{{ route('consultation.form') }}?package={{ $package->slug }}"
                               class="block w-full text-center py-3 rounded-2xl text-sm text-gray-600 dark:text-gray-300 hover:text-yellow-600 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-all border border-gray-200 dark:border-white/10">
                                Konsultasi Dulu
                            </a>
                            <div class="flex justify-center pt-2">
                                <button type="button"
                                        class="w-10 h-10 rounded-full border border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 transition-all flex items-center justify-center"
                                        @click="openDetailModal({{ Js::from([
                                            'id' => $package->id,
                                            'name' => $package->name,
                                            'media' => $detailMediaPayload,
                                        ]) }})">
                                    <i class="fas fa-images text-sm"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        {{-- Booking Modal --}}
        <div x-show="bookingModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4 backdrop-blur-sm">
            <div class="bg-white dark:bg-[#111] rounded-3xl shadow-2xl max-w-lg w-full p-8 relative border border-transparent dark:border-white/10" x-ref="bookingModal" @click.outside="closeBookingModal()">
                <button class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300" @click="closeBookingModal()"><i class="fas fa-times text-lg"></i></button>
                <h3 class="font-playfair text-2xl font-bold text-gray-900 dark:text-white mb-2">Pilih Tanggal untuk <span class="text-yellow-600 dark:text-yellow-500" x-text="selectedPackage?.name"></span></h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Tanggal ini akan dikunci di form booking dan dicek ketersediaannya.</p>
                <div class="space-y-4">
                    <label class="text-xs uppercase tracking-wider font-bold text-gray-500 dark:text-gray-400">Tanggal Acara <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="text" x-model="bookingDate" placeholder="Pilih tanggal perayaan"
                               data-flatpickr :data-min-date="minDate"
                               class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400 focus:outline-none transition-all">
                    </div>
                    <button type="button" class="px-5 py-3 rounded-2xl border dark:border-white/10 text-sm font-bold flex items-center gap-3 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 transition-all"
                            :disabled="!bookingDate || checking" @click="checkAvailability()">
                        <i class="fas" :class="checking ? 'fa-spinner fa-spin' : 'fa-search text-yellow-500'"></i>
                        <span>Cek Ketersediaan</span>
                    </button>
                    <template x-if="availability">
                        <div class="p-4 rounded-2xl text-sm border shadow-sm"
                             :class="availability.status === 'available' ? 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 border-green-200 dark:border-green-800/40' : availability.status === 'tentative' ? 'bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-400 border-yellow-200 dark:border-yellow-800/40' : 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 border-red-200 dark:border-red-800/40'">
                            <div class="flex items-start gap-3">
                                <i class="fas mt-0.5" :class="availability.status === 'available' ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
                                <div>
                                    <p class="font-bold mb-0.5" x-text="availability.label"></p>
                                    <p class="opacity-90 leading-relaxed" x-text="availability.message"></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="mt-8 flex flex-col sm:flex-row justify-end gap-3">
                    <button type="button" class="px-6 py-3.5 rounded-2xl border dark:border-white/10 text-gray-500 dark:text-gray-400 font-bold text-sm hover:bg-gray-50 dark:hover:bg-white/5 transition-all" @click="closeBookingModal()">Batal</button>
                    <button type="button" class="px-8 py-3.5 rounded-2xl gold-gradient text-white font-bold text-sm shadow-lg hover:shadow-xl transition-all disabled:opacity-50 disabled:grayscale"
                            :disabled="!bookingDate || (availability && availability.status === 'full')"
                            @click="continueBooking()">
                        Lanjutkan Booking
                    </button>
                </div>
            </div>
        </div>

        {{-- Comparison Table --}}
        <div class="bg-white dark:bg-[#111] rounded-3xl shadow-lg overflow-hidden mb-16 border border-transparent dark:border-white/10">
            <div class="p-8 border-b dark:border-white/10 text-center">
                <h2 class="font-playfair text-3xl font-bold text-gray-800 dark:text-white">Perbandingan Paket</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Tabel akan mengikuti kategori yang sedang Anda pilih.</p>
            </div>
            @foreach($packagesByCategory as $category => $list)
            <div x-show="tab === '{{ $category }}'" x-cloak class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-white/5">
                            <th class="text-left px-6 py-5 font-bold text-gray-600 dark:text-gray-300">Fitur</th>
                            @foreach($list as $p)
                            <th class="px-6 py-5 font-bold {{ $p->tier === 'gold' ? 'text-yellow-600 dark:text-yellow-500' : 'text-gray-600 dark:text-gray-300' }}">{{ $p->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $features = ['Dekorasi Pelaminan','Makeup Pengantin','Dokumentasi Foto','Videografi','Prewedding','MC Profesional','Musik Live','Koordinator Hari-H','Undangan Digital','Konsultasi','Catering','Souvenir','Wedding Car','Honeymoon'];
                        @endphp
                        @foreach($features as $feat)
                        <tr class="border-t dark:border-white/10 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4 text-gray-700 dark:text-gray-300 font-medium">{{ $feat }}</td>
                            @foreach($list as $p)
                            @php
                                $items = collect($p->feature_items);
                                $keyword = strtok($feat, ' ');
                            @endphp
                            <td class="px-6 py-3 text-center">
                                @if($items->contains(function ($f) use ($keyword) { return stripos($f, $keyword) !== false; }))
                                    <i class="fas fa-check-circle text-green-500 dark:text-green-400 text-lg"></i>
                                @else
                                    <i class="fas fa-times-circle text-gray-300 dark:text-gray-700 text-lg"></i>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        </div>

        {{-- FAQ CTA --}}
        <div class="bg-gradient-to-r from-purple-900 to-indigo-900 rounded-3xl p-10 text-center text-white">
            <h2 class="font-playfair text-3xl font-bold mb-3">Masih Bingung?</h2>
            <p class="text-purple-200 mb-6">Konsultasikan kebutuhan pernikahan Anda secara gratis dengan tim kami.</p>
            <a href="{{ route('consultation.form') }}" class="gold-gradient text-white font-bold px-8 py-4 rounded-full inline-block hover:shadow-xl transition-all">
                <i class="fas fa-comments mr-2"></i> Konsultasi Gratis Sekarang
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function packageExplorer(initialTab) {
    return {
        tab: initialTab,
        price: 'all',
        guests: 'all',
        bookingModal: false,
        detailModal: false,
        detailMedia: [],
        detailIndex: 0,
        detailPackage: null,
        selectedPackage: null,
        bookingDate: '',
        availability: null,
        checking: false,
        minDate: '{{ now()->addDay()->toDateString() }}',
        matches(pkg) {
            const price = Number(pkg.price ?? 0);
            const guests = pkg.guests === null ? null : Number(pkg.guests);

            const priceOk = this.price === 'all'
                || (this.price === 'low' && price < 20000000)
                || (this.price === 'mid' && price >= 20000000 && price <= 40000000)
                || (this.price === 'high' && price > 40000000);

            const guestsOk = this.guests === 'all'
                || (this.guests === 'service' && guests === null)
                || (this.guests === 'small' && guests !== null && guests <= 200)
                || (this.guests === 'medium' && guests !== null && guests >= 201 && guests <= 500)
                || (this.guests === 'large' && guests !== null && guests > 500);

            return priceOk && guestsOk;
        },
        openBookingModal(pkg) {
            this.selectedPackage = pkg;
            this.bookingDate = '';
            this.availability = null;
            this.bookingModal = true;
            this.$nextTick(() => {
                window.initAnggitaPickers?.(this.$refs.bookingModal);
            });
        },
        closeBookingModal() {
            this.bookingModal = false;
            this.selectedPackage = null;
            this.bookingDate = '';
            this.availability = null;
        },
        async checkAvailability() {
            if (!this.bookingDate) return;
            this.checking = true;
            this.availability = null;
            try {
                const res = await fetch(`{{ route('booking.check-date') }}?date=${this.bookingDate}`);
                this.availability = await res.json();
            } catch (e) {
                this.availability = {
                    status: 'error',
                    label: 'Terjadi Kesalahan',
                    message: 'Gagal mengecek tanggal. Coba lagi.',
                };
            } finally {
                this.checking = false;
            }
        },
        continueBooking() {
            if (!this.selectedPackage || !this.bookingDate) return;
            const url = `{{ route('booking.form') }}?package_id=${this.selectedPackage.id}&date=${encodeURIComponent(this.bookingDate)}`;
            window.location.href = url;
        },
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
    };
}
</script>

<script>
// Auto-hide the scroll fade indicator when user scrolls to bottom
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.feature-scroll-area').forEach(el => {
        const fade = el.parentElement?.querySelector('.feature-scroll-fade');
        if (!fade) return;
        
        const checkScroll = () => {
            const atBottom = el.scrollHeight - el.scrollTop - el.clientHeight < 8;
            fade.style.opacity = atBottom ? '0' : '1';
        };
        
        el.addEventListener('scroll', checkScroll, { passive: true });
        checkScroll();
    });
});
</script>

<style>
/* Custom scrollbar for feature lists */
.feature-scroll-area {
    scrollbar-width: thin;
    scrollbar-color: rgba(202, 138, 4, 0.3) transparent;
}

.feature-scroll-area::-webkit-scrollbar {
    width: 4px;
}

.feature-scroll-area::-webkit-scrollbar-track {
    background: transparent;
    border-radius: 10px;
}

.feature-scroll-area::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, rgba(202, 138, 4, 0.2), rgba(202, 138, 4, 0.5));
    border-radius: 10px;
    transition: background 0.3s;
}

.feature-scroll-area::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, rgba(202, 138, 4, 0.4), rgba(202, 138, 4, 0.7));
}

/* Dark mode scrollbar */
.dark .feature-scroll-area {
    scrollbar-color: rgba(234, 179, 8, 0.3) transparent;
}

.dark .feature-scroll-area::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, rgba(234, 179, 8, 0.15), rgba(234, 179, 8, 0.4));
}

.dark .feature-scroll-area::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, rgba(234, 179, 8, 0.3), rgba(234, 179, 8, 0.6));
}

/* Fade transition */
.feature-scroll-fade {
    transition: opacity 0.3s ease;
}

/* Subtle bounce animation for scroll indicator */
@keyframes gentleBounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(3px); }
}

.feature-scroll-fade .animate-bounce {
    animation: gentleBounce 1.5s ease-in-out infinite;
}
</style>
@endpush
