<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
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

                ->make(true);
        }

        return view('admin.attendances.index');
    }
    public function buscar(Request $request)
    {
        $query = Attendance::with('employee');

        if ($request->filled('dni')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('dni', $request->dni);
            });
        }

        if ($request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            $query->whereBetween('date_joined', [$request->fecha_inicio, $request->fecha_fin]);
        }

        return datatables()->of($query)
            ->addColumn('dni', function ($row) {
                return optional($row->employee)->dni ?? '—';
            })
            ->addColumn('full_names', function ($row) {
                return optional($row->employee)->fullnames ?? '—'; // Usamos el accessor
            })
            ->editColumn('date_joined', function ($row) {
                return optional($row->date_joined)->format('d/m/Y');
            })
            ->editColumn('date_end', function ($row) {
                return $row->date_end ? $row->date_end->format('d/m/Y') : '—';
            })
            ->make(true);
    }


    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
