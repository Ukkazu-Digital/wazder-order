<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \App\Traits\BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'payment_term_id',
        'order_code','customer_id','kurir_id','total_price','status','source','notes','payment_method','order_type','tank_id','volume'
    ];

    /**
     * Relasi ke Customer (Satu Order dimiliki oleh satu Customer)
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    /**
     * Relasi ke Kurir (Satu Order dikirim oleh satu Kurir)
     */
    public function kurir()
    {
        return $this->belongsTo(Kurir::class, 'kurir_id');
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

    public function termOfPayment()
    {
        return $this->hasOne(TermOfPayment::class, 'order_id');
    }

    public function paymentSchedules()
    {
        return $this->hasMany(OrderPaymentSchedule::class, 'order_id');
    }

    // Order dengan payment schedule jatuh tempo
    public function overdueSchedules()
    {
        return $this->paymentSchedules()->where('status', 'pending')->where('due_date', '<', now());
    }

    public function scopeHasOverdue($query)
    {
        return $query->whereHas('paymentSchedules', function($q) {
            $q->where('status', 'pending')->where('due_date', '<', now());
        });
    }
}
