<?php

namespace App\Http\Controllers\admin\zones;

use App\Http\Controllers\Controller;
use App\Models\Route;
use App\Models\Zone;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class RouteController extends Controller
{

    public function index(Request $request)
    {
        $routes = Route::select(
            'routes.id',
            'zone.name as zone_name',
            'routes.name',
            'routes.description',
            'routes.created_at',
            'routes.updated_at'
        )
            ->join('zones as zone', 'routes.zone_id', '=', 'zone.id')
            ->get();

        if ($request->ajax()) {
            return DataTables::of($routes)
                ->addColumn('options', function ($route) {
                    return '
                        <button class="btn btn-sm btn-warning btnEditar" id="' . $route->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="' . route('admin.routes.destroy', $route->id) . '" method="POST" class="d-inline frmDelete">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    ';
                })
                ->rawColumns(['options'])
                ->make(true);
        } else {
            return view('admin.routes.index', compact('routes'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $zones = Zone::pluck('name', 'id');
            return view('admin.routes.create', compact('zones'));
        } catch (\Exception $e) {
            return redirect()->route('admin.routes.index')->with('error', 'Ocurrió un error al intentar crear una nueva ruta.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Route::create($request->all());
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

    public function edit(string $id)
    {
        try {
            $route = Route::findOrFail($id);
            $zones = Zone::pluck('name', 'id');
            return view('admin.routes.edit', compact('route', 'zones'));
        } catch (\Exception $e) {
            return redirect()->route('admin.routes.index')->with('error', 'Ocurrió un error al intentar actualizar la ruta.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $route = Route::findOrFail($id);
            $route->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Ruta actualizado con éxito.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la ruta: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
