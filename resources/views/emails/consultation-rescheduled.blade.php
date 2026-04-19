<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #0f172a, #1e3a8a); padding: 30px; text-align: center; }
        .header h1 { color: white; font-size: 22px; margin: 0; }
        .header p { color: rgba(255,255,255,0.75); margin: 5px 0 0; font-size: 13px; }
        .body { padding: 30px; }
        .card { background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .card h3 { color: #1e3a8a; font-size: 13px; margin: 0 0 12px; text-transform: uppercase; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #dbeafe; font-size: 13px; gap: 16px; }
        .info-row:last-child { border-bottom: none; }
        .info-row .label { color: #4b5563; flex-shrink: 0; }
        .info-row .value { font-weight: bold; color: #111827; text-align: right; flex: 1; }
        .cta-btn { display: block; text-align: center; background: linear-gradient(135deg, #D4AF37, #B8960C); color: white; text-decoration: none; padding: 14px 30px; border-radius: 8px; font-weight: bold; font-size: 14px; margin: 22px 0 8px; }
        .footer { background: #333; padding: 20px 30px; text-align: center; color: #999; font-size: 12px; }
        .footer a { color: #D4AF37; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Konsultasi Anda Dikonfirmasi</h1>
            <p>Berikut jadwal terbaru sesuai kesepakatan dengan admin</p>
        </div>
        <div class="body">
            <p style="color:#333;font-size:15px">Hai <strong>{{ $consultation->name }}</strong>,</p>
            <p style="color:#555;font-size:13px;line-height:1.6;margin-top:8px">
                Terima kasih sudah berkoordinasi melalui WhatsApp. Jadwal konsultasi Anda bersama tim Anggita Wedding Organizer
                kini sudah terkonfirmasi. Simpan detail berikut agar tidak terlewat ya!
            </p>

            <div class="card">
                <h3>Detail Jadwal Terbaru</h3>
                <div class="info-row"><span class="label">Kode Konsultasi :</span><span class="value" style="color:#1d4ed8">{{ $consultation->consultation_code }}</span></div>
                <div class="info-row"><span class="label">Tanggal :</span><span class="value">{{ $consultation->preferred_date->isoFormat('dddd, D MMMM Y') }}</span></div>
                <div class="info-row"><span class="label">Waktu :</span><span class="value">{{ $consultation->preferred_time }} WIB</span></div>
                <div class="info-row"><span class="label">Jenis :</span><span class="value">{{ $consultation->consultation_type === 'online' ? 'Online (Video Call)' : 'Offline (Kantor)' }}</span></div>
                @if($consultation->consultation_type === 'offline')
                <div class="info-row"><span class="label">Lokasi Kantor :</span><span class="value">Jl. Bulak Setro Indah 2 Blok C No. 5, Surabaya</span></div>
                @endif
            </div>

            <p style="color:#555;font-size:13px;line-height:1.6">
                Jika ada perubahan mendadak atau butuh bantuan tambahan, langsung saja hubungi admin kami via WhatsApp.
                Kami siap membantu kapan pun diperlukan.
            </p>

            <a href="https://wa.me/6281231122057" class="cta-btn">Hubungi Admin via WhatsApp</a>
        </div>
        <div class="footer">© {{ date('Y') }} Anggita Wedding Organizer</div>
    </div>
</body>
</html>
