<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $employees = Employee::select(
            'employees.id',
            'employees.dni',
            DB::raw('CONCAT(employees.names, " ", employees.lastnames) as full_name'),
            'employees.birthday',
            'employees.license',
            'employees.address',
            'employees.email',
            'employees.photo',
            'employees.phone',
            'employees.password',
            'employees.status',
            't.name as type_name',
            'employees.created_at',
            'employees.updated_at'
        )
            ->join('employeetypes as t', 'employees.type_id', '=', 't.id');
        
        if ($request->ajax()) {
            // sin ->get()

            return DataTables::of($employees)
                ->addColumn('options', function ($employee) {
                    return '
                        <button class="btn btn-sm btn-warning btnEditar" id="' . $employee->id . '">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-secondary btnFoto" id="' . $employee->id . '">
                            <i class="fas fa-image"></i>
                        </button>
                        <form action="' . route('admin.employees.destroy', $employee->id) . '" method="POST" class="d-inline frmDelete">
                            ' . csrf_field() . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    ';
                })
                ->addColumn('photo', function ($employee) {
                    $logoPath = $employee->photo == '' ? 'storage/brands/empty.png' : $employee->photo;
                    return '<img src="' . asset($logoPath) . '" width="50px" height="50px">';
                })

                ->rawColumns(['photo', 'options'])
                ->make(true);
        }

        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        try {
            $employeetypes = EmployeeType::pluck('name', 'id');
            return view('admin.employees.create', compact('employeetypes'));
        } catch (\Exception $e) {
            return redirect()->route('admin.employees.index')->with('error', 'Ocurrió un error al intentar crear un nuevo empleado.');
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dni' => 'required|unique:employees,dni',
            'names' => 'required|string|max:100',
            'lastnames' => 'required|string|max:100',
            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|string|max:20',
            'type_id' => 'required|exists:types,id',
        ]);

        Employee::create($request->all());

        return redirect()->route('admin.employees.index')->with('success', 'Empleado registrado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $employee = Employee::with(['contractType', 'type'])->findOrFail($id);
        return view('admin.employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $employee = Employee::findOrFail($id);
        return view('admin.employees.edit', compact('employee'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $employee = Employee::findOrFail($id);

        $request->validate([
            'dni' => 'required|unique:employees,dni,' . $employee->id,
            'names' => 'required|string|max:100',
            'lastnames' => 'required|string|max:100',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'phone' => 'nullable|string|max:20',
            'type_id' => 'required|exists:types,id',
        ]);

        $employee->update($request->all());

        return redirect()->route('admin.employees.index')->with('success', 'Empleado actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();
        return redirect()->route('admin.employees.index')->with('success', 'Empleado eliminado.');
    }
}
