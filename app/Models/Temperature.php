<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Temperature extends Model
{
    use HasFactory; // Tambahkan ini

    protected $fillable = [
        'temperature',
        'humidity',
        'air',
        'status'
    ];
}
