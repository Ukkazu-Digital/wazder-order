<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tank extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'capacity',
        'current_volume',
        'type',
        'location',
        'latitude',
        'longitude',
        'status',
        'customer_id',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function logs()
    {
        return $this->hasMany(TankLog::class);
    }

    public function refillSchedules()
    {
        return $this->hasMany(RefillSchedule::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getVolumePercentageAttribute()
    {
        if ($this->capacity > 0) {
            return round(($this->current_volume / $this->capacity) * 100, 2);
        }
        return 0;
    }

    public function getIsLowStockAttribute()
    {
        return $this->volume_percentage < 30;
    }
}
