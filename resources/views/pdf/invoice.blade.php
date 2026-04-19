<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #333; padding: 35px; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 25px; border-bottom: 3px solid #D4AF37; padding-bottom: 15px; }
        .company-info .name { font-size: 20px; font-weight: bold; color: #D4AF37; }
        .company-info .sub { color: #888; font-size: 10px; margin-top: 3px; display: flex; flex-wrap: wrap; gap: 6px; }
        .company-info .sub a { color: #D4AF37; text-decoration: none; }
        .company-info .sub a:hover { text-decoration: underline; }
        .invoice-meta { text-align: right; }
        .invoice-meta .invoice-no { font-size: 18px; font-weight: bold; color: #333; }
        .invoice-meta .status { display: inline-block; background: #D4AF37; color: white; padding: 3px 10px; border-radius: 20px; font-size: 10px; font-weight: bold; margin-top: 5px; }
        .client-section { display: table; width: 100%; border-spacing: 15px 0; margin-bottom: 20px; }
        .client-box { display: table-cell; background: #FFF8F0; padding: 12px; border-radius: 6px; vertical-align: top; }
        .box-title { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #888; margin-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #333; color: white; padding: 8px 12px; text-align: left; font-size: 10px; }
        tr:nth-child(even) td { background: #f9f9f9; }
        td { padding: 8px 12px; border-bottom: 1px solid #eee; }
        .total-section { float: right; width: 280px; }
        .total-row { display: flex; justify-content: space-between; padding: 5px 0; font-size: 11px; }
        .total-final { background: #D4AF37; color: white; padding: 8px 12px; border-radius: 6px; font-weight: bold; font-size: 13px; margin-top: 5px; display: flex; justify-content: space-between; }
        .footer { clear: both; margin-top: 40px; border-top: 1px dashed #ccc; padding-top: 15px; text-align: center; color: #999; font-size: 10px; }
        .paid-stamp { position: absolute; top: 100px; right: 50px; border: 4px solid #22c55e; color: #22c55e; font-size: 28px; font-weight: bold; padding: 8px 15px; border-radius: 8px; opacity: 0.5; transform: rotate(-15deg); }
    </style>
</head>
<body>
    @php
        $companyAddress = 'Jl. Bulak Setro Indah 2 Blok C No. 5, Surabaya';
        $mapLink = 'https://www.google.com/maps/search/?api=1&query=' . urlencode($companyAddress);
        $companyEmail = 'anggitaweddingsurabaya@gmail.com';
        $waDisplay = '+62 812-3112-2057';
        $waLink = 'https://wa.me/6281231122057';
        $igHandle = '@anggita_wedding';
        $igLink = 'https://instagram.com/anggita_wedding';
        $extraTotal = ($extraTotal ?? null) ?? $booking->active_extra_charges_total;
        $grandTotal = ($grandTotal ?? null) ?? ($booking->package_price + $extraTotal);
        $isInvitationOnly = (bool) ($booking->is_invitation_only ?? false);
        $packageIncludesInvitation = (bool) (optional($booking->package)->includes_digital_invitation ?? false);
        $invitationTemplate = $booking->invitation?->template;
        $invitationName = $invitationTemplate?->name ? 'Undangan Digital - ' . $invitationTemplate->name : 'Undangan Digital';
        $invitationNote = collect([
            $invitationTemplate?->theme ? 'Tema: ' . $invitationTemplate->theme : null,
            $invitationTemplate?->font_family ? 'Huruf: ' . $invitationTemplate->font_family : null,
            $invitationTemplate?->has_music ? 'Musik & RSVP aktif' : null,
        ])->filter()->implode(', ');
        if ($invitationNote === '') {
            $invitationNote = 'Undangan digital lengkap dengan RSVP, galeri, dan link berbagi siap pakai.';
        }
        $baseLineTitle = $isInvitationOnly ? $invitationName : ($booking->package->name ?? 'Paket Wedding');
        $baseLineSubtitle = $isInvitationOnly
            ? $invitationNote
            : implode(', ', $booking->package->feature_items ?? []);
        if (!$isInvitationOnly && trim($baseLineSubtitle) === '') {
            $baseLineSubtitle = 'Paket wedding lengkap oleh Anggita Wedding Organizer.';
        }
    @endphp
    <div class="header">
        <div class="company-info">
            <div class="name">Anggita Wedding Organizer</div>
            <div class="sub">
                <a href="{{ $mapLink }}" target="_blank">{{ $companyAddress }}</a>
                <span>•</span>
                <a href="mailto:{{ $companyEmail }}">{{ $companyEmail }}</a>
                <span>•</span>
                <a href="{{ $waLink }}" target="_blank"> {{ $waDisplay }}</a>
                <span>•</span>
                <a href="{{ $igLink }}" target="_blank">{{ $igHandle }}</a>
            </div>
        </div>
        <div class="invoice-meta">
            <div class="invoice-no">INVOICE</div>
            <div style="font-size:13px;font-weight:bold;margin:3px 0">{{ $booking->booking_code }}</div>
            <div style="color:#888;font-size:10px">Terbit: {{ now()->isoFormat('D MMMM Y') }}</div>
            <div class="status">{{ strtoupper($booking->status_label) }}</div>
        </div>
    </div>

    <div class="client-section">
        <div class="client-box">
            <div class="box-title">Kepada :</div>
            <div style="font-weight:bold;font-size:13px">{{ $booking->user->name }}</div>
            <div style="color:#555">{{ $booking->user->email }}</div>
            <div style="color:#555">{{ $booking->user->phone }}</div>
        </div>
        <div class="client-box">
            <div class="box-title">Detail Acara</div>
            <div style="font-weight:bold">{{ $booking->couple_short_display }}</div>
            <div style="color:#555;font-size:10px">{{ $booking->groom_name }}</div>
            <div style="color:#555;font-size:10px">{{ $booking->bride_name }}</div>
            <div style="color:#555">{{ $booking->event_date->isoFormat('dddd, D MMMM Y') }}</div>
            <div style="color:#555">{{ $booking->venue }}</div>
        </div>
    </div>

    <table>
        <thead><tr><th>#</th><th>Deskripsi</th><th style="text-align:right">Harga</th></tr></thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>
                    <strong>{{ $baseLineTitle }}</strong><br>
                    <span style="color:#888;font-size:10px">{{ $baseLineSubtitle }}</span>
                </td>
                <td style="text-align:right;font-weight:bold">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</td>
            </tr>
            @if(!$isInvitationOnly && $packageIncludesInvitation)
            <tr>
                <td>2</td>
                <td>
                    <strong>{{ $invitationName }}</strong>
                    <span style="display:inline-block;margin-left:6px;padding:1px 6px;border-radius:10px;background:#eef2ff;color:#3730a3;font-size:9px;">Included</span><br>
                    <span style="color:#888;font-size:10px">{{ $invitationNote }}</span>
                </td>
                <td style="text-align:right;">Rp 0</td>
            </tr>
            @endif
            @foreach($booking->extraCharges as $index => $charge)
            <tr>
                <td>{{ $isInvitationOnly ? $index + 2 : ($packageIncludesInvitation ? $index + 3 : $index + 2) }}</td>
                <td>
                    <strong>{{ $charge->title }}</strong><br>
                    @if($charge->notes)
                        <span style="color:#888;font-size:10px">{{ $charge->notes }}</span>
                    @endif
                </td>
                <td style="text-align:right;font-weight:bold; color: {{ $charge->amount < 0 ? '#dc2626' : '#111827' }};">Rp {{ number_format($charge->amount, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="2" style="text-align:right;font-weight:bold;">Grand Total</td>
                <td style="text-align:right;font-weight:bold;">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:right;font-weight:bold;color:green;">Total Terbayar</td>
                <td style="text-align:right;font-weight:bold;color:green;">Rp {{ number_format($booking->total_paid, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" style="text-align:right;font-weight:bold;color:{{ ($grandTotal - $booking->total_paid) > 0 ? '#dc2626' : 'green' }};">Sisa Tagihan</td>
                <td style="text-align:right;font-weight:bold;color:{{ ($grandTotal - $booking->total_paid) > 0 ? '#dc2626' : 'green' }};">Rp {{ number_format(max(0, $grandTotal - $booking->total_paid), 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    @if($booking->payments->isNotEmpty())
    <div style="margin-top:20px">
        <h3 style="font-size:11px;color:#888;text-transform:uppercase;margin-bottom:8px;border-bottom:1px solid #eee;padding-bottom:5px">Riwayat Pembayaran</h3>
        <table>
            <thead><tr><th>Kode</th><th>Tanggal</th><th>Tipe</th><th>Metode</th><th style="text-align:right">Jumlah</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($booking->payments as $pay)
                <tr>
                    <td>{{ $pay->payment_code }}</td>
                    <td>{{ $pay->paid_at?->format('d/m/Y') ?? $pay->created_at->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($pay->type) }}</td>
                    <td>{{ $pay->method ?? 'Online' }}</td>
                    <td style="text-align:right;font-weight:bold;color:green">Rp {{ number_format($pay->amount, 0, ',', '.') }}</td>
                    <td style="color:{{ $pay->status==='success'?'green':'orange' }}">{{ ucfirst($pay->status) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="footer">
        Terima kasih telah mempercayakan pernikahan Anda kepada Anggita Wedding Organizer<br>
        Dokumen digenerate pada {{ now()->isoFormat('D MMMM Y HH:mm') }} WIB
    </div>
</body>
</html>
