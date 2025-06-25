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
use App\Models\DetailsPrograming;
use App\Models\EmployeeType;

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
                    <form action="' . route('admin.models.destroy', $model['id']) . '" method="POST" class="d-inline frmDelete">
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
        try {
            programming::create($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Programación registrada con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear programacion: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        $programacion = Programming::with('detalles')->findOrFail($id);
        $zonas = Zone::pluck('name', 'id');
        $vehiculos = Vehicle::pluck('name', 'id');
        $empleados = Employee::select('id', DB::raw("CONCAT(names, ' ', lastnames) as full_name"))->pluck('full_name', 'id');
        $horarios = Schedule::pluck('name', 'id');

        return view('admin.programming.edit', compact('programacion', 'zonas', 'vehiculos', 'empleados', 'horarios'));
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

    public function precargarPorZona(Request $request)
    {
        $request->validate([
            'zona_id' => 'required|exists:zones,id'
        ]);
        $zonaId = $request->zona_id;
        $vehiculo = Vehicle::where('zone_id', $zonaId)->where('status', 'ACTIVO')->first();
        $turno = Schedule::where('zone_id', $zonaId)->first();
        $empleados = Employee::whereHas('contracts', function ($q) {
                $q->where('status', 'Activo');
            })
            ->where('zone_id', $zonaId)
            ->selectRaw("CONCAT(names, ' ', lastnames) as fullnames, id")
            ->pluck('fullnames', 'id');
        return response()->json([
            'vehiculo' => $vehiculo,
            'turno' => $turno,
            'empleados' => $empleados
        ]);
    }   
}