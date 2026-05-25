<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 11px; color: #2d3748; line-height: 1.5; }
        .invoice-title { font-size: 18px; font-weight: bold; color: #1a365d; text-transform: uppercase; margin-bottom: 2px; }
        .meta-text { color: #718096; font-size: 10px; margin-bottom: 20px; }
        .summary-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        .summary-card { padding: 10px; border: 1px solid #e2e8f0; background-color: #f7fafc; }
        .card-label { font-size: 8px; text-transform: uppercase; color: #a0aec0; font-weight: bold; }
        .card-value { font-size: 12px; font-weight: bold; margin-top: 2px; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { background-color: #ebf8ff; color: #2b6cb0; padding: 6px 8px; text-align: left; font-size: 9px; text-transform: uppercase; border-bottom: 2px solid #bee3f8; }
        .data-table td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; font-size: 10px; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .bg-light { background-color: #f8fafc; }
    </style>
</head>
<body>

    <div>
        <div class="invoice-title">Laporan Laba Rugi Komprehensif</div>
        <div class="meta-text">Rentang Tanggal: {{ $startDate }} s/d {{ $endDate }} | Kalkulasi Stok: FIFO</div>
    </div>

    <table class="summary-table">
        <tr>
            <td width="23%" class="summary-card">
                <div class="card-label">Total Omset</div>
                <div class="card-value">Rp {{ number_format($revenue, 0, ',', '.') }}</div>
            </td>
            <td width="2%"></td>
            <td width="23%" class="summary-card">
                <div class="card-label" style="color: #e53e3e;">HPP Terjual</div>
                <div class="card-value" style="color: #c53030;">Rp {{ number_format($totalHpp, 0, ',', '.') }}</div>
            </td>
            <td width="2%"></td>
            <td width="23%" class="summary-card">
                <div class="card-label" style="color: #38a169;">Laba Bersih</div>
                <div class="card-value" style="color: #276749;">Rp {{ number_format($grossProfit, 0, ',', '.') }}</div>
            </td>
            <td width="2%"></td>
            <td width="25%" class="summary-card" style="background-color: #ebf8ff;">
                <div class="card-label" style="color: #3182ce;">Nilai Aset Gudang</div>
                <div class="card-value" style="color: #2b6cb0;">Rp {{ number_format($totalAssetValue, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="30%">Produk</th>
                <th class="text-center" width="8%">Terjual</th>
                <th class="text-right" width="14%">Omset</th>
                <th class="text-right" width="14%">HPP</th>
                <th class="text-right" width="14%">Margin</th>
                <th class="text-center" width="8%">Stok</th>
                <th class="text-right" width="12%">Nilai Aset</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productPerformances as $p)
                @php $margin = $p->estimated_revenue - $p->total_hpp_sold; @endphp
                <tr>
                    <td class="font-bold">{{ $p->name }}</td>
                    <td class="text-center">{{ number_format($p->total_qty_sold, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($p->estimated_revenue, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #c53030;">Rp {{ number_format($p->total_hpp_sold, 0, ',', '.') }}</td>
                    <td class="text-right font-bold" style="color: #276749;">Rp {{ number_format($margin, 0, ',', '.') }}</td>
                    <td class="text-center font-bold bg-light">{{ number_format($p->total_qty_in_stock, 0, ',', '.') }}</td>
                    <td class="text-right bg-light" style="color: #4a5568;">Rp {{ number_format($p->total_hpp_in_stock, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>