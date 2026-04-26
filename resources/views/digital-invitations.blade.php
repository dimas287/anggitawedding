@extends('layouts.guest')
@section('title', 'Undangan Digital – Anggita WO')
@section('meta_description', 'Buat undangan digital pernikahan premium dan elegan dengan Anggita Wedding Organizer. Dilengkapi fitur RSVP interaktif, galeri foto, peta lokasi, dan manajemen tamu.')

@section('content')
@php
    $maintenanceMode = (bool) \App\Models\SiteSetting::getValue('invitation_maintenance_mode', false);
@endphp

@if($maintenanceMode)
<div class="bg-red-600 text-white text-center py-3 text-sm font-bold tracking-wide relative z-50">
    <div class="max-w-6xl mx-auto px-4 flex items-center justify-center gap-3">
        <i class="fas fa-exclamation-triangle animate-pulse"></i>
        <span>Undangan Digital sedang Maintenance. Fitur booking dan pembuatan undangan dinonaktifkan sementara.</span>
    </div>
</div>
@endif

<div class="pt-28 bg-gradient-to-b from-purple-900 via-purple-800 to-white dark:to-[#0A0A0A] text-white">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
        <div class="text-center max-w-3xl mx-auto">
            <span class="text-yellow-300 text-xs font-semibold uppercase tracking-[0.5em]">Digital Invitation</span>
            <h1 class="font-playfair text-5xl font-bold mt-4 mb-4">Undangan Digital Premium</h1>
            <p class="text-white/80 text-base md:text-lg mb-8">
                Hadirkan pengalaman undangan interaktif lengkap dengan RSVP realtime, galeri foto, musik, hingga link Maps.
                Pilih template favorit dan kami akan integrasikan ke paket wedding Anda.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#template-list" class="gold-gradient text-white px-8 py-3 rounded-full font-semibold shadow-lg hover:shadow-2xl transition-all">
                    Jelajahi Template
                </a>
                <a href="{{ route('consultation.form') }}" class="px-8 py-3 rounded-full border border-white/60 text-white font-semibold hover:bg-white/10 transition-all">
                    Konsultasi Digital Invite
                </a>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-16" data-reveal>
            @php
                $statsMap = [
                    ['target' => $landingStats['events'] ?? 0, 'suffix' => '+', 'label' => 'Event Sukses'],
                    ['target' => $landingStats['clients'] ?? 0, 'suffix' => '+', 'label' => 'Pasangan Bahagia'],
                    ['target' => $landingStats['templates'] ?? 0, 'label' => 'Template Aktif'],
                    ['target' => $landingStats['years'] ?? 1, 'suffix' => '+', 'label' => 'Tahun Pengalaman'],
                ];
            @endphp
            @foreach($statsMap as $stat)
                <div class="bg-white/15 backdrop-blur rounded-2xl p-5 text-center border border-white/20 shadow-lg">
                    <p class="text-3xl font-bold text-yellow-300">
                        <span data-countup
                              data-target="{{ (float) $stat['target'] }}"
                              data-suffix="{{ $stat['suffix'] ?? '' }}"
                              data-decimals="{{ isset($stat['suffix']) ? 0 : (is_float($stat['target']) ? 1 : 0) }}">
                              0
                        </span>
                    </p>
                    <p class="text-sm text-white/70 mt-1">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div id="template-list" class="bg-white dark:bg-[#0A0A0A] py-16 transition-colors duration-500">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-yellow-600 dark:text-yellow-500 text-xs font-semibold uppercase tracking-[0.4em]">Pilihan Template</span>
            <h2 class="font-playfair text-4xl font-bold text-gray-900 dark:text-white mt-3">Temukan Gaya Undangan Anda</h2>
            <p class="text-gray-500 dark:text-gray-400 max-w-2xl mx-auto mt-4">Setiap template dapat dikustom sesuai nama pasangan, jadwal acara, dan branding warna pernikahan Anda. Booking paket wedding dapat langsung menyertakan template favorit ini.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @forelse($templates as $template)
            <div class="rounded-3xl border border-gray-100 dark:border-white/5 shadow-lg hover:shadow-2xl transition-all overflow-hidden bg-white dark:bg-gray-800/10 flex flex-col">
                <div class="relative group">
                    @if($template->preview_image)
                        <img src="{{ asset('storage/'.$template->preview_image) }}" alt="Preview {{ $template->name }}" class="w-full h-64 object-cover">
                    @elseif($template->thumbnail)
                        <img src="{{ asset('storage/'.$template->thumbnail) }}" alt="Preview {{ $template->name }}" class="w-full h-64 object-cover">
                    @else
                        <div class="h-64 flex items-center justify-center" style="background: {{ $template->secondary_color ?? '#FFFBF0' }}">
                            <div class="text-4xl" style="color: {{ $template->primary_color ?? '#D4AF37' }}">💌</div>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-black/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                </div>
                <div class="p-6 flex-1 flex flex-col">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <p class="text-xs uppercase text-gray-400 dark:text-gray-500 tracking-[0.3em]">{{ ucfirst($template->theme ?? 'Elegan') }}</p>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $template->name }}</h3>
                        </div>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $template->is_premium ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400' : 'bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400' }}">
                            {{ $template->is_premium ? 'Premium' : 'Standar' }}
                        </span>
                    </div>
                    <div class="mb-3">
                        @if($template->has_active_promo)
                            <p class="text-xs text-gray-400 line-through">{{ $template->formatted_price }}</p>
                            <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-500 flex items-center gap-2">
                                {{ $template->formatted_effective_price }}
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-pink-50 dark:bg-pink-900/20 text-pink-600 dark:text-pink-400">-{{ rtrim(rtrim($template->promo_discount_percent, '0'), '.') }}%</span>
                            </p>
                            @if($template->promo_label)
                                <p class="text-xs text-pink-600 dark:text-pink-400">{{ $template->promo_label }}</p>
                            @endif
                        @else
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $template->formatted_price }}</p>
                        @endif
                        <p class="text-xs text-gray-500 mt-1">Harga per paket undangan digital, lengkap dengan hosting dan support.</p>
                    </div>
                    <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400 flex-1">
                        <p><i class="fas fa-palette text-yellow-500 mr-2"></i>Warna utama: <span class="font-semibold text-gray-900 dark:text-gray-200">{{ $template->primary_color ?? '#D4AF37' }}</span></p>
                        <p><i class="fas fa-font text-yellow-500 mr-2"></i>Font: <span class="text-gray-900 dark:text-gray-200">{{ $template->font_family ?? 'Playfair Display' }}</span></p>
                        <p><i class="fas fa-music text-yellow-500 mr-2"></i>{{ $template->has_music ? 'Include musik latar' : 'Musik opsional' }}</p>
                        <p><i class="fas fa-share-alt text-yellow-500 mr-2"></i>Link RSVP, galeri, maps, countdown</p>
                    </div>
                    <div class="mt-6 grid grid-cols-2 gap-3">
                        @php $demoUrl = $template->demo_url; @endphp
                        <a href="{{ $demoUrl ?? '#' }}" target="{{ $demoUrl ? '_blank' : '_self' }}"
                           class="text-sm font-semibold px-4 py-2 rounded-full border {{ $demoUrl ? 'border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300 hover:border-yellow-400 dark:hover:border-yellow-500 hover:text-yellow-600 dark:hover:text-yellow-500' : 'border-dashed border-gray-300 dark:border-white/5 text-gray-400 cursor-not-allowed' }} transition-colors flex items-center justify-center gap-2"
                           {{ $demoUrl ? '' : 'aria-disabled=true' }}>
                            <i class="fas fa-eye"></i> Preview
                        </a>
                        @if($maintenanceMode)
                        <button disabled
                           class="text-sm font-semibold px-4 py-2 rounded-full bg-gray-200 text-gray-400 cursor-not-allowed flex items-center justify-center gap-2">
                            <i class="fas fa-tools"></i> Maintenance
                        </button>
                        @else
                        <a href="{{ route('invitation-order.start') }}?template_slug={{ $template->slug }}"
                           class="text-sm font-semibold px-4 py-2 rounded-full gold-gradient text-white shadow hover:shadow-lg transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-calendar-check"></i> Booking
                        </a>
                        @endif
                    </div>
                    <p class="text-[11px] text-gray-400 mt-3">Hubungi admin untuk kustomisasi, jadwal publikasi, atau kebutuhan fitur tambahan.</p>
                </div>
            </div>
            @empty
                <div class="col-span-full text-center text-gray-400">Belum ada template digital yang aktif.</div>
            @endforelse
        </div>
    </div>
</div>

<div class="bg-gray-50 dark:bg-white/5 py-16 transition-colors duration-500">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h3 class="font-playfair text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">Siap menghadirkan undangan digital yang memukau?</h3>
        <p class="text-gray-600 dark:text-gray-400 mb-8">Kami bantu siapkan konsep, copywriting, hingga publikasi dalam sekali klik. Paket wedding bisa langsung terhubung dengan template favorit Anda.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="{{ route('booking.start') }}" class="gold-gradient text-white px-8 py-3 rounded-full font-semibold shadow-lg hover:shadow-2xl transition-all">
                Pesan Paket + Undangan
            </a>
            <a href="{{ route('consultation.form') }}" class="px-8 py-3 rounded-full border-2 border-yellow-500 text-yellow-600 dark:text-yellow-500 font-semibold hover:bg-yellow-50 dark:hover:bg-yellow-900/10 transition-all">
                Konsultasi Gratis
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function ensureCountupHelper(){
    if (typeof window.initCountupElements === 'function') return;
    window.initCountupElements = function initCountupElements() {
        const counters = document.querySelectorAll('[data-countup]');
        if (!counters.length) return;
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
    };
})();

document.addEventListener('DOMContentLoaded', () => {
    initCountupElements();
});
</script>
@endpush
