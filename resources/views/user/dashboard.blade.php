@extends('layouts.app')
@section('title', 'Dashboard Klien')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Klien Area')

@push('head')
<style>
    .welcome-card {
        background: linear-gradient(135deg, rgba(124,92,191,.2) 0%, rgba(201,168,76,.15) 100%);
        border: 1px solid rgba(201,168,76,.2);
        border-radius: 20px;
        padding: 28px;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }
    .dark .welcome-card {
        background: linear-gradient(135deg, rgba(124,92,191,.25) 0%, rgba(201,168,76,.18) 100%);
        border-color: rgba(201,168,76,.25);
    }
    .welcome-orb-1 {
        position: absolute; top: -40px; right: -20px;
        width: 130px; height: 130px;
        background: radial-gradient(circle, rgba(201,168,76,.18), transparent 70%);
        border-radius: 50%; pointer-events: none;
    }
    .welcome-orb-2 {
        position: absolute; bottom: -30px; left: 30%;
        width: 90px; height: 90px;
        background: radial-gradient(circle, rgba(124,92,191,.15), transparent 70%);
        border-radius: 50%; pointer-events: none;
    }

    .section-header {
        display: flex; align-items: center; gap: 10px; margin-bottom: 16px;
    }
    .section-bar {
        width: 3px; height: 18px; border-radius: 2px;
        background: linear-gradient(180deg, var(--gold), var(--purple));
    }
    .section-label {
        font-size: 11px; font-weight: 600; letter-spacing: .2em;
        text-transform: uppercase; color: var(--text-3);
    }

    /* Empty state */
    .empty-state {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: 56px 32px;
        text-align: center;
        backdrop-filter: blur(12px);
    }
    .empty-icon {
        width: 72px; height: 72px;
        background: linear-gradient(135deg, rgba(201,168,76,.15), rgba(124,92,191,.15));
        border: 1px solid rgba(201,168,76,.2);
        border-radius: 20px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 20px;
        font-size: 28px; color: var(--gold);
    }
    .empty-title {
        font-family: 'Playfair Display', serif;
        font-size: 22px; font-weight: 700;
        color: var(--text-1); margin-bottom: 8px;
    }
    .empty-desc { font-size: 14px; color: var(--text-3); margin-bottom: 28px; max-width: 300px; margin-left: auto; margin-right: auto; }

    .cta-primary {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 12px 24px;
        background: linear-gradient(135deg, var(--gold), var(--purple));
        color: #fff; border-radius: 12px;
        font-size: 13px; font-weight: 600;
        text-decoration: none; transition: all .2s;
        box-shadow: 0 4px 16px rgba(201,168,76,.3);
    }
    .cta-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 24px rgba(201,168,76,.4); }

    .cta-secondary {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 12px 24px;
        background: var(--surface-2);
        border: 1px solid var(--border);
        color: var(--text-2); border-radius: 12px;
        font-size: 13px; font-weight: 600;
        text-decoration: none; transition: all .2s;
    }
    .cta-secondary:hover { background: var(--surface); border-color: rgba(201,168,76,.3); color: var(--gold); }

    /* Booking cards */
    .booking-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 18px;
        overflow: hidden;
        transition: all .22s;
        backdrop-filter: blur(12px);
        margin-bottom: 12px;
    }
    .booking-card:hover {
        border-color: rgba(201,168,76,.3);
        transform: translateY(-1px);
        box-shadow: var(--shadow-lg);
    }
    .booking-strip { height: 3px; }
    .booking-body { padding: 20px; }
    .booking-name {
        font-family: 'Playfair Display', serif;
        font-size: 19px; font-weight: 700; color: var(--text-1);
    }
    .booking-badges { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; margin: 8px 0 10px; }
    .badge {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 3px 10px; border-radius: 20px;
        font-size: 11px; font-weight: 600; border: 1px solid transparent;
    }
    .badge-dot { width: 5px; height: 5px; border-radius: 50%; }

    .booking-meta { display: flex; flex-wrap: wrap; gap: 12px; font-size: 12px; color: var(--text-3); }
    .booking-meta i { color: var(--gold); margin-right: 3px; }

    .booking-financials {
        text-align: right; flex-shrink: 0;
    }
    .booking-amount { font-size: 17px; font-weight: 700; color: var(--text-1); }
    .booking-total { font-size: 11px; color: var(--text-3); margin-top: 2px; }

    .booking-actions {
        display: flex; flex-wrap: wrap; gap: 8px;
        padding-top: 16px;
        border-top: 1px solid var(--border);
        margin-top: 16px;
    }

    .action-btn {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 7px 13px;
        border-radius: 9px;
        font-size: 12px; font-weight: 600;
        text-decoration: none;
        border: 1px solid transparent;
        transition: all .18s;
        cursor: pointer; background: none;
    }
    .action-btn-default {
        background: var(--surface-2); border-color: var(--border); color: var(--text-2);
    }
    .action-btn-default:hover { background: var(--surface); border-color: rgba(201,168,76,.3); color: var(--gold); }
    .action-btn-gold {
        background: linear-gradient(135deg, rgba(201,168,76,.2), rgba(124,92,191,.15));
        border-color: rgba(201,168,76,.3); color: var(--gold);
    }
    .action-btn-gold:hover { background: linear-gradient(135deg, rgba(201,168,76,.3), rgba(124,92,191,.25)); }
    .action-btn-purple {
        background: rgba(192,132,252,.1); border-color: rgba(192,132,252,.25); color: #a78bfa;
    }
    .action-btn-blue {
        background: rgba(96,165,250,.1); border-color: rgba(96,165,250,.2); color: #60a5fa;
    }

    /* Consultation card */
    .consult-list {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 16px; overflow: hidden;
        backdrop-filter: blur(12px);
    }
    .consult-row {
        padding: 14px 20px;
        display: flex; align-items: center; justify-content: space-between;
        border-bottom: 1px solid var(--border);
        transition: background .18s;
    }
    .consult-row:last-child { border-bottom: none; }
    .consult-row:hover { background: var(--surface-2); }
    .consult-code { font-size: 13px; font-weight: 600; color: var(--text-1); margin-bottom: 3px; }
    .consult-meta { font-size: 12px; color: var(--text-3); }
    .consult-meta i { color: var(--gold); margin-right: 3px; }
