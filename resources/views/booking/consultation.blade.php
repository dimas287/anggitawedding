@php
    $layout = Auth::check() && !Auth::user()->isAdmin() ? 'layouts.app' : 'layouts.guest';
    $isApp = $layout === 'layouts.app';
@endphp

@extends($layout)

@section('title', 'Booking Konsultasi Gratis')
@section('meta_description', 'Jadwalkan konsultasi gratis dengan tim Anggita Wedding Organizer. Kami siap mendengarkan impian pernikahan Anda dan memberikan solusi terbaik.')

@section('content')
<div class="{{ $isApp ? 'py-8' : 'min-h-screen pt-28 pb-16' }} dark:bg-[#0A0A0A]">
    <div class="max-w-2xl mx-auto px-4">
        {{-- Header Section --}}
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-3xl bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-500 mb-6 shadow-xl shadow-yellow-400/10">
                <i class="fas fa-comments text-2xl"></i>
            </div>
            
            <a href="javascript:history.back()" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 text-[10px] font-bold uppercase tracking-widest hover:text-gray-900 dark:hover:text-white transition-all mb-4">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            
            <h1 class="font-playfair text-4xl font-bold text-gray-900 dark:text-white">Konsultasi Gratis</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 text-sm leading-relaxed max-w-md mx-auto">
                Ceritakan impian pernikahan Anda. Tim pakar kami siap mendengarkan dan memberikan solusi terbaik tanpa biaya apapun.
            </p>
        </div>

        {{-- Form Card --}}
        <div class="bg-white dark:bg-white/5 rounded-[40px] border border-gray-100 dark:border-white/10 shadow-2xl p-8 md:p-10 relative overflow-hidden">
            {{-- Decorative Orbs --}}
            <div class="absolute -top-20 -right-20 w-40 h-40 bg-yellow-400/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-40 h-40 bg-purple-500/10 rounded-full blur-3xl"></div>

            @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/30 rounded-2xl text-red-600 dark:text-red-400 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/30 rounded-2xl text-red-600 dark:text-red-400 text-sm">
                <ul class="space-y-1">@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <form action="{{ route('consultation.store') }}" method="POST" class="space-y-8 relative z-10">
                @csrf
                {{-- Honeypot Anti-Spam --}}
                <input type="text" name="hp_field" style="display:none !important" tabindex="-1" autocomplete="off">
                
                <div class="space-y-6">
                    {{-- Personal Data --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all"
                                   placeholder="Contoh: Dimas Aditya">
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">No. WhatsApp <span class="text-red-500">*</span></label>
                            <input type="tel" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" required
                                   class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all"
                                   placeholder="081xxxxxxxxx">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required
                               class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all"
                               placeholder="email@example.com">
                    </div>

                    <div class="w-full h-px bg-gray-100 dark:bg-white/5"></div>

                    {{-- Schedule Data --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">Tanggal Konsultasi <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i class="far fa-calendar-alt absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                <input type="text" name="preferred_date" value="{{ old('preferred_date', $date) }}" required
                                       data-flatpickr data-min-date="{{ date('Y-m-d') }}" placeholder="Pilih tanggal"
                                       class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl pl-12 pr-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all">
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">Jam Konsultasi <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <i class="far fa-clock absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                <select name="preferred_time" required
                                        class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl pl-12 pr-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all appearance-none">
                                    <option value="" class="dark:bg-[#151515]">Pilih jam...</option>
                                    @foreach(['09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'] as $t)
                                    <option value="{{ $t }}" class="dark:bg-[#151515]" {{ old('preferred_time') === $t ? 'selected' : '' }}>{{ $t }} WIB</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">Jenis Konsultasi <span class="text-red-500">*</span></label>
                            <select name="consultation_type" required
                                    class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all appearance-none">
                                <option value="offline" class="dark:bg-[#151515]" {{ old('consultation_type') === 'offline' ? 'selected' : '' }}>Offline (Kunjungi Kantor)</option>
                                <option value="online" class="dark:bg-[#151515]" {{ old('consultation_type') === 'online' ? 'selected' : '' }}>Online (Video Call/Meet)</option>
                            </select>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">Rencana Tanggal Menikah</label>
                            <div class="relative">
                                <i class="fas fa-heart absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none"></i>
                                <input type="text" name="event_date" value="{{ old('event_date') }}"
                                       data-flatpickr data-min-date="{{ date('Y-m-d', strtotime('+1 month')) }}" placeholder="Opsional"
                                       class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl pl-12 pr-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all">
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-xs font-bold text-gray-500 dark:text-gray-400 ml-1">Ceritakan Impian Pernikahan Anda</label>
                        <textarea name="message" rows="4"
                                  class="w-full bg-gray-50 dark:bg-white/5 border border-gray-200 dark:border-white/10 rounded-2xl px-5 py-4 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-yellow-400/20 transition-all resize-none"
                                  placeholder="Tema yang diinginkan, estimasi tamu, budget, atau pertanyaan khusus...">{{ old('message') }}</textarea>
                    </div>
                </div>

                @guest
                <div class="bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/30 rounded-2xl p-6 text-sm text-blue-700 dark:text-blue-400">
                    <div class="flex gap-3">
                        <i class="fas fa-info-circle mt-0.5"></i>
                        <p>Anda akan diminta untuk membuat akun atau login setelah mengisi form ini agar kami dapat memberikan layanan personal yang lebih baik.</p>
                    </div>
                </div>
                @endguest

                <button type="submit" class="w-full gold-gradient text-white font-bold py-5 rounded-2xl text-base hover:shadow-2xl hover:scale-[1.02] active:scale-[0.98] transition-all duration-300">
                    <i class="fas fa-calendar-check mr-2"></i> Jadwalkan Sekarang
                </button>
            </form>
        </div>

        {{-- Features / Social Proof --}}
        <div class="mt-12 grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div class="bg-white dark:bg-white/5 rounded-2xl p-4 border border-gray-100 dark:border-white/5 text-center">
                <i class="fas fa-clock text-yellow-600 mb-2"></i>
                <p class="text-[10px] font-bold text-gray-900 dark:text-white uppercase tracking-widest">Respon Cepat</p>
            </div>
            <div class="bg-white dark:bg-white/5 rounded-2xl p-4 border border-gray-100 dark:border-white/5 text-center">
                <i class="fas fa-user-tie text-yellow-600 mb-2"></i>
                <p class="text-[10px] font-bold text-gray-900 dark:text-white uppercase tracking-widest">Pakar WO</p>
            </div>
            <div class="bg-white dark:bg-white/5 rounded-2xl p-4 border border-gray-100 dark:border-white/5 text-center col-span-2 sm:col-span-1">
                <i class="fas fa-hand-holding-heart text-yellow-600 mb-2"></i>
                <p class="text-[10px] font-bold text-gray-900 dark:text-white uppercase tracking-widest">100% Gratis</p>
            </div>
        </div>
    </div>
</div>
@endsection
            <div class="bg-white dark:bg-[#151515] rounded-xl px-4 py-3 shadow-sm border border-gray-100 dark:border-white/5 flex items-center gap-2 text-gray-500 dark:text-gray-400"><i class="fas fa-clock text-blue-500"></i> Cepat</div>
            <div class="bg-white dark:bg-[#151515] rounded-xl px-4 py-3 shadow-sm border border-gray-100 dark:border-white/5 flex items-center gap-2 text-gray-500 dark:text-gray-400"><i class="fas fa-shield-alt text-purple-500"></i> No Commit</div>
        </div>
    </div>
</div>
@endsection
