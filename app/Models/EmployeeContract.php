<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeContract extends Model
{
    use HasFactory;

    protected $table = 'employee_contracts';

    protected $guarded = [];

    public function contractType()
    {
        return $this->belongsTo(ContractType::class, 'contract_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

}
