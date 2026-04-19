@extends('layouts.app')
@section('title', 'Undangan Digital')
@section('page-title', 'Undangan Digital')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl border {{ $includesDigitalInvitation ? 'border-emerald-100 bg-emerald-50/80' : 'border-amber-100 bg-amber-50/80' }} p-5 flex gap-3 items-start">
        <div class="w-10 h-10 rounded-2xl flex items-center justify-center {{ $includesDigitalInvitation ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-600' }}">
            <i class="fas {{ $includesDigitalInvitation ? 'fa-gift' : 'fa-info-circle' }}"></i>
        </div>
        <div class="text-sm text-gray-700">
            @if($includesDigitalInvitation)
                <p class="font-semibold text-emerald-700">Paket Anda sudah termasuk undangan digital.</p>
                <p>Pilih atau ganti template kapan saja tanpa biaya tambahan. Cukup update konten undangan dan klik simpan.</p>
            @else
                <p class="font-semibold text-amber-700">Paket Anda belum termasuk undangan digital.</p>
                <p>Anda tetap bisa memakai template ini dengan biaya sesuai harga yang tertera. Silakan hubungi admin untuk aktivasi atau tambahan fitur.</p>
            @endif
        </div>
    </div>
    @if(!$invitation)
    <div class="bg-white rounded-2xl shadow-sm p-10 text-center">
        <i class="fas fa-envelope-open-text text-5xl text-gray-300 mb-4 block"></i>
        <h3 class="font-playfair text-2xl font-bold text-gray-800 mb-2">Undangan Belum Dibuat</h3>
        <p class="text-gray-500 mb-6">Admin kami akan menyiapkan undangan Anda. Silakan hubungi admin jika belum ada update.</p>
        <a href="{{ route('user.chat.index', $booking->id) }}" class="gold-gradient text-white font-semibold px-6 py-3 rounded-xl text-sm inline-block">
            <i class="fas fa-comments mr-2"></i> Hubungi Admin
        </a>
    </div>
    @else
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-5">
            {{-- Preview Card --}}
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 flex items-center justify-between border-b">
                    <h3 class="font-semibold text-gray-800">Preview Undangan</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('user.invitation.edit', $booking->id) }}"
                           class="text-sm font-medium px-4 py-2 border border-yellow-400 text-yellow-600 rounded-xl hover:bg-yellow-50 transition-colors">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>
                        @if($invitation->is_published)
                        <a href="{{ route('invitation.show', $invitation->slug) }}" target="_blank"
                           class="text-sm font-medium px-4 py-2 bg-green-50 text-green-700 rounded-xl hover:bg-green-100 transition-colors">
                            <i class="fas fa-external-link-alt mr-1"></i> Buka
                        </a>
                        @endif
                    </div>
                </div>
                <div class="p-6" style="background: {{ $invitation->template?->secondary_color ?? '#FFF8F0' }}">
                    <div class="text-center py-10 rounded-2xl border-2" style="border-color: {{ $invitation->template?->primary_color ?? '#D4AF37' }}; font-family: '{{ $invitation->template?->font_family ?? 'Playfair Display' }}', serif">
                        <p class="text-xs tracking-widest uppercase mb-4" style="color: {{ $invitation->template?->primary_color ?? '#D4AF37' }}">The Wedding of</p>
                        <h2 class="text-4xl font-bold mb-2" style="color: {{ $invitation->template?->primary_color ?? '#D4AF37' }}">{{ $invitation->groom_name ?? $booking->groom_name }}</h2>
                        <p class="text-2xl mb-2" style="color: {{ $invitation->template?->primary_color ?? '#D4AF37' }}">&</p>
                        <h2 class="text-4xl font-bold mb-4" style="color: {{ $invitation->template?->primary_color ?? '#D4AF37' }}">{{ $invitation->bride_name ?? $booking->bride_name }}</h2>
                        @if($invitation->reception_datetime)
                        <p class="text-sm text-gray-600">{{ $invitation->reception_datetime->isoFormat('dddd, D MMMM Y') }}</p>
                        @endif
                        @if($invitation->reception_venue)
                        <p class="text-sm text-gray-600 mt-1"><i class="fas fa-map-marker-alt mr-1"></i>{{ $invitation->reception_venue }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            @php $stats = $invitation->rsvp_stats; @endphp
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl p-4 text-center shadow-sm"><p class="text-2xl font-bold text-blue-600">{{ $invitation->view_count }}</p><p class="text-xs text-gray-500 mt-1">Kali Dilihat</p></div>
                <div class="bg-white rounded-xl p-4 text-center shadow-sm"><p class="text-2xl font-bold text-green-600">{{ $stats['hadir'] }}</p><p class="text-xs text-gray-500 mt-1">Konfirmasi Hadir</p></div>
                <div class="bg-white rounded-xl p-4 text-center shadow-sm"><p class="text-2xl font-bold text-red-500">{{ $stats['tidak_hadir'] }}</p><p class="text-xs text-gray-500 mt-1">Tidak Hadir</p></div>
                <div class="bg-white rounded-xl p-4 text-center shadow-sm"><p class="text-2xl font-bold text-gray-700">{{ $stats['total'] }}</p><p class="text-xs text-gray-500 mt-1">Total RSVP</p></div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            <div class="bg-white rounded-2xl shadow-sm p-5">
                <h3 class="font-semibold text-gray-800 mb-4 text-sm">Status & Link Undangan</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Status</span>
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $invitation->is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $invitation->is_published ? '✅ Aktif' : '⏸ Draft' }}
                        </span>
                    </div>
                    @if($invitation->is_published)
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Link Undangan</p>
                        <div class="flex gap-2">
                            <input type="text" value="{{ route('invitation.show', $invitation->slug) }}" readonly
                                   class="flex-1 border border-gray-200 rounded-lg px-3 py-2 text-xs bg-gray-50 text-gray-600" id="invLink">
                            <button onclick="copyLink()" class="px-3 py-2 border border-gray-200 rounded-lg text-xs hover:bg-gray-50"><i class="fas fa-copy text-gray-500"></i></button>
                        </div>
                    </div>
                    @endif
                    <form action="{{ route('user.invitation.publish', $booking->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-2.5 rounded-xl text-sm font-semibold transition-all {{ $invitation->is_published ? 'bg-red-50 text-red-600 hover:bg-red-100' : 'gold-gradient text-white hover:shadow-md' }}">
                            <i class="fas {{ $invitation->is_published ? 'fa-eye-slash' : 'fa-globe' }} mr-1"></i>
                            {{ $invitation->is_published ? 'Sembunyikan' : 'Publikasikan' }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="font-semibold text-gray-800 text-sm">RSVP Terbaru</h3>
                    <a href="{{ route('user.invitation.rsvp', $booking->id) }}" class="text-xs text-yellow-600 hover:underline">Lihat semua</a>
                </div>
                @foreach($invitation->rsvps()->latest()->take(5)->get() as $rsvp)
                <div class="flex items-center justify-between py-2 border-b last:border-0 text-xs">
                    <div><p class="font-medium text-gray-700">{{ $rsvp->name }}</p><p class="text-gray-400">{{ $rsvp->guests_count }} tamu</p></div>
                    <span class="px-2 py-0.5 rounded-full {{ $rsvp->attendance==='hadir'?'bg-green-100 text-green-700':($rsvp->attendance==='tidak_hadir'?'bg-red-100 text-red-600':'bg-yellow-100 text-yellow-700') }}">
                        {{ ucfirst(str_replace('_',' ',$rsvp->attendance)) }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
function copyLink() {
    const el = document.getElementById('invLink');
    el.select(); document.execCommand('copy');
    window.AnggitaStatusModal?.show({ type: 'success', message: 'Link berhasil disalin.' });
}
</script>
@endpush
