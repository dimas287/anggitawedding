<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #22c55e, #16a34a); padding: 30px; text-align: center; }
        .header h1 { color: white; font-size: 22px; margin: 0; }
        .header p { color: rgba(255,255,255,0.85); margin: 5px 0 0; font-size: 13px; }
        .body { padding: 30px; }
        .card { background: #f0fdf4; border: 1px solid #22c55e; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .card h3 { color: #16a34a; font-size: 13px; margin: 0 0 12px; text-transform: uppercase; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #d1fae5; font-size: 13px; gap: 16px; }
        .info-row:last-child { border-bottom: none; }
        .info-row .label { color: #888; flex-shrink: 0; }
        .info-row .value { font-weight: bold; color: #333; text-align: right; flex: 1; }
        .amount-box { background: linear-gradient(135deg, #22c55e, #16a34a); color: white; border-radius: 8px; padding: 15px; text-align: center; margin: 20px 0; }
        .amount-box .amount { font-size: 28px; font-weight: bold; }
        .amount-box .label { font-size: 12px; opacity: 0.85; margin-top: 4px; }
        .cta-btn { display: block; text-align: center; background: linear-gradient(135deg, #D4AF37, #B8960C); color: white; text-decoration: none; padding: 14px 30px; border-radius: 8px; font-weight: bold; font-size: 14px; margin: 20px 0; }
        .next-steps { background: #FFF8F0; border-left: 4px solid #D4AF37; border-radius: 0 8px 8px 0; padding: 15px 18px; margin: 20px 0; }
        .next-steps li { font-size: 13px; color: #555; margin-bottom: 6px; }
        .footer { background: #333; padding: 20px 30px; text-align: center; color: #999; font-size: 12px; }
        .footer a { color: #D4AF37; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Pembayaran Berhasil!</h1>
            <p>Terima kasih atas kepercayaan Anda</p>
        </div>
        <div class="body">
            <p style="color:#333;font-size:15px">Hai <strong>{{ $booking->user->name }}</strong>,</p>
            <p style="color:#555;font-size:13px;line-height:1.6;margin-top:8px">
                Pembayaran Anda telah berhasil diterima. Kini Anda resmi menjadi klien
                <strong>Anggita Wedding Organizer</strong>. Kami sangat senang dapat menjadi bagian dari hari istimewa Anda! 🎊
            </p>

            <div class="amount-box">
                <div class="amount">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
                <div class="label">{{ ucfirst($payment->type) }} Berhasil • {{ $payment->payment_code }}</div>
            </div>

            <div class="card">
                <h3>Detail Pembayaran</h3>
                <div class="info-row"><span class="label">Kode Pembayaran :</span><span class="value" style="color:#16a34a">{{ $payment->payment_code }}</span></div>
                <div class="info-row"><span class="label">Kode Booking :</span><span class="value">{{ $booking->booking_code }}</span></div>
                <div class="info-row"><span class="label">Pengantin :</span><span class="value">{{ $booking->groom_name }} & {{ $booking->bride_name }}</span></div>
                <div class="info-row"><span class="label">Tanggal Acara :</span><span class="value">{{ $booking->event_date->isoFormat('dddd, D MMMM Y') }}</span></div>
                <div class="info-row"><span class="label">Paket :</span><span class="value">{{ $booking->package->name }}</span></div>
                <div class="info-row"><span class="label">Jumlah Dibayar :</span><span class="value" style="color:#16a34a">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span></div>
                <div class="info-row"><span class="label">Sisa Tagihan :</span><span class="value">Rp {{ number_format(max(0, $booking->package_price - $booking->total_paid), 0, ',', '.') }}</span></div>
                <div class="info-row"><span class="label">Tanggal Bayar :</span><span class="value">{{ $payment->paid_at?->isoFormat('D MMMM Y HH:mm') }} WIB</span></div>
            </div>

            <a href="{{ route('user.booking.show', $booking->id) }}" class="cta-btn">📋 Masuk ke Dashboard Klien</a>

            <div class="next-steps">
                <p style="font-weight:bold;color:#D4AF37;margin-bottom:8px;font-size:13px">Langkah selanjutnya:</p>
                <ul>
                    <li>Tim kami akan segera menghubungi Anda untuk konsultasi awal</li>
                    <li>Chat langsung dengan admin untuk diskusi persiapan</li>
                </ul>
            </div>

            <p style="color:#888;font-size:12px;text-align:center">
                Pertanyaan? WhatsApp: <a href="https://wa.me/6281231122057" style="color:#D4AF37">+62 812-3112-2057</a>
            </p>
        </div>
        <div class="footer">© {{ date('Y') }} Anggita Wedding Organizer &nbsp;|&nbsp; <a href="{{ route('landing') }}">Website</a></div>
    </div>
</body>
</html>
