@php
    $layout = Auth::check() && !Auth::user()->isAdmin() ? 'layouts.app' : 'layouts.guest';
    $isApp = $layout === 'layouts.app';
@endphp
@extends($layout)
@section('title', 'Booking Konsultasi Gratis')
@section('meta_description', 'Jadwalkan konsultasi gratis dengan tim Anggita Wedding Organizer. Kami siap mendengarkan impian pernikahan Anda dan memberikan solusi terbaik.')

@section('content')
<div class="{{ $isApp ? 'py-4 px-2' : 'min-h-screen pt-24 pb-16 px-2' }} bg-gray-50 dark:bg-[#0A0A0A]">
    <div class="max-w-md mx-auto px-2">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center mb-4">
                <i class="fas fa-comments text-gray-400 dark:text-white text-3xl"></i>
            </div>
            <h1 class="font-playfair text-3xl font-bold text-gray-900 dark:text-white">Konsultasi Gratis</h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-3 px-2 leading-relaxed">Ceritakan impian pernikahan Anda. Kami siap membantu tanpa biaya apapun!</p>
        </div>

        <div class="bg-white dark:bg-[#111111] rounded-[24px] shadow-xl p-6 md:p-8 border border-gray-200 dark:border-white/5">
            @if(session('error'))<div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/40 rounded-xl text-red-600 dark:text-red-400 text-sm">{{ session('error') }}</div>@endif
            @if($errors->any())<div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/40 rounded-xl text-red-600 dark:text-red-400 text-sm"><ul>@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul></div>@endif

            <form action="{{ route('consultation.store') }}" method="POST" class="space-y-5">
                @csrf
                {{-- Honeypot Anti-Spam --}}
                <input type="text" name="hp_field" style="display:none !important" tabindex="-1" autocomplete="off">
                
                <div class="flex flex-col gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required
                               class="w-full bg-gray-50 dark:bg-[#202020] border border-gray-200 dark:border-none text-gray-900 dark:text-white rounded-xl px-4 py-3.5 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-gray-300 dark:focus:ring-white/20 transition-all placeholder-gray-500"
                               placeholder="Dimas Aditya Wiranata">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1">No. WhatsApp <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" required
                               class="w-full bg-gray-50 dark:bg-[#202020] border border-gray-200 dark:border-none text-gray-900 dark:text-white rounded-xl px-4 py-3.5 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-gray-300 dark:focus:ring-white/20 transition-all placeholder-gray-500"
                               placeholder="085648907544">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required
                               class="w-full bg-gray-50 dark:bg-[#202020] border border-gray-200 dark:border-none text-gray-900 dark:text-white rounded-xl px-4 py-3.5 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-gray-300 dark:focus:ring-white/20 transition-all placeholder-gray-500"
                               placeholder="dimasaw728@gmail.com">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1">Tanggal Konsultasi <span class="text-red-500">*</span></label>
                        <input type="text" name="preferred_date" value="{{ old('preferred_date', $date) }}" required
                               data-flatpickr data-min-date="{{ date('Y-m-d') }}" placeholder="Pilih tanggal"
                               class="w-full bg-gray-50 dark:bg-[#202020] border border-gray-200 dark:border-none text-gray-900 dark:text-white rounded-xl px-4 py-3.5 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-gray-300 dark:focus:ring-white/20 transition-all placeholder-gray-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1">Jam Konsultasi <span class="text-red-500">*</span></label>
                        <select name="preferred_time" required
                                class="w-full bg-gray-50 dark:bg-[#202020] border border-gray-200 dark:border-none text-gray-900 dark:text-white rounded-xl px-4 py-3.5 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-gray-300 dark:focus:ring-white/20 transition-all">
                            <option value="" class="bg-[#111]">Pilih jam...</option>
                            @foreach(['09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'] as $t)
                            <option value="{{ $t }}" class="bg-[#111]" {{ old('preferred_time') === $t ? 'selected' : '' }}>{{ $t }} WIB</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1">Jenis Konsultasi <span class="text-red-500">*</span></label>
                        <select name="consultation_type" required
                                class="w-full bg-gray-50 dark:bg-[#202020] border border-gray-200 dark:border-none text-gray-900 dark:text-white rounded-xl px-4 py-3.5 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-gray-300 dark:focus:ring-white/20 transition-all">
                            <option value="offline" class="bg-[#111]" {{ old('consultation_type') === 'offline' ? 'selected' : '' }}>Offline (Kunjungi Kantor)</option>
                            <option value="online" class="bg-[#111]" {{ old('consultation_type') === 'online' ? 'selected' : '' }}>Online (Video Call/Meet)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1">Rencana Tanggal Pernikahan</label>
                        <input type="text" name="event_date" value="{{ old('event_date') }}"
                               data-flatpickr data-min-date="{{ date('Y-m-d', strtotime('+1 month')) }}" placeholder="Pilih tanggal"
                               class="w-full bg-gray-50 dark:bg-[#202020] border border-gray-200 dark:border-none text-gray-900 dark:text-white rounded-xl px-4 py-3.5 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-gray-300 dark:focus:ring-white/20 transition-all placeholder-gray-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 mb-2 ml-1">Ceritakan Impian Pernikahan Anda</label>
                        <textarea name="message" rows="3"
                                  class="w-full bg-gray-50 dark:bg-[#202020] border border-gray-200 dark:border-none text-gray-900 dark:text-white rounded-xl px-4 py-3.5 text-sm text-gray-900 dark:text-white focus:ring-1 focus:ring-gray-300 dark:focus:ring-white/20 transition-all placeholder-gray-500 resize-none"
                                  placeholder="Tema yang diinginkan, estimasi tamu, budget...">{{ old('message') }}</textarea>
                    </div>
                </div>

                @guest
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/40 rounded-xl p-4 text-sm text-blue-700 dark:text-blue-300">
                    <i class="fas fa-info-circle mr-1"></i>
                    Anda akan diminta login setelah mengisi form ini untuk melanjutkan booking konsultasi.
                </div>
                @endguest

                <button type="submit" class="w-full gold-gradient text-white font-bold py-4 rounded-xl text-sm hover:shadow-lg transition-all">
                    <i class="fas fa-calendar-check mr-2"></i> Booking Konsultasi Gratis
                </button>
            </form>
        </div>

        <div class="mt-6 flex flex-wrap gap-3 justify-center text-center text-xs">
            <div class="bg-white dark:bg-[#111111] rounded-xl px-4 py-3 shadow-sm border border-gray-100 dark:border-white/5 flex items-center gap-2 text-gray-500 dark:text-gray-400"><i class="fas fa-check-circle text-green-500"></i> Gratis</div>
            <div class="bg-white dark:bg-[#111111] rounded-xl px-4 py-3 shadow-sm border border-gray-100 dark:border-white/5 flex items-center gap-2 text-gray-500 dark:text-gray-400"><i class="fas fa-clock text-blue-500"></i> Cepat</div>
            <div class="bg-white dark:bg-[#111111] rounded-xl px-4 py-3 shadow-sm border border-gray-100 dark:border-white/5 flex items-center gap-2 text-gray-500 dark:text-gray-400"><i class="fas fa-shield-alt text-purple-500"></i> No Commit</div>
        </div>
    </div>
</div>
@endsection
