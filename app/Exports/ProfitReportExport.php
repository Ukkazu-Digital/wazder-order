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
            ['LAPORAN LABA RUGI & VALUASI ASET (METODE FIFO)'],
            ['Periode Pencatatan:', $this->startDate . ' s/d ' . $this->endDate],
            [], 
            [
                'Nama Item Produk', 
                'Jumlah Terjual (Qty)', 
                'Estimasi Omset Kotor', 
                'Total HPP Terjual', 
                'Total Laba Kotor',
                'Stok Gudang (Sisa)',
                'Nilai Aset Modal Gudang'
            ]
        ];
    }

    public function map($product): array
    {
        // Sesuaikan dengan alias yang kita gunakan di query:
        // total_hpp_sold (terjual), total_qty_in_stock (sisa), total_hpp_in_stock (aset)
        $margin = $product->estimated_revenue - $product->total_hpp_sold;
        
        return [
            $product->name,
            (int) $product->total_qty_sold,
            (float) $product->estimated_revenue,
            (float) $product->total_hpp_sold,
            (float) $margin,
            (int) $product->total_qty_in_stock,
            (float) $product->total_hpp_in_stock
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER, // Qty terjual
            'C' => '"Rp "#,##0', // Omset
            'D' => '"Rp "#,##0', // HPP Terjual
            'E' => '"Rp "#,##0', // Margin
            'F' => NumberFormat::FORMAT_NUMBER, // Stok Sisa
            'G' => '"Rp "#,##0', // Nilai Aset
        ];
    }
}