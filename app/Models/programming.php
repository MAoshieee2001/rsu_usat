<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Programming extends Model
{
    use HasFactory;

    protected $table = 'programming'; // nombre exacto de tu tabla

    protected $guarded = [];

    protected $casts = [
        'dias_semana' => 'array',
    ];

    // Relaciones
    public function zona()
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function horario()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }

    public function vehiculo()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function detalles()
    {
        return $this->hasMany(DetailsPrograming::class, 'programming_id');
    }
}
