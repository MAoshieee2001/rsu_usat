<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractType extends Model
{
    use HasFactory;

    protected $table = 'contract_types';
    protected $guarded = [];

    public function employees()
{
    return $this->belongsToMany(Employee::class, 'employee_contracts', 'contract_id', 'employee_id')
                ->withTimestamps();
}

}
