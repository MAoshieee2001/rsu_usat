<?php

namespace App\Http\Controllers\admin\programing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DailyProgrammingController extends Controller
{
    public function store(Request $request)
    {
        $programmingId = $request->input('programming_id');
        $fecha = $request->input('date_start');

        // Validar que la programación existe
        $programming = Programming::findOrFail($programmingId);

        // Obtener IDs de vehículos ya asignados ese día
        $vehiculosUsados = DailyProgramming::whereDate('date_start', $fecha)
            ->pluck('vehicle_id')
            ->toArray();

        // Obtener un vehículo aleatorio que no esté usado
        $vehiculoDisponible = Vehicle::whereNotIn('id', $vehiculosUsados)->inRandomOrder()->first();

        if (!$vehiculoDisponible) {
            return response()->json([
                'message' => 'No hay vehículos disponibles para esa fecha.'
            ], 400);
        }

        // Crear la programación diaria
        $daily = DailyProgramming::create([
            'programming_id' => $programmingId,
            'vehicle_id' => $vehiculoDisponible->id,
            'date_start' => $fecha,
        ]);

        return response()->json([
            'message' => 'Programación diaria creada con éxito.',
            'data' => $daily
        ]);
    }
}