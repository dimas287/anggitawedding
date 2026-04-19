<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 620px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 35px rgba(15,23,42,0.08); }
        .header { background: linear-gradient(135deg, #0b0a2f, #3b1d60); padding: 32px; color: white; text-align: center; }
        .header h1 { margin: 0; font-size: 22px; }
        .body { padding: 32px; color: #1f2937; }
        .card { background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 14px; padding: 20px; margin: 22px 0; }
        .card h3 { margin: 0 0 12px; font-size: 14px; letter-spacing: 1px; color: #6b21a8; text-transform: uppercase; }
        .row { display: flex; justify-content: space-between; font-size: 13px; padding: 8px 0; border-bottom: 1px solid #edf2f7; gap: 16px; }
        .row:last-child { border-bottom: none; }
        .label { color: #6b7280; flex-shrink: 0; }
        .value { font-weight: 600; color: #111827; text-align: right; flex: 1; }
        .cta { display: inline-block; margin-top: 20px; padding: 12px 20px; border-radius: 999px; background: #111827; color: white; text-decoration: none; font-size: 13px; }
        .footer { background: #0f172a; color: rgba(255,255,255,0.7); text-align: center; font-size: 12px; padding: 18px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Invoice Booking Anda</h1>
        <p style="margin-top:6px;font-size:13px;color:rgba(255,255,255,0.8)">Kode {{ $booking->booking_code }}</p>
    </div>
    <div class="body">
        <p>Hai <strong>{{ $booking->groom_name }} & {{ $booking->bride_name }}</strong>,</p>
        <p style="font-size:13px;line-height:1.6;color:#4b5563">Terima kasih telah mempercayakan momen spesial kepada Anggita Wedding Organizer. Invoice terbaru untuk booking Anda kami lampirkan pada email ini.</p>

        @php
            $extra = ($extraTotal ?? null) ?? $booking->active_extra_charges_total;
            $grand = ($grandTotal ?? null) ?? ($booking->package_price + $extra);
        @endphp
        <div class="card">
            <h3>Ringkasan Invoice</h3>
            <div class="row"><span class="label">Paket :</span><span class="value">{{ $booking->package->name }}</span></div>
            <div class="row"><span class="label">Total Paket :</span><span class="value">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</span></div>
            <div class="row"><span class="label">Biaya Tambahan :</span><span class="value">Rp {{ number_format($extra, 0, ',', '.') }}</span></div>
            <div class="row"><span class="label">Grand Total :</span><span class="value">Rp {{ number_format($grand, 0, ',', '.') }}</span></div>
            <div class="row"><span class="label">Total Dibayar :</span><span class="value">Rp {{ number_format($booking->total_paid, 0, ',', '.') }}</span></div>
            <div class="row"><span class="label">Sisa Pembayaran :</span><span class="value" style="color:#dc2626">Rp {{ number_format(max(0, $grand - $booking->total_paid), 0, ',', '.') }}</span></div>
            <div class="row"><span class="label">Tanggal Acara :</span><span class="value">{{ $booking->event_date->isoFormat('dddd, D MMMM Y') }}</span></div>
        </div>

        <p style="font-size:13px;line-height:1.6;color:#4b5563">Silakan unduh invoice pada lampiran untuk detail pembayaran. Bila ada pertanyaan atau ingin melakukan konfirmasi pembayaran, hubungi admin kami melalui WhatsApp.</p>

        <a class="cta" href="https://wa.me/6281231122057" target="_blank">Hubungi Admin via WhatsApp</a>
    </div>
    <div class="footer">© {{ date('Y') }} Anggita Wedding Organizer</div>
</div>
</body>
</html>
