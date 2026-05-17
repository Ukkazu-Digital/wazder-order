@extends('layouts.app')

@section('content')
<!-- Memasukkan font modern & icons agar selaras dengan ekosistem POS Kasir -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    .order-detail-container {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }
    .font-monospace-id {
        font-family: 'Courier New', Courier, monospace;
        font-weight: 700;
        letter-spacing: 0.3px;
    }
    .custom-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        overflow: hidden;
        background-color: #ffffff;
    }
    .info-label {
        font-size: 12px;
        text-uppercase: true;
        letter-spacing: 0.5px;
        color: #6c757d;
        margin-bottom: 4px;
    }
    /* Timeline Style untuk Order History */
    .timeline-history {
        position: relative;
        padding-left: 30px;
        list-style: none;
    }
    .timeline-history::before {
        content: "";
        position: absolute;
        left: 9px;
        top: 5px;
        bottom: 5px;
        width: 2px;
        background: #e9ecef;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    .timeline-marker {
        position: absolute;
        left: -26px;
        top: 3px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #dee2e6;
        border: 2px solid #fff;
        box-shadow: 0 0 0 3px #f8f9fa;
    }
    .timeline-item.active .timeline-marker {
        background: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2);
    }
</style>

<div class="container py-4 order-detail-container">
    
    <!-- Top Navigation Header -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-white border rounded-3 p-2 me-3 shadow-sm bg-white" title="Kembali ke Daftar">
            <i class="bi bi-arrow-left fs-5 text-dark"></i>
        </a>
        <div>
            <h1 class="fw-bold h3 mb-1 text-dark">Detail Orderan</h1>
            <p class="text-muted small mb-0">Kelola rincian item belanjaan dan logistik kurir pengiriman.</p>
        </div>
    </div>

    <!-- Layout Grid Utama (Responsif 2 Kolom) -->
    <div class="row g-4">
        
        <!-- KOLOM KIRI: Informasi Transaksi & Detail Produk -->
        <div class="col-lg-8">
            
            <!-- Card Ringkasan Utama Transaksi -->
            <div class="card custom-card mb-4">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-sm-6 col-md-3">
                            <div class="info-label"><i class="bi bi-hash me-1"></i>Kode Order</div>
                            <h5 class="fw-bold text-primary font-monospace-id mb-0">{{ $order->order_code }}</h5>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="info-label"><i class="bi bi-info-circle me-1"></i>Status Saat Ini</div>
                            @if($order->status == 'pending')
                                <span class="badge px-3 py-2 rounded-pill fw-bold" style="background-color: #fff3e0; color: #ef6c00;">Pending</span>
                            @elseif($order->status == 'paid')
                                <span class="badge px-3 py-2 rounded-pill fw-bold" style="background-color: #e8f5e9; color: #2e7d32;">Paid</span>
                            @elseif($order->status == 'shipped')
                                <span class="badge px-3 py-2 rounded-pill fw-bold" style="background-color: #e0f7fa; color: #00838f;">Shipped</span>
                            @elseif($order->status == 'completed')
                                <span class="badge px-3 py-2 rounded-pill fw-bold" style="background-color: #efebe9; color: #4e342e;">Completed</span>
                            @else
                                <span class="badge px-3 py-2 rounded-pill fw-bold" style="background-color: #ffebee; color: #c62828;">{{ ucfirst($order->status) }}</span>
                            @endif
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="info-label"><i class="bi bi-person me-1"></i>Customer</div>
                            <p class="fw-bold text-dark mb-0">{{ $order->customer->customers_name ?? 'Walk-in Customer' }}</p>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="info-label"><i class="bi bi-calendar3 me-1"></i>Tanggal Order</div>
                            <p class="fw-semibold text-secondary small mb-0">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card Tabel Rincian Pembelian Produk -->
            <div class="card custom-card">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-box-seam me-2 text-secondary"></i>Item Produk Yang Dibeli</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover mb-0">
                            <thead class="table-light text-secondary text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                                <tr>
                                    <th class="py-3 ps-4">Nama Produk</th>
                                    <th class="py-3 text-center">Qty</th>
                                    <th class="py-3 text-end">Harga Satuan</th>
                                    <th class="py-3 text-end pe-4">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->details as $detail)
                                <tr>
                                    <td class="fw-semibold text-dark ps-4">
                                        {{ $detail->product->name ?? 'Produk Tidak Diketahui / Terhapus' }}
                                    </td>
                                    <td class="text-center fw-bold text-secondary">{{ $detail->qty }}</td>
                                    <td class="text-end text-secondary">Rp{{ number_format($detail->buy_price, 0, ',', '.') }}</td>
                                    <td class="text-end fw-bold text-dark pe-4">Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                                <tr class="table-light border-top border-dark-subtle">
                                    <td colspan="3" class="text-end fw-bold text-uppercase p-3" style="font-size: 12px; letter-spacing: 0.5px;">Grand Total :</td>
                                    <td class="text-end fw-extrabold text-primary fs-5 pe-4 py-2">
                                        Rp{{ number_format($order->total_price, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN: Update Form (Bisa Terkunci) & Order History Timeline -->
        <div class="col-lg-4">
            
            <!-- Tampilan Info Kurir Terpasang (Jika Ada) -->
            @if($order->kurir)
            <div class="card custom-card border-start border-4 border-warning mb-4 shadow-sm">
                <div class="card-body p-3">
                    <div class="info-label"><i class="bi bi-truck me-1"></i>Kurir Pengirim</div>
                    <h6 class="fw-bold text-dark mb-1">{{ $order->kurir->name }}</h6>
                    <p class="text-secondary font-monospace-id small mb-1" style="font-size: 12px;">No. Plat: {{ $order->kurir->plate_number }}</p>
                </div>
            </div>
            @endif

            <!-- Form Pembaruan Alur Status Order -->
            <div class="card custom-card mb-4">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i>Ubah Status Transaksi</h5>
                </div>
                <div class="card-body p-4">
                    
                    <!-- Validasi JIKA Status Sudah Selesai / Batal (Final) -->
                    @if(in_array($order->status, ['completed', 'cancelled']))
                        <div class="alert alert-light border border-warning text-warning rounded-3 d-flex align-items-start p-3 mb-0" role="alert">
                            <i class="bi bi-lock-fill me-2 fs-5"></i>
                            <div class="small text-dark-emphasis">
                                <strong class="text-warning d-block mb-1">Transaksi Dikunci</strong>
                                Status order ini sudah final (<strong>{{ ucfirst($order->status) }}</strong>). Data tidak dapat diubah kembali demi validitas laporan keuangan.
                            </div>
                        </div>
                        <div class="d-grid gap-2 mt-3">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary py-2 rounded-3 fw-semibold">
                                Kembali ke Daftar Order
                            </a>
                        </div>
                    @else
                        <!-- Form Aktif Jika Status Belum Final -->
                        <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="status" class="form-label fw-bold text-secondary small">Status Alur Kerja <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-select border-2 p-2 rounded-3 @error('status') is-invalid @enderror" required onchange="handleStatusChange()">
                                    <option value="">-- Pilih Status Baru --</option>
                                    <option value="pending" @if($order->status=='pending') selected @endif>Pending (Belum Bayar)</option>
                                    <option value="paid" @if($order->status=='paid') selected @endif>Paid (Sudah Bayar)</option>
                                    <option value="shipped" @if($order->status=='shipped') selected @endif>Shipped (Dikirim Kurir)</option>
                                    <option value="completed" @if($order->status=='completed') selected @endif>Completed (Selesai)</option>
                                    <option value="cancelled" @if($order->status=='cancelled') selected @endif>Cancelled (Dibatalkan)</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Dropdown Kurir Dinamis (Otomatis Muncul jika status diubah ke Shipped) -->
                            <div class="mb-4 bg-light p-3 rounded-4 border" id="kurirField" style="display: none;">
                                <label for="kurir_id" class="form-label fw-bold text-dark small"><i class="bi bi-person-badge-fill me-1 text-warning"></i>Tugaskan Kurir <span class="text-danger">*</span></label>
                                <select name="kurir_id" id="kurir_id" class="form-select rounded-3 @error('kurir_id') is-invalid @enderror">
                                    <option value="">-- Pilih Anggota Kurir --</option>
                                    @foreach($kurirs as $kurir)
                                    <option value="{{ $kurir->id }}" @if($order->kurir_id == $kurir->id) selected @endif>
                                        {{ $kurir->name }} [{{ $kurir->plate_number }}]
                                    </option>
                                    @endforeach
                                </select>
                                @error('kurir_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Tombol Aksi Simpan -->
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary py-2 rounded-3 fw-bold shadow-sm">
                                    <i class="bi bi-floppy-fill me-2"></i>Simpan Perubahan
                                </button>
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary py-2 rounded-3 fw-semibold">
                                    Batal
                                </a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <!-- CARD: Riwayat Status (Timeline Log Jejak Transaksi Lengkap) -->
<div class="card custom-card">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-clock-history me-2 text-secondary"></i>Semua Riwayat Transaksi</h5>
    </div>
    <div class="card-body px-4 pb-4 pt-2">
        <ul class="timeline-history mb-0">
            
            <!-- ====== FASE FINAL (JIKA SELESAI / BATAL) ====== -->
            @if($order->status == 'completed')
                <li class="timeline-item active">
                    <div class="timeline-marker bg-success" style="box-shadow: 0 0 0 3px rgba(46, 125, 50, 0.2);"></div>
                    <h6 class="fw-bold text-success mb-1">Pesanan Selesai (Completed)</h6>
                    <p class="text-muted small mb-1">Transaksi dinyatakan sukses, produk telah diterima pelanggan, dan dana dibukukan.</p>
                    <small class="text-secondary d-block bg-light p-1 px-2 rounded font-monospace" style="font-size: 11px;">
                        <i class="bi bi-calendar-check me-1"></i>{{ $order->updated_at->format('d M Y, H:i') }} WIB
                    </small>
                </li>
            @elseif($order->status == 'cancelled')
                <li class="timeline-item active">
                    <div class="timeline-marker bg-danger" style="box-shadow: 0 0 0 3px rgba(198, 40, 40, 0.2);"></div>
                    <h6 class="fw-bold text-danger mb-1">Pesanan Dibatalkan (Cancelled)</h6>
                    <p class="text-muted small mb-1">Orderan dibatalkan/dihapus dari sistem perdagangan aktif.</p>
                    <small class="text-secondary d-block bg-light p-1 px-2 rounded font-monospace" style="font-size: 11px;">
                        <i class="bi bi-calendar-x me-1"></i>{{ $order->updated_at->format('d M Y, H:i') }} WIB
                    </small>
                </li>
            @endif

            <!-- ====== FASE PENGIRIMAN (SHIPPED) ====== -->
            @if(in_array($order->status, ['shipped', 'completed']) || $order->kurir_id)
                <li class="timeline-item {{ $order->status == 'shipped' ? 'active' : '' }}">
                    <div class="timeline-marker {{ $order->status == 'shipped' ? 'bg-info' : '' }}"></div>
                    <h6 class="fw-bold text-dark mb-1">Pesanan Dikirim (Shipped)</h6>
                    <p class="text-muted small mb-1">
                        Paket diserahkan kepada kurir <strong>{{ $order->kurir->name ?? 'Armada Operasional' }}</strong> 
                        [{{ $order->kurir->plate_number ?? '-' }}].
                    </p>
                    @if($order->status == 'shipped')
                        <small class="text-secondary d-block bg-light p-1 px-2 rounded font-monospace" style="font-size: 11px;">
                            <i class="bi bi-truck me-1"></i>{{ $order->updated_at->format('d M Y, H:i') }} WIB
                        </small>
                    @endif
                </li>
            @endif

            <!-- ====== FASE PEMBAYARAN (PAID) ====== -->
            @if(in_array($order->status, ['paid', 'shipped', 'completed']))
                <li class="timeline-item {{ $order->status == 'paid' ? 'active' : '' }}">
                    <div class="timeline-marker {{ $order->status == 'paid' ? 'bg-success' : '' }}"></div>
                    <h6 class="fw-bold text-dark mb-1">Pembayaran Lunas (Paid)</h6>
                    <p class="text-muted small mb-1">Kasir memverifikasi pembayaran sebesar <strong>Rp{{ number_format($order->total_price, 0, ',', '.') }}</strong> telah diterima.</p>
                    @if($order->status == 'paid')
                        <small class="text-secondary d-block bg-light p-1 px-2 rounded font-monospace" style="font-size: 11px;">
                            <i class="bi bi-cash-coin me-1"></i>{{ $order->updated_at->format('d M Y, H:i') }} WIB
                        </small>
                    @endif
                </li>
            @endif

            <!-- ====== FASE AWAL (PENDING / BARU BUAT) ====== -->
            <li class="timeline-item {{ $order->status == 'pending' ? 'active' : '' }}">
                <div class="timeline-marker {{ $order->status == 'pending' ? 'bg-warning' : '' }}"></div>
                <h6 class="fw-bold text-dark mb-1">Pesanan Masuk (Pending)</h6>
                <p class="text-muted small mb-1">Invoice dibuat via sistem POS Kasir dengan Kode Order <span class="font-monospace text-primary fw-bold">{{ $order->order_code }}</span>.</p>
                <small class="text-secondary d-block bg-light p-1 px-2 rounded font-monospace" style="font-size: 11px;">
                    <i class="bi bi-clock me-1"></i>{{ $order->created_at->format('d M Y, H:i') }} WIB
                </small>
            </li>

        </ul>
    </div>
</div>

    </div>
</div>

<script>
    function handleStatusChange() {
        const statusField = document.getElementById('status');
        if(!statusField) return; // Mencegah error jika form tidak dirender (dikunci)

        const status = statusField.value;
        const kurirField = document.getElementById('kurirField');
        const kurirSelect = document.getElementById('kurir_id');

        if (status === 'shipped') {
            kurirField.style.display = 'block';
            kurirSelect.required = true;
        } else {
            kurirField.style.display = 'none';
            kurirSelect.required = false;
            kurirSelect.value = '';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        handleStatusChange();
    });
</script>
@endsection