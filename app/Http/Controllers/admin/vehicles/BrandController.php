<?php

namespace App\Http\Controllers\admin\vehicles;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $brands = Brand::all();
        if ($request->ajax()) {
            return DataTables::of($brands)
                ->addColumn('logo', function ($brand) {
                    $logoPath = $brand->logo == '' ? 'storage/brands/empty.png' : $brand->logo;
                    return '<img src="' . asset($logoPath) . '" width="50px" height="50px">';
                })

                ->addColumn('options', function ($brand) {
                    return '
                        <button class="btn btn-sm btn-warning btnEditar" id="' . $brand->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="' . route('admin.brands.destroy', $brand->id) . '" method="POST" class="d-inline frmDelete">
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
            return view('admin.brands.index', compact('brands'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('admin.brands.create');
        } catch (\Exception $e) {
            return redirect()->route('admin.brands.index')->with('error', 'Ocurrió un error al intentar crear una nueva marca.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:brands,name',
                'description' => 'nullable|string|max:500',
            ]);

            $logo = "";

            if ($request->hasFile('logo')) {
                $image = $request->file('logo')->store('brands', 'public');
                $logo = 'storage/' . $image; // Ruta accesible desde el navegador
            }

            // return $logo;

            $brand = Brand::create([
                'name' => $request->name,
                'description' => $request->description,
                'logo' => $logo,
            ]);

            // return redirect()->route('admin.brands.index')->with('success', 'Marca registrada con éxito.');
            return response()->json([
                'success' => true,
                'message' => 'Marca registrada con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al intentar registrar la marca. ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $brand = Brand::findOrFail($id);
            return view('admin.brands.show', compact('brand'));
        } catch (\Exception $e) {
            return redirect()->route('admin.brands.index')->with('error', 'Ocurrió un error al intentar mostrar la marca.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $brand = Brand::findOrFail($id);
            return view('admin.brands.edit', compact('brand'));
        } catch (\Exception $e) {
            return redirect()->route('admin.brands.index')->with('error', 'Ocurrió un error al intentar editar la marca.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:brands,name,' . $id,
                'description' => 'nullable|string|max:500',
            ]);
            $brand = Brand::findOrFail($id);

            if ($request->hasFile('logo')) {
                // Eliminar la imagen anterior si existe
                if ($brand->logo && Storage::exists(str_replace('storage/', 'public/', $brand->logo))) {
                    Storage::delete(str_replace('storage/', 'public/', $brand->logo));
                }

                // Guardar la nueva imagen
                $image = $request->file('logo')->store('brands', 'public');
                $brand->logo = 'storage/' . $image; // Ruta accesible desde el navegador
            }

            // Actualizar los datos de la marca
            $brand->update([
                'name' => $request->name,
                'description' => $request->description,
                'logo' => $brand->logo,
            ]);

            // $brand->update($request->all());
            // return redirect()->route('admin.brands.index')->with('success', 'Marca actualizada con éxito.');
            return response()->json([
                'success' => true,
                'message' => 'Marca actualizada con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al intentar actualizar la marca. ' . $e->getMessage(),
            ], 500);
        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $brand = Brand::findOrFail($id);
            $brand->delete();
            return response()->json([
                'success' => true,
                'message' => 'Marca eliminada con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al intentar eliminar la marca. ' . $e->getMessage(),
            ], 500);
        }

    }
}
