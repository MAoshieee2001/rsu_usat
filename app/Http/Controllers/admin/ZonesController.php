<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Zone;
use App\Models\ZoneCoord;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ZonesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $zones = Zone::select(
            'd.name as district_name',
            'zones.id',
            'zones.name',
            'zones.area',
            'zones.description',
            'zones.created_at',
            'zones.updated_at'
        )
            ->join('districts as d', 'zones.district_id', '=', 'd.id')
            ->get();
        if ($request->ajax()) {
            return DataTables::of($zones)
                ->addColumn('options', function ($zone) {
                    return '
                        <a class="btn btn-sm btn-secondary" href="' . route('admin.zones.show', $zone->id) . '">
                            <i class="fas fa-paper-plane"></i>
                        </a>                   
                            <button class="btn btn-sm btn-warning btnEditar" id="' . $zone->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="' . route('admin.zones.destroy', $zone->id) . '" method="POST" class="d-inline frmDelete">
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
            return view('admin.zones.index', compact('zones'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $districts = District::pluck('name', 'id');
            return view('admin.zones.create', compact('districts'));
        } catch (\Exception $e) {
            return redirect()->route('admin.zones.index')->with('error', 'Ocurrió un error al intentar crear una nueva zona.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            Zone::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Zona registrado con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return redirect()->route('admin.zones.index')->with('error', 'Error al crear la zona: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        try {
            $zone = Zone::find($id);
            $zone = Zone::with('district')->find($id);
            $coords = ZoneCoord::where('zone_id', $id)->get();
            $vertice = ZoneCoord::select('latitude as lat', 'longitude as lng')->where('zone_id', $id)->get();
            $lastcoord = ZoneCoord::select('latitude as lat', 'longitude as lng')->where('zone_id', $id)->latest()->first();
            // return view('admin.zones.show', compact('zone'));

            if ($request->ajax()) {
                return DataTables::of($coords)
                    ->addColumn('delete', function ($coords) {
                        return '
   
                        <form action="' . route('admin.zonescoords.destroy', $coords->id) . '" method="POST" class="d-inline frmDelete">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    ';
                    })
                    ->rawColumns(['delete'])
                    ->make(true);
            } else {
                return view('admin.zones.show', compact('zone', 'vertice', 'lastcoord'));
            }


        } catch (\Exception $e) {
            return redirect()->route('admin.zones.index')
                ->with('error', 'Ocurrió un error al intentar registar perimetros en  la zona.' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            $districts = District::pluck('name', 'id');
            $zones = Zone::findOrFail($id); // Tiene el campo 'brand' que es el ID
            return view('admin.zones.edit', compact('zones', 'districts'));
        } catch (\Exception $e) {
            return redirect()->route('admin.zones.index')
                ->with('error', 'Ocurrió un error al intentar editar la zona.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Buscar la zona por ID
            $zone = Zone::findOrFail($id);
            // Actualizar con los datos del request
            $zone->update($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Zona registrado con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return redirect()->route('admin.zones.index')->with('error', 'Error al crear la zona: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $zone = Zone::findOrFail($id);
            $zone->delete();
            return response()->json([
                'success' => true,
                'message' => 'Zona registrado con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrio un error al eliminar zona.',
            ], 500);
        }
    }
}
