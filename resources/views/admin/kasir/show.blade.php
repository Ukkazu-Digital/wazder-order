@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('admin.orders.index') }}" class="btn btn-light me-3">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1 class="fw-bold mb-0">Detail Pesanan Kasir</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-4">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Kode Order</p>
                            <h5 class="fw-bold mb-3">{{ $order->order_code }}</h5>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Status Saat Ini</p>
                            <span class="badge bg-{{ $order->status == 'pending' ? 'secondary' : ($order->status == 'paid' ? 'info' : ($order->status == 'shipped' ? 'warning' : 'success')) }} p-2">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Customer</p>
                            <p class="fw-semibold">{{ $order->customer->customers_name ?? '-' }}</p>
                            <small class="text-muted">{{ $order->customer->address ?? '-' }}</small>
                        </div>
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Total Harga</p>
                            <p class="fw-semibold text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Tanggal Order</p>
                            <p class="fw-semibold">{{ $order->created_at->format('d M Y, H:i') }} WIB</p>
                        </div>
                        @if($order->kurir)
                        <div class="col-md-6">
                            <p class="text-muted mb-1">Kurir</p>
                            <p class="fw-semibold">{{ $order->kurir->name }} ({{ $order->kurir->plate_number }})</p>
                        </div>
                        @endif
                    </div>

                    @if($order->notes)
                    <div class="mb-3">
                        <p class="text-muted mb-1">Catatan</p>
                        <p class="fw-semibold">{{ $order->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Update Status Form -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light p-3">
                    <h5 class="mb-0 fw-semibold">Update Status Pesanan</h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('kasir.updateStatus', $order) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="status" class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required onchange="handleStatusChange()">
                                <option value="">Pilih Status</option>
                                <option value="pending" @if($order->status=='pending') selected @endif>Pending</option>
                                <option value="paid" @if($order->status=='paid') selected @endif>Lunas</option>
                                <option value="shipped" @if($order->status=='shipped') selected @endif>Dikirim</option>
                                <option value="completed" @if($order->status=='completed') selected @endif>Selesai</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Kurir Selection (hanya tampil saat status shipped) -->
                        <div class="mb-3" id="kurirField" style="display: none;">
                            <label for="kurir_id" class="form-label fw-semibold">Pilih Kurir <span class="text-danger">*</span></label>
                            <select name="kurir_id" id="kurir_id" class="form-select @error('kurir_id') is-invalid @enderror">
                                <option value="">-- Pilih Kurir --</option>
                                @foreach($kurirs as $kurir)
                                <option value="{{ $kurir->id }}" @if($order->kurir_id == $kurir->id) selected @endif>
                                    {{ $kurir->name }} - {{ $kurir->plate_number }} ({{ $kurir->phone }})
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hanya kurir yang aktif yang ditampilkan</small>
                            @error('kurir_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle me-2"></i> Update Status
                            </button>
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary px-4">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Order History -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-light p-3">
                    <h5 class="mb-0 fw-semibold">Riwayat Pesanan</h5>
                </div>
                <div class="card-body p-3">
                    @forelse($order->histories as $history)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="badge bg-{{ $history->status == 'pending' ? 'secondary' : ($history->status == 'paid' ? 'info' : ($history->status == 'shipped' ? 'warning' : 'success')) }}">
                                    {{ ucfirst($history->status) }}
                                </span>
                                <p class="small text-muted mt-2 mb-1">{{ $history->created_at->format('d M Y, H:i') }}</p>
                                <p class="small mb-0">{{ $history->note }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted small">Belum ada riwayat</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Produk -->
    <div class="card shadow-sm border-0 mt-4">
        <div class="card-header bg-light p-3">
            <h5 class="mb-0 fw-semibold">Detail Produk</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th>Qty</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->details as $detail)
                        <tr>
                            <td class="fw-semibold">{{ $detail->product->name ?? '-' }}</td>
                            <td>{{ $detail->qty }}</td>
                            <td>Rp {{ number_format($detail->buy_price, 0, ',', '.') }}</td>
                            <td class="fw-semibold">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                        <tr class="table-light">
                            <td colspan="3" class="text-end fw-bold">Total</td>
                            <td class="fw-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function handleStatusChange() {
        const status = document.getElementById('status').value;
        const kurirField = document.getElementById('kurirField');
        const kurirSelect = document.getElementById('kurir_id');

        if (status === 'shipped') {
            kurirField.style.display = 'block';
            kurirSelect.required = true;
        } else {
            kurirField.style.display = 'none';
            kurirSelect.required = false;
        }
    }

    // Check on page load
    window.addEventListener('load', handleStatusChange);
</script>
@endsection
