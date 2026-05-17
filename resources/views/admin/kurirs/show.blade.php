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
    .profile-main-card {
        background: #ffffff;
        border: 1px solid #eef0f3;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.01);
    }
    .photo-frame {
        width: 100%;
        max-width: 200px;
        height: 200px;
        object-fit: cover;
        border-radius: 14px;
        border: 4px solid #ffffff;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
    }
    .photo-placeholder {
        width: 100%;
        max-width: 200px;
        height: 200px;
        background-color: #f1f5f9;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        border: 2px dashed #cbd5e1;
    }
    .meta-title {
        font-size: 12px;
        font-weight: 600;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    .meta-value {
        font-size: 15px;
        color: #1e293b;
        font-weight: 600;
    }
    .plate-display {
        font-family: var(--bs-font-monospace);
        background-color: #f8fafc;
        color: #334155;
        border: 1px solid #e2e8f0;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
        display: inline-block;
    }
    .history-section-card {
        background: #ffffff;
        border: 1px solid #eef0f3;
        border-radius: 16px;
        overflow: hidden;
    }
    .table-history th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        color: #64748b;
        background-color: #f8fafc;
        border-bottom: 2px solid #edf2f7;
        padding: 14px 16px;
    }
    .table-history td {
        padding: 14px 16px;
        font-size: 14px;
        color: #334155;
        border-bottom: 1px solid #f1f5f9;
    }
</style>

<div class="container py-4">
    <!-- Header Page Navigasi -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.kurirs.index') }}" class="btn btn-outline-secondary px-3 py-2 rounded-3 fw-semibold me-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <div>
            <h2 class="fw-bold text-dark mb-0">Detail Profil Kurir</h2>
            <p class="text-muted small mb-0">Informasi berkas armada pengantar beserta rekaman performa distribusi logistik.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-9 col-md-12">
            <!-- Profil Utama Card -->
            <div class="card profile-main-card border-0 mb-4">
                <div class="card-body p-4 p-md-5">
                    <div class="row g-4 align-items-start">
                        <!-- Foto Kurir -->
                        <div class="col-md-4 text-center text-md-start d-flex justify-content-center justify-content-md-start">
                            @if($kurir->photo)
                                <img src="{{ asset('storage/kurir/' . $kurir->photo) }}" alt="Foto {{ $kurir->name }}" class="photo-frame">
                            @else
                                <div class="photo-placeholder mx-auto mx-md-0">
                                    <i class="bi bi-person text-secondary" style="font-size: 4rem;"></i>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Informasi Detail Kurir -->
                        <div class="col-md-8">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <h3 class="fw-bold text-dark mb-0">{{ $kurir->name }}</h3>
                                <span class="badge bg-light text-secondary border font-monospace py-1 px-2" style="font-size: 11px;">ID: KR-{{ str_pad($kurir->id, 3, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <p class="text-muted small mb-4">Terdaftar pada {{ $kurir->created_at->format('d M Y') }}</p>
                            
                            <div class="row g-3">
                                <div class="col-sm-6">
                                    <div class="meta-title"><i class="bi bi-telephone-outbound me-1"></i> Kontak No. HP</div>
                                    <div class="meta-value">{{ $kurir->phone }}</div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="meta-title"><i class="bi bi-car-front me-1"></i> Lisensi Plat Nomor</div>
                                    <div class="meta-value mt-1">
                                        <span class="plate-display shadow-sm">{{ strtoupper($kurir->plate_number ?? '-') }}</span>
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="meta-title"><i class="bi bi-info-circle me-1"></i> Status Tugas</div>
                                    <div class="meta-value mt-1">
                                        @if(trim(strtolower($kurir->status)) === 'aktif' || trim(strtolower($kurir->status)) === 'active')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1.5 fw-semibold" style="font-size: 12px;">
                                                <i class="bi bi-circle-fill me-1" style="font-size: 6px; vertical-align: middle;"></i> Aktif Operasional
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1.5 fw-semibold" style="font-size: 12px;">
                                                <i class="bi bi-circle-fill me-1" style="font-size: 6px; vertical-align: middle;"></i> Non-Aktif
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm-6">
                                    <div class="meta-title"><i class="bi bi-check2-all me-1"></i> Akumulasi Kerja</div>
                                    <div class="meta-value text-primary fs-5 fw-bold">{{ $kurir->orders->count() }} <span class="text-muted fs-6 fw-normal">Pesanan ditangani</span></div>
                                </div>
                            </div>

                            <!-- Tombol Manajemen Aksi -->
                            <div class="d-flex gap-2 mt-4 pt-3 border-top text-secondary opacity-75">
                                <a href="{{ route('admin.kurirs.edit', $kurir) }}" class="btn btn-warning px-4 py-2 rounded-3 fw-semibold text-warning-emphasis">
                                    <i class="bi bi-pencil-square me-2"></i>Edit Profil
                                </a>
                                <form action="{{ route('admin.kurirs.destroy', $kurir) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger px-4 py-2 rounded-3 fw-semibold" onclick="return confirm('Apakah Anda yakin ingin menghapus data kurir ini secara permanen?')">
                                        <i class="bi bi-trash me-2"></i>Hapus Kurir
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabel Riwayat Order Distribusi -->
            @if($kurir->orders->count() > 0)
                <div class="card history-section-card border-0 shadow-sm">
                    <div class="card-header bg-white p-3 border-0 pt-4 px-4">
                        <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                            <i class="bi bi-clock-history text-primary me-2"></i> Log Riwayat Pengiriman Pasokan ({{ $kurir->orders->count() }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-history align-middle table-hover mb-0 m-0">
                                <thead>
                                    <tr>
                                        <th>Kode Order</th>
                                        <th>Nama Penerima / Customer</th>
                                        <th>Total Pembayaran</th>
                                        <th>Status Pengiriman</th>
                                        <th>Tanggal Distribusi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kurir->orders as $order)
                                    <tr>
                                        <td><span class="font-monospace fw-bold text-dark p-1 px-2 rounded bg-light border border-secondary-subtle" style="font-size: 12px;">{{ $order->order_code }}</span></td>
                                        <td class="fw-semibold text-dark">{{ $order->customer->customers_name ?? '-' }}</td>
                                        <td class="fw-bold">Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                                        <td>
                                            @php
                                                $status = strtolower($order->status);
                                            @endphp
                                            @if($status === 'completed' || $status === 'success' || $status === 'selesai')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 fw-semibold">Selesai</span>
                                            @elseif($status === 'shipped' || $status === 'dikirim')
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 fw-semibold">Dikirim</span>
                                            @elseif($status === 'paid' || $status === 'dibayar')
                                                <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle px-2 py-1 fw-semibold">Dibayar</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1 fw-semibold">{{ ucfirst($order->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">
                                            <i class="bi bi-calendar-event me-1"></i> {{ $order->created_at->format('d-m-Y H:i') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection