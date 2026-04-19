<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #6b0f1a, #b91372); padding: 30px; text-align: center; }
        .header h1 { color: white; font-size: 22px; margin: 0; }
        .header p { color: rgba(255,255,255,0.75); margin: 5px 0 0; font-size: 13px; }
        .body { padding: 30px; }
        .card { background: #fff8f7; border: 1px solid #fcd4ce; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .card h3 { color: #c2410c; font-size: 13px; margin: 0 0 12px; text-transform: uppercase; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f5d5cf; font-size: 13px; gap: 16px; }
        .info-row:last-child { border-bottom: none; }
        .info-row .label { color: #888; flex-shrink: 0; }
        .info-row .value { font-weight: bold; color: #333; text-align: right; flex: 1; }
        .cta-btn { display: block; text-align: center; background: #0f172a; color: white; text-decoration: none; padding: 12px 20px; border-radius: 999px; font-weight: bold; font-size: 14px; margin: 16px 0; }
        .footer { background: #333; padding: 20px 30px; text-align: center; color: #999; font-size: 12px; }
        .footer a { color: #D4AF37; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Butuh Penjadwalan Ulang</h1>
            <p>Kami perlu menyesuaikan jadwal konsultasi Anda</p>
        </div>
        <div class="body">
            <p style="color:#333;font-size:15px">Halo <strong>{{ $consultation->name }}</strong>,</p>
            <p style="color:#555;font-size:13px;line-height:1.6;margin-top:8px">
                Mohon maaf, jadwal konsultasi Anda dengan kode <strong>{{ $consultation->consultation_code }}</strong>
                belum bisa kami jalankan sesuai waktu sebelumnya. Silakan hubungi admin kami melalui WhatsApp untuk
                menentukan waktu baru yang paling nyaman bagi Anda.
            </p>

            <div class="card">
                <h3>Detail Terakhir</h3>
                <div class="info-row"><span class="label">Tanggal Sebelumnya :</span><span class="value">{{ $consultation->preferred_date->isoFormat('dddd, D MMMM Y') }}</span></div>
                <div class="info-row"><span class="label">Waktu :</span><span class="value">{{ $consultation->preferred_time }} WIB</span></div>
                <div class="info-row"><span class="label">Jenis Konsultasi :</span><span class="value">{{ $consultation->consultation_type === 'online' ? 'Online (Video Call)' : 'Offline (Kantor)' }}</span></div>
            </div>

            <p style="color:#555;font-size:13px;line-height:1.6">
                Setelah Anda mendapatkan kesepakatan waktu baru dengan admin, kami akan mengirimkan email konfirmasi
                terbaru. Terima kasih atas pengertian dan fleksibilitas Anda.
            </p>

            <a class="cta-btn" href="https://wa.me/6281231122057">Chat Admin via WhatsApp</a>
        </div>
        <div class="footer">© {{ date('Y') }} Anggita Wedding Organizer</div>
    </div>
</body>
</html>
