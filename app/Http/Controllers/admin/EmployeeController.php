<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeType;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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
                'password' => 'required|string|min:6',
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
                'password' => $request->password,
                'status' => $request->status,
                'type_id' => $request->type_id,
            ]);

            // Manejo de foto (opcional)
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('employee_photos', 'public');
                $employee->photo = 'storage/' . $path;
                $employee->save();
            }

            DB::commit();

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
        //
    }

    public function edit(string $id)
    {
        try {
            // Cargar el empleado junto con posibles relaciones (si existieran)
            $employee = Employee::findOrFail($id);
            // Obtener los tipos de empleado para un select
            $types = EmployeeType::pluck('name', 'id');
            return view('admin.employees.edit', compact('employee', 'types'));
        } catch (\Exception $e) {
            return redirect()->route('admin.employees.index')->with('error', 'Ocurrió un error al intentar editar el empleado.');
        }
    }

    public function update(Request $request, string $id)
    {
        try {
            $employee = Employee::findOrFail($id);

            $request->validate([
                'dni' => [
                    'required',
                    'string',
                    'size:8',
                    Rule::unique('employees', 'dni')->ignore($employee->id),
                ],
                'names' => 'required|string|max:100',
                'lastnames' => 'required|string|max:100',
                'birthday' => 'required|date|before:today',
                'license' => 'nullable|string|max:20',
                'address' => 'required|string|max:255',
                'email' => [
                    'required',
                    'email',
                    'max:100',
                    Rule::unique('employees', 'email')->ignore($employee->id),
                ],
                'phone' => 'required|string|max:15',
                'status' => 'required|in:active,inactive',
                'type_id' => 'required|integer|exists:employeetypes,id',
            ]);

            // Actualizar datos del empleado
            $employee->update([
                'dni' => $request->dni,
                'names' => $request->names,
                'lastnames' => $request->lastnames,
                'birthday' => $request->birthday,
                'license' => $request->license,
                'address' => $request->address,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
                'type_id' => $request->type_id,
            ]);

            // Si se sube una nueva foto, se almacena
            if ($request->hasFile('photo')) {
                $path = $request->file('photo')->store('employee_photos', 'public');
                $employee->photo = 'storage/' . $path;
                $employee->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Empleado actualizado correctamente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el empleado: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $employee = Employee::findOrFail($id);

            // Si el empleado tiene imágenes u otros archivos relacionados
            foreach ($employee->images as $image) {
                if ($image->image && Storage::exists('public/' . $image->image)) {
                    Storage::delete('public/' . $image->image);
                }

                $image->delete(); // Elimina el registro de la tabla relacionada, como employee_images
            }

            // Finalmente, elimina el empleado
            $employee->delete();

            return response()->json(['success' => true, 'message' => 'Empleado eliminado correctamente.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el empleado: ' . $e->getMessage()
            ], 500);
        }
    }

}
