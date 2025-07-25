<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use HasFactory;

    protected $table = 'routes';
    protected $guarded = [];


    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
    public function coords()
    {
        return $this->hasMany(RouteCoord::class);
    }

}
