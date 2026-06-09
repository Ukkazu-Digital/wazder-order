<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'name',
        'price',
        'stock',
        'image',
        'is_active',
        'is_consumable',
        'category',
    ];

    public function orderDetails()
    {
        // Satu produk bisa muncul di banyak detail pesanan
        return $this->hasMany(OrderDetail::class, 'product_id');
    }
}
