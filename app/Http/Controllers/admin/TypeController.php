<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $types = VehicleType::all();
        if ($request->ajax()) {
            return DataTables::of($types)
                ->addColumn('options', function ($type) {
                    return '
                        <button class="btn btn-sm btn-warning btnEditar" id="' . $type->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="' . route('admin.types.destroy', $type->id) . '" method="POST" class="d-inline frmDelete">
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
            return view('admin.types.index', compact('types'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('admin.types.create');
        } catch (\Exception $e) {
            return redirect()->route('admin.models.index')->with('error', 'Ocurrió un error al intentar crear un nuevo tipo.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            VehicleType::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Tipo vehiculo registrado con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return redirect()->route('admin.types.index')->with('error', 'Error al crear el tipo vehiculo: ' . $e->getMessage());
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
            $type = VehicleType::findOrFail($id); // Tiene el campo 'brand' que es el ID

            return view('admin.types.edit', compact('type'));
        } catch (\Exception $e) {
            return redirect()->route('admin.types.index')
                ->with('error', 'Ocurrió un error al intentar editar el tipo vehiculo.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $type = VehicleType::findOrFail($id);
            $type->update($request->only(['name', 'description']));

            return response()->json([
                'success' => true,
                'message' => 'Tipo vehiculo actualizado con éxito.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el Tipo vehiculo: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $type = VehicleType::findOrFail($id);
            $type->delete();
            return redirect()->route('admin.types.index')->with('success', 'Tipo vehiculo eliminada con éxito.');
        } catch (\Exception $e) {
            return redirect()->route('admin.types.index')->with('error', 'Ocurrió un error al intentar eliminar el tipo vehiculo.' . $e->getMessage());
        }
    }
}
