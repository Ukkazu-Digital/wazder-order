<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use HasFactory;
    use SoftDeletes;
    use SoftDeletes;

    protected $table = 'contacts';
    protected $fillable = ['wa_id','name','last_message_at','user_id','customer_id'];

    public static function getCustomerbyWaId($waId)
    {
        return Contact::leftJoin('customers','contacts.customer_id','=','customers.id')->where('wa_id', $waId)->first();
    }
}
