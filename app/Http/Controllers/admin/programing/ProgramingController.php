<?php
namespace App\Http\Controllers\admin\Programing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Models\Programming;
use App\Models\Zone;
use App\Models\Schedule;
use App\Models\Vehicle;
use App\Models\Employee;
use App\Models\EmployeeContract;
use App\Models\Vacation;
use App\Models\DetailsPrograming;
use App\Models\EmployeeType;

class ProgramingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $programaciones = Programming::with(['zona', 'horario', 'vehiculo', 'detalles.empleado'])->get();

            $data = $programaciones->map(function ($p) {
                return [
                    'zona_name'     => $p->zona->name,
                    'vehiculo_name' => $p->vehiculo->name,
                    'turno_name'    => $p->horario->name,
                    'date_joined'   => $p->date_joined,
                    'date_end'      => $p->date_end,
                    'dias_semana'   => is_array($p->dias_semana) ? implode(', ', $p->dias_semana) : '',
                    'empleados'     => $p->detalles->map(fn($d) => $d->empleado->fullnames)->implode(', '),
                    'options'       => '<button class="btn btn-warning btnEditar" id="' . $p->id . '"><i class="fas fa-edit"></i></button>',
                ];
            });

            return datatables()->of($data)->rawColumns(['options'])->make(true);
        }

        return view('admin.programming.index');
    }

    public function create()
    {
        $zonas = Zone::pluck('name', 'id');
        $vehiculos = Vehicle::where('status', 'ACTIVO')->pluck('name', 'id');
        $horarios = Schedule::pluck('name', 'id');
        $types = EmployeeType::pluck('name', 'id');

        $empleados = Employee::whereHas('contracts', function ($q) {
            $q->where('status', 'Activo');
        })->selectRaw("CONCAT(names, ' ', lastnames) as fullnames, id")
            ->pluck('fullnames', 'id');

        return view('admin.programming.create', compact('zonas', 'vehiculos', 'horarios', 'types', 'empleados'));
    }

    public function getEmployeesByType(Request $request)
    {
        $typeId = $request->get('type_id');

        $empleados = Employee::where('type_id', $typeId)
            ->whereHas('contracts', fn($q) => $q->where('status', 'Activo'))
            ->pluck('fullnames', 'id');

        return response()->json($empleados);
    }

    public function store(Request $request)
    {
        $request->validate([
            'zona_id' => 'required|exists:zones,id',
            'vehicle_id' => 'required|exists:vehicles,id',
            'horario_id' => 'required|exists:schedules,id',
            'employee_ids' => 'required|array',
            'employee_ids.*' => 'exists:employees,id',
            'dias_semana' => 'required|array',
            'dias_semana.*' => 'string',
            'date_joined' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_joined',
        ]);

        DB::beginTransaction();

        try {
            foreach ($request->employee_ids as $empId) {
                $empleado = Employee::findOrFail($empId);

                if (!EmployeeContract::where('employee_id', $empId)->where('status', 'Activo')->exists()) {
                    return response()->json(['success' => false, 'message' => "El empleado {$empleado->fullnames} no tiene contrato activo."], 422);
                }

                if (Vacation::where('employee_id', $empId)
                    ->where(function ($q) use ($request) {
                        $q->whereBetween('date_start', [$request->date_joined, $request->date_end])
                          ->orWhereBetween('date_end', [$request->date_joined, $request->date_end]);
                    })->exists()) {
                    return response()->json(['success' => false, 'message' => "El empleado {$empleado->fullnames} tiene vacaciones durante ese periodo."], 422);
                }
            }

            foreach ($request->employee_ids as $empId) {
                $conflicto = Programming::where('vehicle_id', $request->vehicle_id)
                    ->where('schedule_id', $request->horario_id)
                    ->where(function ($q) use ($request) {
                        $q->whereBetween('date_joined', [$request->date_joined, $request->date_end])
                          ->orWhereBetween('date_end', [$request->date_joined, $request->date_end]);
                    })
                    ->whereHas('detalles', function ($q) use ($empId) {
                        $q->where('employee_id', $empId);
                    })->exists();

                if ($conflicto) {
                    return response()->json(['success' => false, 'message' => "Conflicto: el empleado ya está programado con el mismo turno y vehículo."], 422);
                }
            }

            $programacion = Programming::create([
                'zone_id' => $request->zona_id,
                'schedule_id' => $request->horario_id,
                'vehicle_id' => $request->vehicle_id,
                'date_joined' => $request->date_joined,
                'date_start' => $request->date_joined,
                'date_end' => $request->date_end,
                'dias_semana' => $request->dias_semana,
            ]);

            foreach ($request->employee_ids as $empId) {
                DetailsPrograming::create([
                    'programming_id' => $programacion->id,
                    'employee_id' => $empId,
                    'date_start' => $request->date_joined,
                    'status' => 'PROGRAMADO',
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Programación registrada correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar programación: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        $programacion = Programming::with('detalles')->findOrFail($id);
        $zonas = Zone::pluck('name', 'id');
        $vehiculos = Vehicle::pluck('name', 'id');
        $empleados = Employee::select('id', DB::raw("CONCAT(names, ' ', lastnames) as full_name"))->pluck('full_name', 'id');
        $horarios = Schedule::pluck('name', 'id');
        $types = EmployeeType::pluck('name', 'id');

        return view('admin.programming.edit', compact('programacion', 'zonas', 'vehiculos', 'empleados', 'horarios', 'types'));
    }

    public function update(Request $request, $id)
    {
        // Misma lógica de validación que en store...
    }

    public function destroy(string $id)
    {
        // No implementado
    }

    public function iniciarRecorridoPorTurno(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date',
            'schedule_id' => 'required|exists:schedules,id'
        ]);

        $programaciones = Programming::where('schedule_id', $request->schedule_id)
            ->where('date_start', '<=', $request->fecha)
            ->where('date_end', '>=', $request->fecha)
            ->with(['vehiculo', 'detalles.empleado'])
            ->get();

        foreach ($programaciones as $programacion) {
            $vehiculo = $programacion->vehiculo;
            if ($vehiculo->status !== 'ACTIVO') {
                continue;
            }

            $puedeIniciar = true;
            foreach ($programacion->detalles as $detalle) {
                $empleado = $detalle->empleado;
                if ($empleado->asistencias()->where('fecha', $request->fecha)->where('status', 'AUSENTE')->exists()) {
                    $reserva = Employee::where('type_id', $empleado->type_id)
                        ->whereDoesntHave('asistencias', function ($q) use ($request) {
                            $q->where('fecha', $request->fecha)->where('status', 'PRESENTE');
                        })
                        ->whereHas('contracts', fn($q) => $q->where('status', 'Activo'))
                        ->first();
                    if ($reserva) {
                        $detalle->employee_id = $reserva->id;
                        $detalle->save();
                    } else {
                        $puedeIniciar = false;
                    }
                }
            }

            if ($puedeIniciar) {
                $programacion->status = 'INICIADO';
                $programacion->save();
            }
        }

        return response()->json(['success' => true, 'message' => 'Recorridos procesados.']);
    }
}