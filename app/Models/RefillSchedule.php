<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RefillSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'tank_id',
        'scheduled_date',
        'target_volume',
        'status',
        'notes',
    ];

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }
}
