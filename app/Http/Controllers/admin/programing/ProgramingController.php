<?php
namespace App\Http\Controllers\admin\Programing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Programming;
use App\Models\Zone;
use App\Models\Schedule;
use App\Models\Modality;
use App\Models\Employee;
use App\Models\Vacation;
use App\Models\EmployeeType;
use App\Models\Vehicle;
use App\Models\DailyProgramming;
use Carbon\Carbon;
use App\Models\DailyEmployee;




class ProgramingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $programaciones = Programming::with(['zona', 'horario', 'modalidad'])->get();

            $data = $programaciones->map(function ($p) {
                return [
                    'id'             => $p->id, // necesario para los botones
                    'zone_name'      => $p->zona?->name ?? 'Sin zona',
                    'schedule_name'  => $p->horario?->name ?? 'Sin horario',
                    'modality_name'  => $p->modalidad?->name ?? 'Sin modalidad',
                    'date_start'     => $p->date_start,
                    'date_end'       => $p->date_end,
                ];
            });

            return datatables()->of($data)
                ->addColumn('options', function ($model) {
                    return '
                        <button class="btn btn-sm btn-warning btnEditar" id="' . $model['id'] . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="' . route('admin.programming.destroy', $model['id']) . '" method="POST" class="d-inline frmDelete">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    ';
                })
                ->rawColumns(['options']) 
                ->make(true);
        }

        return view('admin.programming.index');
    }

    public function create()
    {
        $zonas = Zone::pluck('name', 'id');
        $horarios = Schedule::pluck('name', 'id');
        $modality = Modality::pluck('name', 'id');
        $empleados = Employee::whereHas('contracts', function ($q) {
            $q->where('status', 'Activo');
        })->selectRaw("CONCAT(names, ' ', lastnames) as fullnames, id")
            ->pluck('fullnames', 'id');

        return view('admin.programming.create', compact('zonas', 'horarios', 'modality'));
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'zone_id' => 'required|exists:zones,id',
                'schedule_id' => 'required|exists:schedules,id',
                'modality_id' => 'required|exists:modalities,id',
                'date_start' => 'required|date',
                'date_end' => 'required|date|after_or_equal:date_start',
            ]);

            $fechasRango = collect();
            $start = Carbon::parse($validated['date_start']);
            $end = Carbon::parse($validated['date_end']);

            while ($start->lte($end)) {
                $fechasRango->push($start->copy()->format('Y-m-d'));
                $start->addDay();
            }

            // Verificar disponibilidad de vehículo para las fechas
            $vehiculoDisponible = Vehicle::whereDoesntHave('dailyProgrammings', function ($q) use ($fechasRango) {
                $q->whereIn('date_start', $fechasRango);
            })->first();

            if (!$vehiculoDisponible) {
                DB::rollBack();
                return response()->json([
                    'message' => 'No hay vehículos disponibles para el rango de fechas seleccionado.',
                ], 422);
            }

            // Obtener IDs de tipos
            $tipoConductor = DB::table('employeetypes')->where('name', 'Conductor')->value('id');
            $tipoAsistente = DB::table('employeetypes')->where('name', 'Asistente')->value('id');

            // Buscar empleados disponibles aleatoriamente
            $conductor = Employee::where('status', 1)->where('type_id', $tipoConductor)->inRandomOrder()->first();
            $asistente = Employee::where('status', 1)->where('type_id', $tipoAsistente)->inRandomOrder()->first();

            if (!$conductor || !$asistente) {
                DB::rollBack();
                return response()->json([
                    'message' => 'No hay suficientes empleados activos disponibles (conductor o asistente).',
                ], 422);
            }

            // Crear la programación principal
            $programming = Programming::create([
                'zone_id' => $validated['zone_id'],
                'schedule_id' => $validated['schedule_id'],
                'modality_id' => $validated['modality_id'],
                'date_start' => $validated['date_start'],
                'date_end' => $validated['date_end'],
            ]);

            foreach ($fechasRango as $fecha) {
                $dailyProgramming = DailyProgramming::create([
                    'programming_id' => $programming->id,
                    'vehicle_id' => $vehiculoDisponible->id,
                    'date_start' => $fecha,
                ]);

                // Asignar conductor y asistente
                DailyEmployee::create([
                    'daily_programming_id' => $dailyProgramming->id,
                    'employee_id' => $conductor->id,
                    'employee_type_id' => $tipoConductor,
                ]);

                DailyEmployee::create([
                    'daily_programming_id' => $dailyProgramming->id,
                    'employee_id' => $asistente->id,
                    'employee_type_id' => $tipoAsistente,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Programación registrada con éxito.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar la programación: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function edit($id)
    {
        $programacion = Programming::findOrFail($id);

        $zonas = Zone::pluck('name', 'id');
        $horarios = Schedule::pluck('name', 'id');
        $modality = Modality::pluck('name', 'id');
        $empleados = Employee::whereHas('contracts', function ($q) {
            $q->where('status', 'Activo');
        })->selectRaw("CONCAT(names, ' ', lastnames) as fullnames, id")
            ->pluck('fullnames', 'id');

        return view('admin.programming.edit', compact('programacion', 'zonas', 'horarios', 'modality', 'empleados'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $programacion = Programming::findOrFail($id);
            $programacion->update($request->only([
                'date_start', 'date_end', 'schedule_id', 'zone_id', 'modality_id'
            ]));

            $empleadosSeleccionados = $request->input('employee_ids', []);
            $empleadosValidos = [];

            foreach ($empleadosSeleccionados as $empId) {
                $tieneContratoVigente = \App\Models\EmployeeContract::where('employee_id', $empId)
                    ->where('status', 'Activo')
                    ->whereDate('date_start', '<=', $programacion->date_start)
                    ->whereDate('date_end', '>=', $programacion->date_end)
                    ->exists();

                if ($tieneContratoVigente) {
                    $empleadosValidos[] = $empId;
                }
            }

            if (count($empleadosValidos) !== count($empleadosSeleccionados)) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Uno o más empleados no tienen contrato activo que cubra el rango de fechas.',
                ], 422);
            }

            $vehicleId = Vehicle::first()?->id;
            $dailyProgramming = $programacion->dailyProgramming()->firstOrCreate([
                'programming_id' => $programacion->id
            ], [
                'date_start' => $programacion->date_start,
                'vehicle_id' => $vehicleId ?? $request->input('vehicle_id')
            ]);

            $dailyProgramming->employees()->delete();
            foreach ($empleadosValidos as $empId) {
                $dailyProgramming->employees()->create([
                    'employee_id' => $empId
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Programación actualizada con éxito.',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la programación: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            // Buscar la programación principal
            $programming = Programming::findOrFail($id);

            // Eliminar todas las programaciones diarias asociadas
            DailyProgramming::where('programming_id', $programming->id)->delete();

            // Eliminar la programación principal
            $programming->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Programación eliminada con éxito.',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la programación: ' . $e->getMessage(),
            ], 500);
        }
    }

}

