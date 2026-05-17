<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Kurir extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'kurirs';
    protected $fillable = ['name', 'phone', 'plate_number', 'photo', 'status'];

    /**
     * Relasi ke Order (Satu Kurir bisa mengirim banyak Order)
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'kurir_id');
    }

    /**
     * Get photo URL
     */
    public function getPhotoUrlAttribute()
    {
        return $this->photo ? asset('storage/kurir/' . $this->photo) : null;
    }
}
