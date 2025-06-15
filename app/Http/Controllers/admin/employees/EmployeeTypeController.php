<?php

namespace App\Http\Controllers\admin\employees;

use App\Http\Controllers\Controller;
use App\Models\EmployeeType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class EmployeeTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $types = EmployeeType::all();
        if ($request->ajax()) {
            return DataTables::of($types)
                ->addColumn('options', function ($type) {
                    return '
                        <button class="btn btn-sm btn-warning btnEditar" id="' . $type->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="' . route('admin.employeetypes.destroy', $type->id) . '" method="POST" class="d-inline frmDelete">
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
            return view('admin.employeetypes.index', compact('types'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('admin.employeetypes.create');
        } catch (\Exception $e) {
            return redirect()->route('admin.employeetypes.index')->with('error', 'Ocurrió un error al intentar crear una nueva función.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            EmployeeType::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Función registrado con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la función: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $type = EmployeeType::findOrFail($id); // Tiene el campo 'brand' que es el ID

            return view('admin.employeetypes.edit', compact('type'));
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la función: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $type = EmployeeType::findOrFail($id);
            $type->update($request->only(['name', 'description']));

            return response()->json([
                'success' => true,
                'message' => 'Función actualizado con éxito.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la función: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $type = EmployeeType::findOrFail($id);
            $type->delete();
            return response()->json([
                'success' => true,
                'message' => 'Función eliminado con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la función: ' . $e->getMessage(),
            ], 500);
        }
    }
}
