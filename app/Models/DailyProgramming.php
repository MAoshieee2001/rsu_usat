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
    
    public function programming()
    {
        return $this->belongsTo(Programming::class, 'programming_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
    public function employees()
    {
        return $this->hasMany(DailyEmployee::class);
    }
    
}
