@extends('layouts.guest')
@section('title', 'Form Booking – ' . $package->name)

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-[#0A0A0A] pt-24 pb-16">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('booking.select-package') }}?date={{ $date }}" class="text-yellow-600 dark:text-yellow-500 hover:underline text-sm flex items-center gap-1 mb-4">
                <i class="fas fa-arrow-left text-xs"></i> Kembali pilih paket
            </a>
            <h1 class="font-playfair text-3xl font-bold text-gray-800 dark:text-white">
                {{ $isIndividual ? 'Form Booking Layanan Personal' : 'Form Booking Pernikahan' }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                {{ $isIndividual ? 'Lengkapi data berikut untuk mengamankan slot make-up/rias Anda.' : 'Lengkapi data berikut untuk mengamankan tanggal acara Anda.' }}
            </p>
        </div>

        {{-- Package Summary --}}
        <div class="bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 border border-yellow-200 dark:border-yellow-700/40 rounded-2xl p-5 mb-6 flex items-center justify-between">
            <div>
                <p class="text-xs text-yellow-600 dark:text-yellow-500 font-semibold uppercase tracking-wider">Paket yang dipilih</p>
                <h3 class="font-bold text-gray-800 dark:text-white text-lg">{{ $package->name }}</h3>
                @if($package->hasActivePromo())
                    <div class="text-sm text-gray-400 dark:text-gray-500 line-through">{{ $package->formatted_price }}</div>
                    <p class="text-gray-900 dark:text-gray-100 font-semibold">{{ $package->formattedEffectivePrice }} • DP: Rp {{ number_format($package->dp_amount, 0, ',', '.') }}</p>
                    <p class="text-xs text-pink-600 dark:text-pink-400 font-semibold mt-1 flex items-center gap-1"><i class="fas fa-bolt"></i> {{ $package->promo_label ?? 'Promo Spesial' }} sampai {{ optional($package->promo_expires_at)->isoFormat('D MMM Y') }}</p>
                @else
                    <p class="text-gray-600 dark:text-gray-300">{{ $package->formatted_price }} • DP: Rp {{ number_format($package->dp_amount, 0, ',', '.') }}</p>
                @endif
            </div>
            <div class="w-14 h-14 rounded-2xl gold-gradient flex items-center justify-center">
                <i class="fas fa-{{ $package->tier === 'silver' ? 'medal' : ($package->tier === 'gold' ? 'crown' : 'gem') }} text-white text-xl"></i>
            </div>
        </div>

        <form action="{{ route('booking.store') }}" method="POST" class="bg-white dark:bg-[#111111] rounded-2xl shadow-sm dark:shadow-black/20 p-8 space-y-6 border border-transparent dark:border-white/10">
            @csrf
            {{-- Honeypot Anti-Spam --}}
            <input type="text" name="hp_field" style="display:none !important" tabindex="-1" autocomplete="off">
            <input type="hidden" name="package_id" value="{{ $package->id }}">
            <input type="hidden" name="event_date" value="{{ $date }}">

            @if($errors->any())
            <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/40 rounded-xl text-red-600 dark:text-red-400 text-sm">
                <ul class="space-y-1">@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            @if($isIndividual)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="client_name" value="{{ old('client_name', auth()->user()->name) }}" required
                           class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600 transition-all"
                           placeholder="Nama lengkap Anda">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Label / Peran (opsional)</label>
                    <input type="text" name="client_label" value="{{ old('client_label', $package->category_label) }}"
                           class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600 transition-all"
                           placeholder="Contoh: Wisudawati, Bridesmaid, Pagar Ayu">
                </div>
            </div>
            @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Pengantin Pria <span class="text-red-500">*</span></label>
                    <input type="text" name="groom_name" value="{{ old('groom_name') }}" required
                           class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600 transition-all"
                           placeholder="Nama lengkap pengantin pria">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Pengantin Wanita <span class="text-red-500">*</span></label>
                    <input type="text" name="bride_name" value="{{ old('bride_name') }}" required
                           class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600 transition-all"
                           placeholder="Nama lengkap pengantin wanita">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Singkat Pengantin Pria <span class="text-red-500">*</span></label>
                    <input type="text" name="groom_short_name" value="{{ old('groom_short_name') }}" required
                           class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600 transition-all"
                           placeholder="Contoh: Bagas">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Nama panggilan untuk rundown, undangan, dan koordinasi internal.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Singkat Pengantin Wanita <span class="text-red-500">*</span></label>
                    <input type="text" name="bride_short_name" value="{{ old('bride_short_name') }}" required
                           class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600 transition-all"
                           placeholder="Contoh: Rani">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Digunakan WO untuk koordinasi dan materi publikasi.</p>
                </div>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Acara <span class="text-red-500">*</span></label>
                    <input type="text" value="{{ \Carbon\Carbon::parse($date)->isoFormat('DD/MM/YYYY') }}" readonly
                           class="w-full border border-gray-100 dark:border-white/10 bg-gray-50 dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-700 dark:text-gray-300 cursor-not-allowed"
                           aria-readonly="true">
                    @php
                        $statusColors = ['available' => 'green', 'tentative' => 'yellow', 'full' => 'red'];
                        $currentStatus = $dateAvailability['status'] ?? 'available';
                        $color = $statusColors[$currentStatus] ?? 'gray';
                    @endphp
                    <div class="mt-2 text-xs border border-{{ $color }}-200 dark:border-{{ $color }}-700/40 bg-{{ $color }}-50 dark:bg-{{ $color }}-900/20 text-{{ $color }}-700 dark:text-{{ $color }}-400 rounded-lg px-3 py-2 flex items-start gap-2">
                        <i class="fas {{ $currentStatus === 'full' ? 'fa-times-circle' : ($currentStatus === 'tentative' ? 'fa-clock' : 'fa-check-circle') }} mt-0.5"></i>
                        <div>
                            <p class="font-semibold">{{ $dateAvailability['label'] ?? 'Tanggal Tersedia' }}</p>
                            <p>{{ $dateAvailability['message'] ?? 'Tanggal ini sudah dikunci untuk Anda.' }}</p>
                        </div>
                    </div>
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-2">Butuh ubah tanggal? Kembali ke halaman pilih paket & tanggal.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">No. WhatsApp <span class="text-red-500">*</span></label>
                    <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required
                           class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600 transition-all"
                           placeholder="081xxxxxxxxx">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama/Lokasi Venue <span class="text-red-500">*</span></label>
                <input type="text" name="venue" value="{{ old('venue') }}" required
                       class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600 transition-all"
                       placeholder="Nama gedung/tempat acara">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Alamat Lengkap Venue</label>
                <textarea name="venue_address" rows="2"
                          class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600 transition-all resize-none"
                          placeholder="Alamat lengkap lokasi acara">{{ old('venue_address') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Preferensi Konsultasi</label>
                <select name="consultation_preference"
                        class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600 transition-all">
                    <option value="" class="dark:bg-[#111111]">Pilih...</option>
                    <option value="online" class="dark:bg-[#111111]" {{ old('consultation_preference') === 'online' ? 'selected' : '' }}>Online (Video Call)</option>
                    <option value="offline" class="dark:bg-[#111111]" {{ old('consultation_preference') === 'offline' ? 'selected' : '' }}>Offline (Kunjungi Kantor)</option>
                    <option value="whatsapp" class="dark:bg-[#111111]" {{ old('consultation_preference') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Catatan Tambahan</label>
                <textarea name="notes" rows="3"
                          class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600 transition-all resize-none"
                          placeholder="Ceritakan keinginan, tema, atau permintaan khusus Anda...">{{ old('notes') }}</textarea>
            </div>

            <div class="bg-yellow-50 dark:bg-yellow-900/15 border border-yellow-200 dark:border-yellow-700/40 rounded-xl p-4 text-sm text-yellow-800 dark:text-yellow-300">
                <p class="font-semibold mb-1"><i class="fas fa-info-circle mr-1"></i> Informasi Pembayaran DP</p>
                <p>Setelah submit, Anda akan diarahkan ke halaman pembayaran DP sebesar <strong>Rp {{ number_format($package->dp_amount, 0, ',', '.') }}</strong> (30% dari total harga paket). Pembayaran melalui Midtrans (transfer bank, e-wallet, dll).</p>
            </div>

            <button type="submit" class="w-full gold-gradient text-white font-bold py-4 rounded-xl text-sm hover:shadow-lg transition-all">
                <i class="fas fa-calendar-check mr-2"></i> Booking Sekarang
            </button>
        </form>
    </div>
</div>
@endsection
