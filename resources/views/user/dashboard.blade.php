@extends('layouts.app')
@section('title', 'Dashboard Klien')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">

    {{-- Welcome Banner --}}
    <div class="gold-gradient rounded-2xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-playfair text-2xl font-bold">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
                <p class="text-yellow-100 text-sm mt-1">
                    @if($latestBooking)
                        {{ $latestBooking->couple_short_display }} –
                        <strong>{{ $latestBooking->event_date->diffForHumans() }}</strong>
                    @else
                        Mulai rencanakan pernikahan impian Anda sekarang.
                    @endif
                </p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-rings-wedding text-white/30 text-6xl"></i>
            </div>
        </div>
    </div>

    @if($bookings->isEmpty())
    {{-- No Booking CTA --}}
    <div class="bg-white rounded-2xl shadow-sm p-10 text-center">
        <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-calendar-plus text-yellow-500 text-2xl"></i>
        </div>
        <h3 class="font-playfair text-2xl font-bold text-gray-800 mb-2">Belum Ada Booking</h3>
        <p class="text-gray-500 mb-6">Ayo mulai rencanakan hari istimewa Anda bersama Anggita Wedding Organizer</p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('booking.start') }}" class="gold-gradient text-white font-semibold px-6 py-3 rounded-xl text-sm hover:shadow-lg transition-all">
                <i class="fas fa-calendar-check mr-2"></i> Pesan Paket Sekarang
            </a>
            <a href="{{ route('consultation.form') }}" class="border border-yellow-400 text-yellow-600 font-semibold px-6 py-3 rounded-xl text-sm hover:bg-yellow-50 transition-all">
                <i class="fas fa-comments mr-2"></i> Konsultasi Gratis
            </a>
        </div>
    </div>
    @else

    {{-- Booking Cards --}}
    <div>
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Booking Saya</h3>
        <div class="space-y-4">
            @foreach($bookings as $booking)
            @php
                $extraTotal = $booking->active_extra_charges_total;
                $grandTotal = $booking->package_price + $extraTotal;
                $isInvitationOnly = $booking->is_invitation_only;
                $templateName = optional(optional($booking->invitation)->template)->name;
                $paymentStatusMap = [
                    'unpaid' => 'Belum Bayar',
                    'dp_paid' => 'DP Terbayar',
                    'partially_paid' => 'Cicilan',
                    'paid_full' => 'Lunas',
                ];
            @endphp
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-md transition-all">
                <div class="flex items-stretch">
                    <div class="w-2 flex-shrink-0 {{ ['pending'=>'bg-yellow-400','dp_paid'=>'bg-blue-500','in_progress'=>'bg-indigo-500','completed'=>'bg-green-500','cancelled'=>'bg-red-400'][$booking->status] ?? 'bg-gray-300' }}"></div>
                    <div class="flex-1 p-5">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    <h4 class="font-bold text-gray-800 text-lg font-playfair">{{ $booking->couple_short_display }}</h4>
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                        {{ ['pending'=>'bg-yellow-100 text-yellow-700','dp_paid'=>'bg-blue-100 text-blue-700','in_progress'=>'bg-indigo-100 text-indigo-700','completed'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-600'][$booking->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $booking->status_label }}
                                    </span>
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600">
                                        Pembayaran: {{ $paymentStatusMap[$booking->payment_status] ?? ucwords(str_replace('_', ' ', $booking->payment_status)) }}
                                    </span>
                                    @if($isInvitationOnly)
                                        <span class="px-2 py-0.5 rounded-full text-[11px] font-semibold bg-purple-100 text-purple-700">Undangan Digital Saja</span>
                                    @endif
                                </div>
                                <div class="flex flex-wrap gap-3 text-xs text-gray-500 mt-1">
                                    <span><i class="fas fa-calendar mr-1 text-yellow-500"></i>{{ $booking->event_date->isoFormat('D MMMM Y') }}</span>
                                    @if($isInvitationOnly)
                                        <span><i class="fas fa-tag mr-1 text-purple-500"></i>Undangan Digital{{ $templateName ? ' • ' . $templateName : '' }}</span>
                                    @else
                                        <span><i class="fas fa-map-marker-alt mr-1 text-yellow-500"></i>{{ $booking->venue }}</span>
                                        <span><i class="fas fa-tag mr-1 text-yellow-500"></i>{{ $booking->package->name }}</span>
                                    @endif
                                    <span><i class="fas fa-hashtag mr-1 text-yellow-500"></i>{{ $booking->booking_code }}</span>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <p class="text-xs text-gray-500">{{ $isInvitationOnly ? 'Pembayaran Masuk' : 'Total Bayar' }}</p>
                                <p class="font-bold text-gray-800">Rp {{ number_format($booking->total_paid, 0, ',', '.') }}</p>
                                <p class="text-xs text-gray-400">{{ $isInvitationOnly ? 'Tagihan: Rp ' . number_format($grandTotal, 0, ',', '.') : 'dari Rp ' . number_format($grandTotal, 0, ',', '.') }}</p>
                                @if($extraTotal > 0)
                                    <p class="text-[11px] text-gray-400">(termasuk biaya tambahan Rp {{ number_format($extraTotal, 0, ',', '.') }})</p>
                                @endif
                                @if($isInvitationOnly)
                                    <p class="text-[11px] text-purple-600 mt-1 flex items-center gap-1 justify-end">
                                        <i class="fas fa-bullhorn"></i>
                                        Status Undangan: {{ $booking->invitation && $booking->invitation->is_published ? 'Sudah Publish' : 'Belum Publish' }}
                                    </p>
                                @endif
                            </div>
                        </div>

                        <div class="mt-3 flex flex-wrap gap-2">
                            <a href="{{ route('user.booking.show', $booking->id) }}" class="text-xs font-medium px-3 py-1.5 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition-colors">
                                <i class="fas fa-eye mr-1"></i> Detail
                            </a>
                            @if($isInvitationOnly ? ($booking->payment_status === 'unpaid') : ($booking->status === 'pending'))
                            <a href="{{ route('payment.checkout', $booking->id) }}" class="text-xs font-medium px-3 py-1.5 gold-gradient text-white rounded-lg hover:shadow-md transition-all">
                                <i class="fas fa-credit-card mr-1"></i> {{ $isInvitationOnly ? 'Bayar Undangan' : 'Bayar DP' }}
                            </a>
                            @endif
                            @if($booking->invitation && ($isInvitationOnly || in_array($booking->status, ['dp_paid','in_progress','completed'])))
                            <a href="{{ route('user.invitation.index', $booking->id) }}" class="text-xs font-medium px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors">
                                <i class="fas fa-envelope-open-text mr-1"></i> Undangan
                            </a>
                            @endif
                            <a href="{{ route('user.chat.index', $booking->id) }}" class="text-xs font-medium px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                <i class="fas fa-comments mr-1"></i> Chat Admin
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @if($consultations->count() > 0)
    {{-- Consultations --}}
    <div>
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Konsultasi Terbaru</h3>
        <div class="bg-white rounded-2xl shadow-sm divide-y">
            @foreach($consultations as $c)
            <div class="p-4 flex items-center justify-between">
                <div>
                    <p class="font-medium text-gray-800 text-sm">{{ $c->consultation_code }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        <i class="fas fa-calendar mr-1"></i>{{ $c->preferred_date->isoFormat('D MMM Y') }} pukul {{ $c->preferred_time }}
                        • {{ $c->consultation_type === 'online' ? 'Online' : 'Offline' }}
                    </p>
                </div>
                <span class="px-2 py-1 rounded-full text-xs font-semibold
                    {{ ['pending'=>'bg-yellow-100 text-yellow-700','confirmed'=>'bg-blue-100 text-blue-700','done'=>'bg-green-100 text-green-700','cancelled'=>'bg-red-100 text-red-600','converted'=>'bg-purple-100 text-purple-700'][$c->status] ?? 'bg-gray-100 text-gray-600' }}">
                    {{ $c->status_label }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    @endif
</div>
@endsection
