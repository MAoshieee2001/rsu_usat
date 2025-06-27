<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\MaintenanceSchedule;
use App\Models\Vehicle;
use App\Models\Employee;
use Illuminate\Http\Request;

class MaintenanceScheduleController extends Controller
{
    public function index(Maintenance $maintenance)
    {
        if (request()->ajax()) {
            $schedules = $maintenance->schedules()->with(['vehicle', 'employee']);

            return datatables()->of($schedules)
                ->addColumn('vehicle.name', fn($row) => $row->vehicle->plate ?? '-')
                ->addColumn('employee.name', fn($row) => $row->employee->full_name ?? '-')
                ->addColumn('options', function ($row) use ($maintenance) {
                    return view('admin.maintenances.schedules.template.actions', compact('row', 'maintenance'))->render();
                })
                ->rawColumns(['options'])
                ->make(true);
        }

        return view('admin.maintenances.schedules.index', compact('maintenance'));
    }

    public function create(Maintenance $maintenance)
    {
        $vehicles = Vehicle::pluck('plate', 'id');
        $employees = Employee::selectRaw("CONCAT(names, ' ', lastnames) as full_name, id")
            ->pluck('full_name', 'id');

        $schedule = new MaintenanceSchedule();

        return view('admin.maintenances.schedules.create', compact('schedule', 'maintenance', 'vehicles', 'employees'));
    }

    public function store(Request $request, Maintenance $maintenance)
    {
        $this->validateRequest($request);

        if ($this->isOverlapping($request)) {
            return response()->json(['success' => false, 'message' => 'El horario se solapa con otro mantenimiento del mismo vehículo.'], 422);
        }

        $maintenance->schedules()->create($request->all());

        return response()->json(['success' => true, 'message' => 'Horario registrado correctamente.']);
    }

    public function edit(Maintenance $maintenance, MaintenanceSchedule $schedule)
    {
        $vehicles = Vehicle::pluck('plate', 'id');
        $employees = Employee::selectRaw("CONCAT(names, ' ', lastnames) as full_name, id")
            ->pluck('full_name', 'id');

        return view('admin.maintenances.schedules.edit', compact('schedule', 'maintenance', 'vehicles', 'employees'));
    }

    public function update(Request $request, Maintenance $maintenance, MaintenanceSchedule $schedule)
    {
        $this->validateRequest($request);

        if ($this->isOverlapping($request, $schedule->id)) {
            return response()->json(['success' => false, 'message' => 'El horario se solapa con otro mantenimiento del mismo vehículo.'], 422);
        }

        $schedule->update($request->all());

        return response()->json(['success' => true, 'message' => 'Horario actualizado correctamente.']);
    }

    public function destroy(Maintenance $maintenance, MaintenanceSchedule $schedule)
    {
        $schedule->delete();

        return response()->json(['success' => true, 'message' => 'Horario eliminado correctamente.']);
    }

    private function validateRequest(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'type' => 'required|in:LIMPIEZA,REPARACIÓN',
            'day' => 'required|in:LUNES,MARTES,MIÉRCOLES,JUEVES,VIERNES,SÁBADO',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'employee_id' => 'required|exists:employees,id',
        ]);
    }

    private function isOverlapping(Request $request, $ignoreId = null)
    {
        return MaintenanceSchedule::where('vehicle_id', $request->vehicle_id)
            ->where('day', $request->day)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                      ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                      ->orWhere(function ($q) use ($request) {
                          $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                      });
            })
            ->exists();
    }
}
