<?php

namespace App\Http\Controllers\admin;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Vacation;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class VacationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vacations = Vacation::select(
            'vacations.id',
            'ce.name as contract_name',
            'te.name as type_name',
            \DB::raw('CONCAT(em.names, " ", em.lastnames) AS employee_name'),
            'em.dni as employee_dni',
            'vacations.status',
            'vacations.date_start',
            'vacations.date_end',
            'vacations.created_at',
            'vacations.updated_at'
        )
            ->join('employees as em', 'vacations.employee_id', '=', 'em.id')
            ->join('employee_contracts as ec', function ($join) {
                $join->on('ec.employee_id', '=', 'em.id')
                    ->whereRaw('ec.id = (SELECT id FROM employee_contracts WHERE employee_id = em.id ORDER BY created_at DESC LIMIT 1)');
            })
            ->join('contract_types as ce', 'ec.contract_id', '=', 'ce.id')
            ->join('employeetypes as te', 'em.type_id', '=', 'te.id')
            ->get();

        if ($request->ajax()) {
            return DataTables::of($vacations)
                ->addColumn('options', function ($vacation) {
                    $disabled = $vacation->contract_name == 'Nombrado' ? 'disabled' : '';
                    return '
                        <button class="btn btn-sm btn-warning btnEditar" id="' . $vacation->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="' . route('admin.vacations.destroy', $vacation->id) . '" method="POST" class="d-inline frmDelete">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    ';
                })
                ->editColumn('status', function ($vehicle) {
                    switch ($vehicle->status) {
                        case 'INACTIVO':
                            return '<span class="badge bg-warning">Inactivo</span>';
                        case 'ACTIVO':
                            return '<span class="badge bg-primary text-dark">Activo</span>';
                        default:
                            return '<span class="badge bg-secondary">Programado</span>';
                    }
                })
                ->rawColumns(['status', 'options'])
                ->make(true);
        } else {
            return view('admin.vacations.index', compact('vacations'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $employee = Employee::all()->pluck('fullnames', 'id');
            return view('admin.vacations.create', compact('employee'));
        } catch (\Exception $e) {
            return redirect()->route('admin.vacations.index')->with('error', 'Ocurrió un error al intentar crear un nueva vacación.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
   public function store(Request $request)
    {
        /*
        * ESTADO ACTIVO : ESTA EN VACACIONES
        *        INACTIVO : PENDIENTE  
        *        PROGRAMADO
        */

        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'date_start' => 'required|date',
                'date_end' => 'required|date',
            ]);

            $employeeId = $request->employee_id;

            // ✅ VALIDACIÓN DE CONTRATO
            $employee = Employee::with('contract')->findOrFail($employeeId);

            if (!in_array($employee->contract->name, ['Nombrado', 'Permanente'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'El empleado no tiene un contrato válido para registrar vacaciones (solo "Nombrado" o "Permanente").',
                ], 422);
            }

            //  Validar si ya tiene vacaciones
            $exists = Vacation::where('employee_id', $employeeId)->exists();
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Este empleado ya tiene vacaciones registradas.',
                ], 422);
            }

            // 📅 Validaciones de fecha
            $dateStart = Carbon::parse($request->date_start);
            $dateEnd = Carbon::parse($request->date_end);

            if ($dateEnd->lt($dateStart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La fecha de fin no puede ser menor a la fecha de inicio.',
                ], 422);
            }

            if ($dateEnd->diffInDays($dateStart) < 30) {
                return response()->json([
                    'success' => false,
                    'message' => 'La fecha de fin debe ser al menos 30 días después de la fecha de inicio.',
                ], 422);
            }

            // ✅ Crear registro
            Vacation::create([
                'employee_id' => $employeeId,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'status' => 'INACTIVO'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vacaciones registrada con éxito.',
            ], 200);

        } catch (\Exception $e) {
            return redirect()->route('admin.vacations.index')
                ->with('error', 'Error al crear el vacaciones: ' . $e->getMessage());
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
            $vacation = Vacation::findOrFail($id);
            $employee = Employee::all()->pluck('fullnames', 'id');
            return view('admin.vacations.edit', compact('vacation', 'employee'));
        } catch (\Exception $e) {
            return redirect()->route('admin.vacations.index')->with('error', 'Ocurrió un error al intentar editar las vacaciones.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'employee_id' => 'required|exists:employees,id',
                'date_start' => 'required|date',
                'date_end' => 'required|date',
            ]);

            $employeeId = $request->employee_id;

            // ✅ Validación de contrato
            $employee = Employee::with('contract')->findOrFail($employeeId);

            if (!in_array($employee->contract->name, ['Nombrado', 'Permanente'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'El empleado no tiene un contrato válido para registrar vacaciones (solo "Nombrado" o "Permanente").',
                ], 422);
            }

            // 📅 Validaciones de fecha
            $dateStart = Carbon::parse($request->date_start);
            $dateEnd = Carbon::parse($request->date_end);

            if ($dateEnd->lt($dateStart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La fecha de fin no puede ser menor a la fecha de inicio.',
                ], 422);
            }

            if ($dateEnd->diffInDays($dateStart) < 30) {
                return response()->json([
                    'success' => false,
                    'message' => 'La fecha de fin debe ser al menos 30 días después de la fecha de inicio.',
                ], 422);
            }

            // 🛡️ Validar solapamientos de vacaciones
            $existeVacacion = Vacation::where('employee_id', $employeeId)
                ->where('id', '!=', $id)
                ->where(function ($query) use ($request) {
                    $query->whereBetween('date_start', [$request->date_start, $request->date_end])
                        ->orWhereBetween('date_end', [$request->date_start, $request->date_end])
                        ->orWhere(function ($query) use ($request) {
                            $query->where('date_start', '<=', $request->date_start)
                                ->where('date_end', '>=', $request->date_end);
                        });
                })
                ->exists();

            if ($existeVacacion) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existe un registro de vacaciones para este empleado en ese rango de fechas.',
                ], 422);
            }

            // ✅ Actualizar registro
            $vacation = Vacation::findOrFail($id);

            $vacation->update([
                'employee_id' => $employeeId,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'status' => 'INACTIVO'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vacaciones actualizada con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return redirect()->route('admin.vacations.index')
                ->with('error', 'Error al actualizar las vacaciones: ' . $e->getMessage());
        }
    }

    public function destroy(string $id)
    {
        try {
            $vacation = Vacation::findOrFail($id);
            $vacation->delete();
            return redirect()->route('admin.vacations.index')->with('success', 'Vacaciones eliminada con éxito.');
        } catch (\Exception $e) {
            return redirect()->route('admin.vacations.index')->with('error', 'Ocurrió un error al intentar eliminar las vacaciones.' . $e->getMessage());
        }
    }
}
