<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    protected $fillable = [
        'contact_wa_id', 'direction', 'type', 'body', 'latitude', 'longitude', 'address', 'button_id', 'timestamp_unix', 'created_at', 'msg_id', 'status', 'updated_at'
    ];
    public $timestamps = false;
}
