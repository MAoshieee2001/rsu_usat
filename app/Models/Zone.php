<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;
    
    protected $table = 'zones';
    protected $guarded = [];

    public function district()
{
    return $this->belongsTo(District::class);
}

public function coordinates()
{
    return $this->hasMany(ZoneCoord::class); // o el nombre de tu modelo de coordenadas
}
}
