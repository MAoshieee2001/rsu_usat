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
use Illuminate\Validation\Rule;


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
                    $image = VehicleImage::where('vehicle_id', $vehicle->id)
                        ->where('profile', 1)
                        ->first();
                    if (!$image) {
                        // Si no hay imagen de perfil, tomar la última
                        $image = VehicleImage::where('vehicle_id', $vehicle->id)
                            ->orderByDesc('id')
                            ->first();
                    }
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
                'plate' => [
                    'required',
                    'regex:/^[A-Z0-9]{6}$|^[A-Z0-9]{2}-[A-Z0-9]{4}$|^[A-Z0-9]{3}-[A-Z0-9]{3}$/i',
                    'unique:vehicles,plate'
                ],
                'year' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
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
                'plate' => [
                    'required',
                    'regex:/^[A-Z0-9]{6}$|^[A-Z0-9]{2}-[A-Z0-9]{4}$|^[A-Z0-9]{3}-[A-Z0-9]{3}$/i',
                    Rule::unique('vehicles', 'plate')->ignore($vehicle->id)
                ],
                'year' => 'required|digits:4|integer|min:1900|max:' . date('Y'),
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
                    'profile' => $request->profile ?? '0', // por defecto "0"
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Vehículo actualizado correctamente.',
            ], 200);
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

            return response()->json([
                'success' => true,
                'message' => 'Vehículo eliminado correctamente.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error al eliminar el vehiculo: ' . $e->getMessage()], 500);
        }
    }

    /** Establecer una imagen como foto de perfil*/
    public function setProfileImage(Request $request, $imageId)
    {
        try {
            // Buscar la imagen por ID
            $image = VehicleImage::findOrFail($imageId);
            $vehicleId = $request->vehicle_id;
            // Remover el perfil de todas las imágenes del vehículo
            // Establecer is_profile a false para todas las imágenes del vehículo
            VehicleImage::where('vehicle_id', $vehicleId)
                ->update(['profile' => false]);

            // Establecer la nueva imagen como perfil
            $image->update(['profile' => true]);
            return response()->json([
                'success' => true,
                'message' => 'Foto de perfil actualizada correctamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al establecer la foto de perfil: ' . $e->getMessage()
            ], 500);
        }
    }

    /** Eliminar una imagen*/
    public function deleteImage($imageId)
    {
        try {
            $image = VehicleImage::findOrFail($imageId);
            // Verificar que no sea la única imagen del vehículo
            $totalImages = VehicleImage::where('vehicle_id', $image->vehicle_id)->count();
            if ($totalImages <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la única imagen del vehículo.'
                ], 400);
            }
            // Si es la imagen de perfil, asignar otra como perfil
            if ($image->is_profile) {
                $newProfileImage = VehicleImage::where('vehicle_id', $image->vehicle_id)
                    ->where('id', '!=', $image->id)
                    ->first();
                if ($newProfileImage) {
                    $newProfileImage->update(['is_profile' => true]);
                }
            }
            // Eliminar el archivo físico
            // Extraer la ruta sin 'storage/' para usar con Storage
            $imagePath = str_replace('storage/', '', $image->image);
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            // Eliminar el registro de la base de datos
            $image->delete();
            return response()->json([
                'success' => true,
                'message' => 'Imagen eliminada correctamente.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la imagen: ' . $e->getMessage()
            ], 500);
        }
    }

}
