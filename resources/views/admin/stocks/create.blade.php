@extends('layouts.app')

@section('content')
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
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .fade-in-alert {
        animation: fadeIn 0.3s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="container py-4">
    <div class="mb-3">
        <a href="{{ route('admin.stocks.index') }}" class="text-decoration-none text-primary fw-semibold small">
            <i class="bi bi-arrow-left me-1"></i>Kembali ke Log Stok
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="form-wrapper">
                <div class="mb-4">
                    <h3 class="fw-bold text-dark mb-1">Penerimaan Stok Gudang</h3>
                    <p class="text-muted small">Input barang masuk untuk menerbitkan batch antrean baru pada perhitungan FIFO Anda.</p>
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

                <form action="{{ route('admin.stocks.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label for="product_id" class="form-label fw-semibold text-secondary small">Pilih Produk Master *</label>
                        <select name="product_id" id="product_id" required class="form-select rounded-3 p-2.5 fs-6" onchange="checkPriceMismatch()">
                            <option value="" disabled selected>-- Pilih Produk --</option>
                            @foreach($products as $product)
                                @php
                                    // Ambil harga beli dari batch paling terakhir di database
                                    $lastBatchPrice = $product->stockEntries->first()->purchase_price ?? ($product->buy_price ?? 0);
                                @endphp
                                <option value="{{ $product->id }}" 
                                        data-buy="{{ $lastBatchPrice }}" 
                                        data-selling="{{ $product->selling_price ?? 0 }}"
                                        {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }} (Stok Saat Ini: {{ $product->totalStock() }} pcs)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="qty_received" class="form-label fw-semibold text-secondary small">Jumlah Unit Masuk *</label>
                            <div class="input-group">
                                <input type="number" name="qty_received" id="qty_received" value="{{ old('qty_received') }}" min="1" required placeholder="0" class="form-control rounded-start-3 p-2.5 fs-6">
                                <span class="input-group-text bg-light text-muted border-start-0 rounded-end-3">Pcs</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="purchase_price" class="form-label fw-semibold text-secondary small">Harga Modal Per Unit *</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted border-end-0 rounded-start-3">Rp</span>
                                <input type="number" name="purchase_price" id="purchase_price" value="{{ old('purchase_price') }}" min="0" required placeholder="Modal per unit" class="form-control rounded-end-3 p-2.5 font-monospace fs-6 border-start-0" oninput="checkPriceMismatch()">
                            </div>
                        </div>
                    </div>

                    <div id="price-alert-container" class="mb-3 fade-in-alert" style="display: none;">
                        <div class="alert alert-warning rounded-4 border-0 shadow-sm p-3 d-flex align-items-start" role="alert">
                            <i class="bi bi-exclamation-triangle-fill text-warning me-3 fs-4 mt-1"></i>
                            <div class="w-100">
                                <h6 class="fw-bold text-dark mb-1">Perbedaan Harga Modal Terdeteksi!</h6>
                                <p class="text-muted small mb-2">
                                    Harga modal batch sebelumnya: <strong class="text-dark" id="old-buy-label">Rp0</strong>.<br>
                                    Harga modal masuk baru: <strong class="text-primary" id="new-buy-label">Rp0</strong>.
                                </p>
                                
                                <hr class="my-2 text-warning-subtle">
                                
                                <div class="form-check form-switch pt-1">
                                    <input class="form-check-input" type="checkbox" role="switch" name="update_selling_price" id="updateSellingPriceSwitch" onchange="toggleSellingPriceInput()">
                                    <label class="form-check-label fw-semibold text-dark small" for="updateSellingPriceSwitch">
                                        Ya, saya ingin menyesuaikan harga penjualan untuk produk ini
                                    </label>
                                </div>

                                <div class="mt-3 bg-white p-3 rounded-3 border" id="new-selling-price-field" style="display: none;">
                                    <label class="form-label fw-bold text-secondary small mb-1">Harga Jual Baru *</label>
                                    <div class="input-group mb-1">
                                        <span class="input-group-text bg-light fw-bold text-secondary border-end-0">Rp</span>
                                        <input type="number" name="new_selling_price" id="new_selling_price" class="form-control font-monospace border-start-0" placeholder="Masukkan harga jual baru" value="{{ old('new_selling_price') }}">
                                    </div>
                                    <div class="form-text text-muted small" style="font-size: 11px;">
                                        Harga jual saat ini: <span class="fw-bold text-dark" id="current-selling-label">Rp0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="reference_id" class="form-label fw-semibold text-secondary small">No. Invoice / Nota Supplier (Opsional)</label>
                        <input type="text" name="reference_id" id="reference_id" value="{{ old('reference_id') }}" placeholder="Contoh: INV-2026/05/12" class="form-control rounded-3 p-2.5 fs-6 font-monospace">
                        <div class="form-text text-muted" style="font-size: 11px;">Membantu pencarian dan pelacakan audit jika terjadi selisih stok dengan supplier.</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-3 fw-semibold shadow-sm">
                        <i class="bi bi-file-earmark-plus me-2"></i>Amankan Stok Masuk
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function checkPriceMismatch() {
        const productSelect = document.getElementById('product_id');
        const purchasePriceInput = document.getElementById('purchase_price');
        const alertContainer = document.getElementById('price-alert-container');
        
        if (!productSelect.value || !purchasePriceInput.value) {
            alertContainer.style.display = 'none';
            resetSellingPriceSwitch();
            return;
        }

        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const oldBuyPrice = parseFloat(selectedOption.getAttribute('data-buy')) || 0;
        const currentSellingPrice = parseFloat(selectedOption.getAttribute('data-selling')) || 0;
        const newBuyPrice = parseFloat(purchasePriceInput.value) || 0;

        // Tampilkan alert jika harga baru diisi dan nominalnya berbeda (baik naik ataupun turun)
        if (newBuyPrice > 0 && newBuyPrice !== oldBuyPrice) {
            document.getElementById('old-buy-label').innerText = formatRupiah(oldBuyPrice);
            document.getElementById('new-buy-label').innerText = formatRupiah(newBuyPrice);
            document.getElementById('current-selling-label').innerText = formatRupiah(currentSellingPrice);
            
            alertContainer.style.display = 'block';
        } else {
            alertContainer.style.display = 'none';
            resetSellingPriceSwitch();
        }
    }

    function toggleSellingPriceInput() {
        const isChecked = document.getElementById('updateSellingPriceSwitch').checked;
        const sellingPriceField = document.getElementById('new-selling-price-field');
        const sellingPriceInput = document.getElementById('new_selling_price');
        
        const productSelect = document.getElementById('product_id');
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const currentSellingPrice = selectedOption ? (selectedOption.getAttribute('data-selling') || '') : '';

        if (isChecked) {
            sellingPriceField.style.display = 'block';
            sellingPriceInput.required = true;
            if (!sellingPriceInput.value) {
                sellingPriceInput.value = currentSellingPrice;
            }
        } else {
            sellingPriceField.style.display = 'none';
            sellingPriceInput.required = false;
            sellingPriceInput.value = '';
        }
    }

    function resetSellingPriceSwitch() {
        // Hanya reset jika switch sedang aktif
        if(document.getElementById('updateSellingPriceSwitch').checked) {
            document.getElementById('updateSellingPriceSwitch').checked = false;
            toggleSellingPriceInput();
        }
    }

    function formatRupiah(angka) {
        return 'Rp' + new Intl.NumberFormat('id-ID').format(angka);
    }

    document.addEventListener('DOMContentLoaded', function() {
        if(document.getElementById('product_id').value && document.getElementById('purchase_price').value) {
            checkPriceMismatch();
            // Jika validasi error kembali, pastikan status switch dan input harga terjaga
            if("{{ old('update_selling_price') }}" === 'on') {
                document.getElementById('updateSellingPriceSwitch').checked = true;
                toggleSellingPriceInput();
            }
        }
    });
</script>
@endsection