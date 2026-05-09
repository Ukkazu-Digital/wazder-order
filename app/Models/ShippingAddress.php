<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShippingAddress extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'shipping_address';
    protected $fillable = ['order_id','address','updated_by','long','lat'];
}
