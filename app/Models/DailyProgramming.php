<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyProgramming extends Model
{
    use HasFactory;
    protected $table = 'daily_programming';

    protected $fillable = [
        'programming_id',
        'vehicle_id',
        'date_start',
        // agrega aquí otros campos si los usas
    ];
}
