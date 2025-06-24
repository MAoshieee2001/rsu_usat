<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailsPrograming extends Model
{
    use HasFactory;

    protected $table = 'details_programings'; // asegúrate de que esta sea la tabla real

    protected $guarded = [];

    // Relación con empleado
    public function empleado()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    // Relación inversa con programación
    public function programacion()
    {
        return $this->belongsTo(Programming::class, 'programming_id');
    }
}
