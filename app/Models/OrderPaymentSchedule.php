<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderPaymentSchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'due_date',
        'amount_due',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
