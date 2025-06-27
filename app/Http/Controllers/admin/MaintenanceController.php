<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Requests\StoreMaintenanceRequest;
use App\Http\Requests\UpdateMaintenanceRequest;
use App\Models\Maintenance;
use Exception;

class MaintenanceController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $maintenances = Maintenance::query();
            return DataTables::of($maintenances)
                ->addColumn('options', function ($item) {
                    return view('admin.maintenances.template.options', compact('item'))->render();
                })
                ->rawColumns(['options'])
                ->make(true);
        }

        return view('admin.maintenances.index');
    }

    public function create()
    {
        return view('admin.maintenances.create');
    }

    public function store(StoreMaintenanceRequest $request)
    {
        try {
            $data = $request->validated();

            $exists = Maintenance::where(function ($query) use ($data) {
                $query->whereBetween('start_date', [$data['start_date'], $data['end_date']])
                      ->orWhereBetween('end_date', [$data['start_date'], $data['end_date']])
                      ->orWhere(function ($query) use ($data) {
                          $query->where('start_date', '<=', $data['start_date'])
                                ->where('end_date', '>=', $data['end_date']);
                      });
            })->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Las fechas se solapan con un mantenimiento existente.',
                ], 422);
            }

            Maintenance::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Mantenimiento registrado con éxito.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al registrar el mantenimiento.',
                'error' => $e->getMessage(), // 👈 Agregado para depurar
            ], 500);
        }
    }

    public function edit($id)
    {
        $maintenance = Maintenance::findOrFail($id);
        return view('admin.maintenances.edit', compact('maintenance'));
    }

    public function update(UpdateMaintenanceRequest $request, $id)
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $maintenance = Maintenance::findOrFail($id);

            $exists = Maintenance::where('id', '!=', $id)
                ->where(function ($query) use ($data) {
                    $query->whereBetween('start_date', [$data['start_date'], $data['end_date']])
                        ->orWhereBetween('end_date', [$data['start_date'], $data['end_date']])
                        ->orWhere(function ($query) use ($data) {
                            $query->where('start_date', '<=', $data['start_date'])
                                    ->where('end_date', '>=', $data['end_date']);
                        });
                })->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Las fechas se solapan con otro mantenimiento.',
                ], 422);
            }

            $maintenance->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Mantenimiento actualizado con éxito.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el mantenimiento.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $maintenance = Maintenance::findOrFail($id);
            $maintenance->delete();

            return response()->json([
                'success' => true,
                'message' => 'Mantenimiento eliminado con éxito.',
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el mantenimiento.',
            ], 500);
        }
    }
}
