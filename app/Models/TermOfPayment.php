<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermOfPayment extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function histories()
    {
        return $this->hasMany(TopPaymentHistorie::class, 'term_of_payment_id');
    }
}
