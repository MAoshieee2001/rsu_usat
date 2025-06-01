<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Brandmodel;
use App\Models\Brand;
use Yajra\DataTables\Facades\DataTables;

class BrandModelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // $models = Brandmodel::all();
        $models = Brandmodel::select(
            'brandmodels.id',
            'brandmodels.name as model_name',
            'code',
            'b.name as brand_name',
            'brandmodels.description',
            'brandmodels.created_at',
            'brandmodels.updated_at'
        )
            ->join('brands as b', 'brandmodels.brand_id', '=', 'b.id')
            ->get();

        if ($request->ajax()) {
            return DataTables::of($models)
                ->addColumn('options', function ($model) {
                    return '
                        <button class="btn btn-sm btn-warning btnEditar" id="' . $model->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="' . route('admin.models.destroy', $model->id) . '" method="POST" class="d-inline frmDelete">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    ';
                })
                ->rawColumns(['logo', 'options'])
                ->make(true);
        } else {
            return view('admin.models.index', compact('models'));
        }

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        try {
            $brand = Brand::pluck('name', 'id');
            return view('admin.models.create', ['brand' => $brand]);
        } catch (\Exception $e) {
            return redirect()->route('admin.models.index')->with('error', 'Ocurrió un error al intentar crear un nuevo modelo.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:brandmodels,name',
            'code' => 'required',
            'brand_id' => 'required',
        ]);

        try {
            Brandmodel::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Marca registrada con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return redirect()->route('admin.models.index')->with('error', 'Error al crear el modelo: ' . $e->getMessage());
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
            $model = Brandmodel::findOrFail($id); // Tiene el campo 'brand' que es el ID
            $brand = Brand::pluck('name', 'id');

            return view('admin.models.edit', compact('model', 'brand'));
        } catch (\Exception $e) {
            return redirect()->route('admin.models.index')
                ->with('error', 'Ocurrió un error al intentar editar el modelo.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|unique:brandmodels,name,' . $id,
            'code' => 'required',
            'brand_id' => 'required|exists:brands,id',
        ]);

        try {
            $model = Brandmodel::findOrFail($id);
            $model->update($request->only(['name', 'code', 'brand_id', 'description']));

            return response()->json([
                'success' => true,
                'message' => 'Modelo actualizado con éxito.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el modelo: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $brandmodel = Brandmodel::findOrFail($id);
            $brandmodel->delete();
            return redirect()->route('admin.models.index')->with('success', 'Modelo eliminada con éxito.');
        } catch (\Exception $e) {
            return redirect()->route('admin.models.index')->with('error', 'Ocurrió un error al intentar eliminar el modelo.' . $e->getMessage());
        }
    }
}
