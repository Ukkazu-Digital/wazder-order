<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ProfitReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, ShouldAutoSize, WithColumnFormatting
{
    protected $data;
    protected $startDate;
    protected $endDate;

    public function __construct($data, $startDate, $endDate)
    {
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function collection()
    {
        return $this->data;
    }

    public function title(): string
    {
        return 'Laporan Laba Rugi';
    }

    public function headings(): array
    {
        return [
            ['LAPORAN LABA RUGI BERSIH (METODE FIFO)'],
            ['Periode Pencatatan:', $this->startDate . ' s/d ' . $this->endDate],
            [], 
            ['Nama Item Produk', 'Jumlah Terjual (Qty)', 'Estimasi Omset Kotor', 'Total HPP FIFO', 'Total Laba Kotor']
        ];
    }

    public function map($product): array
    {
        $margin = $product->estimated_revenue - $product->total_cost_hpp;
        
        return [
            $product->name,
            (int) $product->total_qty_sold,
            (float) $product->estimated_revenue,
            (float) $product->total_cost_hpp,
            (float) $margin
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'C' => '"Rp "#,##0',
            'D' => '"Rp "#,##0',
            'E' => '"Rp "#,##0',
        ];
    }
}