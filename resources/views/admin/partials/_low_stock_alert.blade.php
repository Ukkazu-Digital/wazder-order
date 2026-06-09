
    @if ($lowStockCount > 0)
    <div class="mb-4">
        <div class="d-flex align-items-center mb-3">
            <div class="bg-danger opacity-10 rounded-circle me-2" style="width: 8px; height: 8px;"></div>
            <h5 class="fw-bold text-uppercase tracking-wider text-danger small mb-0">ALERT STOK RENDAH (< 30%)</h5>
        </div>
        
        <div class="row g-3">
            @foreach ($lowStockTanks as $tank)
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm bg-white rounded-3 p-3 h-100 border-danger border-start" style="border-width: 4px !important;">
                    <div class="card-body p-2 flex-grow-1">
                        <small class="text-uppercase text-danger fw-semibold small">{{ $tank->name }}</small>
                        <h3 class="fw-bold text-danger mt-2 mb-0">{{ number_format($tank->volume_percentage, 2) }}%</h3>
                        <p class="text-muted text-xs mb-0">Sisa {{ number_format($tank->current_volume, 2) }} {{ $tank->type }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
