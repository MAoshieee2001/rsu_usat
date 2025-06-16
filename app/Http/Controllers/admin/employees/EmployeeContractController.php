<?php

namespace App\Http\Controllers\admin\employees;

use App\Http\Controllers\Controller;
use App\Models\ContractType;
use App\Models\Employee;
use App\Models\EmployeeContract;
use DB;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class EmployeeContractController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $contracts = EmployeeContract::select(
            'employee_contracts.id',
            DB::raw('CONCAT(emp.names, " ", emp.lastnames) as full_name'),
            'ctp.name as contract_name',
            'employee_contracts.date_start',
            'employee_contracts.date_end',
            'employee_contracts.status',
            'employee_contracts.created_at',
            'employee_contracts.updated_at',
        )
            ->join('employees as emp', 'employee_contracts.employee_id', '=', 'emp.id')
            ->join('contract_types as ctp', 'employee_contracts.contract_id', '=', 'ctp.id')
            ->get();

        if ($request->ajax()) {
            return DataTables::of($contracts)
                ->editColumn('date_end', function ($contract) {
                    return $contract->date_end ? $contract->date_end : '-----------';
                })
                ->editColumn('status', function ($vehicle) {
                    switch ($vehicle->status) {
                        case 'Inactivo':
                            return '<span class="badge bg-danger">Terminado</span>';
                        case 'Activo':
                            return '<span class="badge bg-primary text-dark">Activo</span>';
                    }
                })
                ->addColumn('options', function ($contract) {
                    return '
                        <button class="btn btn-sm btn-warning btnEditar" id="' . $contract->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form action="' . route('admin.contracts.destroy', $contract->id) . '" method="POST" class="d-inline frmDelete">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    ';
                })
                ->rawColumns(['status', 'options'])
                ->make(true);
        } else {
            return view('admin.contracts.index', compact('contracts'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        try {
            $employee = Employee::all()->pluck('fullnames', 'id');
            $contract = ContractType::all()->pluck('name', 'id');
            return view('admin.contracts.create', compact('employee', 'contract'));
        } catch (\Exception $e) {
            return redirect()->route('admin.contracts.index')->with('error', 'Ocurrió un error al intentar crear un nuevo contracto.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $contract = ContractType::find($request->contract_id);
            // Nueva validación general: verificar si el empleado tiene algún contrato activo
            $existingActiveContract = EmployeeContract::where('employee_id', $request->employee_id)
                ->where('status', 'Activo')
                ->first();
            if ($existingActiveContract) {
                return response()->json([
                    'success' => false,
                    'message' => 'El empleado ya tiene un contrato activo. No se puede registrar un nuevo contrato hasta que el actual esté inactivo.',
                ], 422);
            }
            // Validación para contratos temporales: fecha de fin obligatoria
            if (strtolower($contract->name) === 'temporal' && empty($request->date_end)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La fecha de finalización es obligatoria para contratos temporales.',
                ], 422);
            }
            // Crear nuevo contrato
            EmployeeContract::create([
                'employee_id' => $request->employee_id,
                'contract_id' => $request->contract_id,
                'date_start' => now(),
                'date_end' => $request->date_end,
                'status' => $request->status ?? 'Activo',
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Contrato registrado con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el contrato: ' . $e->getMessage(),
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
            $employeecontract = EmployeeContract::findOrFail($id);
            $employee = Employee::all()->pluck('fullnames', 'id');
            $contract = ContractType::all()->pluck('name', 'id');
            return view('admin.contracts.edit', compact('employee', 'contract', 'employeecontract'));

        } catch (\Exception $e) {
            return redirect()->route('admin.contracts.index')
                ->with('error', 'Ocurrió un error al intentar editar el contrato.');
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $contract = ContractType::find($request->contract_id);
            $employeeContract = EmployeeContract::findOrFail($id);
            // Verificar si ya existe otro contrato activo para el mismo empleado
            $existingActiveContract = EmployeeContract::where('employee_id', $request->employee_id)
                ->where('status', 'Activo')
                ->where('id', '!=', $id)
                ->first();
            if ($existingActiveContract && ($request->status === 'Activo' || $employeeContract->status === 'Activo')) {
                return response()->json([
                    'success' => false,
                    'message' => 'El empleado ya tiene otro contrato activo. No se puede activar este contrato.',
                ], 422);
            }
            // Validación para contratos temporales
            if (strtolower($contract->name) === 'temporal' && empty($request->date_end)) {
                return response()->json([
                    'success' => false,
                    'message' => 'La fecha de finalización es obligatoria para contratos temporales.',
                ], 422);
            }
            // Actualizar el contrato
            $employeeContract->update([
                'employee_id' => $request->employee_id,
                'contract_id' => $request->contract_id,
                'date_start' => $request->date_start,
                'date_end' => $request->date_end,
                'status' => $request->status ?? $employeeContract->status,
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Contrato actualizado con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el contrato: ' . $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $employeeContract = EmployeeContract::findOrFail($id);
            $employeeContract->delete();
            return response()->json([
                'success' => true,
                'message' => 'Contrato eliminado con éxito.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el contrato: ' . $e->getMessage(),
            ], 500);
        }
    }
}
