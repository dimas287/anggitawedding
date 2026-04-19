<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color: #333; padding: 30px; }
        .header { text-align: center; border-bottom: 3px solid #D4AF37; padding-bottom: 15px; margin-bottom: 20px; }
        .company-name { font-size: 20px; font-weight: bold; color: #D4AF37; }
        .title { font-size: 15px; font-weight: bold; margin: 5px 0; }
        .period { color: #888; font-size: 11px; }
        .summary-grid { display: flex; gap: 15px; margin-bottom: 20px; }
        .summary-card { flex: 1; padding: 12px; border-radius: 6px; text-align: center; }
        .summary-card .label { font-size: 10px; color: #888; text-transform: uppercase; margin-bottom: 5px; }
        .summary-card .value { font-size: 16px; font-weight: bold; }
        .income-card { background: #f0fdf4; }
        .expense-card { background: #fef2f2; }
        .profit-card { background: #FFF8F0; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 10px; }
        th { background: #333; color: white; padding: 7px 10px; text-align: left; }
        tr:nth-child(even) td { background: #f9f9f9; }
        td { padding: 7px 10px; border-bottom: 1px solid #eee; }
        .income-row td { color: #15803d; }
        .expense-row td { color: #dc2626; }
        .footer { margin-top: 20px; border-top: 1px dashed #ccc; padding-top: 10px; text-align: center; color: #999; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">Anggita Wedding Organizer</div>
        <div class="title">LAPORAN KEUANGAN</div>
        <div class="period">Periode: {{ $period }}</div>
    </div>

    <div class="summary-grid">
        <div class="summary-card income-card">
            <div class="label">Total Pemasukan</div>
            <div class="value" style="color:#15803d">Rp {{ number_format($summary['income'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-card expense-card">
            <div class="label">Total Pengeluaran</div>
            <div class="value" style="color:#dc2626">Rp {{ number_format($summary['expense'], 0, ',', '.') }}</div>
        </div>
        <div class="summary-card profit-card">
            <div class="label">Profit Bersih</div>
            <div class="value" style="color:{{ $summary['profit'] >= 0 ? '#D4AF37' : '#dc2626' }}">
                Rp {{ number_format($summary['profit'], 0, ',', '.') }}
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Deskripsi</th>
                <th>Kategori</th>
                <th>Booking</th>
                <th style="text-align:right">Jumlah</th>
                <th>Tipe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $tx)
            <tr class="{{ $tx->type === 'income' ? 'income-row' : 'expense-row' }}">
                <td>{{ \Carbon\Carbon::parse($tx->transaction_date)->format('d/m/Y') }}</td>
                <td>{{ $tx->description }}</td>
                <td>{{ $tx->category ?? '-' }}</td>
                <td>{{ $tx->booking?->booking_code ?? '-' }}</td>
                <td style="text-align:right;font-weight:bold">{{ $tx->type==='income'?'+':'-' }}Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
                <td>{{ $tx->type === 'income' ? 'Masuk' : 'Keluar' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Laporan digenerate pada {{ now()->isoFormat('D MMMM Y HH:mm') }} WIB oleh Admin Anggita WO
    </div>
</body>
</html>
