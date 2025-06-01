<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Brandmodel;
use App\Models\Color;
use App\Models\Vehicle;
use App\Models\VehicleImage;
use App\Models\VehicleType;
use DB;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vehicles = Vehicle::select(
            'vehicles.id',
            'vehicles.code',
            'm.name as model_name',
            't.name as type_name',
            'b.name as brand_name',
            'c.name as color_name',
            'vehicles.name',
            'vehicles.plate',
            'vehicles.year',
            'vehicles.load_capacity',
            'vehicles.capacity',
            'vehicles.fuel_capacity',
            'vehicles.compaction_capacity',
            'vehicles.description',
            'vehicles.status',
            'vehicles.created_at',
            'vehicles.updated_at'
        )
            ->join('brandmodels as m', 'vehicles.model_id', '=', 'm.id')
            ->join('brands as b', 'vehicles.brand_id', '=', 'b.id')
            ->join('vehiclestypes as t', 'vehicles.type_id', '=', 't.id')
            ->join('colors as c', 'vehicles.color_id', '=', 'c.id')
            ->get();

        if ($request->ajax()) {
            return DataTables::of($vehicles)
                ->addColumn('options', function ($vehicle) {
                    return '
                        <button class="btn btn-sm btn-warning btnEditar" id="' . $vehicle->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-secondary btnFoto" id="' . $vehicle->id . '">
                            <i class="fas fa-image"></i>
                        </button>
                        <form action="' . route('admin.vehicles.destroy', $vehicle->id) . '" method="POST" class="d-inline frmDelete">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    ';
                })
                ->addColumn('image', function ($vehicle) {
                    // Buscar la primera imagen vinculada al vehículo
                    $image = VehicleImage::where('vehicle_id', $vehicle->id)
                        ->orderByDesc('id') // o 'created_at' si prefieres
                        ->first();

                    $path = $image && $image->image ? asset($image->image) : asset('storage/brands/empty.png');

                    return '<img src="' . $path . '" alt="Imagen del vehículo" style="width: 70px; height: 50px; object-fit: contain;">';
                })
                ->editColumn('status', function ($vehicle) {
                    switch ($vehicle->status) {
                        case 0:
                            return '<span class="badge bg-danger">Inactivo</span>';
                        case 1:
                            return '<span class="badge bg-success text-dark">Activo</span>';
                        default:
                            return '<span class="badge bg-warning">En mantemiento</span>';
                    }
                })
                ->editColumn('load_capacity', function ($vehicle) {
                    return $vehicle->capacity . ' KG';
                })
                ->editColumn('fuel_capacity', function ($vehicle) {
                    return $vehicle->fuel_capacity . ' L';
                })
                ->rawColumns(['image', 'options', 'status'])
                ->make(true);
        } else {
            return view('admin.vehicles.index', compact('vehicles'));
        }

    }

    public function getImages($vehicleId)
    {
        try {
            // Obtener las imágenes del vehículo
            $images = VehicleImage::where('vehicle_id', $vehicleId)->get();

            // Formatear las rutas de las imágenes con asset()
            $images->transform(function ($image) {
                // Si existe ruta, construimos URL pública
                $image->image = $image->image
                    ? asset($image->image)
                    : asset('storage/brands/empty.png'); // imagen por defecto

                return $image;
            });

            return response()->json($images);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las imágenes: ' . $e->getMessage()
            ], 500);
        }
    }
    public function create()
    {
        try {
            $brand = Brand::pluck('name', 'id');
            $model = Brandmodel::pluck('name', 'id');
            $type = VehicleType::pluck('name', 'id');
            $color = Color::pluck('name', 'id');
            return view('admin.vehicles.create', compact('brand', 'model', 'type', 'color'));
        } catch (\Exception $e) {
            return redirect()->route('admin.vehicles.index')->with('error', 'Ocurrió un error al intentar crear un nuevo vehiculo.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        try {

            $request->validate([
                'capacity' => 'required|numeric|max:3',
            ]);

            // Crear el vehículo
            $vehicle = Vehicle::create([
                'name' => $request->name,
                'code' => $request->code,
                'plate' => $request->plate,
                'year' => $request->year,
                'load_capacity' => $request->load_capacity,
                'capacity' => $request->capacity,
                'fuel_capacity' => $request->fuel_capacity,
                'compaction_capacity' => $request->compaction_capacity,
                'description' => $request->description,
                'status' => $request->status,
                'model_id' => $request->model_id,
                'brand_id' => $request->brand_id,
                'type_id' => $request->type_id,
                'color_id' => $request->color_id,
            ]);

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('vehicleimages', 'public');

                VehicleImage::create([
                    'vehicle_id' => $vehicle->id,
                    'image' => 'storage/' . $path,
                    'profile' => $request->profile ?? 'main', // default a 'main'
                ]);
            }


            return response()->json([
                'success' => true,
                'message' => 'Vehículo registrado con éxito.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el vehículo: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getModelsByBrand(string $brand_id)
    {
        $models = Brandmodel::where('brand_id', $brand_id)->pluck('name', 'id');
        return response()->json($models);
    }

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
            $vehicle = Vehicle::with('images')->findOrFail($id);
            $brand = Brand::pluck('name', 'id');
            $model = Brandmodel::pluck('name', 'id');
            $type = VehicleType::pluck('name', 'id');
            $color = Color::pluck('name', 'id');
            return view('admin.vehicles.edit', compact('vehicle', 'brand', 'model', 'type', 'color'));
        } catch (\Exception $e) {
            return redirect()->route('admin.vehicles.index')->with('error', 'Ocurrió un error al intentar editarel vehiculo.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {

            $vehicle = Vehicle::findOrFail($id);

            $request->validate([
                'capacity' => 'required|numeric|max:3',
            ]);

            $vehicle->update([
                'name' => $request->name,
                'code' => $request->code,
                'plate' => $request->plate,
                'year' => $request->year,
                'load_capacity' => $request->load_capacity,
                'capacity' => $request->capacity,
                'fuel_capacity' => $request->fuel_capacity,
                'compaction_capacity' => $request->compaction_capacity,
                'description' => $request->description,
                'status' => $request->status,
                'model_id' => $request->model_id,
                'brand_id' => $request->brand_id,
                'type_id' => $request->type_id,
                'color_id' => $request->color_id,
            ]);

            // Si se sube una nueva imagen, la almacenamos
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('vehicleimages', 'public');

                VehicleImage::create([
                    'vehicle_id' => $vehicle->id,
                    'image' => 'storage/' . $path,
                    'profile' => $request->profile ?? 'main', // por defecto "main"
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Vehículo actualizado correctamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el vehículo: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $vehicle = Vehicle::findOrFail($id);

            // Borrar imágenes físicas del storage y registros de la BD
            foreach ($vehicle->images as $image) {
                if ($image->image && Storage::exists('public/' . $image->image)) {
                    Storage::delete('public/' . $image->image);
                }

                $image->delete(); // Elimina el registro de la tabla vehicleimages
            }

            // Finalmente, elimina el vehículo
            $vehicle->delete();

            return response()->json(['success' => true, 'message' => 'Vehículo eliminado correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el vehiculo: ' . $e->getMessage()], 500);
        }
    }
}
