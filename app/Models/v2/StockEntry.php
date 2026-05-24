<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Import trait
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockEntry extends Model
{
    use SoftDeletes; // 2. Gunakan trait

    protected $fillable = [
        'product_id',
        'qty_received',
        'qty_remaining',
        'purchase_price',
    ];

    /**
     * Relasi balik ke Produk (Gunakan withTrashed agar relasi tidak putus jika produknya di-softdelete)
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function stockMutations(): HasMany
    {
        return $this->hasMany(StockMutation::class);
    }

    /**
     * Scope FIFO otomatis mengabaikan data yang sudah di-softdelete karena bawaan Laravel
     */
    public function scopeAvailableFifo($query, $productId)
    {
        return $query->where('product_id', $productId)
                     ->where('qty_remaining', '>', 0)
                     ->orderBy('created_at', 'asc');
    }
}