</style>
@endpush

@section('content')
<div>
    {{-- Welcome Banner --}}
    <div class="welcome-card">
        <div class="welcome-orb-1"></div>
        <div class="welcome-orb-2"></div>
        <div style="position:relative;z-index:1;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <p style="font-size:11px;letter-spacing:.22em;text-transform:uppercase;color:var(--gold);margin-bottom:6px;">
                    Selamat datang kembali
                </p>
                <h2 class="font-playfair" style="font-size:24px;font-weight:700;color:var(--text-1);margin-bottom:6px;">
                    {{ auth()->user()->name }} 👋
                </h2>
                <p style="font-size:13px;color:var(--text-3);">
                    @if($latestBooking)
                        {{ $latestBooking->couple_short_display }} –
                        <strong style="color:var(--gold);">{{ $latestBooking->event_date->diffForHumans() }}</strong>
                    @else
                        Mulai rencanakan pernikahan impian Anda bersama kami.
                    @endif
                </p>
            </div>
            <div style="font-size:52px;color:var(--gold);opacity:.15;flex-shrink:0;margin-left:12px;" class="hidden md:block">
                <i class="fas fa-rings-wedding"></i>
            </div>
        </div>
    </div>

    @if($bookings->isEmpty())
    {{-- Empty State --}}
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-calendar-plus"></i></div>
        <div class="empty-title">Belum Ada Booking</div>
        <p class="empty-desc">Ayo mulai rencanakan hari istimewa Anda bersama Anggita Wedding Organizer</p>
        <div style="display:flex;flex-direction:column;gap:10px;align-items:center;">
            <a href="{{ route('booking.start') }}" class="cta-primary">
                <i class="fas fa-calendar-check"></i> Pesan Paket Sekarang
            </a>
            <a href="{{ route('consultation.form') }}" class="cta-secondary">
                <i class="fas fa-comments"></i> Konsultasi Gratis
            </a>
        </div>
    </div>

    @else

    {{-- Booking Cards --}}
    <div style="margin-bottom:28px;">
        <div class="section-header">
            <div class="section-bar"></div>
            <span class="section-label">Booking Saya</span>
        </div>

        @foreach($bookings as $booking)
        @php
            $extraTotal = $booking->active_extra_charges_total;
            $grandTotal = $booking->package_price + $extraTotal;
            $isInv = $booking->is_invitation_only;
            $templateName = optional(optional($booking->invitation)->template)->name;
            $payMap = ['unpaid'=>'Belum Bayar','dp_paid'=>'DP Terbayar','partially_paid'=>'Cicilan','paid_full'=>'Lunas'];

            $sc = match($booking->status) {
                'pending'     => ['strip'=>'#EAB308','bg'=>'rgba(234,179,8,.12)','border'=>'rgba(234,179,8,.3)','text'=>'#D97706','dot'=>'#EAB308'],
                'dp_paid'     => ['strip'=>'#3B82F6','bg'=>'rgba(59,130,246,.12)','border'=>'rgba(59,130,246,.3)','text'=>'#60A5FA','dot'=>'#3B82F6'],
                'in_progress' => ['strip'=>'#8B5CF6','bg'=>'rgba(139,92,246,.12)','border'=>'rgba(139,92,246,.3)','text'=>'#A78BFA','dot'=>'#8B5CF6'],
                'completed'   => ['strip'=>'#22C55E','bg'=>'rgba(34,197,94,.12)','border'=>'rgba(34,197,94,.3)','text'=>'#4ADE80','dot'=>'#22C55E'],
                'cancelled'   => ['strip'=>'#EF4444','bg'=>'rgba(239,68,68,.12)','border'=>'rgba(239,68,68,.3)','text'=>'#F87171','dot'=>'#EF4444'],
                default       => ['strip'=>'#9CA3AF','bg'=>'rgba(156,163,175,.1)','border'=>'rgba(156,163,175,.2)','text'=>'var(--text-3)','dot'=>'#9CA3AF'],
            };
        @endphp

        <div class="booking-card">
            <div class="booking-strip" style="background:{{ $sc['strip'] }};"></div>
            <div class="booking-body">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;">
                    <div style="flex:1;min-width:0;">
                        <div class="booking-name">{{ $booking->couple_short_display }}</div>
                        <div class="booking-badges">
                            <span class="badge" style="background:{{ $sc['bg'] }};border-color:{{ $sc['border'] }};color:{{ $sc['text'] }};">
                                <span class="badge-dot" style="background:{{ $sc['dot'] }};"></span>
                                {{ $booking->status_label }}
                            </span>
                            <span class="badge" style="background:var(--surface-2);border-color:var(--border);color:var(--text-3);">
                                {{ $payMap[$booking->payment_status] ?? ucwords(str_replace('_',' ',$booking->payment_status)) }}
                            </span>
                            @if($isInv)
                            <span class="badge" style="background:rgba(168,85,247,.1);border-color:rgba(168,85,247,.25);color:#c084fc;">
                                Undangan Digital
                            </span>
                            @endif
                        </div>
                        <div class="booking-meta">
                            <span><i class="fas fa-calendar"></i>{{ $booking->event_date->isoFormat('D MMMM Y') }}</span>
                            @if(!$isInv && $booking->venue)
                                <span><i class="fas fa-map-marker-alt"></i>{{ $booking->venue }}</span>
                            @endif
                            <span><i class="fas fa-tag"></i>{{ $isInv ? ($templateName ?? 'Undangan') : $booking->package->name }}</span>
                            <span><i class="fas fa-hashtag"></i>{{ $booking->booking_code }}</span>
                        </div>
                    </div>
                    <div class="booking-financials">
                        <p style="font-size:10px;color:var(--text-3);margin-bottom:3px;">{{ $isInv ? 'Terbayar' : 'Total Bayar' }}</p>
                        <p class="booking-amount">Rp {{ number_format($booking->total_paid, 0, ',', '.') }}</p>
                        <p class="booking-total">dari Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
                    </div>
                </div>

                <div class="booking-actions">
                    <a href="{{ route('user.booking.show', $booking->id) }}" class="action-btn action-btn-default">
                        <i class="fas fa-eye"></i> Detail
                    </a>
                    @if($isInv ? ($booking->payment_status === 'unpaid') : ($booking->status === 'pending'))
                    <a href="{{ route('payment.checkout', $booking->id) }}" class="action-btn action-btn-gold">
                        <i class="fas fa-credit-card"></i> {{ $isInv ? 'Bayar Undangan' : 'Bayar DP' }}
                    </a>
                    @endif
                    @if($booking->invitation && ($isInv || in_array($booking->status, ['dp_paid','in_progress','completed'])))
                    <a href="{{ route('user.invitation.index', $booking->id) }}" class="action-btn action-btn-purple">
                        <i class="fas fa-envelope-open-text"></i> Undangan
                    </a>
                    @endif
                    <a href="{{ route('user.chat.index', $booking->id) }}" class="action-btn action-btn-blue">
                        <i class="fas fa-comments"></i> Chat
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($consultations->count() > 0)
    {{-- Consultations --}}
    <div>
        <div class="section-header">
            <div class="section-bar" style="background:linear-gradient(180deg,#60a5fa,#818cf8);"></div>
            <span class="section-label">Konsultasi Terbaru</span>
        </div>
        <div class="consult-list">
            @foreach($consultations as $c)
            @php
                $cc = match($c->status) {
                    'pending'   => ['bg'=>'rgba(234,179,8,.12)','border'=>'rgba(234,179,8,.25)','text'=>'#D97706'],
                    'confirmed' => ['bg'=>'rgba(59,130,246,.12)','border'=>'rgba(59,130,246,.25)','text'=>'#60A5FA'],
                    'done'      => ['bg'=>'rgba(34,197,94,.12)','border'=>'rgba(34,197,94,.25)','text'=>'#4ADE80'],
                    'cancelled' => ['bg'=>'rgba(239,68,68,.12)','border'=>'rgba(239,68,68,.25)','text'=>'#F87171'],
                    'converted' => ['bg'=>'rgba(168,85,247,.12)','border'=>'rgba(168,85,247,.25)','text'=>'#C084FC'],
                    default     => ['bg'=>'var(--surface-2)','border'=>'var(--border)','text'=>'var(--text-3)'],
                };
            @endphp
            <div class="consult-row">
                <div>
                    <div class="consult-code">{{ $c->consultation_code }}</div>
                    <div class="consult-meta">
                        <i class="fas fa-calendar"></i>{{ $c->preferred_date->isoFormat('D MMM Y') }} pukul {{ $c->preferred_time }}
                        &bull; {{ $c->consultation_type === 'online' ? 'Online' : 'Offline' }}
                    </div>
                </div>
                <span class="badge" style="background:{{ $cc['bg'] }};border-color:{{ $cc['border'] }};color:{{ $cc['text'] }};">
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
