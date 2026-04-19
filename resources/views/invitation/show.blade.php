<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Undangan {{ $invitation->groom_name }} & {{ $invitation->bride_name }}</title>
    <meta property="og:title" content="Undangan Pernikahan {{ $invitation->groom_name }} & {{ $invitation->bride_name }}">
    <meta property="og:description" content="{{ $invitation->reception_datetime?->isoFormat('D MMMM Y') }} | {{ $invitation->reception_venue }}">
    @if($invitation->photo_prewedding)
    <meta property="og:image" content="{{ Storage::url($invitation->photo_prewedding) }}">
    @endif
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Poppins:wght@300;400;500;600&family=Great+Vibes&family=Dancing+Script:wght@400;600;700&display=swap" rel="stylesheet">
    @php
    $primaryColor = $invitation->template?->primary_color ?? '#D4AF37';
    $secondaryColor = $invitation->template?->secondary_color ?? '#FFFBF0';
    $fontFamily = $invitation->template?->font_family ?? 'Playfair Display';
    @endphp
    <style>
        body { font-family: 'Poppins', sans-serif; background: {{ $secondaryColor }}; }
        .invitation-font { font-family: '{{ $fontFamily }}', serif; }
        .primary-color { color: {{ $primaryColor }}; }
        .primary-bg { background-color: {{ $primaryColor }}; }
        .primary-border { border-color: {{ $primaryColor }}; }
        .primary-btn { background: {{ $primaryColor }}; color: white; }
        .hero-overlay { background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.6)); }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body x-data="{ rsvpOpen: false, audioPlaying: false, coverOpen: true }" x-cloak>

{{-- Cover / Splash Screen --}}
<div x-show="coverOpen" class="fixed inset-0 z-50 flex flex-col items-center justify-center text-center"
     style="background: linear-gradient(135deg, {{ $primaryColor }}22, {{ $secondaryColor }});">
    <div class="bg-white rounded-3xl shadow-2xl p-10 max-w-sm mx-4">
        @if($invitation->photo_prewedding)
        <img src="{{ Storage::url($invitation->photo_prewedding) }}" class="w-28 h-28 rounded-full object-cover mx-auto mb-5 border-4" style="border-color: {{ $primaryColor }}">
        @else
        <div class="w-28 h-28 rounded-full mx-auto mb-5 flex items-center justify-center text-5xl primary-bg text-white">💍</div>
        @endif
        <p class="text-xs tracking-widest uppercase mb-2 primary-color font-semibold">Undangan Pernikahan</p>
        <h1 class="invitation-font text-3xl font-bold mb-1 primary-color">{{ $invitation->groom_name }}</h1>
        <p class="text-2xl mb-1 primary-color">&amp;</p>
        <h1 class="invitation-font text-3xl font-bold mb-4 primary-color">{{ $invitation->bride_name }}</h1>
        @if($invitation->reception_datetime)
        <p class="text-sm text-gray-600 mb-2">{{ $invitation->reception_datetime->isoFormat('dddd, D MMMM Y') }}</p>
        @endif
        <button @click="coverOpen = false" class="w-full primary-btn font-semibold py-3.5 rounded-xl mt-4 text-sm hover:opacity-90 transition-opacity">
            <i class="fas fa-envelope-open mr-2"></i> Buka Undangan
        </button>
        <p class="text-xs text-gray-400 mt-3">Kepada: Bapak/Ibu/Saudara/i Tamu Undangan</p>
    </div>
</div>

