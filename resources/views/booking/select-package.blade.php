@php
    $layout = Auth::check() && !Auth::user()->isAdmin() ? 'layouts.app' : 'layouts.guest';
    $isApp = $layout === 'layouts.app';
    $initialTab = $packagesByCategory->keys()->first() ?? 'rumahan';
@endphp

@extends($layout)

@section('title', 'Pilih Paket Wedding – Anggita WO')

@push('head')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="{{ $isApp ? 'py-8 px-4' : 'min-h-screen pt-28 pb-20' }} bg-white dark:bg-[#0A0A0A] transition-colors duration-500"
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
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Header --}}
        <div class="text-center mb-16">
            <span class="text-gray-400 dark:text-gray-500 text-[10px] font-bold uppercase tracking-[0.35em] mb-4 block">Reservation Step 1</span>
            <h1 class="font-playfair text-4xl lg:text-5xl font-light text-gray-900 dark:text-white leading-tight">Pilih Paket Wedding</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-4 text-sm max-w-md mx-auto leading-relaxed">
                Tanggal acara: <strong class="text-yellow-600 dark:text-yellow-500" x-text="formattedDate">{{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM Y') }}</strong>
            </p>
        </div>

        {{-- Date Checker (Landing Page Style) --}}
        <div class="mb-16 max-w-xl mx-auto">
            <div class="bg-gray-50 dark:bg-black/40 backdrop-blur-md border border-gray-200 dark:border-white/10 p-6 rounded-xl">
                <p class="text-gray-400 dark:text-white/70 text-[10px] uppercase tracking-[0.25em] font-medium mb-4 text-center sm:text-left">Ganti Tanggal Acara</p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <input type="text" x-ref="dateInput" x-model="selectedDate" placeholder="Pilih tanggal"
                           data-flatpickr :data-min-date="minDate"
                           class="flex-1 bg-white dark:bg-transparent border border-gray-200 dark:border-none dark:border-b dark:border-white/20 text-gray-900 dark:text-white rounded-lg sm:rounded-none px-4 sm:px-2 py-3 sm:py-2 text-sm placeholder-gray-400 dark:placeholder-white/40 focus:outline-none focus:border-yellow-500 dark:focus:border-white transition-colors" />
                    
                    <button @click="checkDate()" :disabled="!selectedDate || loading"
                            class="bg-gray-900 dark:bg-white text-white dark:text-black px-8 py-3 rounded-lg sm:rounded-none text-[10px] font-bold tracking-widest uppercase hover:bg-black dark:hover:bg-gray-200 transition-all disabled:opacity-50 shrink-0">
                        <span x-show="!loading">Cek Ketersediaan</span>
                        <span x-show="loading"><i class="fas fa-spinner fa-spin"></i></span>
                    </button>
                </div>
                
                <div x-show="status" x-cloak class="mt-4 p-4 rounded-lg text-xs"
                     :class="status?.status === 'available' ? 'bg-green-500/10 text-green-700 dark:text-green-300 border-l-2 border-green-500' :
                             status?.status === 'tentative' ? 'bg-yellow-500/10 text-yellow-700 dark:text-yellow-300 border-l-2 border-yellow-500' :
                             'bg-red-500/10 text-red-700 dark:text-red-300 border-l-2 border-red-500'">
                    <p class="flex items-center gap-2">
                        <i :class="status?.status === 'available' ? 'fa-check' : status?.status === 'tentative' ? 'fa-clock' : 'fa-times'" class="fas"></i>
                        <strong x-text="status?.label"></strong> – <span x-text="status?.message"></span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Categories (Landing Page Style) --}}
        <div class="flex flex-wrap gap-6 justify-center mb-16">
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

        {{-- Packages Grid --}}
        @foreach($packagesByCategory as $category => $list)
            <div x-show="tab === '{{ $category }}'" x-cloak>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($list as $package)
                        @php
                            $categoryPopularId = $popularPackageIdsByCategory[$category] ?? null;
                            $isPopular = $categoryPopularId === $package->id;
                        @endphp
                        <div class="bg-[#FAF9F6] dark:bg-[#111111] rounded-xl hover:shadow-xl dark:hover:shadow-black/30 transition-all overflow-hidden relative border border-gray-100/50 dark:border-white/10 flex flex-col">
                            @if($isPopular)
                                <div class="bg-gray-900 dark:bg-gray-800 text-white text-center py-2 text-[10px] font-medium uppercase tracking-[0.2em]">
                                    Signature Choice
                                </div>
                            @endif
                            
                            <div class="p-8 pb-10 flex flex-col flex-1">
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

                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6 text-center leading-relaxed">{{ $package->description }}</p>

                                @if($package->has_digital_invitation)
                                <div class="flex items-center justify-center gap-2 text-yellow-700 dark:text-yellow-500 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-100 dark:border-yellow-800/30 rounded-xl py-2.5 text-xs font-semibold mb-6 shadow-sm">
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
                                                    @php
                                                        $trimmedItem = trim($item);
                                                        $isSubheading = str_starts_with($trimmedItem, '##');
                                                        $cleanItem = $isSubheading ? trim(preg_replace('/^##\s*/', '', $trimmedItem)) : $item;
                                                    @endphp

                                                    @if($isSubheading)
                                                        <li class="pt-3 pb-1 first:pt-0">
                                                            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-amber-600 dark:text-amber-500 border-b border-gray-100 dark:border-gray-700/50 pb-1 mb-0.5">{{ $cleanItem }}</p>
                                                        </li>
                                                    @else
                                                        <li class="flex items-start gap-2">
                                                            <span class="w-5 h-5 rounded-full bg-yellow-100 dark:bg-yellow-900/40 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                                <i class="fas fa-check text-yellow-600 dark:text-yellow-500 text-[10px]"></i>
                                                            </span>
                                                            <span class="break-words">{{ $item }}</span>
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

                                <div class="mt-auto">
                                    <a href="{{ route('booking.form') }}?date={{ $date }}&package_id={{ $package->id }}"
                                       :href="packageLink({{ $package->id }})"
                                       class="block w-full text-center py-4 rounded-lg bg-gray-900 dark:bg-yellow-600 text-white font-medium text-[11px] tracking-[0.2em] uppercase hover:bg-black dark:hover:bg-yellow-700 transition-colors border border-gray-900 dark:border-yellow-600">
                                        Pilih Paket Ini
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <div class="text-center mt-20">
            <p class="text-gray-500 dark:text-gray-400 text-sm">Butuh bantuan memilih? <a href="{{ route('consultation.form') }}" class="text-gray-900 dark:text-white font-semibold hover:underline border-b border-gray-900 dark:border-white">Konsultasi Gratis</a></p>
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
            if (!this.status) return 'Pilih tanggal dan tekan cek untuk melihat ketersediaan.';
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
        statusBadgeClass() {
            switch (this.status?.status) {
                case 'available':
                    return 'bg-green-100 text-green-700 border-green-200';
                case 'tentative':
                    return 'bg-yellow-100 text-yellow-700 border-yellow-200';
                case 'full':
                    return 'bg-red-100 text-red-700 border-red-200';
                default:
                    return 'bg-gray-100 text-gray-600 border-gray-200';
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
        applyDate() {
            if (!this.selectedDate || !this.$refs.applyForm) return;
            this.$refs.applyInput.value = this.selectedDate;
            this.$refs.applyForm.submit();
        },
        packageLink(packageId) {
            const date = encodeURIComponent(this.selectedDate || this.initialDate || '');
            return `${this.formBase}?date=${date}&package_id=${packageId}`;
        }
    };
}
</script>
@endpush
