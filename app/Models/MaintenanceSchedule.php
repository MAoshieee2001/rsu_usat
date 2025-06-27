<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
{
    protected $fillable = [
        'maintenance_id',
        'vehicle_id',
        'employee_id',
        'day',
        'type',
        'start_time',
        'end_time',
    ];

    public function maintenance()
    {
        return $this->belongsTo(Maintenance::class);
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}