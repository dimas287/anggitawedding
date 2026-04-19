<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #0b0a2f, #3b1d60); padding: 30px; text-align: center; }
        .header h1 { color: white; font-size: 22px; margin: 0; }
        .header p { color: rgba(255,255,255,0.75); margin: 5px 0 0; font-size: 13px; }
        .body { padding: 30px; }
        .card { background: #f8f4ff; border: 1px solid #9b59b6; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .card h3 { color: #7d3c98; font-size: 13px; margin: 0 0 12px; text-transform: uppercase; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #e8d5f5; font-size: 13px; gap: 16px; }
        .info-row:last-child { border-bottom: none; }
        .info-row .label { color: #888; flex-shrink: 0; }
        .info-row .value { font-weight: bold; color: #333; text-align: right; flex: 1; }
        .footer { background: #333; padding: 20px 30px; text-align: center; color: #999; font-size: 12px; }
        .footer a { color: #D4AF37; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🙏 Terima Kasih!</h1>
            <p>Permintaan konsultasi Anda sedang kami review</p>
        </div>
        <div class="body">
            <p style="color:#333;font-size:15px">Hai <strong>{{ $consultation->name }}</strong>,</p>
            <p style="color:#555;font-size:13px;line-height:1.6;margin-top:8px">
                Kami sudah menerima permintaan konsultasi Anda untuk kode <strong>{{ $consultation->consultation_code }}</strong>.
                Tim Anggita Wedding Organizer akan mengecek ketersediaan jadwal dan segera menghubungi Anda
                untuk konfirmasi jadwal final.
            </p>

            <div class="card">
                <h3>Detail Permintaan</h3>
                <div class="info-row"><span class="label">Tanggal yang Diinginkan :</span><span class="value">{{ $consultation->preferred_date->isoFormat('dddd, D MMMM Y') }}</span></div>
                <div class="info-row"><span class="label">Waktu :</span><span class="value">{{ $consultation->preferred_time }} WIB</span></div>
                <div class="info-row"><span class="label">Jenis Konsultasi :</span><span class="value">{{ $consultation->consultation_type === 'online' ? 'Online (Video Call)' : 'Offline (Kantor)' }}</span></div>
                @if($consultation->event_date)
                    <div class="info-row"><span class="label">Target Tanggal Event :</span><span class="value">{{ $consultation->event_date->isoFormat('D MMMM Y') }}</span></div>
                @endif
            </div>

            <p style="color:#555;font-size:13px;line-height:1.6">
                Mohon standby pada nomor WhatsApp/email yang terdaftar. Jika dalam 1 hari kerja belum ada kabar,
                Anda dapat menghubungi kami melalui WhatsApp di
                <a href="https://wa.me/6281231122057" style="color:#D4AF37;font-weight:bold">+62 812-3112-2057</a>.
            </p>
        </div>
        <div class="footer">© {{ date('Y') }} Anggita Wedding Organizer</div>
    </div>
</body>
</html>
