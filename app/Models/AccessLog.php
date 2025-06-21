<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AccessLog extends Model
{
    protected $fillable = ['rfid_tag_id', 'access_granted', 'device_id'];
    
    public function rfidTag()
    {
        return $this->belongsTo(RFIDTag::class);
    }
}