<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'orders';
    protected $fillable = ['order_code','customer_id','total_price','status','notes'];

    /**
     * Relasi ke Customer (Satu Order dimiliki oleh satu Customer)
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Relasi ke OrderDetail (Satu Order memiliki banyak Detail/Produk)
     * Ini yang digunakan di fungsi sendWhatsAppNotification
     */
    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }

    /**
     * Relasi ke ShippingAddress (Satu Order memiliki satu alamat pengiriman)
     */
    public function shippingAddress()
    {
        return $this->hasOne(ShippingAddress::class, 'order_id');
    }

    public function histories()
    {
        return $this->hasMany(OrderHistory::class, 'order_id');
    }
}
