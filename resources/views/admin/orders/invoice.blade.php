@extends('layouts.app')

@section('content')

<style>
    /* Thermal-friendly styling for invoice view */
    .receipt-container { display:flex; justify-content:center; padding:18px 0; }
    .receipt { width:384px; background:#fff; padding:10px; font-family: monospace; font-size:12px; color:#000; }
    .receipt .divider { border-top:1px dashed #000; margin:8px 0; }
    
    @media print {
        body * { visibility: hidden; }
        .receipt, .receipt * { visibility: visible; }
        .receipt { position: absolute; left: 0; top: 0; }
        @page { size: 72mm; margin: 0; }
        
        /* Tambahan untuk menyembunyikan tombol saat print */
        .no-print, .no-print * { display: none !important; visibility: hidden !important; }
    }
</style>

<div class="receipt-container">
    <div class="receipt">
        <div style="text-align:center; font-weight:bold;">{{ config('app.name', 'Larawaba') }}</div>
        <div style="text-align:center;">STRUK PEMBELIAN</div>
        <div class="divider"></div>

        <div>
            <div>Invoice: {{ $order->order_code }}</div>
            <div>Tgl: {{ $order->created_at->format('d-m-Y H:i') }}</div>
        </div>

        <div class="divider"></div>

        @foreach($order->details as $d)
            <div style="display:flex; justify-content:space-between;">
                <div style="flex:1">{{ \Illuminate\Support\Str::limit($d->product->name ?? '-', 24) }}</div>
                <div style="width:50px; text-align:right">x{{ $d->qty }}</div>
            </div>
            <div style="display:flex; justify-content:space-between; color:#444; font-size:11px;">
                <div>Rp {{ number_format($d->buy_price,0,',','.') }}</div>
                <div>Rp {{ number_format($d->subtotal,0,',','.') }}</div>
            </div>
        @endforeach

        <div class="divider"></div>
        <div style="display:flex; justify-content:space-between; font-weight:bold;">
            <div>TOTAL</div>
            <div>Rp {{ number_format($order->total_price,0,',','.') }}</div>
        </div>

        <div style="margin-top:8px; font-size:11px;">Pelanggan: {{ $order->customer->customers_name ?? '-' }}</div>
        <div style="font-size:11px;">Alamat: {{ $order->customer->address ?? '-' }}</div>

        <div class="divider"></div>
        <div style="text-align:center; font-size:11px;">Terima kasih atas kunjungan Anda</div>

        <!-- Menambahkan class no-print di bawah ini -->
        <div class="no-print" style="margin-top:12px; display:flex; gap:6px;">
            <button onclick="window.print()" class="btn btn-primary btn-sm">Cetak</button>
            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-secondary btn-sm">Detail</a>
            <form action="{{ route('admin.orders.sendInvoice', $order) }}" method="POST" style="display:inline-block;">
                @csrf
                <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Kirim invoice via WhatsApp?')">Kirim WA</button>
            </form>
        </div>
    </div>
</div>

@endsection