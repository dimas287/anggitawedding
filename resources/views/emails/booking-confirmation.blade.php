<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #D4AF37, #B8960C); padding: 30px; text-align: center; }
        .header h1 { color: white; font-size: 22px; margin: 0; }
        .header p { color: rgba(255,255,255,0.85); margin: 5px 0 0; font-size: 13px; }
        .body { padding: 30px; }
        .greeting { font-size: 16px; color: #333; margin-bottom: 15px; }
        .booking-card { background: #FFF8F0; border: 1px solid #D4AF37; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .booking-card h3 { color: #D4AF37; font-size: 14px; margin: 0 0 12px; text-transform: uppercase; letter-spacing: 1px; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f0e6c8; font-size: 13px; }
        .info-row:last-child { border-bottom: none; }
        .info-row .label { color: #888; min-width: 140px; margin-right: 12px; }
        .info-row .value { font-weight: bold; color: #333; text-align: right; flex: 1; }
        .cta-btn { display: block; text-align: center; background: linear-gradient(135deg, #D4AF37, #B8960C); color: white; text-decoration: none; padding: 14px 30px; border-radius: 8px; font-weight: bold; font-size: 14px; margin: 20px 0; }
        .steps { background: #f9f9f9; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .step { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; }
        .step-num { background: #D4AF37; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; flex-shrink: 0; }
        .step-text { font-size: 13px; color: #555; padding-top: 3px; }
        .footer { background: #333; padding: 20px 30px; text-align: center; color: #999; font-size: 12px; }
        .footer a { color: #D4AF37; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>💍 Booking Dikonfirmasi!</h1>
            <p>Selamat! Pernikahan impian Anda kini mulai direncanakan</p>
        </div>
        <div class="body">
            <p class="greeting">Hai <strong>{{ $booking->user->name }}</strong>,</p>
            <p style="color:#555;font-size:13px;line-height:1.6">
                Terima kasih telah mempercayakan momen istimewa Anda kepada <strong>Anggita Wedding Organizer</strong>.
                Booking paket pernikahan Anda telah berhasil dikonfirmasi!
            </p>

            <div class="booking-card">
                <h3>Detail Booking</h3>
                <div class="info-row"><span class="label">Kode Booking:</span><span class="value" style="color:#D4AF37">{{ $booking->booking_code }}</span></div>
                <div class="info-row"><span class="label">Nama Pasangan:</span><span class="value">{{ $booking->couple_short_display }}</span></div>
                <div class="info-row"><span class="label">Pengantin Pria:</span><span class="value">{{ $booking->groom_name }}</span></div>
                <div class="info-row"><span class="label">Pengantin Wanita:</span><span class="value">{{ $booking->bride_name }}</span></div>
                <div class="info-row"><span class="label">Tanggal Acara:</span><span class="value">{{ $booking->event_date->isoFormat('dddd, D MMMM Y') }}</span></div>
                <div class="info-row"><span class="label">Venue:</span><span class="value">{{ $booking->venue }}</span></div>
                <div class="info-row"><span class="label">Paket:</span><span class="value">{{ $booking->package->name }}</span></div>
                <div class="info-row"><span class="label">Total Harga:</span><span class="value">Rp {{ number_format($booking->package_price, 0, ',', '.') }}</span></div>
                <div class="info-row"><span class="label">DP yang Diperlukan:</span><span class="value" style="color:#D4AF37">Rp {{ number_format($booking->dp_amount, 0, ',', '.') }}</span></div>
            </div>

            @if($booking->status === 'pending')
            <a href="{{ route('payment.checkout', $booking->id) }}" class="cta-btn">
                💳 Bayar DP Sekarang
            </a>
            @else
            <a href="{{ route('user.booking.show', $booking->id) }}" class="cta-btn">
                📋 Lihat Dashboard Klien
            </a>
            @endif

            <div class="steps">
                <p style="font-weight:bold;color:#333;font-size:13px;margin-bottom:12px">Langkah Selanjutnya:</p>
                <div class="step"><div class="step-num">1</div><div class="step-text">Lakukan pembayaran DP sebesar 30% untuk mengamankan tanggal</div></div>
                <div class="step"><div class="step-num">2</div><div class="step-text">Tim kami akan menghubungi Anda untuk jadwal konsultasi pertama</div></div>
                <div class="step"><div class="step-num">3</div><div class="step-text">Bersama-sama kita rencanakan hari paling indah dalam hidup Anda!</div></div>
            </div>

            <p style="color:#888;font-size:12px;text-align:center">
                Pertanyaan? Hubungi kami via WhatsApp: <a href="https://wa.me/6281231122057" style="color:#D4AF37">+62 812-3112-2057</a>
            </p>
        </div>
        <div class="footer">
            © {{ date('Y') }} Anggita Wedding Organizer &nbsp;|&nbsp;
            <a href="{{ route('landing') }}">Website</a>
        </div>
    </div>
</body>
</html>
