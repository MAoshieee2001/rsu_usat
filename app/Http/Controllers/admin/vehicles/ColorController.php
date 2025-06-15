<?php

namespace App\Http\Controllers\admin\vehicles;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ColorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $colors = Color::all();
        if ($request->ajax()) {
            return DataTables::of($colors)
                ->addColumn('preview', function ($color) {
                    return '<div style="width: 30px; height: 30px; background-color: ' . $color->code . '; border: 1px solid #000;"></div>';
                })
                ->addColumn('options', function ($color) {
                    return '
                        <button class="btn btn-sm btn-warning btnEditar" id="' . $color->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="' . route('admin.colors.destroy', $color->id) . '" method="POST" class="d-inline frmDelete">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    ';
                })
                ->rawColumns(['preview', 'options'])
                ->make(true);
        } else {
            return view('admin.colors.index', compact('colors'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('admin.colors.create');
        } catch (\Exception $e) {
            return redirect()->route('admin.colors.index')->with('error', 'Ocurrió un error al intentar crear un nuevo color.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:colors,name',
                'code' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            ]);

            Color::create([
                'name' => $request->name,
                'code' => $request->code,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Color registrado con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al intentar registrar el color. ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $color = Color::findOrFail($id);
            return view('admin.colors.show', compact('color'));
        } catch (\Exception $e) {
            return redirect()->route('admin.colors.index')->with('error', 'Ocurrió un error al intentar mostrar el color.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $color = Color::findOrFail($id);
            return view('admin.colors.edit', compact('color'));
        } catch (\Exception $e) {
            return redirect()->route('admin.colors.index')->with('error', 'Ocurrió un error al intentar editar el color.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:colors,name,' . $id,
                'code' => 'required|string|regex:/^#[0-9A-Fa-f]{6}$/',
            ]);

            $color = Color::findOrFail($id);

            $color->update([
                'name' => $request->name,
                'code' => $request->code,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Color actualizado con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al intentar actualizar el color. ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $color = Color::findOrFail($id);
            $color->delete();

            return response()->json([
                'success' => true,
                'message' => 'Color eliminado con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al intentar eliminar el color. ' . $e->getMessage(),
            ], 500);
        }
    }
}
