<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    protected $table = 'attendances';

    protected $guarded = [];

    protected $dates = ['date_joined', 'date_end'];
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
