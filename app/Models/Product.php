<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'products';
    protected $fillable = ['name','price','stock','image','is_active'];

    public function orderDetails()
    {
        // Satu produk bisa muncul di banyak detail pesanan
        return $this->hasMany(OrderDetail::class, 'product_id');
    }
}
