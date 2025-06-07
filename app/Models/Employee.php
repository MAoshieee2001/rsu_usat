<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $table = 'employees';

    public function getFullnamesAttribute()
    {
        return $this->names . ' ' . $this->lastnames;
    }

    public function contractTypes()
    {
        return $this->belongsToMany(ContractType::class, 'employee_contracts', 'employee_id', 'contract_id')
            ->withTimestamps();
    }
}