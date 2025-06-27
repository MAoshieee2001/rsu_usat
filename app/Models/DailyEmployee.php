<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyEmployee extends Model
{
    use HasFactory;

    protected $table = 'daily_employees'; // nombre real de la tabla

    protected $fillable = [
        'daily_programming_id',
        'employee_id',
        'employeetypes_id',
    ];

    public function dailyProgramming()
    {
        return $this->belongsTo(DailyProgramming::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function employeeType()
    {
        return $this->belongsTo(EmployeeType::class);
    }
}