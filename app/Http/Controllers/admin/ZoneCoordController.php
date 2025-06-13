<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ZoneCoord;
use Illuminate\Http\Request;

class ZoneCoordController extends Controller
{
    public function edit(string $id)
    {
        $vertice = ZoneCoord::select('latitude as lat', 'longitude as lng')->where('zone_id', $id)->get();
        $lastcoord = ZoneCoord::select('latitude as lat', 'longitude as lng')->where('zone_id', $id)->latest()->first();
        return View('admin.zonescoords.create', compact('vertice','lastcoord'));
    }
}
