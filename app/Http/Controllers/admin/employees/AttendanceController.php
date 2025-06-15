<?php

namespace App\Http\Controllers\admin\employees;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Attendance::with('employee');

            // Filtro por DNI solo si está presente
            if ($request->filled('dni')) {
                $query->whereHas('employee', function ($q) use ($request) {
                    $q->where('dni', $request->dni);
                });
            }

            // Filtro por rango de fechas solo si ambos están presentes
            if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
                $query->whereBetween('date_joined', [$request->fecha_inicio, $request->fecha_fin]);
            }

            // Si no hay filtros, devolvemos vacío
            if (!$request->filled('dni') && (!$request->filled('fecha_inicio') || !$request->filled('fecha_fin'))) {
                return datatables()->of(collect([]))->make(true);
            }

            return datatables()->of($query)
                ->addColumn('dni', fn($row) => optional($row->employee)->dni ?? '—')
                ->addColumn('full_names', fn($row) => optional($row->employee)->fullnames ?? '—')
                ->editColumn('date_joined', fn($row) => $row->date_joined ? Carbon::parse($row->date_joined)->format('d/m/Y') : '—')
                ->editColumn('date_end', fn($row) => $row->date_end ? Carbon::parse($row->date_end)->format('d/m/Y') : '—')
                // 👇 Aquí sacamos la hora de los mismos campos datetime
                ->addColumn('hour_joined', fn($row) => $row->date_joined ? Carbon::parse($row->date_joined)->format('H:i:s') : '—')
                ->addColumn('hour_end', fn($row) => $row->date_end ? Carbon::parse($row->date_end)->format('H:i:s') : '—')
                ->make(true);
        }

        return view('admin.attendances.index');
    }

    public function create()
    {
        try {
            $employee = Employee::all()->pluck('fullnames', 'id');
            return view('admin.attendances.create', compact('employee'));
        } catch (\Exception $e) {
            return redirect()->route('admin.attendances.index')->with('error', 'Ocurrió un error al intentar crear una nueva asistencia.');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dni' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $employee = Employee::where('dni', $request->dni)
                ->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Empleado no encontrado o contraseña incorrecta.',
                ], 404);
            }

            $today = Carbon::today();

            $attendance = Attendance::where('employee_id', $employee->id)
                ->whereDate('date_joined', $today)
                ->first();

            if (!$attendance) {
                Attendance::create([
                    'employee_id' => $employee->id,
                    'date_joined' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Entrada registrada con éxito.',
                ], 200);
            }

            if (!$attendance->date_end) {
                $attendance->update([
                    'date_end' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Salida registrada con éxito.',
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Ya se ha registrado entrada y salida para hoy.',
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al intentar registrar la marca. ' . $e->getMessage(),
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
