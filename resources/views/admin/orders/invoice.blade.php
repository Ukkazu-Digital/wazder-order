@extends('layouts.app')

@section('content')

<style>
    /* Styling Tampilan Layar (Desktop/Tablet) */
    .receipt-container { 
        display: flex; 
        flex-direction: column; /* Mengubah ke column agar alert bisa berada di atas struk */
        align-items: center; 
        padding: 24px 0; 
        background: #f4f6f9; 
        min-height: 100vh;
    }
    .receipt { 
        width: 320px; /* Dioptimalkan agar pas di lebar kertas 72mm-80mm */
        background: #fff; 
        padding: 16px; 
        font-family: 'Courier New', Courier, monospace; 
        font-size: 12px; 
        color: #000; 
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border-radius: 8px;
    }
    .receipt .divider { 
        border-top: 1px dashed #000; 
        margin: 10px 0; 
    }
    .receipt-header {
        text-align: center;
        margin-bottom: 8px;
    }
    .receipt-meta table {
        width: 100%;
        font-size: 11px;
    }
    .receipt-meta td {
        vertical-align: top;
        padding-bottom: 2px;
    }
    .item-row {
        margin-bottom: 6px;
    }

    /* Kunci Utama Cetak Thermal Bluetooth / USB */
    @media print {
        body { 
            background: #fff; 
            padding: 0; 
            margin: 0; 
        }
        body * { 
            visibility: hidden; 
        }
        .receipt-container {
            background: none;
            padding: 0;
            margin: 0;
            display: block;
        }
        .receipt, .receipt * { 
            visibility: visible; 
        }
        .receipt { 
            position: absolute; 
            left: 0; 
            top: 0; 
            width: 100%; /* Memaksa menggunakan lebar media print */
            box-shadow: none;
            padding: 0;
            margin: 0;
        }
        @page { 
            size: 72mm auto; /* Tinggi auto mencegah kertas keluar kosong kepanjangan */
            margin: 0mm; 
        }
        .no-print { 
            display: none !important; 
            visibility: hidden !important; 
        }
    }
</style>

<div class="receipt-container">

    <!-- Alert Notifikasi (Disembunyikan saat dicetak) -->
    <div class="no-print" style="width: 320px; margin-bottom: 15px;">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show p-2 small mb-2" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
                <button type="button" class="btn-close p-2 small" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show p-2 small mb-2" role="alert">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close p-2 small" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <div class="receipt">
        <!-- Header Toko -->
        <div class="receipt-header">
            <div style="font-size: 15px; font-weight: bold; text-uppercase: true;">{{ config('app.name', 'Larawaba') }}</div>
            <div style="font-size: 10px; letter-spacing: 0.5px; margin-top: 2px;">STRUK RESMI PEMBELIAN</div>
        </div>
        
        <div class="divider"></div>

        <!-- Meta Informasi Nota (Dibuat tabel agar titik dua ":" sejajar lurus) -->
        <div class="receipt-meta">
            <table>
                <tr>
                    <td style="width: 70px;">Nota</td>
                    <td style="width: 10px;">:</td>
                    <td style="font-weight: bold;">{{ $order->order_code }}</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>:</td>
                    <td>{{ $order->created_at->format('d-m-Y H:i') }}</td>
                </tr>
                <tr>
                    <td>Kasir</td>
                    <td>:</td>
                    <td>{{ auth()->user()->name ?? 'Admin POS' }}</td>
                </tr>
            </table>
        </div>

        <div class="divider"></div>

        <!-- Daftar Item Belanjaan -->
        <div style="font-weight: bold; font-size: 10px; margin-bottom: 6px;">DAFTAR BELANJAAN:</div>
        @foreach($order->details as $d)
            <div class="item-row">
                <div style="display: flex; justify-content: space-between;">
                    <div style="flex: 1; padding-right: 4px;">{{ \Illuminate\Support\Str::limit($d->product->name ?? '-', 22) }}</div>
                    <div style="width: 45px; text-align: right;">x{{ $d->qty }}</div>
                </div>
                <div style="display: flex; justify-content: space-between; color: #333; font-size: 11px; padding-left: 8px;">
                    <div>@ Rp {{ number_format($d->buy_price, 0, ',', '.') }}</div>
                    <div>Rp {{ number_format($d->subtotal, 0, ',', '.') }}</div>
                </div>
            </div>
        @endforeach

        <div class="divider"></div>
        
        <!-- Kalkulasi Total Belanja -->
        <div style="display: flex; justify-content: space-between; font-size: 13px; font-weight: bold;">
            <div>TOTAL AKHIR</div>
            <div>Rp {{ number_format($order->total_price, 0, ',', '.') }}</div>
        </div>

        <div class="divider"></div>

        <!-- Data Penerima / Logistik Pelanggan -->
        <div class="receipt-meta" style="background-color: #fafafa; padding: 6px; border-radius: 4px;">
            <table>
                <tr>
                    <td style="width: 65px; color: #555;">Pelanggan</td>
                    <td style="width: 10px; color: #555;">:</td>
                    <td style="font-weight: 600;">{{ $order->customer->customers_name ?? 'Walk-in Customer' }}</td>
                </tr>
                @if($order->customer && $order->customer->address)
                <tr>
                    <td style="color: #555;">Alamat</td>
                    <td>:</td>
                    <td style="font-size: 11px; line-height: 13px;">{{ $order->customer->address }}</td>
                </tr>
                @endif
                @if($order->kurir)
                <tr>
                    <td style="color: #555;">Kurir</td>
                    <td>:</td>
                    <td>{{ $order->kurir->name }} [{{ $order->kurir->plate_number }}]</td>
                </tr>
                @endif
            </table>
        </div>

        <div class="divider"></div>
        
        <!-- Footer Penutup -->
        <div style="text-align: center; font-size: 11px; font-style: italic; margin-bottom: 4px;">
            Terima kasih atas kunjungan Anda
        </div>
        <div style="text-align: center; font-size: 9px; color: #666;">
            Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.
        </div>

        <!-- Panel Navigasi Tombol Kontrol (Disembunyikan saat dicetak) -->
        <div class="no-print" style="margin-top: 20px; display: flex; flex-direction: column; gap: 6px;">
            <button onclick="window.print()" class="btn btn-primary btn-sm w-100 py-2 fw-bold shadow-sm">
                <i class="bi bi-printer-fill me-2"></i>Cetak Struk Thermal
            </button>
            
            <div class="d-flex gap-2">
                <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline-secondary btn-sm flex-fill">
                    <i class="bi bi-arrow-left-short"></i> Kembali
                </a>
                
                <form action="{{ route('admin.orders.sendInvoice', $order) }}" method="POST" class="flex-fill">
                    @csrf
                    {{-- <button type="submit" class="btn btn-success btn-sm w-100" onclick="return confirm('Kirim berkas PDF struk belanja ini via WhatsApp?')"> --}}
                        {{-- <i class="bi bi-whatsapp me-1"></i> Kirim WA --}}
                    {{-- </button> --}}
                </form>
            </div>
        </div>

    </div>
</div>

@endsection