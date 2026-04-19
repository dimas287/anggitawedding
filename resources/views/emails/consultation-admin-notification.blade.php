<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f7fafc; margin: 0; padding: 20px; }
        .container { max-width: 640px; margin: 0 auto; background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 8px 24px rgba(15,23,42,0.08); }
        .header { background: linear-gradient(135deg, #1f1236, #5a2f83); padding: 28px 32px; color: white; }
        .header h1 { margin: 0; font-size: 20px; }
        .body { padding: 32px; }
        .badge { display: inline-flex; align-items: center; gap: 6px; font-size: 12px; text-transform: uppercase; letter-spacing: 2px; background: #fde68a; color: #92400e; padding: 6px 12px; border-radius: 999px; }
        .card { margin-top: 20px; border: 1px solid #e5e7eb; border-radius: 14px; overflow: hidden; }
        .card-header { background: #f8f4ff; padding: 16px 20px; font-weight: 600; font-size: 14px; color: #6b21a8; }
        .card-body { padding: 12px 20px 20px; }
        .info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #f3f4f6; font-size: 13px; gap: 16px; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #6b7280; flex-shrink: 0; }
        .info-value { font-weight: 600; color: #111827; text-align: right; flex: 1; }
        .footer { background: #f9fafb; padding: 18px 32px; font-size: 12px; text-align: center; color: #6b7280; }
        .cta { display: inline-block; margin-top: 16px; padding: 12px 24px; border-radius: 999px; background: #1e293b; color: #fff; font-size: 13px; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="badge">Konsultasi Baru</span>
            <h1>Permintaan konsultasi menunggu konfirmasi</h1>
            <p style="margin-top:8px;font-size:13px;color:rgba(247, 241, 241, 0.75)">
                Segera konfirmasi permintaan ini melalui dashboard admin.
            </p>
        </div>
        <div class="body">
            <p style="margin:0;font-size:14px;color:#1f2937">Halo tim Admin,</p>
            <p style="font-size:13px;color:#4b5563;line-height:1.6">Ada klien yang baru saja mengajukan konsultasi. Berikut detailnya:</p>

            <div class="card">
                <div class="card-header">Data Klien</div>
                <div class="card-body">
                    <div class="info-row"><span class="info-label">Nama :</span><span class="info-value">{{ $consultation->name }}</span></div>
                    <div class="info-row"><span class="info-label">Email :</span><span class="info-value">{{ $consultation->email }}</span></div>
                    <div class="info-row"><span class="info-label">No. WhatsApp :</span><span class="info-value">{{ $consultation->phone }}</span></div>
                    <div class="info-row"><span class="info-label">Kode Konsultasi :</span><span class="info-value">{{ $consultation->consultation_code }}</span></div>
                </div>
            </div>

            <div class="card" style="margin-top:18px">
                <div class="card-header">Detail Jadwal</div>
                <div class="card-body">
                    <div class="info-row"><span class="info-label">Tanggal Permintaan :</span><span class="info-value">{{ $consultation->preferred_date->isoFormat('dddd, D MMMM Y') }}</span></div>
                    <div class="info-row"><span class="info-label">Waktu :</span><span class="info-value">{{ $consultation->preferred_time }} WIB</span></div>
                    <div class="info-row"><span class="info-label">Jenis :</span><span class="info-value">{{ $consultation->consultation_type === 'online' ? 'Online (Video Call)' : 'Offline (Kantor)' }}</span></div>
                    @if($consultation->event_date)
                        <div class="info-row"><span class="info-label">Target Event :</span><span class="info-value">{{ $consultation->event_date->isoFormat('D MMMM Y') }}</span></div>
                    @endif
                </div>
            </div>

            <p style="font-size:13px;color:#4b5563;line-height:1.6;margin-top:18px">
                Mohon segera hubungi klien untuk memastikan jadwal dan lakukan konfirmasi di dashboard admin.
            </p>
            <a href="{{ route('admin.consultations.index') }}" class="cta">Buka Dashboard Konsultasi</a>
        </div>
        <div class="footer">
            Email otomatis dari sistem Anggita Wedding Organizer
        </div>
    </div>
</body>
</html>
