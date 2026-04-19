<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #e67e22, #d35400); padding: 30px; text-align: center; }
        .header h1 { color: white; font-size: 22px; margin: 0; }
        .header p { color: rgba(255,255,255,0.85); margin: 5px 0 0; font-size: 13px; }
        .body { padding: 30px; }
        .card { background: #fff8f0; border: 1px solid #e67e22; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .card h3 { color: #d35400; font-size: 13px; margin: 0 0 12px; text-transform: uppercase; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #fde8c8; font-size: 13px; gap: 16px; }
        .info-row:last-child { border-bottom: none; }
        .info-row .label { color: #888; flex-shrink: 0; }
        .info-row .value { font-weight: bold; color: #333; text-align: right; flex: 1; }
        .cta-btn { display: block; text-align: center; background: linear-gradient(135deg, #D4AF37, #B8960C); color: white; text-decoration: none; padding: 14px 30px; border-radius: 8px; font-weight: bold; font-size: 14px; margin: 20px 0; }
        .prep-list { background: #f9f9f9; border-radius: 8px; padding: 18px; margin: 20px 0; }
        .prep-list li { font-size: 13px; color: #555; margin-bottom: 8px; list-style: none; padding-left: 20px; position: relative; }
        .prep-list li::before { content: '✓'; position: absolute; left: 0; color: #D4AF37; font-weight: bold; }
        .footer { background: #333; padding: 20px 30px; text-align: center; color: #999; font-size: 12px; }
        .footer a { color: #D4AF37; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⏰ Pengingat Konsultasi!</h1>
            <p>Konsultasi Anda dijadwalkan besok</p>
        </div>
        <div class="body">
            <p style="color:#333;font-size:15px">Hai <strong>{{ $consultation->name }}</strong>,</p>
            <p style="color:#555;font-size:13px;line-height:1.6;margin-top:8px">
                Ini adalah pengingat bahwa Anda memiliki jadwal konsultasi dengan tim <strong>Anggita Wedding Organizer</strong>
                <strong>besok</strong>. Kami sangat menantikan pertemuan dengan Anda!
            </p>

            <div class="card">
                <h3>🗓 Jadwal Konsultasi</h3>
                <div class="info-row"><span class="label">Tanggal :</span><span class="value" style="color:#e67e22">{{ $consultation->preferred_date->isoFormat('dddd, D MMMM Y') }}</span></div>
                <div class="info-row"><span class="label">Waktu :</span><span class="value">{{ $consultation->preferred_time }} WIB</span></div>
                <div class="info-row"><span class="label">Jenis :</span><span class="value">{{ $consultation->consultation_type === 'online' ? '🖥 Online (Video Call)' : '🏢 Offline (Kantor)' }}</span></div>
                <div class="info-row"><span class="label">Kode :</span><span class="value">{{ $consultation->consultation_code }}</span></div>
                @if($consultation->consultation_type === 'offline')
                <div class="info-row"><span class="label">Lokasi :</span><span class="value">Jl. Bulak Setro Indah 2 Blok C No. 5, Surabaya</span></div>
                @endif
            </div>

            <div class="prep-list">
                <p style="font-weight:bold;color:#333;margin-bottom:10px;font-size:13px">Persiapkan untuk konsultasi: </p>
                <ul>
                    <li>Referensi tema/dekorasi yang disukai (foto/gambar)</li>
                    <li>Estimasi jumlah tamu yang akan diundang</li>
                    <li>Rencana tanggal dan venue yang diinginkan</li>
                    <li>Anggaran (budget) yang tersedia</li>
                    <li>Pertanyaan yang ingin ditanyakan kepada tim kami</li>
                </ul>
            </div>

            <a href="{{ route('landing') }}" class="cta-btn">Kunjungi Website Kami</a>

            <p style="color:#888;font-size:12px;text-align:center">
                Jika ada kendala, hubungi kami: <a href="https://wa.me/6281231122057" style="color:#D4AF37">+62 812-3112-2057</a>
            </p>
        </div>
        <div class="footer">© {{ date('Y') }} Anggita Wedding Organizer &nbsp;|&nbsp; <a href="{{ route('landing') }}">Website</a></div>
    </div>
</body>
</html>
