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
            $programacion = Programming::create($request->only([
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

            $dailyProgramming = $programacion->dailyProgramming()->create([
                'date_start' => $programacion->date_start,
                'vehicle_id' => $request->input('vehicle_id')
            ]);

            foreach ($empleadosValidos as $empId) {
                $dailyProgramming->employees()->create([
                    'employee_id' => $empId
                ]);
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Programación registrada con éxito.',
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear programación: ' . $e->getMessage(),
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

            $dailyProgramming = $programacion->dailyProgramming()->firstOrCreate([
                'programming_id' => $programacion->id
            ], [
                'date_start' => $programacion->date_start,
                'vehicle_id' => $request->input('vehicle_id')
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


    public function destroy(String $id)
    {
        try {
            $programacion = Programming::findOrFail($id);
            $programacion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Programación eliminada con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la programación: ' . $e->getMessage(),
            ], 500);
        }
    }
  
}