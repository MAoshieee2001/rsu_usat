<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'vehicles';
    protected $guarded = [];

    public function images()
    {
        return $this->hasMany(VehicleImage::class);
    }

    // app/Models/Vehicle.php
    public function dailyProgrammings()
    {
        return $this->hasMany(DailyProgramming::class, 'vehicle_id');
    }
}
