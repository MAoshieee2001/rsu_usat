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
        try {
            // Validaciones
            $request->validate([
                'dni' => 'required|string|size:8|unique:employees,dni',
                'names' => 'required|string|max:100',
                'lastnames' => 'required|string|max:100',
                'birthday' => 'required|date|before:today',
                'license' => 'nullable|string|max:20',
                'address' => 'required|string|max:255',
                'email' => 'required|email|max:100|unique:employees,email',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'phone' => 'required|string|max:15',
                'password' => 'required|string|min:6|confirmed',
                'status' => 'required|in:active,inactive',
                'type_id' => 'required|integer|exists:employeetypes,id',
            ]);

            // Crear empleado
            $employee = Employee::create([
                'dni' => $request->dni,
                'names' => $request->names,
                'lastnames' => $request->lastnames,
                'birthday' => $request->birthday,
                'license' => $request->license,
                'address' => $request->address,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => bcrypt($request->password),
                'status' => $request->status,
                'type_id' => $request->type_id,
            ]);

            // Manejo de foto (opcional)
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('employee_photos', 'public');
                $employee->photo = 'storage/' . $path;
                $employee->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Empleado registrado con éxito.',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar el empleado: ' . $e->getMessage(),
            ], 500);
        }
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
