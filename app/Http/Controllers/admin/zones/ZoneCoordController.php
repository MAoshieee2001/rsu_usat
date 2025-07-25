<?php

namespace App\Http\Controllers\admin\zones;

use App\Http\Controllers\Controller;
use App\Models\Zone;
use App\Models\ZoneCoord;
use Illuminate\Http\Request;

class ZoneCoordController extends Controller
{
    public function edit(string $id)
    {
        $zone = Zone::find($id);
        $vertice = ZoneCoord::select('latitude as lat', 'longitude as lng')->where('zone_id', $id)->get();
        $lastcoord = ZoneCoord::select('latitude as lat', 'longitude as lng')->where('zone_id', $id)->latest()->first();
        return View('admin.zonescoords.create', compact('vertice', 'lastcoord', 'zone'));
    }

    public function store(Request $request)
    {
        try {
            ZoneCoord::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Coordenada registrada con éxito.',
            ], 200);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Hubo un error en el registro.' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        try {
            $coord = ZoneCoord::findOrFail($id);
            $coord->delete();
            return response()->json([
                'success' => true,
                'message' => 'Coordenada eliminada con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error en al eliminar coordenada.' . $e->getMessage(),
            ], 500);
        }
    }
}
