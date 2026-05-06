@php
    $layout = Auth::check() && !Auth::user()->isAdmin() ? 'layouts.app' : 'layouts.guest';
    $isApp = $layout === 'layouts.app';
@endphp

@extends($layout)

@section('title', 'Pilih Paket Wedding')

@push('head')
<style>
    [x-cloak] { display: none !important; }
    .gold-glow { box-shadow: 0 0 20px rgba(201,168,76,0.15); }
    .dark .gold-glow { box-shadow: 0 0 30px rgba(201,168,76,0.1); }
</style>
@endpush

@php
    $initialTab = $packagesByCategory->keys()->first() ?? 'rumahan';
@endphp

@section('content')
<div class="{{ $isApp ? 'py-6 px-2' : 'min-h-screen pt-28 pb-16' }} dark:bg-[#0A0A0A]"
     x-data="packageSelectPage({
        initialTab: @js($initialTab),
        initialDate: @js($date),
        initialStatus: @js($dateAvailability),
        minDate: @js(now()->addDay()->toDateString()),
        checkUrl: '{{ route('booking.check-date') }}',
        applyUrl: '{{ route('booking.select-package') }}',
        formUrl: '{{ route('booking.form') }}'
     })"
     x-init="init()">
    
    <div class="max-w-6xl mx-auto px-4">
        {{-- Progress Stepper --}}
        <div class="flex items-center justify-center mb-10">
            <div class="flex items-center gap-4">
                <div class="flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-2xl gold-gradient text-white flex items-center justify-center shadow-lg font-bold">1</div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-yellow-600 dark:text-yellow-500">Paket</span>
                </div>
                <div class="w-12 h-px bg-gray-200 dark:bg-white/10 -mt-5"></div>
                <div class="flex flex-col items-center gap-2 opacity-50">
                    <div class="w-10 h-10 rounded-2xl bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-400 flex items-center justify-center font-bold">2</div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Data</span>
                </div>
                <div class="w-12 h-px bg-gray-200 dark:bg-white/10 -mt-5"></div>
                <div class="flex flex-col items-center gap-2 opacity-50">
                    <div class="w-10 h-10 rounded-2xl bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-400 flex items-center justify-center font-bold">3</div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Bayar</span>
                </div>
            </div>
        </div>

        {{-- Header Section --}}
        <div class="text-center mb-12">
            <h1 class="font-playfair text-4xl font-bold text-gray-900 dark:text-white mb-4">Pilih Paket Wedding</h1>
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-2xl bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-800/30">
                <i class="far fa-calendar-alt text-yellow-600"></i>
                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Acara: </span>
                <strong class="text-sm text-yellow-700 dark:text-yellow-500" x-text="formattedDate">{{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM Y') }}</strong>
            </div>
        </div>

        {{-- Availability Check Card --}}
        <div class="bg-white dark:bg-white/5 rounded-[32px] border border-gray-100 dark:border-white/10 shadow-xl p-6 md:p-8 mb-12 max-w-lg mx-auto relative overflow-hidden">
            {{-- Decoration --}}
            <div class="absolute -top-12 -left-12 w-24 h-24 bg-yellow-400/10 rounded-full blur-2xl"></div>
            
            <div class="relative z-10 space-y-6">
                <div>
                    <label class="text-[10px] uppercase tracking-[0.2em] text-gray-400 font-bold mb-3 block">Periksa Tanggal Lain</label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <input type="text" x-ref="dateInput" x-model="selectedDate" placeholder="Pilih tanggal"
                                   data-flatpickr
                                   :data-min-date="minDate"
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-900 dark:text-white rounded-2xl px-5 py-3.5 text-sm focus:ring-2 focus:ring-yellow-400/20 focus:border-yellow-400/50 transition-all placeholder-gray-400" />
                            <i class="far fa-calendar absolute right-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                        <button type="button" @click="checkDate" :disabled="!selectedDate || loading"
                                class="bg-gray-900 dark:bg-white text-white dark:text-gray-900 px-6 py-3.5 rounded-2xl text-sm font-bold hover:opacity-90 transition disabled:opacity-50">
                            <span x-show="!loading">Cek</span>
                            <span x-show="loading"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    </div>
                </div>
                
                <div class="bg-gray-50 dark:bg-black/20 rounded-2xl p-5 border border-gray-100 dark:border-white/5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-2 h-2 rounded-full animate-pulse" :class="statusBadgeClass() === 'bg-green-100 text-green-700' ? 'bg-green-500' : 'bg-red-500'"></div>
                        <p class="text-[10px] uppercase tracking-widest text-gray-500 dark:text-gray-400 font-bold">Status Ketersediaan</p>
                    </div>
                    <div class="flex flex-col gap-2">
                        <h4 class="font-bold text-gray-900 dark:text-white text-sm" x-text="statusLabel"></h4>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed" x-text="statusMessage"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Category Tabs --}}
        <div class="flex flex-wrap items-center justify-center gap-3 mb-12">
            @foreach($categoryLabels as $key => $label)
                @if(isset($packagesByCategory[$key]) && $packagesByCategory[$key]->isNotEmpty())
                    <button @click="tab='{{ $key }}'" type="button"
                        :class="tab === '{{ $key }}' ? 'gold-gradient text-white shadow-lg scale-105' : 'bg-white dark:bg-white/5 text-gray-500 dark:text-gray-400 border-gray-200 dark:border-white/10 hover:border-yellow-400/30'"
                        class="px-6 py-3.5 rounded-2xl text-sm font-bold transition-all border transform duration-300">
                        {{ $label }}
                    </button>
                @endif
            @endforeach
        </div>

        {{-- Packages Grid --}}
        @foreach($packagesByCategory as $category => $list)
        <div x-show="tab === '{{ $category }}'" x-cloak 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0">
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($list as $package)
                @php
                    $categoryPopularId = $popularPackageIdsByCategory[$category] ?? null;
                    $isPopular = $categoryPopularId === $package->id;
                @endphp
                
                <div class="group relative bg-white dark:bg-white/5 rounded-[40px] border border-gray-100 dark:border-white/10 shadow-sm hover:shadow-2xl transition-all duration-500 flex flex-col overflow-hidden {{ $isPopular ? 'gold-glow' : '' }}">
                    @if($isPopular)
                    <div class="gold-gradient text-white text-center py-2 text-[10px] font-black uppercase tracking-widest">
                        <i class="fas fa-star mr-1"></i> Recommended
                    </div>
                    @endif

                    <div class="p-8 flex-1 flex flex-col">
                        <div class="text-center mb-8">
                            <div class="flex flex-wrap justify-center gap-2 mb-4">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-yellow-50 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-500">
                                    {{ $package->category_label }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300">
                                    {{ ucfirst($package->tier ?? 'Premium') }}
                                </span>
                            </div>
                            
                            <h3 class="font-playfair text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $package->name }}</h3>
                            
                            <div class="mb-4">
                                @if($package->hasActivePromo())
                                    <p class="text-xs text-gray-400 line-through mb-1">{{ $package->formatted_price }}</p>
                                    <p class="text-4xl font-bold text-yellow-600 dark:text-yellow-500">{{ $package->formattedEffectivePrice }}</p>
                                @else
                                    <p class="text-4xl font-bold text-yellow-600 dark:text-yellow-500">{{ $package->formatted_price }}</p>
                                @endif
                                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mt-2">DP Booking: Rp {{ number_format($package->dp_amount, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        {{-- Features --}}
                        <div class="space-y-6 mb-8 flex-1">
                            @if($package->has_digital_invitation)
                            <div class="flex items-center gap-3 p-4 rounded-2xl bg-purple-50 dark:bg-purple-900/20 border border-purple-100 dark:border-purple-800/20">
                                <div class="w-8 h-8 rounded-xl bg-purple-100 dark:bg-purple-800/40 flex items-center justify-center text-purple-600 dark:text-purple-300">
                                    <i class="fas fa-envelope-open-text text-sm"></i>
                                </div>
                                <span class="text-xs font-bold text-purple-700 dark:text-purple-300">Gratis Undangan Digital</span>
                            </div>
                            @endif

                            <div class="space-y-4">
                                @foreach($package->feature_sections as $section)
                                <div class="space-y-2">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ $section['title'] }}</p>
                                    <ul class="space-y-2">
                                        @foreach(array_slice($section['items'], 0, 5) as $item)
                                        <li class="flex items-start gap-3 text-sm text-gray-600 dark:text-gray-400">
                                            <i class="fas fa-check-circle text-green-500 mt-1 text-xs"></i>
                                            <span>{{ preg_replace('/^##\s*/', '', trim($item)) }}</span>
                                        </li>
                                        @endforeach
                                        @if(count($section['items']) > 5)
                                        <li class="text-[10px] font-bold text-gray-400 italic ml-6">+ {{ count($section['items']) - 5 }} item lainnya...</li>
                                        @endif
                                    </ul>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Action --}}
                        <button @click="applyPackage(@js($package->id))" 
                                class="w-full py-4 rounded-2xl font-bold text-sm transition-all gold-gradient hover:shadow-lg active:scale-95">
                            Pilih Paket Ini
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>

    {{-- Consultation CTA --}}
    <div class="max-w-4xl mx-auto px-4 mt-16 text-center">
        <div class="bg-gray-50 dark:bg-white/5 rounded-[32px] p-8 border border-gray-100 dark:border-white/10">
            <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Belum yakin dengan pilihan Anda?</h4>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Konsultasikan pernikahan impian Anda bersama tim ahli kami secara gratis.</p>
            <a href="{{ route('consultation.form') }}" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-2xl border-2 border-yellow-400 dark:border-yellow-500 text-yellow-600 dark:text-yellow-500 font-bold hover:bg-yellow-400 hover:text-white dark:hover:bg-yellow-500 dark:hover:text-gray-900 transition-all">
                <i class="fas fa-headset"></i> Mulai Konsultasi Gratis
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
function packageSelectPage(config) {
    const formatLabel = (dateStr) => {
        if (!dateStr) return '-';
        const date = new Date(`${dateStr}T00:00:00`);
        if (Number.isNaN(date.getTime())) return dateStr;
        return date.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    };

    return {
        tab: config.initialTab,
        selectedDate: config.initialDate,
        date: config.initialDate,
        dateStatus: config.initialStatus,
        minDate: config.minDate,
        loading: false,
        formattedDate: formatLabel(config.initialDate),
        applyUrl: config.applyUrl,
        formUrl: config.formUrl,
        checkUrl: config.checkUrl,
        
        init() {
            this.$watch('selectedDate', value => {
                this.formattedDate = formatLabel(value);
            });
        },
        
        async checkDate() {
            if (!this.selectedDate) return;
            this.loading = true;
            try {
                const res = await fetch(`${this.checkUrl}?date=${this.selectedDate}`);
                const data = await res.json();
                this.dateStatus = data.status;
                if (data.status === 'available') {
                    window.location.href = `${this.applyUrl}?date=${this.selectedDate}`;
                }
            } catch (e) {
                console.error(e);
            } finally {
                this.loading = false;
            }
        },
        
        statusBadgeClass() {
            if (this.dateStatus === 'available') return 'bg-green-100 text-green-700';
            if (this.dateStatus === 'unavailable') return 'bg-red-100 text-red-700';
            return 'bg-gray-100 text-gray-700';
        },
        
        get statusLabel() {
            if (this.dateStatus === 'available') return 'Tersedia';
            if (this.dateStatus === 'unavailable') return 'Sudah Terisi';
            return 'Pilih Tanggal';
        },
        
        get statusMessage() {
            if (this.dateStatus === 'available') return 'Tanggal ini masih kosong. Anda bisa melanjutkan booking.';
            if (this.dateStatus === 'unavailable') return 'Maaf, tanggal ini sudah dibooking oleh klien lain. Silakan pilih tanggal lain.';
            return 'Silakan pilih tanggal untuk mengecek ketersediaan.';
        },
        
        applyPackage(id) {
            window.location.href = `${this.formUrl}?date=${this.date}&package_id=${id}`;
        }
    }
}
</script>
@endpush
@endsection
