<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LinkOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'link_order';
    protected $fillable = ['wa_id','kode_pesanan','expired_at'];
}
