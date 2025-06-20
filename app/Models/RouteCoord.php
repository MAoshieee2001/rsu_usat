<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RouteCoord extends Model
{
    use HasFactory;

    protected $table = 'routes_coords';
    protected $guarded = [];


    public function route()
    {
        return $this->belongsTo(Route::class);
    }
}
