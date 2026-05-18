<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'customers';
    protected $fillable = ['customers_name','customers_wa_id','address','latitude','longtitude','updated_by'];

    public function orders()
    {
        // Satu pelanggan bisa punya banyak pesanan
        return $this->hasMany(Order::class, 'customer_id');
    }
}
