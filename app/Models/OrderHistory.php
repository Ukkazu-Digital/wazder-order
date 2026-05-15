<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderHistory extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'order_histories';
    protected $fillable = ['order_id', 'status', 'note'];
}
