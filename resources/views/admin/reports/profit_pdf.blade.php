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
        .summary-card { padding: 12px; border: 1px solid #e2e8f0; background-color: #f7fafc; }
        .card-label { font-size: 9px; text-transform: uppercase; color: #a0aec0; font-weight: bold; }
        .card-value { font-size: 14px; font-weight: bold; margin-top: 4px; }
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { background-color: #ebf8ff; color: #2b6cb0; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; border-bottom: 2px solid #bee3f8; }
        .data-table td { padding: 8px 10px; border-bottom: 1px solid #e2e8f0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>

    <div>
        <div class="invoice-title">Laporan Laba Rugi Komprehensif</div>
        <div class="meta-text">Rentang Tanggal: {{ $startDate }} s/d {{ $endDate }} | Kalkulasi Stok: First In, First Out (FIFO)</div>
    </div>

    <table class="summary-table">
        <tr>
            <td width="32%" class="summary-card">
                <div class="card-label">Total Omset Kotor</div>
                <div class="card-value" style="color: #2d3748;">Rp {{ number_format($revenue, 0, ',', '.') }}</div>
            </td>
            <td width="2%"></td>
            <td width="32%" class="summary-card" style="background-color: #fff5f5;">
                <div class="card-label" style="color: #e53e3e;">Total Pokok HPP</div>
                <div class="card-value" style="color: #c53030;">Rp {{ number_format($totalHpp, 0, ',', '.') }}</div>
            </td>
            <td width="2%"></td>
            <td width="32%" class="summary-card" style="background-color: #f0fff4;">
                <div class="card-label" style="color: #38a169;">Laba Operasional</div>
                <div class="card-value" style="color: #276749;">Rp {{ number_format($grossProfit, 0, ',', '.') }}</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th width="40%">Deskripsi Item Produk</th>
                <th class="text-center" width="12%">Qty Terjual</th>
                <th class="text-right" width="16%">Pendapatan</th>
                <th class="text-right" width="16%">Nilai HPP</th>
                <th class="text-right" width="16%">Net Margin</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($productPerformances as $product)
                @php $margin = $product->estimated_revenue - $product->total_cost_hpp; @endphp
                <tr>
                    <td class="font-bold" style="color: #2d3748;">{{ $product->name }}</td>
                    <td class="text-center font-bold">{{ number_format($product->total_qty_sold, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($product->estimated_revenue, 0, ',', '.') }}</td>
                    <td class="text-right" style="color: #c53030;">Rp {{ number_format($product->total_cost_hpp, 0, ',', '.') }}</td>
                    <td class="text-right font-bold" style="color: #276749;">Rp {{ number_format($margin, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>