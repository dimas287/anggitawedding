@php
    $layout = Auth::check() && !Auth::user()->isAdmin() ? 'layouts.app' : 'layouts.guest';
    $isApp = $layout === 'layouts.app';
@endphp

@extends($layout)

@section('title', 'Form Booking – ' . $package->name)

@section('content')
<div class="{{ $isApp ? 'py-8' : 'min-h-screen pt-28 pb-16' }} dark:bg-[#0A0A0A]">
    <div class="max-w-3xl mx-auto px-4">
        {{-- Progress Stepper --}}
        <div class="flex items-center justify-center mb-10">
            <div class="flex items-center gap-4">
                <div class="flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-2xl bg-green-500 text-white flex items-center justify-center shadow-lg font-bold">
                        <i class="fas fa-check"></i>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-green-600 dark:text-green-500">Paket</span>
                </div>
                <div class="w-12 h-px bg-green-500 -mt-5"></div>
                <div class="flex flex-col items-center gap-2">
                    <div class="w-10 h-10 rounded-2xl gold-gradient text-white flex items-center justify-center shadow-lg font-bold">2</div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-yellow-600 dark:text-yellow-500">Data</span>
                </div>
                <div class="w-12 h-px bg-gray-200 dark:bg-white/10 -mt-5"></div>
                <div class="flex flex-col items-center gap-2 opacity-50">
                    <div class="w-10 h-10 rounded-2xl bg-white dark:bg-white/5 border border-gray-200 dark:border-white/10 text-gray-400 flex items-center justify-center font-bold">3</div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Bayar</span>
                </div>
            </div>
        </div>

        {{-- Header --}}
        <div class="mb-10 text-center">
            <a href="{{ route('booking.select-package') }}?date={{ $date }}" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 text-[10px] font-bold uppercase tracking-widest hover:text-gray-900 dark:hover:text-white transition-all mb-4">
                <i class="fas fa-arrow-left"></i> Kembali Pilih Paket
            </a>
            <h1 class="font-playfair text-4xl font-bold text-gray-900 dark:text-white">Lengkapi Data</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm">Sedikit lagi untuk mengamankan tanggal spesial Anda.</p>
        </div>

        {{-- Selected Package Info --}}
        <div class="relative bg-white dark:bg-white/5 rounded-[32px] p-6 mb-8 border border-gray-100 dark:border-white/10 overflow-hidden shadow-xl">
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-yellow-400/10 rounded-full blur-2xl"></div>
            
            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-widest text-yellow-600 dark:text-yellow-500">Paket yang dipilih</span>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mt-1">{{ $package->name }}</h3>
                    <div class="mt-2 flex items-center gap-3">
                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $package->hasActivePromo() ? $package->formattedEffectivePrice : $package->formatted_price }}</span>
                        <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-700"></span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">DP: Rp {{ number_format($package->dp_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 py-3 px-5 bg-yellow-50 dark:bg-yellow-900/20 rounded-2xl border border-yellow-100 dark:border-yellow-800/30">
                    <i class="far fa-calendar-alt text-yellow-600"></i>
                    <div class="text-left">
                        <p class="text-[9px] font-bold uppercase tracking-widest text-yellow-600/70">Tanggal Acara</p>
                        <p class="text-xs font-bold text-yellow-700 dark:text-yellow-500">{{ \Carbon\Carbon::parse($date)->isoFormat('D MMMM Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('booking.store') }}" method="POST" class="bg-white dark:bg-white/5 rounded-[40px] border border-gray-100 dark:border-white/10 shadow-2xl p-8 md:p-12 space-y-10 relative overflow-hidden">
            @csrf
            {{-- Honeypot Anti-Spam --}}
            <input type="text" name="hp_field" style="display:none !important" tabindex="-1" autocomplete="off">
            <input type="hidden" name="package_id" value="{{ $package->id }}">
            <input type="hidden" name="event_date" value="{{ $date }}">

            @if($errors->any())
            <div class="p-5 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/30 rounded-2xl text-red-600 dark:text-red-400 text-sm">
                <div class="flex items-center gap-2 mb-2 font-bold uppercase tracking-widest text-[10px]">
                    <i class="fas fa-exclamation-circle"></i> Perhatian
                </div>
                <ul class="space-y-1">@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <div class="space-y-8">
                {{-- Names Section --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-4 bg-yellow-500 rounded-full"></div>
                        <h4 class="text-sm font-bold uppercase tracking-widest text-gray-900 dark:text-white">Data Mempelai</h4>
                    </div>
                    
                    @if($isIndividual)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="client_name" value="{{ old('client_name', auth()->user()->name) }}" required
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all"
                                   placeholder="Nama lengkap Anda">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">Label / Peran</label>
                            <input type="text" name="client_label" value="{{ old('client_label', $package->category_label) }}"
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all"
                                   placeholder="Contoh: Wisudawati, Bridesmaid">
                        </div>
                    </div>
                    @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">Mempelai Pria <span class="text-red-500">*</span></label>
                            <input type="text" name="groom_name" value="{{ old('groom_name') }}" required
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all"
                                   placeholder="Nama lengkap pengantin pria">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">Mempelai Wanita <span class="text-red-500">*</span></label>
                            <input type="text" name="bride_name" value="{{ old('bride_name') }}" required
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all"
                                   placeholder="Nama lengkap pengantin wanita">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">Nama Panggilan (Pria) <span class="text-red-500">*</span></label>
                            <input type="text" name="groom_short_name" value="{{ old('groom_short_name') }}" required
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all"
                                   placeholder="Contoh: Bagas">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">Nama Panggilan (Wanita) <span class="text-red-500">*</span></label>
                            <input type="text" name="bride_short_name" value="{{ old('bride_short_name') }}" required
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all"
                                   placeholder="Contoh: Rani">
                        </div>
                    </div>
                    @endif
                </div>

                <div class="w-full h-px bg-gray-100 dark:bg-white/5"></div>

                {{-- Venue Section --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-4 bg-yellow-500 rounded-full"></div>
                        <h4 class="text-sm font-bold uppercase tracking-widest text-gray-900 dark:text-white">Detail Lokasi</h4>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">No. WhatsApp <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all"
                                   placeholder="081xxxxxxxxx">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">Nama Venue <span class="text-red-500">*</span></label>
                            <input type="text" name="venue" value="{{ old('venue') }}" required
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all"
                                   placeholder="Nama gedung/tempat acara">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">Alamat Lengkap Venue</label>
                        <textarea name="venue_address" rows="2"
                                  class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all resize-none"
                                  placeholder="Alamat lengkap lokasi acara">{{ old('venue_address') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">Preferensi Konsultasi</label>
                            <select name="consultation_preference"
                                    class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all">
                                <option value="" class="dark:bg-[#151515]">Pilih...</option>
                                <option value="online" class="dark:bg-[#151515]" {{ old('consultation_preference') === 'online' ? 'selected' : '' }}>Online (Video Call)</option>
                                <option value="offline" class="dark:bg-[#151515]" {{ old('consultation_preference') === 'offline' ? 'selected' : '' }}>Offline (Kunjungi Kantor)</option>
                                <option value="whatsapp" class="dark:bg-[#151515]" {{ old('consultation_preference') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="w-full h-px bg-gray-100 dark:bg-white/5"></div>

                {{-- Additional Section --}}
                <div class="space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-1 h-4 bg-yellow-500 rounded-full"></div>
                        <h4 class="text-sm font-bold uppercase tracking-widest text-gray-900 dark:text-white">Informasi Tambahan</h4>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">Catatan Tambahan</label>
                        <textarea name="notes" rows="4"
                                  class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all resize-none"
                                  placeholder="Ceritakan keinginan, tema, atau permintaan khusus Anda...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Footer Info --}}
            <div class="bg-yellow-50 dark:bg-yellow-900/10 border border-yellow-100 dark:border-yellow-800/30 rounded-2xl p-6">
                <div class="flex gap-4">
                    <div class="w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-800/30 flex items-center justify-center shrink-0 text-yellow-600 dark:text-yellow-500">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div>
                        <h5 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Informasi Pembayaran DP</h5>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Setelah submit, Anda akan diarahkan ke halaman pembayaran DP sebesar <span class="font-bold text-yellow-600 dark:text-yellow-500">Rp {{ number_format($package->dp_amount, 0, ',', '.') }}</span> untuk mengunci tanggal acara.</p>
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full gold-gradient text-white font-bold py-5 rounded-2xl text-base hover:shadow-2xl hover:scale-[1.02] active:scale-[0.98] transition-all duration-300">
                <i class="fas fa-calendar-check mr-2"></i> Lanjutkan Ke Pembayaran
            </button>
        </form>
    </div>
</div>
@endsection
