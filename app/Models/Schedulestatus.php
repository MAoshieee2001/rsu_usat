<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedulestatus extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        // otros campos si tienes
    ];
}