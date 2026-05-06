@extends('layouts.app')
@php
    $extraTotal = $booking->active_extra_charges_total;
    $grandTotal = $booking->package_price + $extraTotal;
    $remaining = max(0, $grandTotal - $booking->total_paid);
    $isInvitationOnly = (bool) $booking->is_invitation_only;
    $invitationTemplate = optional($booking->invitation)->template;
    $canUserCancel = $booking->status === 'pending' && (int) $booking->total_paid <= 0;
@endphp

@section('title', $isInvitationOnly ? 'Pesanan Undangan Digital' : $booking->couple_short_display)

@section('content')
<div class="space-y-10 py-4">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 px-2">
        <div>
            <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gray-100 dark:bg-white/5 text-gray-500 dark:text-gray-400 text-[10px] font-bold uppercase tracking-widest hover:text-gray-900 dark:hover:text-white transition-all mb-4">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
            <h1 class="font-playfair text-3xl md:text-4xl font-bold text-gray-900 dark:text-white">
                {{ $isInvitationOnly ? 'Detail Undangan Digital' : 'Detail Wedding Booking' }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm mt-2">
                Booking ID: <span class="font-bold text-gray-900 dark:text-white">{{ $booking->booking_code }}</span> • 
                {{ $booking->created_at->isoFormat('D MMMM Y') }}
            </p>
        </div>
        
        <div class="flex gap-3">
            @if($isInvitationOnly && $booking->payment_status === 'unpaid' && $booking->status !== 'cancelled')
                <a href="{{ route('payment.checkout', $booking->id) }}"
                   class="gold-gradient text-white font-bold px-6 py-3.5 rounded-2xl text-xs flex items-center gap-2 shadow-xl shadow-yellow-500/20 hover:scale-[1.02] transition-all">
                    <i class="fas fa-credit-card"></i> Bayar Sekarang
                </a>
            @endif
        </div>
    </div>

    {{-- Urgent Notification --}}
    @if($isInvitationOnly && $booking->payment_status === 'unpaid' && $booking->status !== 'cancelled')
    <div class="bg-white dark:bg-white/5 rounded-[32px] border border-yellow-200 dark:border-yellow-900/30 p-6 shadow-2xl relative overflow-hidden group">
        <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-125 transition-transform duration-700">
            <i class="fas fa-envelope-open-text text-8xl text-yellow-600"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row items-center gap-6 text-center md:text-left">
            <div class="w-16 h-16 rounded-3xl bg-yellow-100 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-500 flex items-center justify-center text-2xl shadow-inner">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="flex-1">
                <h3 class="font-bold text-gray-900 dark:text-white text-lg">Selesaikan Pembayaran Anda</h3>
                <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Pembayaran diperlukan untuk mulai mengisi data dan mempublikasikan undangan digital Anda.</p>
            </div>
            <a href="{{ route('payment.checkout', $booking->id) }}" class="px-8 py-4 rounded-2xl bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold text-sm hover:shadow-xl transition-all">
                Lanjut ke Pembayaran
            </a>
        </div>
    </div>
    @endif

    @if($booking->status === 'cancelled')
    <div class="bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/30 rounded-[32px] p-6 flex items-center gap-4 text-red-700 dark:text-red-400">
        <div class="w-12 h-12 rounded-2xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center text-xl">
            <i class="fas fa-ban"></i>
        </div>
        <div>
            <p class="font-bold">Booking Dibatalkan</p>
            <p class="text-xs opacity-80 mt-0.5">Silakan hubungi admin jika ini adalah kesalahan.</p>
        </div>
    </div>
    @endif

    {{-- Main Status Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $statusColors = [
                'pending' => ['bg'=>'bg-amber-50 dark:bg-amber-900/10', 'text'=>'text-amber-600 dark:text-amber-500', 'icon'=>'fa-clock'],
                'dp_paid' => ['bg'=>'bg-blue-50 dark:bg-blue-900/10', 'text'=>'text-blue-600 dark:text-blue-500', 'icon'=>'fa-check-circle'],
                'in_progress' => ['bg'=>'bg-purple-50 dark:bg-purple-900/10', 'text'=>'text-purple-600 dark:text-purple-500', 'icon'=>'fa-spinner'],
                'completed' => ['bg'=>'bg-green-50 dark:bg-green-900/10', 'text'=>'text-green-600 dark:text-green-500', 'icon'=>'fa-crown'],
                'cancelled' => ['bg'=>'bg-red-50 dark:bg-red-900/10', 'text'=>'text-red-600 dark:text-red-500', 'icon'=>'fa-times-circle'],
            ];
            $s = $statusColors[$booking->status] ?? ['bg'=>'bg-gray-50', 'text'=>'text-gray-600', 'icon'=>'fa-info-circle'];
        @endphp

        <div class="bg-white dark:bg-white/5 rounded-[32px] p-6 border border-gray-100 dark:border-white/10 shadow-xl flex flex-col items-center text-center">
            <div class="w-12 h-12 rounded-2xl {{ $s['bg'] }} {{ $s['text'] }} flex items-center justify-center text-xl mb-4 shadow-inner">
                <i class="fas {{ $s['icon'] }}"></i>
            </div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Status Booking</p>
            <p class="font-bold text-gray-900 dark:text-white">{{ $booking->status_label }}</p>
        </div>

        <div class="bg-white dark:bg-white/5 rounded-[32px] p-6 border border-gray-100 dark:border-white/10 shadow-xl flex flex-col items-center text-center">
            <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/10 text-blue-600 dark:text-blue-500 flex items-center justify-center text-xl mb-4 shadow-inner">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tanggal Acara</p>
            <p class="font-bold text-gray-900 dark:text-white">{{ $booking->event_date->isoFormat('D MMM Y') }}</p>
        </div>

        <div class="bg-white dark:bg-white/5 rounded-[32px] p-6 border border-gray-100 dark:border-white/10 shadow-xl flex flex-col items-center text-center">
            <div class="w-12 h-12 rounded-2xl bg-yellow-50 dark:bg-yellow-900/10 text-yellow-600 dark:text-yellow-500 flex items-center justify-center text-xl mb-4 shadow-inner">
                <i class="fas fa-wallet"></i>
            </div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Tagihan</p>
            <p class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
        </div>

        <div class="bg-white dark:bg-white/5 rounded-[32px] p-6 border border-gray-100 dark:border-white/10 shadow-xl flex flex-col items-center text-center">
            <div class="w-12 h-12 rounded-2xl bg-green-50 dark:bg-green-900/10 text-green-600 dark:text-green-500 flex items-center justify-center text-xl mb-4 shadow-inner">
                <i class="fas fa-check-double"></i>
            </div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Sudah Dibayar</p>
            <p class="font-bold text-gray-900 dark:text-white text-lg">Rp {{ number_format($booking->total_paid, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-5">
            {{-- Names Section --}}
            <div class="bg-white dark:bg-white/5 rounded-[32px] p-8 border border-gray-100 dark:border-white/10 shadow-xl flex flex-col md:flex-row md:items-center md:justify-between gap-8 relative overflow-hidden group">
                <div class="absolute -top-10 -left-10 w-40 h-40 bg-yellow-400/5 rounded-full blur-3xl group-hover:bg-yellow-400/10 transition-colors duration-700"></div>
                
                <div class="relative z-10">
                    <p class="text-[10px] font-bold text-yellow-600 dark:text-yellow-500 uppercase tracking-[0.3em] mb-3 flex items-center gap-2">
                        <span class="w-1 h-3 bg-yellow-400 rounded-full"></span> Nama Pasangan
                    </p>
                    <h2 class="text-4xl font-playfair font-bold text-gray-900 dark:text-white">{{ $booking->couple_short_display }}</h2>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm relative z-10">
                    <div class="bg-gray-50 dark:bg-white/5 rounded-2xl p-4 border border-gray-100 dark:border-white/10 transition-all hover:border-yellow-400/30">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Mempelai Pria</p>
                        <p class="font-bold text-gray-900 dark:text-white">{{ $booking->groom_name }}</p>
                        <p class="text-xs text-gray-500 mt-1 italic">"{{ $booking->groom_short_name ?: '-' }}"</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-white/5 rounded-2xl p-4 border border-gray-100 dark:border-white/10 transition-all hover:border-yellow-400/30">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-2">Mempelai Wanita</p>
                        <p class="font-bold text-gray-900 dark:text-white">{{ $booking->bride_name }}</p>
                        <p class="text-xs text-gray-500 mt-1 italic">"{{ $booking->bride_short_name ?: '-' }}"</p>
                    </div>
                </div>
            </div>

            {{-- Booking Info --}}
            <div class="bg-white dark:bg-white/5 rounded-[32px] p-8 border border-gray-100 dark:border-white/10 shadow-xl">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-2xl bg-yellow-50 dark:bg-yellow-900/20 text-yellow-600 dark:text-yellow-500 flex items-center justify-center shadow-sm">
                        <i class="fas {{ $isInvitationOnly ? 'fa-envelope-open-text' : 'fa-info-circle' }}"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white">{{ $isInvitationOnly ? 'Detail Pesanan Undangan' : 'Informasi Booking' }}</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-0.5">Ringkasan Data Pemesanan</p>
                    </div>
                </div>

                @php
                    $infoRows = $isInvitationOnly ? [
                        'Kode Pesanan'          => $booking->booking_code,
                        'Template'              => $invitationTemplate ? $invitationTemplate->name : '-',
                        'Harga'                 => 'Rp ' . number_format($booking->package_price, 0, ',', '.'),
                        'Tanggal Pesan'         => $booking->created_at->isoFormat('dddd, D MMMM Y'),
                        'No. HP'                => $booking->phone,
                        'Email'                 => $booking->email ?? $booking->user->email,
                    ] : [
                        'Kode Booking'          => $booking->booking_code,
                        'Paket'                 => $booking->package->name,
                        'Tanggal Acara'         => $booking->event_date->isoFormat('dddd, D MMMM Y'),
                        'Venue'                 => $booking->venue ?? '-',
                        'No. HP'                => $booking->phone,
                        'Email'                 => $booking->email ?? $booking->user->email,
                    ];
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                    @foreach($infoRows as $label => $value)
                        <div class="flex flex-col gap-1">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $label }}</span>
                            <p class="font-bold text-gray-900 dark:text-white text-sm">{{ $value }}</p>
                        </div>
                    @endforeach
                    
                    @if($booking->venue_address)
                        <div class="md:col-span-2 flex flex-col gap-1 pt-2 border-t border-gray-50 dark:border-white/5">
                            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Alamat Venue</span>
                            <p class="text-gray-900 dark:text-white text-sm italic">"{{ $booking->venue_address }}"</p>
                        </div>
                    @endif

                    @if($booking->client_notes)
                        <div class="md:col-span-2 flex flex-col gap-1 p-4 bg-yellow-50 dark:bg-yellow-900/10 rounded-2xl border border-yellow-100 dark:border-yellow-900/20 mt-2">
                            <span class="text-[10px] font-bold text-yellow-600 dark:text-yellow-500 uppercase tracking-widest">Catatan Admin</span>
                            <p class="text-yellow-800 dark:text-yellow-200 text-sm italic">{{ $booking->client_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Extra Charges: hanya tampil untuk paket wedding --}}
            @if(!$isInvitationOnly)
            <div class="bg-white dark:bg-white/5 rounded-2xl shadow-sm p-6 border border-gray-100 dark:border-white/10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2"><i class="fas fa-receipt text-amber-500"></i> Biaya Tambahan</h3>
                </div>
                @if($booking->extraCharges->isEmpty())
                    <p class="text-gray-400 dark:text-gray-500 text-sm text-center py-4">Belum ada biaya tambahan.</p>
                @else
                    <div class="space-y-2">
                        @foreach($booking->extraCharges as $charge)
                        <div class="p-3 rounded-xl border border-amber-100 dark:border-amber-900/30 bg-amber-50 dark:bg-amber-900/10 flex flex-col sm:flex-row sm:items-center sm:justify-between text-sm gap-2">
                            <div>
                                <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $charge->title }}</p>
                                @if($charge->notes)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $charge->notes }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-amber-700 dark:text-amber-400">Rp {{ number_format($charge->amount, 0, ',', '.') }}</p>
                                <span class="text-xs inline-flex px-2 py-0.5 rounded-full font-semibold {{ match($charge->status) {
                                    'paid' => 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400',
                                    'billed' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/20 dark:text-blue-400',
                                    'waived' => 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400',
                                    default => 'bg-amber-100 text-amber-700 dark:bg-amber-900/20 dark:text-amber-400',
                                } }}">{{ ucfirst($charge->status) }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
            @endif {{-- end !$isInvitationOnly (Extra Charges) --}}

            @if(!$isInvitationOnly)
            {{-- Fitting Schedule --}}
            <div class="bg-white dark:bg-white/5 rounded-2xl shadow-sm p-6 space-y-5 border border-gray-100 dark:border-white/10" x-data="{ fittingModal:false }">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2"><i class="fas fa-ruler-combined text-indigo-500"></i> Jadwal Fitting</h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400">Pantau dan atur jadwal fitting busana Anda.</span>
                    </div>
                    <button type="button" @click="fittingModal = true"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold gold-gradient text-white hover:shadow-lg">
                        <i class="fas fa-plus-circle"></i> Tambah Jadwal
                    </button>
                </div>
                @if($booking->fittings->isEmpty())
                    <p class="text-center text-gray-400 text-sm py-4">Belum ada jadwal fitting tercatat.</p>
                @else
                    <div class="space-y-3">
                        @foreach($booking->fittings as $fitting)
                            @php
                                $formattedSchedule = optional($fitting->scheduled_at)
                                    ? $fitting->scheduled_at->copy()->locale(app()->getLocale())->isoFormat('dddd, D MMMM Y • HH:mm')
                                    : '-';
                            @endphp
                            <div class="p-4 border border-gray-100 rounded-2xl flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-semibold text-gray-900">{{ $formattedSchedule }}</p>
                                    <p class="text-sm text-gray-600 mt-1"><i class="fas fa-map-marker-alt text-indigo-400 mr-1"></i>{{ $fitting->location ?: 'Lokasi belum ditentukan' }}</p>
                                    <div class="text-xs text-gray-500 mt-1 flex flex-wrap gap-3">
                                        <span><i class="fas fa-bullseye text-indigo-400 mr-1"></i>{{ $fitting->focus ?: 'Fokus belum diisi' }}</span>
                                        <span><i class="fas fa-sticky-note text-indigo-400 mr-1"></i>{{ $fitting->notes ?: 'Tidak ada catatan' }}</span>
                                    </div>
                                    <span class="inline-flex items-center gap-1 mt-2 text-[11px] px-2 py-0.5 rounded-full {{ $fitting->created_by === auth()->id() ? 'bg-green-50 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        <i class="fas fa-user"></i>
                                        {{ $fitting->created_by === auth()->id() ? 'Ditambahkan oleh Anda' : (optional($fitting->creator)->name ?? 'Admin') }}
                                    </span>
                                </div>
                                @if($fitting->created_by === auth()->id())
                                <form action="{{ route('user.booking.fitting.delete', [$booking->id, $fitting->id]) }}" method="POST" class="self-start">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-red-500 hover:text-red-600" onclick="return confirm('Hapus jadwal fitting ini?')">
                                        <i class="fas fa-trash-alt mr-1"></i>Hapus
                                    </button>
                                </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                <div x-show="fittingModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-black/60" @click="fittingModal = false"></div>
                    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-semibold text-gray-900">Tambah Jadwal Fitting</h4>
                                <p class="text-xs text-gray-500">Isi detail fitting baru agar tim kami siap.</p>
                            </div>
                            <button type="button" class="text-gray-400 hover:text-gray-600" @click="fittingModal = false"><i class="fas fa-times"></i></button>
                        </div>
                        <form action="{{ route('user.booking.fitting.store', $booking->id) }}" method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400">Tanggal & Waktu</label>
                                <input type="datetime-local" name="scheduled_at" required class="mt-1 w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/10 rounded-xl px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400">Lokasi</label>
                                    <input type="text" name="location" class="mt-1 w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/10 rounded-xl px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400" placeholder="Studio / Rumah / Toko">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500 dark:text-gray-400">Fokus</label>
                                    <input type="text" name="focus" class="mt-1 w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/10 rounded-xl px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400" placeholder="Gaun Akad, Kebaya, dll">
                                </div>
                            </div>
                            <div>
                                <label class="text-xs text-gray-500 dark:text-gray-400">Catatan</label>
                                <textarea name="notes" rows="3" class="mt-1 w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/10 rounded-xl px-3 py-2 text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-400" placeholder="Ukuran, reminder, dsb"></textarea>
                            </div>
                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" class="px-4 py-2 text-xs font-semibold text-gray-500" @click="fittingModal = false">Batal</button>
                                <button type="submit" class="px-5 py-2 rounded-xl gold-gradient text-white text-sm font-semibold hover:shadow-md">
                                    <i class="fas fa-check mr-1"></i> Simpan Jadwal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            {{-- Payments --}}
            <div class="bg-white dark:bg-white/5 rounded-2xl shadow-sm p-6 border border-gray-100 dark:border-white/10">
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4 flex items-center gap-2"><i class="fas fa-credit-card text-green-500"></i> Riwayat Pembayaran</h3>
                @if($booking->payments->isEmpty())
                <div class="text-center py-6">
                    <p class="text-gray-400 text-sm mb-3">Belum ada pembayaran</p>
                    @if($isInvitationOnly ? ($booking->payment_status === 'unpaid') : ($booking->status === 'pending'))
                    <div class="flex flex-col items-center gap-2">
                        <a href="{{ route('payment.checkout', $booking->id) }}" class="gold-gradient text-white font-semibold px-5 py-2.5 rounded-xl text-sm inline-block hover:shadow-lg transition-all">
                            <i class="fas fa-credit-card mr-2"></i> {{ $booking->payments->where('status', 'pending')->isNotEmpty() ? 'Lanjutkan Pembayaran' : ($isInvitationOnly ? 'Bayar Undangan' : 'Bayar DP Sekarang') }}
                        </a>
                        @if($booking->payments->where('status', 'pending')->isNotEmpty())
                            <a href="{{ route('payment.checkout', $booking->id) }}?reset=1" class="text-xs text-yellow-600 hover:text-yellow-700 font-medium mt-1">
                                <i class="fas fa-sync-alt mr-1"></i> Ganti metode pembayaran?
                            </a>
                        @endif
                    </div>
                    @endif
                </div>
                @else
                <div class="space-y-2">
                    @foreach($booking->payments->whereIn('status', ['success', 'pending']) as $pay)
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 bg-gray-50 rounded-xl text-sm border {{ $pay->status === 'pending' ? 'border-yellow-200 bg-yellow-50/30' : 'border-gray-100' }}">
                        <div class="mb-2 sm:mb-0">
                            <div class="flex items-center gap-2 mb-1">
                                <p class="font-bold text-gray-800">{{ $pay->payment_code }}</p>
                                <span class="text-[10px] px-2 py-0.5 rounded-full font-medium tracking-wide {{ $pay->status === 'success' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ $pay->status === 'pending' ? 'Menunggu Pembayaran' : ucfirst($pay->status) }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-500">{{ ucfirst($pay->type) }} • {{ strtoupper($pay->method) }} {{ $pay->status === 'success' ? '• ' . $pay->paid_at?->format('d M Y') : '' }}</p>
                        </div>
                        <div class="text-left sm:text-right flex flex-col justify-center">
                            <p class="font-bold text-lg {{ $pay->status === 'success' ? 'text-green-600' : 'text-yellow-600' }} mb-1">Rp {{ number_format($pay->amount, 0, ',', '.') }}</p>
                            @if($pay->status === 'pending')
                                <a href="{{ route('payment.success', $booking->id) }}" class="text-[11px] font-semibold text-yellow-700 hover:text-yellow-800 underline flex items-center gap-1 sm:justify-end">
                                    Lihat Instruksi Pembayaran <i class="fas fa-arrow-right"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                @if($booking->payment_status === 'unpaid' && $booking->status !== 'cancelled')
                <div class="mt-5 border-t pt-5">
                    <h4 class="font-semibold text-gray-800 text-sm mb-2">Upload Bukti Transfer (Manual)</h4>
                    <p class="text-xs text-gray-500 mb-3">Unggah bukti pembayaran, lalu tunggu admin memverifikasi. Setelah terverifikasi, Anda bisa publish undangan.</p>
                    <form action="{{ route('payment.manual', $booking->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="text-xs font-semibold text-gray-600">Metode</label>
                                <select name="method" class="mt-1 w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/10 rounded-xl px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                    <option value="transfer" class="dark:bg-[#1a1a1a]">Transfer</option>
                                    <option value="cash" class="dark:bg-[#1a1a1a]">Cash</option>
                                    <option value="other" class="dark:bg-[#1a1a1a]">Lainnya</option>
                                </select>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">Nominal</label>
                                <input type="number" min="1000" name="amount" value="{{ $isInvitationOnly ? (int) $booking->package_price : (int) $booking->dp_amount }}" class="mt-1 w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/10 rounded-xl px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-400" {{ $isInvitationOnly ? 'readonly' : '' }} />
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">Catatan (opsional)</label>
                            <input type="text" name="notes" class="mt-1 w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/10 rounded-xl px-3 py-2 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-400" placeholder="Contoh: Transfer BCA a.n. ..." />
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600">Bukti Transfer</label>
                            <input type="file" name="proof_attachment" accept="image/*,application/pdf" class="mt-1 w-full text-xs text-gray-600 dark:text-gray-400 file:cursor-pointer file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100 dark:file:bg-[#202020] dark:file:text-yellow-500 dark:hover:file:bg-[#2a2a2a] transition-all" required />
                            <p class="text-[11px] text-gray-400 mt-1">Format JPG/PNG/WEBP/PDF • maks 5MB</p>
                        </div>
                        <button type="submit" class="w-full sm:w-auto border-2 border-yellow-400 text-yellow-700 font-semibold px-5 py-3 rounded-xl text-sm hover:bg-yellow-50 transition-all">
                            <i class="fas fa-upload mr-2"></i> Upload Bukti Pembayaran
                        </button>
                    </form>
                </div>
                @endif
            </div>

            {{-- Documents --}}
            <div class="bg-white dark:bg-white/5 rounded-2xl shadow-sm p-6 border border-gray-100 dark:border-white/10">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2"><i class="fas fa-folder-open text-blue-500"></i> Dokumen</h3>
                </div>
                @if($booking->documents->isEmpty())
                <p class="text-gray-400 text-sm text-center py-4">Belum ada dokumen</p>
                @else
                <div class="space-y-2 mb-4">
                    @foreach($booking->documents->where('is_visible_to_client', true) as $doc)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl text-sm">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-file-alt text-blue-400"></i>
                            <div><p class="font-medium text-gray-800">{{ $doc->name }}</p><p class="text-xs text-gray-500">{{ ucfirst($doc->category) }} • {{ number_format($doc->file_size/1024, 0) }} KB</p></div>
                        </div>
                        <a href="{{ route('user.document.download', $doc->id) }}" class="text-blue-500 hover:text-blue-600 text-xs font-medium"><i class="fas fa-download mr-1"></i> Unduh</a>
                    </div>
                    @endforeach
                </div>
                @endif

                <form action="{{ route('user.document.store', $booking->id) }}" method="POST" enctype="multipart/form-data" class="border-2 border-dashed border-gray-200 rounded-xl p-4">
                    @csrf
                    <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mb-3">Upload Dokumen</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <input type="text" name="name" placeholder="Nama dokumen" required class="border border-gray-200 dark:border-white/10 bg-white dark:bg-white/10 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-yellow-400">
                        <select name="category" class="border border-gray-200 dark:border-white/10 bg-white dark:bg-white/10 rounded-lg px-3 py-2 text-xs text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-yellow-400">
                            <option value="photo" class="dark:bg-[#1a1a1a]">Foto</option>
                            <option value="contract" class="dark:bg-[#1a1a1a]">Kontrak</option>
                            <option value="other" class="dark:bg-[#1a1a1a]">Lainnya</option>
                        </select>
                        <input type="file" name="file" required class="text-xs text-gray-600 dark:text-gray-400 file:cursor-pointer file:mr-2 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-yellow-50 file:text-yellow-700 hover:file:bg-yellow-100 dark:file:bg-[#202020] dark:file:text-yellow-500 dark:hover:file:bg-[#2a2a2a] transition-all">
                    </div>
                    <button type="submit" class="mt-3 text-xs font-semibold px-4 py-2 gold-gradient text-white rounded-lg hover:shadow-md transition-all">Upload</button>
                </form>
            </div>

            {{-- Review: hanya untuk paket wedding yang selesai --}}
            @if(!$isInvitationOnly && $booking->status === 'completed' && !$booking->review)
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2"><i class="fas fa-star text-yellow-500"></i> Berikan Ulasan</h3>
                <form action="{{ route('user.booking.review', $booking->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4" x-data="{ rating: 5 }">
                    @csrf
                    <div>
                        <p class="text-sm text-gray-600 mb-2">Rating</p>
                        <div class="flex gap-2">
                            @for($i = 1; $i <= 5; $i++)
                            <button type="button" @click="rating = {{ $i }}" class="text-2xl transition-colors" :class="{{ $i }} <= rating ? 'text-yellow-400' : 'text-gray-300'">★</button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" :value="rating">
                    </div>
                    <input type="text" name="title" placeholder="Judul ulasan (opsional)" class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/10 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-400">
                    <textarea name="review" rows="4" placeholder="Ceritakan pengalaman Anda bersama Anggita WO..." required
                              class="w-full border border-gray-200 dark:border-white/10 bg-white dark:bg-white/10 rounded-xl px-4 py-3 text-sm text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 resize-none"></textarea>
                    <button type="submit" class="gold-gradient text-white font-semibold px-6 py-2.5 rounded-xl text-sm hover:shadow-lg transition-all">
                        <i class="fas fa-paper-plane mr-2"></i> Kirim Ulasan
                    </button>
                </form>
            </div>
            @elseif(!$isInvitationOnly && $booking->review)
            <div class="bg-white rounded-2xl shadow-sm p-6">
                <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2"><i class="fas fa-star text-yellow-500"></i> Ulasan Anda</h3>
                <div class="flex gap-1 mb-2">@for($i=1;$i<=5;$i++)<i class="fas fa-star text-sm {{ $i <= $booking->review->rating ? 'text-yellow-400' : 'text-gray-200' }}"></i>@endfor</div>
                @if($booking->review->title)<p class="font-semibold text-gray-800 mb-1">{{ $booking->review->title }}</p>@endif
                <p class="text-sm text-gray-600">{{ $booking->review->review }}</p>
                <p class="text-xs text-gray-400 mt-2">{{ $booking->review->is_published ? '✅ Dipublikasikan' : '⏳ Menunggu moderasi' }}</p>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Quick Actions --}}
            <div class="bg-white dark:bg-white/5 rounded-2xl shadow-sm p-5 border border-gray-100 dark:border-white/10" x-data="bookingActions({{ $booking->id }}, '{{ $booking->event_date->toDateString() }}')">
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-4 text-sm">Aksi Cepat</h3>
                <div class="space-y-2">
                    <a href="{{ route('user.chat.index', $booking->id) }}"
                       class="flex items-center gap-3 p-3 bg-blue-50 text-blue-700 rounded-xl text-sm font-medium hover:bg-blue-100 transition-colors">
                        <i class="fas fa-comments w-4 text-center"></i> Chat Admin
                        @if($unreadChats > 0)<span class="ml-auto bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">{{ $unreadChats }}</span>@endif
                    </a>
                    <a href="{{ route('user.booking.invoice', $booking->id) }}" data-no-loader
                       class="flex items-center gap-3 p-3 bg-green-50 text-green-700 rounded-xl text-sm font-medium hover:bg-green-100 transition-colors">
                        <i class="fas fa-file-invoice w-4 text-center"></i> Download Invoice
                    </a>
                    @if(in_array($booking->status, ['dp_paid','in_progress','completed']) && $booking->invitation)
                    <a href="{{ route('user.invitation.index', $booking->id) }}"
                       class="flex items-center gap-3 p-3 bg-purple-50 text-purple-700 rounded-xl text-sm font-medium hover:bg-purple-100 transition-colors">
                        <i class="fas fa-envelope-open-text w-4 text-center"></i> Undangan Digital
                    </a>
                    @endif
                    @if($isInvitationOnly)
                        @if($booking->payment_status === 'unpaid')
                        <a href="{{ route('payment.checkout', $booking->id) }}"
                           class="flex items-center gap-3 p-3 gold-gradient text-white rounded-xl text-sm font-medium hover:shadow-md transition-all">
                            <i class="fas fa-credit-card w-4 text-center"></i> Bayar Undangan
                        </a>
                        @endif
                        @if($canUserCancel)
                        <button type="button" @click="confirmCancel = true"
                                class="w-full flex items-center gap-3 p-3 border border-red-200 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                            <i class="fas fa-ban w-4 text-center"></i> Batalkan Pesanan
                        </button>
                        @endif
                        <div class="text-xs text-gray-400 border border-dashed border-gray-200 rounded-xl px-3 py-2">
                            Aksi lain seperti ganti paket & ubah tanggal tidak berlaku untuk pesanan undangan digital saja.
                        </div>
                    @else
                        @if($booking->status === 'pending')
                        <a href="{{ route('payment.checkout', $booking->id) }}"
                           class="flex items-center gap-3 p-3 gold-gradient text-white rounded-xl text-sm font-medium hover:shadow-md transition-all">
                            <i class="fas fa-credit-card w-4 text-center"></i> Bayar DP
                        </a>
                        <button type="button" @click="openPackageModal()"
                                class="w-full flex items-center gap-3 p-3 border border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-swatchbook w-4 text-center"></i> Ganti Paket
                        </button>
                        <button type="button" @click="openRescheduleModal()"
                                class="w-full flex items-center gap-3 p-3 border border-yellow-200 rounded-xl text-sm font-medium text-yellow-700 bg-yellow-50 hover:bg-yellow-100 transition-colors">
                            <i class="fas fa-calendar-alt w-4 text-center"></i> Ajukan Ubah Tanggal
                        </button>
                        @if($canUserCancel)
                            <button type="button" @click="confirmCancel = true"
                                    class="w-full flex items-center gap-3 p-3 border border-red-200 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 transition-colors">
                                <i class="fas fa-ban w-4 text-center"></i> Batalkan Booking
                            </button>
                        @endif
                        @endif
                    @endif
                </div>

                {{-- Change Package Modal --}}
                <div x-show="packageModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
                    <div class="bg-white rounded-3xl shadow-2xl max-w-3xl w-full p-6 relative" @click.outside="closePackageModal()">
                        <button class="absolute top-4 right-4 text-gray-400 hover:text-gray-600" @click="closePackageModal()"><i class="fas fa-times"></i></button>
                        <h4 class="font-semibold text-lg.mb-4">Ganti Paket</h4>
                        <p class="text-sm text-gray-500 mb-4">Pilih paket baru. Harga akan menyesuaikan dan DP mungkin berubah.</p>
                        <form method="POST" action="{{ route('user.booking.change-package', $booking->id) }}" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-72 overflow-y-auto">
                                @foreach($availablePackages as $pkg)
                                <label class="border rounded-2xl p-4 cursor-pointer hover:border-yellow-400 transition-colors flex flex-col gap-2" :class="selectedPackage == {{ $pkg->id }} ? 'border-yellow-400 bg-yellow-50' : ''">
                                    <input type="radio" name="package_id" class="hidden" value="{{ $pkg->id }}" @change="selectedPackage = {{ $pkg->id }}">
                                    <span class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $pkg->name }}</span>
                                    <span class="text-xs text-gray-500">{{ $pkg->formattedEffectivePrice }}</span>
                                </label>
                                @endforeach
                            </div>
                            <div class="flex justify-end gap-3 text-sm">
                                <button type="button" class="px-4 py-2 rounded-xl border" @click="closePackageModal()">Batal</button>
                                <button type="submit" class="px-4 py-2 rounded-xl gold-gradient text-white font-semibold disabled:opacity-50" :disabled="!selectedPackage">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Reschedule Modal --}}
                <div x-show="rescheduleModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
                    <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 relative" @click.outside="closeRescheduleModal()">
                        <button class="absolute top-4 right-4 text-gray-400 hover:text-gray-600" @click="closeRescheduleModal()"><i class="fas fa-times"></i></button>
                        <h4 class="font-semibold text-lg mb-4">Ajukan Ubah Tanggal</h4>
                        <form method="POST" action="{{ route('user.booking.reschedule', $booking->id) }}" class="space-y-3">
                            @csrf
                            @method('PUT')
                            <label class="text-sm font-medium text-gray-700">Tanggal Baru <span class="text-red-500">*</span></label>
                            <input type="date" name="event_date" x-model="rescheduleDate" :min="minDate" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:ring-2 focus:ring-yellow-400">
                            <button type="button" class="px-4 py-2 rounded-xl border text-sm font-semibold" :disabled="!rescheduleDate" @click="checkAvailability()">
                                <i class="fas fa-search mr-1"></i> Cek Ketersediaan
                            </button>
                            <template x-if="availability">
                                <div class="p-3 rounded-xl text-sm" :class="availability.status === 'available' ? 'bg-green-50 text-green-700 border border-green-200' : availability.status === 'tentative' ? 'bg-yellow-50 text-yellow-700 border border-yellow-200' : 'bg-red-50 text-red-600 border border-red-200'">
                                    <p class="font-semibold mb-1" x-text="availability.label"></p>
                                    <p x-text="availability.message"></p>
                                </div>
                            </template>
                            <div class="flex justify-end gap-3 text-sm pt-2">
                                <button type="button" class="px-4 py-2 rounded-xl border" @click="closeRescheduleModal()">Batal</button>
                                <button type="submit" class="px-4 py-2 rounded-xl gold-gradient text-white font-semibold disabled:opacity-50" :disabled="!rescheduleDate || !availability || availability.status === 'full'">Kirim Permintaan</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Cancel Confirmation --}}
                <div x-show="confirmCancel" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
                    <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full p-6" @click.outside="confirmCancel = false">
                        <h4 class="font-semibold text-lg mb-2 text-red-600">Batalkan Booking?</h4>
                        <p class="text-sm text-gray-600 mb-4">Pembatalan tidak bisa diubah lagi, dan DP yang sudah dibayar mengikuti kebijakan refund. Yakin ingin melanjutkan?</p>
                        <div class="flex justify-end gap-3 text-sm">
                            <button class="px-4 py-2 rounded-xl border" @click="confirmCancel = false">Tutup</button>
                            <form method="POST" action="{{ route('user.booking.cancel', $booking->id) }}">
                                @csrf
                                <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white font-semibold">Ya, Batalkan</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Consultation --}}
            @if($booking->consultations->isNotEmpty())
            <div class="bg-white dark:bg-white/5 rounded-2xl shadow-sm p-5 border border-gray-100 dark:border-white/10">
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-3 text-sm">Jadwal Konsultasi</h3>
                @foreach($booking->consultations->take(3) as $c)
                <div class="p-3 bg-gray-50 rounded-xl text-xs mb-2">
                    <p class="font-semibold text-gray-700">{{ $c->preferred_date->isoFormat('D MMM Y') }} – {{ $c->preferred_time }}</p>
                    <p class="text-gray-500 mt-0.5">{{ ucfirst($c->consultation_type) }} • <span class="{{ 'confirmed'===$c->status?'text-green-600':'text-yellow-600' }}">{{ $c->status_label }}</span></p>
                </div>
                @endforeach
            </div>
            @endif

            {{-- Vendors --}}
            @if($booking->vendors->isNotEmpty())
            <div class="bg-white dark:bg-white/5 rounded-2xl shadow-sm p-5 border border-gray-100 dark:border-white/10">
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-3 text-sm">Vendor Terkonfirmasi</h3>
                <div class="space-y-2">
                    @foreach($booking->vendors as $v)
                    <div class="flex items-center justify-between text-sm">
                        <div>
                            <p class="font-medium text-gray-700">{{ $v->vendor_name }}</p>
                            <p class="text-xs text-gray-500">{{ $v->category }}</p>
                        </div>
                        <span class="text-xs px-2 py-0.5 rounded-full {{ $v->status==='confirmed'?'bg-green-100 text-green-700':'bg-yellow-100 text-yellow-700' }}">{{ ucfirst($v->status) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function bookingActions(bookingId, currentDate) {
    return {
        packageModal: false,
        rescheduleModal: false,
        confirmCancel: false,
        selectedPackage: null,
        rescheduleDate: currentDate,
        availability: null,
        minDate: new Date().toISOString().split('T')[0],
        openPackageModal() { this.packageModal = true; },
        closePackageModal() { this.packageModal = false; this.selectedPackage = null; },
        openRescheduleModal() { this.rescheduleModal = true; this.availability = null; this.rescheduleDate = currentDate; },
        closeRescheduleModal() { this.rescheduleModal = false; },
        async checkAvailability() {
            if (!this.rescheduleDate) return;
            const res = await fetch(`{{ route('booking.check-date') }}?date=${this.rescheduleDate}&booking_id=${bookingId}`);
            this.availability = await res.json();
        },
    };
}
</script>
@endpush
