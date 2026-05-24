<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Import trait
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMutation extends Model
{
    use SoftDeletes; // 2. Gunakan trait jika mutasi bisa dibatalkan

    protected $fillable = [
        'product_id',
        'stock_entry_id',
        'type',          
        'category',      
        'qty',
        'price',         
        'reference_id',  
    ];

    /**
     * Relasi ke Produk
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    /**
     * Relasi ke Batch Asal (Gunakan withTrashed)
     */
    public function stockEntry(): BelongsTo
    {
        return $this->belongsTo(StockEntry::class)->withTrashed();
    }
}