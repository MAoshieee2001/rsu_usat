<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scheduleshifts extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'description',
        // otros campos si tienes
    ];
}