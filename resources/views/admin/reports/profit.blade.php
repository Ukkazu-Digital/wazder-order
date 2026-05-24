@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@section('content')
@php
    $fmt = fn($val) => number_format($val, 0, ',', '.');
@endphp

<div class="container-fluid py-4">
    <header class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center bg-white p-4 p-md-5 mb-4 shadow-sm rounded-4 border border-light">
        <div>
            <h2 class="fw-bolder text-dark mb-1">Laporan Laba Rugi</h2>
            <p class="text-secondary small mb-0">Analisis performa finansial real-time berbasis metode FIFO.</p>
        </div>
        <div class="d-flex gap-2 mt-3 mt-sm-0">
            <a href="{{ route('admin.reports.profit.excel', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success px-4 py-2 rounded-3">
                <i class="bi bi-file-earmark-excel"></i> Excel
            </a>
            <a href="{{ route('admin.reports.profit.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" target="_blank" class="btn btn-danger px-4 py-2 rounded-3">
                <i class="bi bi-file-earmark-pdf"></i> PDF
            </a>
        </div>
    </header>

    <section class="bg-white p-4 p-md-4 mb-4 shadow-sm rounded-4 border border-light">
        <form action="{{ route('admin.reports.profit.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-12 col-md-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Tanggal Mulai</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="form-control form-control-lg bg-light border-0 rounded-3">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small fw-bold text-uppercase text-muted">Tanggal Akhir</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="form-control form-control-lg bg-light border-0 rounded-3">
            </div>
            <div class="col-12 col-md-3">
                <button type="submit" class="btn btn-dark btn-lg w-100 rounded-3 shadow">
                    Update Laporan
                </button>
            </div>
        </form>
    </section>

    <section class="row g-4 mb-4">
        @foreach([
            ['label' => 'Total Pendapatan', 'value' => $revenue, 'color' => 'text-dark', 'icon' => 'bi-wallet2'],
            ['label' => 'Total Beban HPP', 'value' => $totalHpp, 'color' => 'text-danger', 'icon' => 'bi-box-arrow-down'],
            ['label' => 'Laba Bersih', 'value' => $grossProfit, 'color' => 'text-success', 'icon' => 'bi-graph-up-arrow']
        ] as $card)
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm rounded-4 h-100 p-4">
                    <div class="d-flex align-items-center gap-2 text-muted mb-3">
                        <i class="bi {{ $card['icon'] }}"></i>
                        <span class="small fw-bold text-uppercase">{{ $card['label'] }}</span>
                    </div>
                    <h3 class="fw-bolder {{ $card['color'] }} font-monospace">Rp {{ $fmt($card['value']) }}</h3>
                </div>
            </div>
        @endforeach
    </section>

    <section class="bg-white shadow-sm rounded-4 border border-light overflow-hidden">
        <div class="p-4 border-bottom border-light d-flex justify-content-between align-items-center">
            <h5 class="fw-bold m-0">Performa Produk</h5>
            <span class="badge bg-light text-dark rounded-pill px-3">{{ count($productPerformances) }} Produk</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light text-uppercase small text-muted">
                    <tr>
                        <th class="py-3 px-4">Nama Produk</th>
                        <th class="py-3 px-4 text-center">Terjual</th>
                        <th class="py-3 px-4 text-end">Omset</th>
                        <th class="py-3 px-4 text-end">HPP</th>
                        <th class="py-3 px-4 text-end">Margin</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productPerformances as $p)
                        <tr>
                            <td class="py-3 px-4 fw-bold text-dark">{{ $p->name }}</td>
                            <td class="py-3 px-4 text-center font-monospace text-secondary">{{ $fmt($p->total_qty_sold) }}</td>
                            <td class="py-3 px-4 text-end font-monospace">Rp {{ $fmt($p->estimated_revenue) }}</td>
                            <td class="py-3 px-4 text-end font-monospace text-danger">Rp {{ $fmt($p->total_cost_hpp) }}</td>
                            <td class="py-3 px-4 text-end font-monospace fw-bold text-success">Rp {{ $fmt($p->estimated_revenue - $p->total_cost_hpp) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted italic">Data belum tersedia</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection