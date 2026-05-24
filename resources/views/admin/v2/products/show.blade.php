@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" style="max-width: 1400px;">
    
    <!-- Header Navigasi -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 text-muted small fw-medium">
                    <li class="breadcrumb-item"><a href="{{ route('admin.v2.products.index') }}" class="text-decoration-none text-secondary">Produk</a></li>
                    <li class="breadcrumb-item active text-dark" aria-current="page">Detail Kartu Stok</li>
                </ol>
            </nav>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-light text-secondary border font-monospace py-1.5 px-2 small">ID #{{ $product->id }}</span>
                <h3 class="fw-bold text-dark mb-0 tracking-tight">{{ $product->name }}</h3>
            </div>
        </div>
        <div>
            <a href="{{ route('admin.v2.products.edit', $product->id) }}" class="btn btn-white border border-gray-300 rounded-3 shadow-sm px-3 py-2 fw-semibold btn-hover-zoom">
                <i class="bi bi-pencil text-secondary me-1.5"></i> Edit Detail Produk
            </a>
        </div>
    </div>

    <!-- Ringkasan Stok & Nilai Aset (Widget Cepat) -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total Stok -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100 position-relative overflow-hidden">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-bold tracking-wider">TOTAL STOK SEKARANG</span>
                    <div class="bg-primary-subtle text-primary rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-box-seam-fill fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline mt-1">
                    <span class="fs-1 fw-extrabold text-dark tracking-tight">{{ number_format($product->totalStock(), 0, ',', '.') }}</span>
                    <span class="text-secondary ms-2 fw-semibold fs-6">Pcs</span>
                </div>
                <div class="position-absolute bottom-0 start-0 end-0 bg-primary opacity-10" style="height: 4px;"></div>
            </div>
        </div>
        
        <!-- Card 2: Estimasi Nilai Aset -->
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-3 bg-white p-3 h-100 position-relative overflow-hidden">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-secondary small fw-bold tracking-wider">ESTIMASI NILAI ASET (HPP)</span>
                    <div class="bg-success-subtle text-success rounded-3 p-2 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </div>
                </div>
                <div class="d-flex align-items-baseline mt-1">
                    <span class="fs-1 fw-extrabold text-dark tracking-tight">
                        Rp{{ number_format($product->stockEntries->sum(fn($b) => $b->qty_remaining * $b->purchase_price), 0, ',', '.') }}
                    </span>
                </div>
                <div class="position-absolute bottom-0 start-0 end-0 bg-success opacity-10" style="height: 4px;"></div>
            </div>
        </div>
    </div>

    <!-- Menu Tab Navigasi Konten -->
    <div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
        <div class="card-header bg-transparent border-bottom p-0">
            <ul class="nav nav-tabs card-header-tabs m-0 px-3 gap-2 border-bottom-0 navbar-custom-tabs" id="productTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active py-3 px-3 fw-bold border-0 text-secondary" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button" role="tab">
                        <i class="bi bi-info-circle me-1.5"></i> Informasi Umum
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-3 fw-bold border-0 text-secondary d-flex align-items-center" id="batches-tab" data-bs-toggle="tab" data-bs-target="#batches" type="button" role="tab">
                        <i class="bi bi-layers me-1.5"></i> Antrean Batch FIFO
                        <span class="badge bg-light text-dark border ms-2 rounded-pill font-monospace px-2 py-1 small">{{ $product->stockEntries->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link py-3 px-3 fw-bold border-0 text-secondary" id="ledger-tab" data-bs-toggle="tab" data-bs-target="#ledger" type="button" role="tab">
                        <i class="bi bi-journal-text me-1.5"></i> Kartu Stok (Ledger Mutasi)
                    </button>
                </li>
            </ul>
        </div>
        
        <div class="card-body p-3 p-md-4">
            <div class="tab-content" id="productTabContent">
                
                <!-- TAB 1: INFORMASI UMUM -->
                <div class="tab-pane fade show active" id="info" role="tabpanel">
                    <div class="bg-light rounded-3 p-4 border border-dashed" style="max-width: 700px;">
                        <h5 class="fw-bold text-dark mb-3 small tracking-wider text-uppercase">Spesifikasi Entri Produk</h5>
                        
                        <div class="row g-3">
                            <div class="col-sm-4 text-secondary fw-medium">ID Sistem</div>
                            <div class="col-sm-8 text-dark font-monospace fw-semibold">#{{ $product->id }}</div>
                            <hr class="my-1 text-muted opacity-25">
                            
                            <div class="col-sm-4 text-secondary fw-medium">Nama Produk Resmi</div>
                            <div class="col-sm-8 text-dark fw-bold">{{ $product->name }}</div>
                            <hr class="my-1 text-muted opacity-25">
                            
                            <div class="col-sm-4 text-secondary fw-medium">Harga Jual Standar Toko</div>
                            <div class="col-sm-8 text-danger fw-bold fs-5">Rp{{ number_format($product->selling_price, 0, ',', '.') }}</div>
                            <hr class="my-1 text-muted opacity-25">
                            
                            <div class="col-sm-4 text-secondary fw-medium">Waktu Pembuatan Sistem</div>
                            <div class="col-sm-8 text-muted fw-normal">{{ $product->created_at->format('d F Y \j\a\m H:i') }} WIB</div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: ANTREAN BATCH FIFO AKTIF -->
                <div class="tab-pane fade" id="batches" role="tabpanel">
                    <div class="alert bg-light border-0 text-dark rounded-3 small d-flex align-items-start mb-4 p-3 shadow-none">
                        <i class="bi bi-info-circle-fill text-primary fs-5 me-2.5 mt-0.5"></i>
                        <div>
                            <span class="fw-bold text-dark">Mekanisme FIFO Aktif:</span> Urutan antrean di bawah tersusun berdasarkan waktu masuk barang terlama. Transaksi kasir atau pengeluaran sistem otomatis memotong kuota sisa dari batch **paling atas** terlebih dahulu untuk menjaga akurasi HPP modal Anda.
                        </div>
                    </div>
                    
                    <div class="table-responsive rounded-3 border border-gray-200">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light border-bottom text-secondary small fw-bold text-uppercase">
                                <tr>
                                    <th class="ps-3" style="width: 220px;">Urutan Antrean</th>
                                    <th>Kode Batch</th>
                                    <th>Tanggal Masuk</th>
                                    <th class="text-end">Jumlah Awal</th>
                                    <th class="text-end bg-light-subtle">Sisa Stok Sekarang</th>
                                    <th class="text-end">Harga Modal (HPP)</th>
                                    <th class="text-end pe-3">Subtotal Nilai Sisa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($product->stockEntries as $index => $batch)
                                    <tr>
                                        <td class="ps-3">
                                            @if($index === 0)
                                                <span class="badge bg-danger text-white rounded-2 px-2.5 py-1.5 small fw-bold">
                                                    <i class="bi bi-fire me-1"></i> Utama (FIFO)
                                                </span>
                                            @else
                                                <span class="badge bg-white text-secondary border rounded-2 px-2.5 py-1.5 small fw-medium">
                                                    Antrean Ke-{{ $index + 1 }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="font-monospace fw-bold text-primary">BATCH-#{{ $batch->id }}</td>
                                        <td class="text-secondary small">{{ $batch->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="text-end text-muted font-monospace">{{ number_format($batch->qty_received, 0, ',', '.') }} Pcs</td>
                                        <td class="text-end fw-extrabold text-dark bg-light-subtle font-monospace fs-6">{{ number_format($batch->qty_remaining, 0, ',', '.') }} Pcs</td>
                                        <td class="text-end font-monospace text-secondary">Rp{{ number_format($batch->purchase_price, 0, ',', '.') }}</td>
                                        <td class="text-end fw-bold text-success pe-3 font-monospace">
                                            Rp{{ number_format($batch->qty_remaining * $batch->purchase_price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="bi bi-inbox fs-2 text-secondary opacity-50 d-block mb-2"></i>
                                            Tidak ada entri batch dengan sisa kuota aktif untuk produk ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 3: KARTU STOK (LEDGER MUTASI) -->
                <div class="tab-pane fade" id="ledger" role="tabpanel">
                    <div class="table-responsive rounded-3 border border-gray-200">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light border-bottom text-secondary small fw-bold text-uppercase">
                                <tr>
                                    <th class="ps-3">Waktu Mutasi</th>
                                    <th>Ref ID / Nota</th>
                                    <th>Kategori</th>
                                    <th>Batch Terikat</th>
                                    <th class="text-end" style="width: 140px;">Masuk (+)</th>
                                    <th class="text-end" style="width: 140px;">Keluar (-)</th>
                                    <th class="text-end pe-3" style="width: 180px;">HPP Unit Terikat</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($product->stockMutations as $mutation)
                                    <tr>
                                        <td class="ps-3 text-secondary small">{{ $mutation->created_at->format('d/m/Y H:i') }}</td>
                                        <td class="font-monospace text-dark fw-bold text-uppercase" style="letter-spacing: -0.3px;">
                                            {{ $mutation->reference_id ?? '-' }}
                                        </td>
                                        <td>
                                            @if($mutation->category == 'purchase')
                                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1">Pembelian</span>
                                            @elseif($mutation->category == 'sale')
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1">Penjualan</span>
                                            @elseif($mutation->category == 'damaged')
                                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1">Barang Rusak</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1">{{ ucfirst($mutation->category) }}</span>
                                            @endif
                                        </td>
                                        <td class="font-monospace text-secondary small">BATCH-#{{ $mutation->stock_entry_id }}</td>
                                        
                                        <!-- Perubahan Desain Kolom Masuk/Keluar dengan Background Lembut -->
                                        <td class="text-end fw-bold font-monospace text-success {{ $mutation->type === 'in' ? 'bg-success-subtle opacity-75' : '' }}">
                                            {{ $mutation->type === 'in' ? '+' . number_format($mutation->qty, 0, ',', '.') . ' Pcs' : '-' }}
                                        </td>
                                        <td class="text-end fw-bold font-monospace text-danger {{ $mutation->type === 'out' ? 'bg-danger-subtle opacity-75' : '' }}">
                                            {{ $mutation->type === 'out' ? '-' . number_format($mutation->qty, 0, ',', '.') . ' Pcs' : '-' }}
                                        </td>
                                        
                                        <td class="text-end text-secondary font-monospace small pe-3">
                                            Rp{{ number_format($mutation->price, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-5">
                                            <i class="bi bi-folder-x fs-2 text-secondary opacity-50 d-block mb-2"></i>
                                            Belum ada riwayat mutasi perpindahan barang masuk/keluar.
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

<!-- Custom CSS Utility to Match Professional SaaS Frameworks -->
<style>
    .fw-extrabold { font-weight: 800; }
    .border-dashed { border-style: dashed !important; }
    .bg-light-subtle { background-color: #f9fafb; }
    
    /* Perbaikan Visual Tab Kustom */
    .navbar-custom-tabs .nav-link {
        border-bottom: 3px solid transparent !important;
        transition: all 0.2s ease;
    }
    .navbar-custom-tabs .nav-link:hover {
        color: var(--bs-dark) !important;
        background-color: #f8f9fa;
    }
    .navbar-custom-tabs .nav-link.active {
        color: var(--bs-primary) !important;
        border-bottom-color: var(--bs-primary) !important;
        background: transparent !important;
    }

    .btn-white {
        background-color: #fff;
        color: #374151;
    }
    .btn-white:hover {
        background-color: #f9fafb;
        color: #111827;
    }
    .btn-hover-zoom {
        transition: transform 0.15s ease;
    }
    .btn-hover-zoom:hover {
        transform: translateY(-1px);
    }
</style>
@endsection