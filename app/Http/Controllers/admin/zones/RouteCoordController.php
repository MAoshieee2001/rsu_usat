<?php

namespace App\Http\Controllers\admin\zones;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\RouteCoord;
use Illuminate\Http\Request;

class RouteCoordController extends Controller
{
    public function edit(string $id)
    {
        $route = Route::find($id);
        $vertice = RouteCoord::select('latitude as lat', 'longitude as lng')->where('route_id', $id)->get();
        $lastcoord = RouteCoord::select('latitude as lat', 'longitude as lng')->where('route_id', $id)->latest()->first();
        $zonePolygonCoords = $route->zone->coordinates->map(function ($coord) {
            return ['lat' => (float) $coord->latitude, 'lng' => (float) $coord->longitude];
        })->values();
        return View('admin.routescoords.create', compact('vertice', 'lastcoord', 'route','zonePolygonCoords'));
    }

    public function store(Request $request)
    {
        try {
            RouteCoord::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Ruta registrada con éxito.',
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
            $coord = RouteCoord::findOrFail($id);
            $coord->delete();
            return response()->json([
                'success' => true,
                'message' => 'Ruta eliminada con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Hubo un error en al eliminar ruta.' . $e->getMessage(),
            ], 500);
        }
    }
}
