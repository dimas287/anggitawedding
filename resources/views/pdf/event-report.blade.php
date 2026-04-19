<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 28px 32px; }
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #333; }
        .header-table { width: 100%; border-bottom: 3px solid #D4AF37; margin-bottom: 18px; padding-bottom: 12px; }
        .header-table td { vertical-align: top; }
        .brand-logo { width: 56px; height: 56px; object-fit: contain; }
        .company-name { font-size: 20px; font-weight: bold; color: #D4AF37; }
        .report-title { font-size: 15px; font-weight: bold; margin-top: 2px; text-transform: uppercase; }
        .contact-links { margin-top: 6px; font-size: 10px; color: #666; }
        .contact-links a { color: #D4AF37; text-decoration: none; }
        .contact-links a + span,
        .contact-links span + a { margin-left: 4px; }
        .section-title { font-size: 12px; color: #D4AF37; border-bottom: 1px solid #D4AF37; padding-bottom: 4px; margin: 18px 0 10px; text-transform: uppercase; letter-spacing: .5px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        .info-table td { padding: 4px 6px; font-size: 10px; }
        .info-table .label { width: 120px; color: #777; text-transform: uppercase; letter-spacing: .4px; }
        .info-table .value { font-weight: 600; color: #222; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .data-table th { background: #f5f5f5; color: #555; font-size: 10px; text-transform: uppercase; letter-spacing: .3px; padding: 7px; border: 1px solid #eee; }
        .data-table td { padding: 7px; border: 1px solid #f0f0f0; font-size: 10px; }
        .muted { color: #777; }
        .tag { display: inline-block; padding: 2px 6px; border-radius: 10px; font-size: 9px; background: #eef2ff; color: #3730a3; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 9px; background: #d1fae5; color: #065f46; font-weight: 600; }
        .summary-box { border: 1px solid #D4AF37; background: #FFF8F0; border-radius: 8px; padding: 12px; width: 100%; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 6px; font-size: 11px; }
        .summary-row:last-child { margin-bottom: 0; }
        .footer { margin-top: 25px; border-top: 1px dashed #bbb; padding-top: 8px; font-size: 10px; text-align: center; color: #777; }
        .review-box { background: #f9f9f9; border-radius: 6px; padding: 10px 12px; border: 1px solid #eee; }
    </style>
</head>
<body>
    @php
        $companyAddress = 'Jl. Bulak Setro Indah 2 Blok C No. 5, Surabaya';
        $mapLink = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($companyAddress);
        $companyEmail = 'anggitaweddingsurabaya@gmail.com';
        $waDisplay = '+62 812-3456-7890';
        $waLink = 'https://wa.me/6281234567890';
        $igHandle = '@anggitawedding';
        $igLink = 'https://instagram.com/anggitawedding';
        $isInvitationOnly = (bool) ($booking->is_invitation_only ?? false);
        $packageIncludesInvitation = (bool) (optional($booking->package)->includes_digital_invitation ?? false);
        $invitationTemplate = $booking->invitation?->template;
        $invitationName = $invitationTemplate?->name ? ('Undangan Digital - ' . $invitationTemplate->name) : 'Undangan Digital';
        $invitationNote = collect([
            $invitationTemplate?->theme ? 'Tema: ' . $invitationTemplate->theme : null,
            $invitationTemplate?->font_family ? 'Huruf: ' . $invitationTemplate->font_family : null,
            $invitationTemplate?->has_music ? 'Musik/RSVP aktif' : null,
        ])->filter()->implode(', ');
        if ($invitationNote === '') {
            $invitationNote = 'Undangan digital lengkap dengan RSVP, galeri, dan link berbagi.';
        }
        $baseLineTitle = $isInvitationOnly ? $invitationName : ($booking->package->name ?? 'Paket Wedding');
        $baseLineSubtitle = $isInvitationOnly
            ? $invitationNote
            : implode(', ', $booking->package->feature_items ?? []);
    @endphp

    <table class="header-table">
        <tr>
            <td>
                <div class="company-name">Anggita Wedding Organizer</div>
                <div class="report-title">Laporan Event / Booking</div>
                <div class="contact-links">
                    <a href="{{ $mapLink }}" target="_blank">{{ $companyAddress }}</a>
                    <span>•</span>
                    <a href="mailto:{{ $companyEmail }}">{{ $companyEmail }}</a>
                    <span>•</span>
                    <a href="{{ $waLink }}" target="_blank">WA {{ $waDisplay }}</a>
                    <span>•</span>
                    <a href="{{ $igLink }}" target="_blank">{{ $igHandle }}</a>
                </div>
                <div style="margin-top:4px; color:#777; font-size:10px;">{{ $booking->couple_short_display }}</div>
            </td>
        </tr>
    </table>

    <div class="section-title">Informasi Event / Booking</div>
    <table class="info-table">
        <tr>
            <td class="label">Kode Booking</td><td class="value">{{ $booking->booking_code }}</td>
            <td class="label">Tanggal Acara</td><td class="value">{{ $booking->event_date?->isoFormat('D MMMM Y') ?? '-' }}</td>
        </tr>
        <tr>
            <td class="label">Nama Pasangan</td><td class="value" colspan="3">{{ $booking->couple_short_display }}</td>
        </tr>
        <tr>
            <td class="label">Nama Lengkap Pria</td><td class="value">{{ $booking->groom_name }}</td>
            <td class="label">Nama Lengkap Wanita</td><td class="value">{{ $booking->bride_name }}</td>
        </tr>
        <tr>
            <td class="label">Venue/Jenis</td><td class="value">{{ $booking->venue }}</td>
            <td class="label">Order</td><td class="value">{{ $isInvitationOnly ? 'Undangan Digital' : 'Paket Wedding' }}</td>
        </tr>
        <tr>
            <td class="label">Paket</td><td class="value">{{ $isInvitationOnly ? $invitationName : ($booking->package->name ?? '-') }}</td>
            <td class="label">Klien</td><td class="value">{{ $booking->user->name }}</td>
        </tr>
        <tr>
            <td class="label">Kontak</td><td class="value">{{ $booking->phone ?? $booking->user->phone ?? '-' }}</td>
            <td class="label">Status</td><td class="value">{{ ucfirst($booking->status ?? '-') }} / {{ ucfirst($booking->payment_status ?? '-') }}</td>
        </tr>
    </table>

    <div class="section-title">Invoice & Tagihan</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:24px">#</th>
                <th>Deskripsi</th>
                <th style="width:90px;text-align:right">Harga</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>
                    <strong>{{ $baseLineTitle }}</strong><br>
                    <span class="muted">{{ $baseLineSubtitle }}</span>
                </td>
                <td style="text-align:right;font-weight:bold">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</td>
            </tr>
            @if(!$isInvitationOnly && $packageIncludesInvitation)
            <tr>
                <td>2</td>
                <td>
                    <strong>{{ $invitationName }}</strong>
                    <span class="tag">Included</span><br>
                    <span class="muted">{{ $invitationNote }}</span>
                </td>
                <td style="text-align:right;">Rp 0</td>
            </tr>
            @endif
            @foreach($booking->extraCharges as $idx => $charge)
            <tr>
                <td>{{ $isInvitationOnly ? $idx + 2 : ($packageIncludesInvitation ? $idx + 3 : $idx + 2) }}</td>
                <td>
                    <strong>{{ $charge->title }}</strong><br>
                    @if($charge->notes)
                        <span class="muted">{{ $charge->notes }}</span>
                    @endif
                </td>
                <td style="text-align:right;font-weight:bold; color: {{ $charge->amount < 0 ? '#dc2626' : '#111827' }};">
                    Rp {{ number_format($charge->amount, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2" style="text-align:right;font-weight:bold;">Grand Total</td>
                <td style="text-align:right;font-weight:bold;">Rp {{ number_format($booking->package_price + $booking->active_extra_charges_total, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:right;font-weight:bold;color:green;">Total Terbayar</td>
                <td style="text-align:right;font-weight:bold;color:green;">Rp {{ number_format($booking->total_paid, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:right;font-weight:bold;color:{{ ($booking->remaining_amount) > 0 ? '#dc2626' : 'green' }};">Sisa Tagihan</td>
                <td style="text-align:right;font-weight:bold;color:{{ ($booking->remaining_amount) > 0 ? '#dc2626' : 'green' }};">Rp {{ number_format(max(0, $booking->remaining_amount), 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if($booking->payments->count() > 0)
        <div class="section-title">Pembayaran & Bukti</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Tanggal</th>
                    <th>Tipe</th>
                    <th>Metode</th>
                    <th style="text-align:right">Jumlah</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->payments as $pay)
                    <tr>
                        <td>{{ $pay->payment_code }}</td>
                        <td>{{ $pay->paid_at?->format('d/m/Y') ?? $pay->created_at->format('d/m/Y') }}</td>
                        <td>{{ ucfirst($pay->type) }}</td>
                        <td>{{ $pay->method ?? 'Online' }}</td>
                        <td style="text-align:right;font-weight:bold;">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                        <td>{{ ucfirst($pay->status) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($booking->rundowns->count() > 0)
        <div class="section-title">Rundown Acara</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Kegiatan</th>
                    <th>Durasi</th>
                    <th>PIC</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->rundowns->sortBy('sort_order') as $rd)
                    <tr>
                        <td style="color:#D4AF37;font-weight:bold;">{{ $rd->time }}</td>
                        <td>{{ $rd->activity }}</td>
                        <td>{{ $rd->duration_minutes ? $rd->duration_minutes.' mnt' : '-' }}</td>
                        <td>{{ $rd->pic ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($booking->vendors->count() > 0)
        <div class="section-title">Vendor yang Terlibat</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Kategori</th>
                    <th>Nama Vendor</th>
                    <th>Kontak</th>
                    <th>Biaya</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->vendors as $v)
                    <tr>
                        <td>{{ $v->category }}</td>
                        <td>{{ $v->vendor_name }}</td>
                        <td>{{ $v->contact ?? '-' }}</td>
                        <td>{{ $v->cost ? 'Rp '.number_format($v->cost,0,',','.') : '-' }}</td>
                        <td><span class="badge">{{ ucfirst($v->status) }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($booking->fittings->count() > 0)
        <div class="section-title">Fitting</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Lokasi</th>
                    <th>Fokus</th>
                    <th>Catatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->fittings as $fit)
                    <tr>
                        <td>{{ $fit->scheduled_at?->format('d/m/Y H:i') ?? '-' }}</td>
                        <td>{{ $fit->location ?? '-' }}</td>
                        <td>{{ $fit->focus ?? '-' }}</td>
                        <td>{{ $fit->notes ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($booking->wardrobeItems->count() > 0)
        <div class="section-title">Wardrobe</div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Kategori</th>
                    <th>Warna/Size</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($booking->wardrobeItems as $item)
                    <tr>
                        <td>{{ $item->name ?? '-' }}</td>
                        <td>{{ $item->category ?? '-' }}</td>
                        <td>{{ $item->color_size ?? ($item->size ?? '-') }}</td>
                        <td>{{ ucfirst($item->status ?? '-') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif


    @if($booking->review)
        <div class="section-title">Ulasan Klien</div>
        <div class="review-box">
            <div style="margin-bottom:6px;">
                @for($i = 1; $i <= 5; $i++)
                    <span style="color:{{ $i <= $booking->review->rating ? '#D4AF37' : '#ddd' }};">★</span>
                @endfor
                <span style="margin-left:6px;font-weight:600;">{{ $booking->review->rating }}/5</span>
            </div>
            @if($booking->review->title)
                <p style="font-weight:600;margin-bottom:4px;">{{ $booking->review->title }}</p>
            @endif
            <p style="color:#555;font-style:italic;">"{{ $booking->review->review }}"</p>
        </div>
    @endif

    <div class="footer">
        Laporan digenerate pada {{ now()->isoFormat('D MMMM Y HH:mm') }} WIB oleh Anggita Wedding Organizer
    </div>
</body>
</html>
