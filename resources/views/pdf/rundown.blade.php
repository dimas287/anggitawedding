<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #333; padding: 30px; }
        .header { text-align: center; border-bottom: 3px solid #D4AF37; padding-bottom: 15px; margin-bottom: 20px; }
        .company-name { font-size: 20px; font-weight: bold; color: #D4AF37; }
        .title { font-size: 16px; font-weight: bold; color: #333; margin: 5px 0; }
        .subtitle { color: #666; font-size: 12px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; background: #FFF8F0; padding: 12px; border-radius: 6px; }
        .info-item label { color: #888; font-size: 10px; display: block; text-transform: uppercase; margin-bottom: 2px; }
        .info-item span { font-weight: bold; color: #333; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #D4AF37; color: white; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; }
        tr:nth-child(even) { background: #FFF8F0; }
        td { padding: 8px 10px; border-bottom: 1px solid #eee; vertical-align: top; }
        .time-col { font-weight: bold; color: #D4AF37; width: 80px; }
        .footer { margin-top: 30px; border-top: 1px solid #ddd; padding-top: 15px; text-align: center; color: #999; font-size: 10px; }
        .signature-area { margin-top: 40px; display: grid; grid-template-columns: 1fr 1fr; gap: 40px; text-align: center; }
        .signature-box { border-top: 1px solid #333; padding-top: 8px; color: #555; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">Anggita Wedding Organizer</div>
        <div class="title">RUNDOWN ACARA PERNIKAHAN</div>
        <div class="subtitle">{{ $booking->couple_short_display }}</div>
    </div>

    <div class="info-grid">
        <div class="info-item"><label>Nama Pasangan</label><span>{{ $booking->couple_short_display }}</span></div>
        <div class="info-item"><label>Nama Lengkap Pria</label><span>{{ $booking->groom_name }}</span></div>
        <div class="info-item"><label>Nama Lengkap Wanita</label><span>{{ $booking->bride_name }}</span></div>
        <div class="info-item"><label>Tanggal Acara</label><span>{{ $booking->event_date->isoFormat('dddd, D MMMM Y') }}</span></div>
        <div class="info-item"><label>Venue</label><span>{{ $booking->venue }}</span></div>
        <div class="info-item"><label>Paket</label><span>{{ $booking->package->name }}</span></div>
        <div class="info-item"><label>Kode Booking</label><span>{{ $booking->booking_code }}</span></div>
        @if($booking->estimated_guests)<div class="info-item"><label>Estimasi Tamu</label><span>{{ number_format($booking->estimated_guests) }} orang</span></div>@endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:80px">Waktu</th>
                <th>Kegiatan</th>
                <th style="width:100px">Durasi</th>
                <th style="width:120px">PIC</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($rundowns as $item)
            <tr>
                <td class="time-col">{{ $item->time }}</td>
                <td><strong>{{ $item->activity }}</strong></td>
                <td>{{ $item->duration_minutes ? $item->duration_minutes.' menit' : '-' }}</td>
                <td>{{ $item->pic ?? '-' }}</td>
                <td>{{ $item->notes ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center;padding:20px;color:#999">Belum ada rundown</td></tr>
            @endforelse
        </tbody>
    </table>

    @if($booking->vendors->count() > 0)
    <div style="margin-top:20px">
        <h3 style="color:#D4AF37;margin-bottom:10px;font-size:13px;border-bottom:2px solid #D4AF37;padding-bottom:5px">TIM VENDOR</h3>
        <table>
            <thead><tr><th>Kategori</th><th>Nama Vendor</th><th>Kontak</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($booking->vendors->where('status','confirmed') as $v)
                <tr><td>{{ $v->category }}</td><td>{{ $v->vendor_name }}</td><td>{{ $v->contact ?? '-' }}</td><td style="color:green">{{ ucfirst($v->status) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="signature-area">
        <div><div style="height:50px"></div><div class="signature-box">Wedding Organizer<br><strong>Anggita Wedding Organizer</strong></div></div>
        <div><div style="height:50px"></div><div class="signature-box">Pengantin<br><strong>{{ $booking->couple_short_display }}</strong></div></div>
    </div>

    <div class="footer">
        Dokumen ini digenerate otomatis oleh sistem Anggita Wedding Organizer<br>
        {{ now()->isoFormat('D MMMM Y HH:mm') }} WIB
    </div>
</body>
</html>
