@php
    $layout = Auth::check() && !Auth::user()->isAdmin() ? 'layouts.app' : 'layouts.guest';
    $isApp = $layout === 'layouts.app';
@endphp

@extends($layout)

@section('title', 'Pilih Paket Wedding')

@push('head')
<style>
    [x-cloak] { display: none !important; }
    .gold-gradient-text {
        background: linear-gradient(135deg, #C9A84C, #A78B40);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>
@endpush

@php
    $initialTab = $packagesByCategory->keys()->first() ?? 'rumahan';
@endphp

@section('content')
<div class="{{ $isApp ? 'py-6 px-4' : 'min-h-screen pt-28 pb-16' }} bg-gray-50 dark:bg-[#0A0A0A]"
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
    
    <div class="max-w-6xl mx-auto">
        {{-- Progress Header --}}
        <div class="text-center mb-10">
            <div class="flex items-center justify-center gap-4 mb-8">
                <div class="flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-2xl gold-gradient text-white flex items-center justify-center shadow-lg shadow-yellow-500/20 font-bold">1</div>
                    <span class="text-[9px] uppercase tracking-widest font-bold text-yellow-600 dark:text-yellow-500">Pilih Paket</span>
                </div>
                <div class="w-12 h-px bg-gray-200 dark:bg-white/10 mb-6"></div>
                <div class="flex flex-col items-center gap-2 opacity-40">
                    <div class="w-10 h-10 rounded-2xl bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-500 flex items-center justify-center font-bold">2</div>
                    <span class="text-[9px] uppercase tracking-widest font-bold text-gray-500">Isi Data</span>
                </div>
                <div class="w-12 h-px bg-gray-200 dark:bg-white/10 mb-6"></div>
                <div class="flex flex-col items-center gap-2 opacity-40">
                    <div class="w-10 h-10 rounded-2xl bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-500 flex items-center justify-center font-bold">3</div>
                    <span class="text-[9px] uppercase tracking-widest font-bold text-gray-500">Pembayaran</span>
                </div>
            </div>
            
            <h1 class="font-playfair text-4xl font-bold text-gray-900 dark:text-white mb-4">Pilih Paket Wedding</h1>
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-sm">
                <span class="text-gray-500 dark:text-gray-400">Tanggal Acara:</span>
                <span class="font-bold text-yellow-600 dark:text-yellow-500" x-text="formattedDate">{{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM Y') }}</span>
            </div>
        </div>

        {{-- Availability & Date Picker Section --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-white/5 rounded-[32px] p-8 border border-gray-100 dark:border-white/10 shadow-sm sticky top-24">
                    <div class="mb-8">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-4 block">Ganti Tanggal</label>
                        <div class="space-y-3">
                            <div class="relative">
                                <i class="far fa-calendar absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                                <input type="text" x-ref="dateInput" x-model="selectedDate" placeholder="Pilih tanggal"
                                       data-flatpickr :data-min-date="minDate"
                                       class="w-full bg-gray-50 dark:bg-[#1A1A1A] border border-gray-100 dark:border-white/5 text-gray-900 dark:text-white rounded-2xl pl-11 pr-4 py-4 text-sm focus:ring-2 focus:ring-yellow-500/20 transition-all outline-none" />
                            </div>
                            <button type="button" @click="checkDate" :disabled="!selectedDate || loading"
                                    class="w-full gold-gradient text-white font-bold py-4 rounded-2xl text-sm hover:shadow-lg transition-all disabled:opacity-50">
                                <span x-show="!loading"><i class="fas fa-search mr-2"></i> Cek Ketersediaan</span>
                                <span x-show="loading"><i class="fas fa-spinner fa-spin mr-2"></i> Mengecek...</span>
                            </button>
                        </div>
                    </div>

                    <div class="p-6 rounded-2xl" :class="statusBadgeContainerClass()">
                        <p class="text-[10px] font-bold uppercase tracking-widest mb-3 opacity-60">Status Ketersediaan</p>
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-2 h-2 rounded-full animate-pulse" :class="statusDotClass()"></div>
                            <span class="font-bold text-sm" x-text="statusLabel">Belum dicek</span>
                        </div>
                        <p class="text-xs opacity-70 leading-relaxed" x-text="statusMessage">Pilih tanggal untuk melihat ketersediaan jadwal kami.</p>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                {{-- Category Tabs --}}
                <div class="flex flex-wrap gap-2 mb-8 p-1.5 bg-gray-100 dark:bg-white/5 rounded-2xl w-fit">
                    @foreach($categoryLabels as $key => $label)
                        @if(isset($packagesByCategory[$key]) && $packagesByCategory[$key]->isNotEmpty())
                            <button @click="tab='{{ $key }}'" type="button"
                                :class="tab === '{{ $key }}' ? 'bg-white dark:bg-white/10 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white'"
                                class="px-6 py-2.5 rounded-xl text-sm font-bold transition-all">
                                {{ $label }}
                            </button>
                        @endif
                    @endforeach
                </div>

                {{-- Packages Grid --}}
                @foreach($packagesByCategory as $category => $list)
                <div x-show="tab === '{{ $category }}'" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($list as $package)
                        @php
                            $categoryPopularId = $popularPackageIdsByCategory[$category] ?? null;
                            $isPopular = $categoryPopularId === $package->id;
                        @endphp
                        <div class="group relative bg-white dark:bg-white/5 rounded-[32px] border border-gray-100 dark:border-white/10 shadow-sm hover:shadow-xl hover:border-yellow-400/50 dark:hover:border-yellow-500/30 transition-all duration-300 flex flex-col overflow-hidden">
                            @if($isPopular)
                            <div class="absolute top-4 right-4 z-20">
                                <div class="gold-gradient text-white px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider flex items-center gap-1 shadow-lg">
                                    <i class="fas fa-star text-[8px]"></i> Popular
                                </div>
                            </div>
                            @endif

                            <div class="p-8 flex-1 flex flex-col">
                                <div class="mb-6">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-white/10 text-[9px] font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ $package->category_label }}</span>
                                        <span class="px-2.5 py-1 rounded-lg {{ $package->tier === 'gold' ? 'bg-yellow-50 text-yellow-600 dark:bg-yellow-900/20 dark:text-yellow-500' : 'bg-gray-100 dark:bg-white/10 text-gray-500 dark:text-gray-400' }} text-[9px] font-bold uppercase tracking-wider">
                                            {{ $package->tier ?? 'Standard' }}
                                        </span>
                                    </div>
                                    <h3 class="font-playfair text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $package->name }}</h3>
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-3xl font-bold text-yellow-600 dark:text-yellow-500">{{ $package->formattedEffectivePrice }}</span>
                                        @if($package->hasActivePromo())
                                            <span class="text-sm text-gray-400 line-through">{{ $package->formatted_price }}</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-gray-400 mt-1">Minimal DP: Rp {{ number_format($package->dp_amount, 0, ',', '.') }}</p>
                                </div>

                                <div class="space-y-4 mb-8 flex-1">
                                    @php $sections = $package->feature_sections; @endphp
                                    @foreach(array_slice($sections, 0, 3) as $section)
                                        <div class="space-y-2">
                                            @if($section['title'])
                                                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ $section['title'] }}</p>
                                            @endif
                                            <ul class="space-y-2">
                                                @foreach(array_slice($section['items'], 0, 4) as $item)
                                                    @php $isSub = str_starts_with(trim($item), '##'); @endphp
                                                    @if(!$isSub)
                                                    <li class="flex items-start gap-3 text-xs text-gray-600 dark:text-gray-400">
                                                        <i class="fas fa-check text-yellow-500 mt-0.5"></i>
                                                        <span>{{ $item }}</span>
                                                    </li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endforeach
                                    @if(count($sections) > 0)
                                        <button type="button" class="text-[10px] font-bold text-yellow-600 hover:underline">Lihat semua detail paket...</button>
                                    @endif
                                </div>

                                <a href="{{ route('booking.form') }}?date={{ $date }}&package_id={{ $package->id }}"
                                   :href="packageLink({{ $package->id }})"
                                   class="block w-full text-center py-4 rounded-2xl font-bold text-sm bg-gray-900 dark:bg-white text-white dark:text-gray-900 group-hover:gold-gradient group-hover:text-white transition-all duration-300">
                                    Pilih Paket Ini
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>

        {{-- Footer/Back link --}}
        <div class="text-center pb-8 border-t border-gray-100 dark:border-white/5 pt-8">
            <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">
                Belum yakin? <a href="{{ route('consultation.form') }}" class="text-yellow-600 dark:text-yellow-500 font-bold hover:underline">Konsultasi gratis</a> dengan tim kami.
            </p>
            <a href="{{ route('booking.start') }}" class="text-sm font-bold text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-arrow-left text-xs"></i> Kembali Pilih Layanan
            </a>
        </div>
    </div>

    <form x-ref="applyForm" method="GET" :action="applyUrl" class="hidden">
        <input type="hidden" name="date" x-ref="applyInput">
    </form>
</div>
@endsection

@push('scripts')
<script>
function packageSelectPage(config = {}) {
    const formatLabel = (dateStr) => {
        if (!dateStr) return '-';
        const date = new Date(`${dateStr}T00:00:00`);
        if (Number.isNaN(date.getTime())) return dateStr;
        return date.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    };

    return {
        tab: config.initialTab || 'rumahan',
        initialDate: config.initialDate || '',
        selectedDate: config.initialDate || '',
        lastCheckedDate: config.initialDate || '',
        formattedDate: formatLabel(config.initialDate || ''),
        status: config.initialStatus || null,
        minDate: config.minDate || null,
        checkUrl: config.checkUrl,
        applyUrl: config.applyUrl,
        formBase: config.formUrl,
        loading: false,
        init() {
            this.$nextTick(() => this.initPicker());
            if (this.$watch) {
                this.$watch('selectedDate', value => {
                    this.formattedDate = formatLabel(value);
                    if (value !== this.lastCheckedDate) {
                        this.status = null;
                    }
                });
            }
        },
        get statusLabel() {
            if (!this.status) return 'Belum dicek';
            return this.status.label || 'Tidak diketahui';
        },
        get statusMessage() {
            if (!this.status) return 'Pilih tanggal untuk cek ketersediaan.';
            return this.status.message || '';
        },
        initPicker() {
            if (!window.flatpickr || !this.$refs.dateInput) return;
            flatpickr(this.$refs.dateInput, {
                dateFormat: 'Y-m-d',
                defaultDate: this.selectedDate,
                minDate: this.minDate,
                disableMobile: true,
                onChange: (selectedDates, dateStr) => {
                    this.selectedDate = dateStr;
                }
            });
        },
        statusBadgeContainerClass() {
            if (!this.status) return 'bg-gray-50 dark:bg-white/5 text-gray-500';
            switch (this.status?.status) {
                case 'available': return 'bg-green-50 dark:bg-green-900/10 text-green-700 dark:text-green-400 border border-green-100 dark:border-green-900/30';
                case 'tentative': return 'bg-yellow-50 dark:bg-yellow-900/10 text-yellow-700 dark:text-yellow-400 border border-yellow-100 dark:border-yellow-900/30';
                case 'full': return 'bg-red-50 dark:bg-red-900/10 text-red-700 dark:text-red-400 border border-red-100 dark:border-red-900/30';
                default: return 'bg-gray-50 dark:bg-white/5 text-gray-500';
            }
        },
        statusDotClass() {
            if (!this.status) return 'bg-gray-400';
            switch (this.status?.status) {
                case 'available': return 'bg-green-500';
                case 'tentative': return 'bg-yellow-500';
                case 'full': return 'bg-red-500';
                default: return 'bg-gray-400';
            }
        },
        async checkDate() {
            if (!this.selectedDate) return;
            this.loading = true;
            try {
                const params = new URLSearchParams({ date: this.selectedDate });
                const response = await fetch(`${this.checkUrl}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                if (!response.ok) throw new Error('Request failed');
                const data = await response.json();
                this.status = data;
                this.lastCheckedDate = this.selectedDate;
            } catch (error) {
                console.error(error);
                this.status = {
                    status: 'error',
                    label: 'Gagal Mengecek',
                    message: 'Tidak dapat mengecek ketersediaan. Silakan coba lagi.'
                };
            } finally {
                this.loading = false;
            }
        },
        packageLink(packageId) {
            const date = encodeURIComponent(this.selectedDate || this.initialDate || '');
            return `${this.formBase}?date=${date}&package_id=${packageId}`;
        }
    };
}
</script>
@endpush
