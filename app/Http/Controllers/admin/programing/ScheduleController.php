<?php

namespace App\Http\Controllers\admin\programing;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ScheduleController extends Controller
{

    public function index(Request $request)
    {
        $schedules = Schedule::all();
        if ($request->ajax()) {
            return DataTables::of($schedules)
                ->addColumn('options', function ($schedule) {
                    return '
                        <button class="btn btn-sm btn-warning btnEditar" id="' . $schedule->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="' . route('admin.schedules.destroy', $schedule->id) . '" method="POST" class="d-inline frmDelete">
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
            return view('admin.schedules.index', compact('schedules'));
        }
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            return view('admin.schedules.create');
        } catch (\Exception $e) {
            return redirect()->route('admin.schedules.index')->with('error', 'Ocurrió un error al intentar crear un nuevo horario.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->merge([
                'date_joined' => substr($request->input('date_joined'), 0, 5),
                'date_end' => substr($request->input('date_end'), 0, 5),
            ]);
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date_joined' => 'required|date_format:H:i',
                'date_end' => 'required|date_format:H:i|after:date_joined',
            ]);
            $existe = Schedule::where('date_joined', $request->date_joined)
                ->where('date_end', $request->date_end)
                ->exists();

            if ($existe) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un horario con esas horas.',
                ], 500);
            }
            Schedule::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Horario registrado con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrio un error al registrar horario' . $e->getMessage(),
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
            $schedule = Schedule::findOrFail($id);
            return view('admin.schedules.edit', compact('schedule'));
        } catch (\Exception $e) {
            return redirect()->route('admin.schedules.index')->with('error', 'Ocurrió un error al intentar actualizar un nuevo horario.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $request->merge([
                'date_joined' => substr($request->input('date_joined'), 0, 5),
                'date_end' => substr($request->input('date_end'), 0, 5),
            ]);
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'date_joined' => 'required|date_format:H:i',
                'date_end' => 'required|date_format:H:i|after:date_joined',
            ]);

            // Buscamos el registro actual
            $schedule = Schedule::findOrFail($id);

            // Verificar si ya existe otro horario con las mismas horas (excluyendo este mismo)
            $existe = Schedule::where('date_joined', $validated['date_joined'])
                ->where('date_end', $validated['date_end'])
                ->where('id', '!=', $id)
                ->exists();

            if ($existe) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un horario con esas horas.',
                ], 500);
            }

            // Actualizamos el horario
            $schedule->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Horario actualizado con éxito.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al actualizar el horario: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $schedule = Schedule::findOrFail($id);
            $schedule->delete();
            return response()->json([
                'success' => true,
                'message' => 'Horario eliminado con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrio un error al eliminar Horario.',
            ], 500);
        }
    }
}
