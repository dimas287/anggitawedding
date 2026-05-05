@extends('layouts.app')
@section('title', 'Dashboard Klien')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Klien Area')

@section('content')
<div style="space-y: 0;">

    {{-- ─── WELCOME BANNER ─── --}}
    <div style="
        background: linear-gradient(135deg, rgba(124,92,191,.3) 0%, rgba(201,168,76,.2) 100%);
        border: 1px solid rgba(201,168,76,.2);
        border-radius: 20px;
        padding: 28px 28px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    ">
        {{-- Decorative orbs --}}
        <div style="position:absolute;top:-30px;right:-30px;width:120px;height:120px;background:radial-gradient(circle,rgba(201,168,76,.15),transparent 70%);border-radius:50%;pointer-events:none;"></div>
        <div style="position:absolute;bottom:-20px;left:40%;width:80px;height:80px;background:radial-gradient(circle,rgba(124,92,191,.15),transparent 70%);border-radius:50%;pointer-events:none;"></div>

        <div style="display:flex;align-items:center;justify-content:space-between;position:relative;z-index:1;">
            <div>
                <p style="font-size:12px;letter-spacing:.2em;text-transform:uppercase;color:rgba(201,168,76,.8);margin-bottom:6px;">
                    Selamat datang kembali
                </p>
                <h2 style="font-family:'Playfair Display',serif;font-size:26px;font-weight:700;color:#f0e8d8;margin-bottom:6px;">
                    {{ auth()->user()->name }}
                </h2>
                <p style="font-size:13px;color:rgba(255,255,255,.5);">
                    @if($latestBooking)
                        {{ $latestBooking->couple_short_display }} –
                        <strong style="color:rgba(201,168,76,.8);">{{ $latestBooking->event_date->diffForHumans() }}</strong>
                    @else
                        Mulai rencanakan pernikahan impian Anda sekarang.
                    @endif
                </p>
            </div>
            <div style="opacity:.15;font-size:64px;color:#c9a84c;flex-shrink:0;margin-left:16px;" class="hidden md:block">
                <i class="fas fa-rings-wedding"></i>
            </div>
        </div>
    </div>

    @if($bookings->isEmpty())
    {{-- ─── NO BOOKING CTA ─── --}}
    <div style="
        background: rgba(255,255,255,.04);
        border: 1px solid rgba(255,255,255,.07);
        border-radius: 20px;
        padding: 60px 32px;
        text-align: center;
    ">
        <div style="
            width: 72px; height: 72px;
            background: linear-gradient(135deg, rgba(201,168,76,.2), rgba(124,92,191,.2));
            border: 1px solid rgba(201,168,76,.25);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px;
            color: #c9a84c;
        ">
            <i class="fas fa-calendar-plus"></i>
        </div>
        <h3 style="font-family:'Playfair Display',serif;font-size:22px;font-weight:700;color:#f0e8d8;margin-bottom:8px;">
            Belum Ada Booking
        </h3>
        <p style="font-size:14px;color:rgba(255,255,255,.4);margin-bottom:28px;max-width:320px;margin-left:auto;margin-right:auto;">
            Ayo mulai rencanakan hari istimewa Anda bersama kami
        </p>
        <div style="display:flex;flex-direction:column;gap:12px;align-items:center;" class="sm:flex-row sm:justify-center">
            <a href="{{ route('booking.start') }}" style="
                display: inline-flex; align-items: center; gap: 8px;
                padding: 12px 24px;
                background: linear-gradient(135deg, #c9a84c, #7c5cbf);
                color: #fff;
                border-radius: 12px;
                font-size: 13px; font-weight: 600;
                text-decoration: none;
                transition: all .2s;
            ">
                <i class="fas fa-calendar-check"></i> Pesan Paket Sekarang
            </a>
            <a href="{{ route('consultation.form') }}" style="
                display: inline-flex; align-items: center; gap: 8px;
                padding: 12px 24px;
                background: rgba(255,255,255,.06);
                border: 1px solid rgba(255,255,255,.12);
                color: rgba(255,255,255,.7);
                border-radius: 12px;
                font-size: 13px; font-weight: 600;
                text-decoration: none;
                transition: all .2s;
            ">
                <i class="fas fa-comments"></i> Konsultasi Gratis
            </a>
        </div>
    </div>

    @else

    {{-- ─── BOOKING CARDS ─── --}}
    <div style="margin-bottom: 28px;">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <div style="width:3px;height:18px;background:linear-gradient(180deg,#c9a84c,#7c5cbf);border-radius:2px;"></div>
            <h3 style="font-size:12px;font-weight:600;letter-spacing:.2em;text-transform:uppercase;color:rgba(255,255,255,.4);">
                Booking Saya
            </h3>
        </div>

        <div style="display:flex;flex-direction:column;gap:12px;">
            @foreach($bookings as $booking)
            @php
                $extraTotal = $booking->active_extra_charges_total;
                $grandTotal = $booking->package_price + $extraTotal;
                $isInvitationOnly = $booking->is_invitation_only;
                $templateName = optional(optional($booking->invitation)->template)->name;
                $statusColors = [
                    'pending'     => ['bg' => 'rgba(234,179,8,.15)',  'border' => 'rgba(234,179,8,.3)',  'text' => '#facc15', 'dot' => '#eab308'],
                    'dp_paid'     => ['bg' => 'rgba(59,130,246,.15)', 'border' => 'rgba(59,130,246,.3)', 'text' => '#60a5fa', 'dot' => '#3b82f6'],
                    'in_progress' => ['bg' => 'rgba(99,102,241,.15)', 'border' => 'rgba(99,102,241,.3)', 'text' => '#818cf8', 'dot' => '#6366f1'],
                    'completed'   => ['bg' => 'rgba(34,197,94,.15)',  'border' => 'rgba(34,197,94,.3)',  'text' => '#4ade80', 'dot' => '#22c55e'],
                    'cancelled'   => ['bg' => 'rgba(239,68,68,.15)',  'border' => 'rgba(239,68,68,.3)',  'text' => '#f87171', 'dot' => '#ef4444'],
                ];
                $sc = $statusColors[$booking->status] ?? ['bg'=>'rgba(255,255,255,.06)','border'=>'rgba(255,255,255,.1)','text'=>'rgba(255,255,255,.5)','dot'=>'rgba(255,255,255,.3)'];
                $paymentStatusMap = ['unpaid'=>'Belum Bayar','dp_paid'=>'DP Terbayar','partially_paid'=>'Cicilan','paid_full'=>'Lunas'];
            @endphp

            <div style="
                background: rgba(255,255,255,.04);
                border: 1px solid rgba(255,255,255,.07);
                border-radius: 16px;
                overflow: hidden;
                transition: all .2s;
            " onmouseover="this.style.background='rgba(255,255,255,.06)';this.style.borderColor='rgba(201,168,76,.15)'" onmouseout="this.style.background='rgba(255,255,255,.04)';this.style.borderColor='rgba(255,255,255,.07)'">

                {{-- Status strip --}}
                <div style="height:3px;background:{{ $sc['dot'] }};opacity:.8;"></div>

                <div style="padding: 20px;">
                    {{-- Header row --}}
                    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:14px;">
                        <div style="flex:1;min-width:0;">
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:6px;">
                                <h4 style="font-family:'Playfair Display',serif;font-size:18px;font-weight:700;color:#f0e8d8;">
                                    {{ $booking->couple_short_display }}
                                </h4>

                                {{-- Status badge --}}
                                <span style="
                                    display: inline-flex; align-items: center; gap: 5px;
                                    padding: 3px 10px;
                                    background: {{ $sc['bg'] }};
                                    border: 1px solid {{ $sc['border'] }};
                                    border-radius: 20px;
                                    font-size: 11px; font-weight: 600;
                                    color: {{ $sc['text'] }};
                                ">
                                    <span style="width:5px;height:5px;border-radius:50%;background:{{ $sc['dot'] }};display:inline-block;"></span>
                                    {{ $booking->status_label }}
                                </span>

                                @if($isInvitationOnly)
                                <span style="padding:3px 10px;background:rgba(192,132,252,.12);border:1px solid rgba(192,132,252,.25);border-radius:20px;font-size:11px;font-weight:600;color:#c084fc;">
                                    Undangan Digital
                                </span>
                                @endif
                            </div>

                            {{-- Meta info --}}
                            <div style="display:flex;flex-wrap:wrap;gap:12px;font-size:12px;color:rgba(255,255,255,.4);">
                                <span>
                                    <i class="fas fa-calendar" style="color:#c9a84c;margin-right:4px;"></i>
                                    {{ $booking->event_date->isoFormat('D MMMM Y') }}
                                </span>
                                @if(!$isInvitationOnly && $booking->venue)
                                <span>
                                    <i class="fas fa-map-marker-alt" style="color:#c9a84c;margin-right:4px;"></i>
                                    {{ $booking->venue }}
                                </span>
                                @endif
                                <span>
                                    <i class="fas fa-tag" style="color:#c9a84c;margin-right:4px;"></i>
                                    {{ $isInvitationOnly ? ($templateName ?? 'Undangan Digital') : $booking->package->name }}
                                </span>
                                <span>
                                    <i class="fas fa-hashtag" style="color:#c9a84c;margin-right:4px;"></i>
                                    {{ $booking->booking_code }}
                                </span>
                            </div>
                        </div>

                        {{-- Payment info --}}
                        <div style="text-align:right;flex-shrink:0;">
                            <p style="font-size:10px;color:rgba(255,255,255,.35);margin-bottom:3px;">
                                {{ $isInvitationOnly ? 'Terbayar' : 'Total Bayar' }}
                            </p>
                            <p style="font-size:17px;font-weight:700;color:#f0e8d8;">
                                Rp {{ number_format($booking->total_paid, 0, ',', '.') }}
                            </p>
                            <p style="font-size:11px;color:rgba(255,255,255,.3);">
                                dari Rp {{ number_format($grandTotal, 0, ',', '.') }}
                            </p>
                            {{-- Payment status pill --}}
                            <div style="margin-top:6px;">
                                <span style="font-size:10px;padding:2px 8px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1);border-radius:20px;color:rgba(255,255,255,.5);">
                                    {{ $paymentStatusMap[$booking->payment_status] ?? ucwords(str_replace('_',' ',$booking->payment_status)) }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Action buttons --}}
                    <div style="display:flex;flex-wrap:wrap;gap:8px;padding-top:14px;border-top:1px solid rgba(255,255,255,.06);">
                        <a href="{{ route('user.booking.show', $booking->id) }}" style="
                            display:inline-flex;align-items:center;gap:6px;
                            padding:7px 14px;
                            background:rgba(255,255,255,.07);
                            border:1px solid rgba(255,255,255,.1);
                            color:rgba(255,255,255,.7);
                            border-radius:10px;
                            font-size:12px;font-weight:600;
                            text-decoration:none;
                            transition:all .2s;
                        " onmouseover="this.style.background='rgba(255,255,255,.12)'" onmouseout="this.style.background='rgba(255,255,255,.07)'">
                            <i class="fas fa-eye"></i> Detail
                        </a>

                        @if($isInvitationOnly ? ($booking->payment_status === 'unpaid') : ($booking->status === 'pending'))
                        <a href="{{ route('payment.checkout', $booking->id) }}" style="
                            display:inline-flex;align-items:center;gap:6px;
                            padding:7px 14px;
                            background:linear-gradient(135deg,rgba(201,168,76,.8),rgba(124,92,191,.8));
                            color:#fff;
                            border-radius:10px;
                            font-size:12px;font-weight:600;
                            text-decoration:none;
                            border:1px solid transparent;
                            transition:all .2s;
                        ">
                            <i class="fas fa-credit-card"></i>
                            {{ $isInvitationOnly ? 'Bayar Undangan' : 'Bayar DP' }}
                        </a>
                        @endif

                        @if($booking->invitation && ($isInvitationOnly || in_array($booking->status, ['dp_paid','in_progress','completed'])))
                        <a href="{{ route('user.invitation.index', $booking->id) }}" style="
                            display:inline-flex;align-items:center;gap:6px;
                            padding:7px 14px;
                            background:rgba(192,132,252,.12);
                            border:1px solid rgba(192,132,252,.25);
                            color:#c084fc;
                            border-radius:10px;
                            font-size:12px;font-weight:600;
                            text-decoration:none;
                            transition:all .2s;
                        ">
                            <i class="fas fa-envelope-open-text"></i> Undangan
                        </a>
                        @endif

                        <a href="{{ route('user.chat.index', $booking->id) }}" style="
                            display:inline-flex;align-items:center;gap:6px;
                            padding:7px 14px;
                            background:rgba(96,165,250,.1);
                            border:1px solid rgba(96,165,250,.2);
                            color:#60a5fa;
                            border-radius:10px;
                            font-size:12px;font-weight:600;
                            text-decoration:none;
                            transition:all .2s;
                        ">
                            <i class="fas fa-comments"></i> Chat Admin
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    @if($consultations->count() > 0)
    {{-- ─── CONSULTATIONS ─── --}}
    <div>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
            <div style="width:3px;height:18px;background:linear-gradient(180deg,#60a5fa,#818cf8);border-radius:2px;"></div>
            <h3 style="font-size:12px;font-weight:600;letter-spacing:.2em;text-transform:uppercase;color:rgba(255,255,255,.4);">
                Konsultasi Terbaru
            </h3>
        </div>

        <div style="
            background: rgba(255,255,255,.03);
            border: 1px solid rgba(255,255,255,.06);
            border-radius: 16px;
            overflow: hidden;
        ">
            @foreach($consultations as $c)
            @php
                $cColors = ['pending'=>['bg'=>'rgba(234,179,8,.12)','border'=>'rgba(234,179,8,.25)','text'=>'#facc15'],'confirmed'=>['bg'=>'rgba(59,130,246,.12)','border'=>'rgba(59,130,246,.25)','text'=>'#60a5fa'],'done'=>['bg'=>'rgba(34,197,94,.12)','border'=>'rgba(34,197,94,.25)','text'=>'#4ade80'],'cancelled'=>['bg'=>'rgba(239,68,68,.12)','border'=>'rgba(239,68,68,.25)','text'=>'#f87171'],'converted'=>['bg'=>'rgba(168,85,247,.12)','border'=>'rgba(168,85,247,.25)','text'=>'#c084fc']];
                $cc = $cColors[$c->status] ?? ['bg'=>'rgba(255,255,255,.05)','border'=>'rgba(255,255,255,.1)','text'=>'rgba(255,255,255,.5)'];
            @endphp
            <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(255,255,255,.05);" class="last:border-0">
                <div>
                    <p style="font-size:13px;font-weight:600;color:rgba(255,255,255,.75);margin-bottom:3px;">
                        {{ $c->consultation_code }}
                    </p>
                    <p style="font-size:12px;color:rgba(255,255,255,.35);">
                        <i class="fas fa-calendar" style="margin-right:4px;color:#c9a84c;"></i>
                        {{ $c->preferred_date->isoFormat('D MMM Y') }} pukul {{ $c->preferred_time }}
                        &bull; {{ $c->consultation_type === 'online' ? 'Online' : 'Offline' }}
                    </p>
                </div>
                <span style="padding:4px 12px;background:{{ $cc['bg'] }};border:1px solid {{ $cc['border'] }};border-radius:20px;font-size:11px;font-weight:600;color:{{ $cc['text'] }};">
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
