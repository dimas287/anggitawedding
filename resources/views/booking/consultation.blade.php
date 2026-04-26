@extends('layouts.guest')
@section('title', 'Booking Konsultasi Gratis')
@section('meta_description', 'Jadwalkan konsultasi gratis dengan tim Anggita Wedding Organizer. Kami siap mendengarkan impian pernikahan Anda dan memberikan solusi terbaik.')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-[#0A0A0A] pt-24 pb-16">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full gold-gradient mb-4">
                <i class="fas fa-comments text-white text-xl"></i>
            </div>
            <h1 class="font-playfair text-3xl font-bold text-gray-800 dark:text-white">Konsultasi Gratis</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2">Ceritakan impian pernikahan Anda. Kami siap membantu tanpa biaya apapun!</p>
        </div>

        <div class="bg-white dark:bg-[#111111] rounded-2xl shadow-sm dark:shadow-black/20 p-8 border border-transparent dark:border-white/10">
            @if(session('error'))<div class="mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/40 rounded-xl text-red-600 dark:text-red-400 text-sm">{{ session('error') }}</div>@endif
            @if($errors->any())<div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700/40 rounded-xl text-red-600 dark:text-red-400 text-sm"><ul>@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul></div>@endif

            <form action="{{ route('consultation.store') }}" method="POST" class="space-y-5">
                @csrf
                {{-- Honeypot Anti-Spam --}}
                <input type="text" name="hp_field" style="display:none !important" tabindex="-1" autocomplete="off">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()?->name) }}" required
                               class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600 transition-all"
                               placeholder="Nama lengkap Anda">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">No. WhatsApp <span class="text-red-500">*</span></label>
                        <input type="tel" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" required
                               class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600 transition-all"
                               placeholder="081xxxxxxxxx">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()?->email) }}" required
                           class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600 transition-all"
                           placeholder="email@contoh.com">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Konsultasi <span class="text-red-500">*</span></label>
                        <input type="text" name="preferred_date" value="{{ old('preferred_date', $date) }}" required
                               data-flatpickr data-min-date="{{ date('Y-m-d') }}" placeholder="Pilih tanggal"
                               class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600 transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jam Konsultasi <span class="text-red-500">*</span></label>
                        <select name="preferred_time" required
                                class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600 transition-all">
                            <option value="" class="dark:bg-[#111111]">Pilih jam...</option>
                            @foreach(['09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00'] as $t)
                            <option value="{{ $t }}" class="dark:bg-[#111111]" {{ old('preferred_time') === $t ? 'selected' : '' }}>{{ $t }} WIB</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Jenis Konsultasi <span class="text-red-500">*</span></label>
                        <select name="consultation_type" required
                                class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600 transition-all">
                            <option value="offline" class="dark:bg-[#111111]" {{ old('consultation_type') === 'offline' ? 'selected' : '' }}>Offline (Kunjungi Kantor)</option>
                            <option value="online" class="dark:bg-[#111111]" {{ old('consultation_type') === 'online' ? 'selected' : '' }}>Online (Video Call/Meet)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Rencana Tanggal Pernikahan</label>
                        <input type="text" name="event_date" value="{{ old('event_date') }}"
                               data-flatpickr data-min-date="{{ date('Y-m-d', strtotime('+1 month')) }}" placeholder="Pilih tanggal"
                               class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600 transition-all">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Ceritakan Impian Pernikahan Anda</label>
                    <textarea name="message" rows="4"
                              class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/5 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400 dark:focus:ring-yellow-600 transition-all resize-none"
                              placeholder="Tema yang diinginkan, estimasi tamu, budget, atau hal lain yang ingin Anda diskusikan...">{{ old('message') }}</textarea>
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

        <div class="mt-6 grid grid-cols-3 gap-4 text-center text-sm">
            <div class="bg-white dark:bg-[#111111] rounded-xl p-4 shadow-sm dark:shadow-black/20 border border-transparent dark:border-white/10"><i class="fas fa-check-circle text-green-500 text-xl mb-2 block"></i><p class="text-gray-600 dark:text-gray-400">100% Gratis</p></div>
            <div class="bg-white dark:bg-[#111111] rounded-xl p-4 shadow-sm dark:shadow-black/20 border border-transparent dark:border-white/10"><i class="fas fa-clock text-blue-500 text-xl mb-2 block"></i><p class="text-gray-600 dark:text-gray-400">Respons Cepat</p></div>
            <div class="bg-white dark:bg-[#111111] rounded-xl p-4 shadow-sm dark:shadow-black/20 border border-transparent dark:border-white/10"><i class="fas fa-shield-alt text-purple-500 text-xl mb-2 block"></i><p class="text-gray-600 dark:text-gray-400">Tanpa Komitmen</p></div>
        </div>
    </div>
</div>
@endsection
