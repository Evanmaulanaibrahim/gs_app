<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RFIDTag extends Model
{
    protected $table = 'rfid_tags';

    protected $fillable = ['uid', 'name', 'position'];
    
    public function accessLogs()
    {
        return $this->hasMany(AccessLog::class);
    }
    
    public function hasAccess()
    {
        return in_array($this->position, ['manager', 'director']);
    }
}