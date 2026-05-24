@extends('layouts.app')

@section('title', 'Laporan Nilai Aset Gudang')

@section('content')
@php
    $fmt = fn($val) => number_format($val, 0, ',', '.');
@endphp

<div class="container-fluid py-4">
    <header class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center bg-white p-4 p-md-5 mb-4 shadow-sm rounded-4 border border-light">
        <div>
            <h2 class="fw-bolder text-dark mb-1">Laporan Nilai Aset Gudang</h2>
            <p class="text-secondary small mb-0">Kalkulasi nilai uang yang tertanam di gudang saat ini menggunakan sisa kuota batch FIFO.</p>
        </div>
        <div class="mt-3 mt-sm-0 no-print">
            <button onclick="window.print()" class="btn btn-primary px-4 py-2 rounded-3 shadow-sm">
                <i class="bi bi-printer"></i> Cetak Halaman
            </button>
        </div>
    </header>

    <section class="row g-4 mb-4">
        <div class="col-12 col-md-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary bg-opacity-10 h-100 d-flex flex-column justify-content-center">
                <p class="small fw-bold text-uppercase text-primary mb-1">Total Nilai Kondisi Aset Saat Ini (FIFO)</p>
                <h2 class="fw-bolder text-primary font-monospace mb-0">Rp {{ $fmt($totalValuation) }}</h2>
            </div>
        </div>
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 h-100 d-flex flex-column justify-content-center">
                <p class="small fw-bold text-uppercase text-muted mb-1">Total Jenis Produk Aktif</p>
                <h3 class="fw-bolder text-dark mb-0">{{ $productAssets->count() }} Produk</h3>
            </div>
        </div>
    </section>

    <section class="bg-white shadow-sm rounded-4 border border-light overflow-hidden">
        <div class="p-4 border-bottom border-light">
            <h5 class="fw-bold m-0">Rincian Komposisi Nilai Produk di Gudang</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase small text-muted">
                    <tr>
                        <th class="py-3 px-4">Nama Item Produk</th>
                        <th class="py-3 px-4 text-center">Sisa Stok Fisik</th>
                        <th class="py-3 px-4 text-end">Estimasi Nilai Aset (HPP)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productAssets as $asset)
                        <tr>
                            <td class="py-3 px-4 fw-bold text-dark">{{ $asset['name'] }}</td>
                            <td class="py-3 px-4 text-center font-monospace text-secondary">
                                {{ $fmt($asset['current_stock']) }}
                            </td>
                            <td class="py-3 px-4 text-end font-monospace">
                                <span class="fw-bold text-indigo">{{ $fmt($asset['asset_valuation']) }}</span>
                                <small class="d-block text-muted" style="font-size: 0.75rem">
                                    Rata-rata modal: Rp {{ $fmt($asset['current_stock'] > 0 ? ($asset['asset_valuation'] / $asset['current_stock']) : 0) }}
                                </small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted fst-italic">
                                Seluruh stok produk kosong / tidak ada aset bernilai yang tersisa di gudang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

<style>
    @media print {
        .no-print, header .btn, .sidebar, .navbar { display: none !important; }
        .container-fluid { padding: 0 !important; }
        .card, .bg-white { border: none !important; box-shadow: none !important; }
    }
</style>
@endsection