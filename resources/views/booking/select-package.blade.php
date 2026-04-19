@extends('layouts.guest')
@section('title', 'Pilih Paket Wedding')

@push('head')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@php
    $initialTab = $packagesByCategory->keys()->first() ?? 'rumahan';
@endphp

@section('content')
<div class="min-h-screen pt-28 pb-16 bg-transparent"
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
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="text-yellow-600 dark:text-yellow-500 text-xs font-semibold uppercase tracking-[0.4em]">Langkah 1</span>
            <h1 class="font-playfair text-4xl font-bold text-gray-900 dark:text-white mt-3">Pilih Paket Wedding</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-2">Tanggal acara:
                <strong class="text-yellow-600" x-text="formattedDate">{{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM Y') }}</strong>
            </p>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Paket menyesuaikan gaya perayaan Anda.</p>
        </div>

        <div class="bg-white/90 dark:bg-[#111111]/90 rounded-3xl border border-gray-100 dark:border-white/10 shadow-lg dark:shadow-black/20 p-6 md:p-8 mb-12">
            <div class="grid gap-6 lg:gap-10 md:grid-cols-[minmax(0,360px)_minmax(0,1fr)] items-start">
                <div class="space-y-4">
                    <label class="text-xs uppercase tracking-[0.3em] text-gray-500 dark:text-gray-400 font-semibold">Ganti Tanggal</label>
                    <div class="flex flex-col gap-3">
                        <input type="text" x-ref="dateInput" x-model="selectedDate" placeholder="Pilih tanggal"
                               data-flatpickr
                               :data-min-date="minDate"
                               class="w-full bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:border-yellow-500 focus:ring-0" />
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="button" @click="checkDate" :disabled="!selectedDate || loading"
                                    class="w-full sm:w-auto px-4 py-3 rounded-2xl text-sm font-semibold border border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300 hover:border-yellow-500 hover:text-yellow-600 dark:hover:text-yellow-500 transition disabled:opacity-50">
                                <span x-show="!loading">Cek Tanggal</span>
                                <span x-show="loading"><i class="fas fa-spinner fa-spin"></i></span>
                            </button>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-white/5 border border-dashed border-gray-200 dark:border-white/10 rounded-2xl p-5 h-full self-stretch flex flex-col justify-center">
                    <p class="text-xs uppercase tracking-[0.3em] text-gray-500 dark:text-gray-400 font-semibold mb-3">Status Ketersediaan</p>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                        <div class="px-4 py-2 rounded-full border text-xs font-semibold" :class="statusBadgeClass()" x-text="statusLabel"></div>
                        <p class="text-sm text-gray-600 dark:text-gray-400" x-text="statusMessage"></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-3 justify-center mb-10">
            @foreach($categoryLabels as $key => $label)
                @if(isset($packagesByCategory[$key]) && $packagesByCategory[$key]->isNotEmpty())
                    <button @click="tab='{{ $key }}'" type="button"
                        :class="tab === '{{ $key }}' ? 'gold-gradient text-white shadow-lg' : 'bg-white dark:bg-[#111111] text-gray-600 dark:text-gray-300 border dark:border-white/10'"
                        class="px-5 py-2 rounded-full text-sm font-semibold transition-all border border-transparent">
                        {{ $label }}
                    </button>
                @endif
            @endforeach
        </div>

        @foreach($packagesByCategory as $category => $list)
        <div x-show="tab === '{{ $category }}'" x-cloak>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($list as $package)
                @php
                    $categoryPopularId = $popularPackageIdsByCategory[$category] ?? null;
                    $isPopular = $categoryPopularId === $package->id;
                @endphp
                <div class="bg-white/90 dark:bg-[#111111]/90 backdrop-blur rounded-3xl shadow-lg hover:shadow-2xl dark:shadow-black/20 transition-all overflow-hidden flex flex-col relative border border-white/60 dark:border-white/10">
                    @if($isPopular)
                    <div class="gold-gradient text-white text-center py-2 text-xs font-bold uppercase tracking-wider flex items-center justify-center gap-2">
                        <i class="fas fa-star"></i> Paket Terfavorit
                    </div>
                    @endif
                    <div class="p-7 flex-1 flex flex-col gap-5">
                        <div class="text-center space-y-3">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-semibold uppercase bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300">
                                <i class="fas fa-map-marker-alt text-yellow-500"></i> {{ $package->category_label }}
                            </span>
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold uppercase
                                {{ $package->tier === 'silver' ? 'bg-gray-100 text-gray-600' : ($package->tier === 'gold' ? 'bg-yellow-100 text-yellow-700' : 'bg-purple-100 text-purple-700') }}">
                                <i class="fas {{ $package->tier === 'gold' ? 'fa-crown' : ($package->tier === 'silver' ? 'fa-medal' : 'fa-gem') }}"></i> {{ ucfirst($package->tier ?? 'Premium') }}
                            </span>
                            @if($package->hasActivePromo())
                                <span class="inline-flex items-center gap-2 px-4 py-1 rounded-full text-xs font-semibold uppercase bg-pink-50 text-pink-600">
                                    <i class="fas fa-bolt"></i> {{ $package->promo_label ?? 'Promo Spesial' }}
                                </span>
                            @endif
                            <h3 class="font-playfair text-3xl font-bold text-gray-900 dark:text-white">{{ $package->name }}</h3>
                            <div class="space-y-1">
                                @if($package->hasActivePromo())
                                    <div class="text-sm text-gray-400 line-through">{{ $package->formatted_price }}</div>
                                    <div class="text-4xl font-bold text-yellow-600">{{ $package->formattedEffectivePrice }}</div>
                                @else
                                    <div class="text-4xl font-bold text-yellow-600">{{ $package->formatted_price }}</div>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">DP: Rp {{ number_format($package->dp_amount, 0, ',', '.') }}</p>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 text-center">{{ $package->description }}</p>

                        @if($package->has_digital_invitation)
                        <div class="flex items-center justify-center gap-2 text-yellow-700 bg-yellow-50 border border-yellow-100 rounded-xl py-2 text-xs font-semibold shadow-sm">
                            <i class="fas fa-envelope-open-text"></i>
                            Termasuk Undangan Digital
                        </div>
                        @endif

                        @php $sections = $package->feature_sections; @endphp
                        <div class="grid grid-cols-1 gap-3 mb-6 flex-1">
                            @forelse($sections as $section)
                                <div class="rounded-2xl border border-gray-100 dark:border-white/10 bg-white/70 dark:bg-white/5 p-3 flex flex-col gap-2 min-h-[160px]">
                                    @if($section['title'])
                                        <p class="text-[11px] uppercase tracking-wide text-gray-500 dark:text-gray-400 font-semibold">{{ $section['title'] }}</p>
                                    @endif
                                    <ul class="space-y-1.5 text-sm text-gray-700 dark:text-gray-300 max-h-56 overflow-y-auto pr-1">
                                        @foreach($section['items'] as $item)
                                        <li class="flex items-start gap-2">
                                            <span class="w-4.5 h-4.5 rounded-full bg-yellow-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <i class="fas fa-check text-yellow-600 text-[9px]"></i>
                                            </span>
                                            <span class="break-words">{{ $item }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @empty
                                <div class="rounded-2xl border border-dashed border-gray-200 dark:border-white/10 p-4 text-center text-xs text-gray-400 dark:text-gray-500">
                                    Belum ada fitur yang ditambahkan.
                                </div>
                            @endforelse
                        </div>
                        <a href="{{ route('booking.form') }}?date={{ $date }}&package_id={{ $package->id }}"
                           :href="packageLink({{ $package->id }})"
                           class="block w-full text-center py-3.5 rounded-2xl font-semibold text-sm transition-all gold-gradient text-white hover:shadow-2xl">
                            <i class="fas fa-calendar-check mr-2"></i> Pilih Paket Ini
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach

        <div class="text-center mt-10">
            <p class="text-gray-500 dark:text-gray-400 text-sm">Belum yakin? <a href="{{ route('consultation.form') }}" class="text-yellow-600 dark:text-yellow-500 font-semibold hover:underline">Konsultasi gratis dulu</a> dengan tim kami.</p>
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