<div x-show="!coverOpen" class="max-w-lg mx-auto">

    {{-- Hero --}}
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden">
        @if($invitation->photo_prewedding)
        <div class="absolute inset-0">
            <img src="{{ Storage::url($invitation->photo_prewedding) }}" class="w-full h-full object-cover">
            <div class="hero-overlay absolute inset-0"></div>
        </div>
        @else
        <div class="absolute inset-0 primary-bg opacity-90"></div>
        @endif
        <div class="relative z-10 text-center text-white p-8">
            <p class="text-xs tracking-widest uppercase mb-6 opacity-80">The Wedding of</p>
            <h1 class="invitation-font text-5xl md:text-6xl font-bold mb-3">{{ $invitation->groom_name }}</h1>
            <p class="invitation-font text-4xl mb-3">&amp;</p>
            <h1 class="invitation-font text-5xl md:text-6xl font-bold mb-8">{{ $invitation->bride_name }}</h1>
            @if($invitation->reception_datetime)
            <div class="bg-white/20 backdrop-blur-sm rounded-2xl px-6 py-4 inline-block mb-6">
                <p class="text-sm font-medium">{{ $invitation->reception_datetime->isoFormat('dddd') }}</p>
                <p class="text-3xl font-bold">{{ $invitation->reception_datetime->format('d') }}</p>
                <p class="text-sm">{{ $invitation->reception_datetime->isoFormat('MMMM YYYY') }}</p>
            </div>
            @endif
            @if($invitation->hashtag)
            <p class="text-sm opacity-80">#{{ $invitation->hashtag }}</p>
            @endif
        </div>
        <div class="absolute bottom-8 left-0 right-0 text-center text-white">
            <p class="text-xs animate-bounce">Scroll ke bawah <i class="fas fa-chevron-down ml-1"></i></p>
        </div>
    </section>

    {{-- Opening Quote --}}
    @if($invitation->opening_quote)
    <section class="py-16 px-8 text-center" style="background: {{ $secondaryColor }}">
        <div class="max-w-md mx-auto">
            <p class="text-gray-600 text-sm leading-loose italic">"{{ $invitation->opening_quote }}"</p>
        </div>
    </section>
    @endif

    {{-- Couple Info --}}
    <section class="py-16 px-6 bg-white text-center">
        <p class="text-xs tracking-widest uppercase primary-color font-semibold mb-8">Pengantin Kami</p>
        <div class="grid grid-cols-2 gap-8">
            <div>
                <div class="w-24 h-24 rounded-full primary-bg flex items-center justify-center text-white text-4xl mx-auto mb-4">👨</div>
                <h3 class="invitation-font text-xl font-bold primary-color">{{ $invitation->groom_name }}</h3>
                @if($invitation->groom_father || $invitation->groom_mother)
                <p class="text-xs text-gray-500 mt-2">Putra dari:<br>
                    @if($invitation->groom_father)Bpk. {{ $invitation->groom_father }}@endif
                    @if($invitation->groom_mother) & Ibu. {{ $invitation->groom_mother }}@endif
                </p>
                @endif
            </div>
            <div>
                <div class="w-24 h-24 rounded-full primary-bg flex items-center justify-center text-white text-4xl mx-auto mb-4">👰</div>
                <h3 class="invitation-font text-xl font-bold primary-color">{{ $invitation->bride_name }}</h3>
                @if($invitation->bride_father || $invitation->bride_mother)
                <p class="text-xs text-gray-500 mt-2">Putri dari:<br>
                    @if($invitation->bride_father)Bpk. {{ $invitation->bride_father }}@endif
                    @if($invitation->bride_mother) & Ibu. {{ $invitation->bride_mother }}@endif
                </p>
                @endif
            </div>
        </div>
    </section>

    {{-- Akad --}}
    @if($invitation->akad_datetime || $invitation->akad_venue)
    <section class="py-12 px-6 text-center" style="background: {{ $secondaryColor }}">
        <p class="text-xs tracking-widest uppercase primary-color font-semibold mb-6">Akad Nikah</p>
        @if($invitation->akad_datetime)
        <p class="invitation-font text-2xl font-bold primary-color mb-2">{{ $invitation->akad_datetime->isoFormat('dddd, D MMMM Y') }}</p>
        <p class="text-gray-600 text-sm">Pukul {{ $invitation->akad_datetime->format('H:i') }} WIB</p>
        @endif
        @if($invitation->akad_venue)
        <p class="font-semibold text-gray-800 mt-3">{{ $invitation->akad_venue }}</p>
        @endif
        @if($invitation->akad_address)
        <p class="text-gray-500 text-sm mt-1">{{ $invitation->akad_address }}</p>
        @endif
    </section>
    @endif

    {{-- Resepsi --}}
    @if($invitation->reception_datetime || $invitation->reception_venue)
    <section class="py-12 px-6 text-center bg-white">
        <p class="text-xs tracking-widest uppercase primary-color font-semibold mb-6">Resepsi</p>
        @if($invitation->reception_datetime)
        <p class="invitation-font text-2xl font-bold primary-color mb-2">{{ $invitation->reception_datetime->isoFormat('dddd, D MMMM Y') }}</p>
        <p class="text-gray-600 text-sm">Pukul {{ $invitation->reception_datetime->format('H:i') }} WIB</p>
        @endif
        @if($invitation->reception_venue)
        <p class="font-semibold text-gray-800 mt-3">{{ $invitation->reception_venue }}</p>
        @endif
        @if($invitation->reception_address)
        <p class="text-gray-500 text-sm mt-1">{{ $invitation->reception_address }}</p>
        @endif
        @if($invitation->maps_link)
        <a href="{{ $invitation->maps_link }}" target="_blank"
           class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl text-sm font-semibold primary-btn">
            <i class="fas fa-map-marker-alt"></i> Buka Google Maps
        </a>
        @endif
    </section>
    @endif

    {{-- Gallery --}}
    @if($invitation->gallery_photos && count($invitation->gallery_photos) > 0)
    <section class="py-12 px-4" style="background: {{ $secondaryColor }}">
        <p class="text-xs tracking-widest uppercase primary-color font-semibold text-center mb-6">Galeri Foto</p>
        <div class="grid grid-cols-2 gap-2">
            @foreach($invitation->gallery_photos as $photo)
            <img src="{{ Storage::url($photo) }}" class="w-full h-36 object-cover rounded-xl">
            @endforeach
        </div>
    </section>
    @endif

    {{-- RSVP --}}
    <section class="py-16 px-6 bg-white" x-data="{ sent: false }">
        <p class="text-xs tracking-widest uppercase primary-color font-semibold text-center mb-2">Konfirmasi Kehadiran</p>
        <h3 class="invitation-font text-2xl font-bold text-center primary-color mb-6">RSVP</h3>

        <div x-show="sent" x-cloak class="text-center py-6">
            <div class="w-16 h-16 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-3"><i class="fas fa-check-circle text-green-500 text-2xl"></i></div>
            <p class="text-gray-700 font-semibold">Terima kasih atas konfirmasi Anda! 🎉</p>
        </div>

        <form x-show="!sent" action="{{ route('invitation.rsvp', $invitation->slug) }}" method="POST" class="space-y-4" @submit.prevent="$el.submit(); sent = true">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 transition-all" style="focus-ring-color: {{ $primaryColor }}" placeholder="Nama Anda">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">No. HP</label>
                <input type="tel" name="phone" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 transition-all" placeholder="08xxxxxxxxxx">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jumlah Tamu <span class="text-red-500">*</span></label>
                <select name="guests_count" required class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none">
                    @for($i = 1; $i <= 10; $i++)<option value="{{ $i }}">{{ $i }} orang</option>@endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Kehadiran <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach([['value'=>'hadir','label'=>'✅ Hadir'],['value'=>'tidak_hadir','label'=>'❌ Tidak Hadir'],['value'=>'mungkin','label'=>'❓ Mungkin']] as $opt)
                    <label class="cursor-pointer"><input type="radio" name="attendance" value="{{ $opt['value'] }}" class="sr-only peer" {{ $loop->first ? 'checked' : '' }}>
                    <div class="text-center py-2.5 rounded-xl border-2 text-xs font-semibold transition-all peer-checked:text-white peer-checked:primary-bg" style="--tw-ring-color: {{ $primaryColor }}">{{ $opt['label'] }}</div></label>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Ucapan & Doa</label>
                <textarea name="message" rows="3" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none resize-none" placeholder="Sampaikan ucapan terbaik Anda..."></textarea>
            </div>
            <button type="submit" class="w-full primary-btn font-bold py-4 rounded-xl text-sm hover:opacity-90 transition-opacity">
                <i class="fas fa-paper-plane mr-2"></i> Kirim Konfirmasi
            </button>
        </form>
    </section>

    {{-- RSVP List --}}
    @if($invitation->rsvps->isNotEmpty())
    <section class="py-12 px-6" style="background: {{ $secondaryColor }}">
        <p class="text-xs tracking-widest uppercase primary-color font-semibold text-center mb-6">Ucapan & Doa</p>
        <div class="space-y-3 max-h-80 overflow-y-auto">
            @foreach($invitation->rsvps()->where('message', '!=', null)->latest()->take(20)->get() as $rsvp)
            <div class="bg-white rounded-2xl p-4 shadow-sm">
                <div class="flex items-center justify-between mb-2">
                    <p class="font-semibold text-gray-800 text-sm">{{ $rsvp->name }}</p>
                    <span class="text-xs px-2 py-0.5 rounded-full {{ $rsvp->attendance==='hadir'?'bg-green-100 text-green-700':'bg-gray-100 text-gray-600' }}">{{ $rsvp->attendance==='hadir'?'✅ Hadir':($rsvp->attendance==='tidak_hadir'?'❌ Tidak Hadir':'❓ Mungkin') }}</span>
                </div>
                @if($rsvp->message)<p class="text-sm text-gray-600 italic">"{{ $rsvp->message }}"</p>@endif
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- Closing --}}
    <section class="py-16 px-6 text-center primary-bg text-white">
        <p class="invitation-font text-2xl font-bold mb-3">{{ $invitation->groom_name }} & {{ $invitation->bride_name }}</p>
        <p class="text-white/80 text-sm leading-relaxed mb-6">{{ $invitation->closing_message ?? 'Merupakan suatu kebahagiaan dan kehormatan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir dan memberikan doa restu.' }}</p>
        <p class="text-white/60 text-xs">Dibuat dengan ❤ oleh Anggita Wedding Organizer</p>
    </section>

</div>

{{-- Music Player --}}
@if($invitation->music_file)
<audio id="bgMusic" loop><source src="{{ Storage::url($invitation->music_file) }}"></audio>
<button @click="audioPlaying = !audioPlaying; audioPlaying ? $el.previousElementSibling.play() : $el.previousElementSibling.pause()"
        x-bind:previousElementSibling="$el.previousElementSibling"
        class="fixed bottom-6 right-6 w-12 h-12 primary-btn rounded-full shadow-lg flex items-center justify-center hover:scale-110 transition-transform z-40">
    <i :class="audioPlaying ? 'fa-pause' : 'fa-music'" class="fas text-sm"></i>
</button>
@endif

<script>
// Auto-play music when cover is opened
document.addEventListener('alpine:init', () => {});
</script>
</body>
</html>
