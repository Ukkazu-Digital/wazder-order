<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TankLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'tank_id',
        'water_level',
        'notes',
    ];

    public function tank()
    {
        return $this->belongsTo(Tank::class);
    }
}
