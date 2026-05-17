@extends('layouts.app')

@section('content')
<!-- Import Font Modern & Icons -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8f9fa;
    }
    .profile-card {
        background: #ffffff;
        border: 1px solid #eef0f3;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.01);
    }
    .avatar-large {
        width: 72px;
        height: 72px;
        background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        color: #0369a1;
        font-weight: 700;
        font-size: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 20px;
        box-shadow: 0 8px 16px rgba(3, 105, 161, 0.05);
    }
    .info-label {
        font-size: 12px;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
    }
    .info-value {
        font-size: 14px;
        color: #1e293b;
        font-weight: 500;
    }
    .history-card {
        background: #ffffff;
        border: 1px solid #eef0f3;
        border-radius: 16px;
    }
    .table-order th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        color: #64748b;
        background-color: #f8fafc;
        border-bottom: 2px solid #edf2f7;
        padding: 12px 16px;
    }
    .table-order td {
        padding: 14px 16px;
        font-size: 14px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
    .code-badge {
        font-family: var(--bs-font-monospace);
        background-color: #f1f5f9;
        color: #0f172a;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        border: 1px solid #e2e8f0;
    }
</style>

<div class="container py-4">
    <!-- Header Page Navigasi -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">Detail Profil Customer</h2>
            <p class="text-muted small mb-0">Informasi personal pelanggan beserta akumulasi riwayat transaksi pesanan.</p>
        </div>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 fw-semibold">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
        </a>
    </div>

    <div class="row g-4">
        <!-- SISI KIRI: Profil Ringkas Komponen -->
        <div class="col-md-4">
            <div class="card profile-card border-0 p-3 mb-4">
                <div class="card-body text-center pt-3">
                    <div class="avatar-large mx-auto mb-3">
                        {{ strtoupper(substr($customer->customers_name, 0, 2)) }}
                    </div>
                    <h4 class="fw-bold text-dark mb-1">{{ $customer->customers_name }}</h4>
                    <span class="badge bg-light text-secondary border px-2 py-1 mb-4 font-monospace">ID: Cust-{{ $customer->id }}</span>
                    
                    <hr class="text-secondary opacity-25 my-3">

                    <div class="text-start">
                        <div class="mb-3">
                            <div class="info-label"><i class="bi bi-envelope me-1"></i> Alamat Email</div>
                            <div class="info-value text-break">{{ $customer->email ?? '-' }}</div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="info-label"><i class="bi bi-geo-alt me-1"></i> Alamat Pengiriman</div>
                            <div class="info-value" style="white-space: pre-line; text-align: justify; text-justify: inter-word;">{{ $customer->address ?? 'Tidak ada data alamat.' }}</div>
                        </div>

                        <div>
                            <div class="info-label"><i class="bi bi-bag-check me-1"></i> Total Aktivitas Belanja</div>
                            <div class="info-value">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 mt-1 fw-semibold" style="font-size: 13px;">
                                    {{ $customer->orders->count() }} Kali Pemesanan
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SISI KANAN: Daftar Riwayat Transaksi -->
        <div class="col-md-8">
            <div class="card history-card border-0 p-2">
                <div class="card-body p-3">
                    <h5 class="fw-bold text-dark mb-3 d-flex align-items-center">
                        <i class="bi bi-clock-history text-primary me-2"></i> Riwayat Transaksi Pesanan
                    </h5>

                    <div class="table-responsive">
                        <table class="table table-order align-middle table-hover m-0">
                            <thead>
                                <tr>
                                    <th>Kode Order</th>
                                    <th>Status Pesanan</th>
                                    <th>Nilai Transaksi</th>
                                    <th>Waktu Pembelian</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customer->orders as $order)
                                <tr>
                                    <td>
                                        <span class="code-badge shadow-sm">{{ $order->order_code }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $status = strtolower($order->status);
                                        @endphp
                                        @if($status === 'completed' || $status === 'selesai' || $status === 'success')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-semibold">Selesai</span>
                                        @elseif($status === 'pending' || $status === 'proses' || $status === 'processing')
                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 fw-semibold">Diproses</span>
                                        @elseif($status === 'cancelled' || $status === 'batal')
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 fw-semibold">Dibatalkan</span>
                                        @else
                                            <span class="badge bg-light text-dark border px-2 py-1 fw-semibold">{{ $order->status }}</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-dark">
                                        Rp{{ number_format($order->total_price, 0, ',', '.') }}
                                    </td>
                                    <td class="text-muted small">
                                        <i class="bi bi-calendar3 me-1"></i> {{ $order->created_at->format('d M Y, H:i') }} WIB
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-5">
                                        <i class="bi bi-cart-x fs-2 d-block text-black-50 mb-2"></i>
                                        Belum ada riwayat transaksi tercatat untuk customer ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection