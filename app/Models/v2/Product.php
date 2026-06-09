<?php

namespace App\Models\v2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Import trait
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use SoftDeletes; // 2. Gunakan trait

    protected $fillable = [
            'name',
            'selling_price',
            'category',
            'is_consumable',
            'image',
            'unit'
        ];

    /**
     * Relasi ke Batch Stok Masuk
     */
    public function stockEntries(): HasMany
    {
        return $this->hasMany(StockEntry::class);
    }

    /**
     * Relasi ke Log Mutasi Stok
     */
    public function stockMutations(): HasMany
    {
        return $this->hasMany(StockMutation::class);
    }

    public function totalStock(): int
    {
        return $this->stockEntries()->where('qty_remaining', '>', 0)->sum('qty_remaining');
    }
}