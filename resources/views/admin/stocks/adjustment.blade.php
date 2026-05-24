@extends('layouts.app')

@section('content')
<!-- Google Fonts & Icons -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

<style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
        background-color: #f8f9fa;
    }
    .form-wrapper {
        background: #ffffff;
        border: 1px solid #eef0f3;
        border-radius: 16px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.02);
        padding: 32px;
    }
    .form-control:focus, .form-select:focus {
        border-color: #ef4444;
        box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
    }
</style>

<div class="container py-4">
    <div class="mb-3">
        <a href="{{ route('admin.stocks.index') }}" class="text-decoration-none text-secondary fw-semibold small">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Log Stok
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-wrapper">
                <div class="mb-4">
                    <h3 class="fw-bold text-dark mb-1">Form Stok Keluar & Penyesuaian</h3>
                    <p class="text-muted small">Kurangi kuota barang untuk keperluan opname, retur, atau kerusakan. Sistem akan memotong urutan batch terlama secara otomatis.</p>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger border-0 rounded-3 shadow-sm mb-4">
                        <ul class="mb-0 small fw-medium">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            <form action="{{ route('admin.stocks.store_adjustment') }}" method="POST">
                @csrf

                <!-- Pilih Produk -->
                <div class="mb-3">
                    <label for="product_id" class="form-label fw-semibold text-secondary small">Pilih Produk *</label>
                    <select name="product_id" id="product_id" required class="form-select rounded-3 p-2.5 fs-6">
                        <option value="" disabled selected>-- Pilih Produk --</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                {{ $product->name }} (Total Stok: {{ $product->totalStock() }} pcs)
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Pilih Batch Spesifik (Dinamis via JavaScript) -->
                <div class="mb-3">
                    <label for="stock_entry_id" class="form-label fw-semibold text-secondary small">Metode Pemotongan / Batch Asal</label>
                    <select name="stock_entry_id" id="stock_entry_id" class="form-select rounded-3 p-2.5 fs-6 bg-light" disabled>
                        <option value="">-- Pilih Produk Terlebih Dahulu --</option>
                    </select>
                    <div class="form-text text-muted" style="font-size: 11px;">Biarkan opsi ini bernilai <strong>"Otomatis Sesuai Antrean FIFO"</strong> jika pengeluaran didasarkan pada selisih opname berkala.</div>
                </div>

                <div class="row g-3 mb-3">
                    <!-- Kategori Pengeluaran -->
                    <div class="col-md-6">
                        <label for="category" class="form-label fw-semibold text-secondary small">Alasan / Kategori *</label>
                        <select name="category" id="category" required class="form-select rounded-3 p-2.5 fs-6">
                            <option value="" disabled selected>-- Pilih Alasan --</option>
                            <option value="adjustment" {{ old('category') == 'adjustment' ? 'selected' : '' }}>Adjustment (Selisih Opname)</option>
                            <option value="damaged" {{ old('category') == 'damaged' ? 'selected' : '' }}>Damaged (Barang Rusak / Expired)</option>
                            <option value="return" {{ old('category') == 'return' ? 'selected' : '' }}>Return (Retur ke Supplier)</option>
                        </select>
                    </div>

                    <!-- Qty Keluar -->
                    <div class="col-md-6">
                        <label for="qty" class="form-label fw-semibold text-secondary small">Jumlah Unit Keluar *</label>
                        <div class="input-group">
                            <input type="number" name="qty" id="qty" value="{{ old('qty') }}" min="1" required placeholder="0" class="form-control rounded-start-3 p-2.5 fs-6">
                            <span class="input-group-text bg-light text-muted border-start-0 rounded-end-3">Pcs</span>
                        </div>
                    </div>
                </div>

                <!-- Nomor Dokumen -->
                <div class="mb-4">
                    <label for="reference_id" class="form-label fw-semibold text-secondary small">No. Referensi / Berita Acara (Opsional)</label>
                    <input type="text" name="reference_id" id="reference_id" value="{{ old('reference_id') }}" placeholder="Contoh: BA-OPNAME-001" class="form-control rounded-3 p-2.5 fs-6 font-monospace">
                </div>

                <button type="submit" class="btn btn-danger w-100 py-2.5 rounded-3 fw-semibold shadow-sm">
                    <i class="bi bi-check-circle me-2"></i>Eksekusi Potong Stok
                </button>
            </form>

            <!-- Tambahkan script AJAX di bagian bawah view -->
            <script>
            document.getElementById('product_id').addEventListener('change', function() {
                const productId = this.value;
                const batchSelect = document.getElementById('stock_entry_id');
                
                // Reset dropdown batch
                batchSelect.innerHTML = '<option value="">-- Memuat daftar batch aktif... --</option>';
                batchSelect.disabled = true;
                batchSelect.classList.add('bg-light');

                if (!productId) return;

                fetch(`{{ url('/admin/stocks/active-batches/') }}/${productId}`)
                    .then(response => response.json())
                    .then(data => {
                        batchSelect.innerHTML = '<option value="">⚡ Otomatis Sesuai Antrean FIFO (Rekomendasi) ⚡</option>';
                        
                        if (data.length === 0) {
                            batchSelect.innerHTML = '<option value="">-- Tidak ada batch aktif (Stok Kosong) --</option>';
                        } else {
                            data.forEach(batch => {
                                // Format tanggal sederhana untuk visual user
                                const date = new Date(batch.created_at).toLocaleDateString('id-ID', {
                                    day: '2-digit', month: '2-digit', year: 'numeric'
                                });
                                
                                const option = document.createElement('option');
                                option.value = batch.id;
                                option.text = `BATCH-#${batch.id} [Sisa: ${batch.qty_remaining} Pcs] - Masuk Tgl: ${date} (Modal: Rp${parseInt(batch.purchase_price).toLocaleString('id-ID')})`;
                                batchSelect.appendChild(option);
                            });
                            batchSelect.disabled = false;
                            batchSelect.classList.remove('bg-light');
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching batches:', error);
                        batchSelect.innerHTML = '<option value="">-- Gagal memuat data batch --</option>';
                    });
            });
            </script>
            </div>
        </div>
    </div>
</div>
@endsection