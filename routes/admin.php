<?php

use App\Http\Controllers\admin\programing\ProgramingController;
use App\Http\Controllers\admin\zones\RouteController;
use App\Http\Controllers\admin\zones\RouteCoordController;
use App\Http\Controllers\admin\zones\ZoneCoordController;
use App\Http\Controllers\admin\zones\ZoneController;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\employees\AttendanceController;
use App\Http\Controllers\admin\programing\ScheduleController;
use App\Http\Controllers\admin\vehicles\BrandController;
use App\Http\Controllers\admin\vehicles\BrandModelController;
use App\Http\Controllers\admin\employees\EmployeeContractController;
use App\Http\Controllers\admin\employees\EmployeeController;
use App\Http\Controllers\admin\employees\EmployeeTypeController;
use App\Http\Controllers\admin\vehicles\TypeController;
use App\Http\Controllers\admin\employees\VacationController;
use App\Http\Controllers\admin\vehicles\VehicleController;
use App\Http\Controllers\admin\vehicles\ColorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\MaintenanceScheduleController;

Route::resource('/', AdminController::class)->names('admin');
# RUTAS DE GESTION VEHICULOS
Route::resource('brands', BrandController::class)->names('admin.brands');
Route::resource('models', BrandModelController::class)->names('admin.models');
Route::resource('types', TypeController::class)->names('admin.types');
Route::resource('vehicles', VehicleController::class)->names('admin.vehicles');
# RUTA DE GESTION DE EMPLEADOS
Route::resource('employees', EmployeeController::class)->names('admin.employees');
Route::resource('contracts', EmployeeContractController::class)->names('admin.contracts');
Route::resource('vacations', VacationController::class)->names('admin.vacations');
Route::resource('colors', ColorController::class)->names('admin.colors');
Route::resource('attendances', AttendanceController::class)->names('admin.attendances');
# RUTA DE GESTION DE ZONAS
Route::resource('zones', ZoneController::class)->names('admin.zones');
Route::resource('zonescoords', ZoneCoordController::class)->names('admin.zonescoords');
Route::resource('routes', RouteController::class)->names('admin.routes');
Route::resource('routescoords', RouteCoordController::class)->names('admin.routescoords');
Route::resource('employeetypes', EmployeeTypeController::class)->names('admin.employeetypes');
# RUTA DE GESTION DE PROGRAMACION
Route::resource('schedules', ScheduleController::class)->names('admin.schedules');
Route::resource('programming', ProgramingController::class)->names('admin.programming');
use App\Http\Controllers\admin\programing\DailyProgrammingController;

Route::post('/daily-programming', [DailyProgrammingController::class, 'store'])->name('daily-programming.store');
Route::get('/admin/employees/by-type/{id}', function ($id) {
    return \App\Models\Employee::where('type_id', $id)
        ->whereHas('contracts', fn($q) => $q->where('status', 'Activo'))
        ->selectRaw("CONCAT(names, ' ', lastnames) as fullnames, id")
        ->pluck('fullnames', 'id');
})->name('admin.programming.employeesByType');


//? RUTAS PARAMETRIZADAS PARA ASISTENCIAS 
Route::get('/attendances/buscar', [AttendanceController::class, 'buscar'])->name('admin.attendances.buscar');
#* RUTAS PARAMETRIZADAS PARA ZONAS
Route::get('maps', [ZoneController::class, 'getAllZones'])->name('admin.zones.all');
Route::get('coords_zone/{id}', [ZoneController::class, 'getCoords'])->name('admin.zones.getCoords');
Route::get('coords/{id}', [RouteController::class, 'getCoords'])->name('admin.routes.getCoords');

//? RUTAS PARAMETRIZADAS PARA VEHICLES
// Rutas para gestión de imágenes de vehículos
Route::get('models-by-brand/{brand_id}', [VehicleController::class, 'getModelsByBrand'])->name('admin.models.byBrand');
// Ruta para obtener las iamgenes de los vehiculos
Route::get('vehicles/images/{id}', [VehicleController::class, 'getImages']);
// Ruta  para actualizar las imagenes vehiculos desde el modals
Route::post('vehicles/images/{image}/set-profile', [VehicleController::class, 'setProfileImage'])->name('admin.vehicles.images.set-profile');
// Ruta para eliminar una imagene
Route::delete('vehicles/images/{image}', [VehicleController::class, 'deleteImage'])->name('admin.vehicles.images.delete');
Route::resource('maintenances', MaintenanceController::class)->names('admin.maintenances');
Route::prefix('maintenances/{maintenance}')->group(function () {
    Route::get('schedules', [MaintenanceScheduleController::class, 'index'])->name('admin.maintenances.schedules.index');
    Route::get('schedules/create', [MaintenanceScheduleController::class, 'create'])->name('admin.maintenances.schedules.create');
    Route::post('schedules', [MaintenanceScheduleController::class, 'store'])->name('admin.maintenances.schedules.store');
    Route::get('schedules/{schedule}/edit', [MaintenanceScheduleController::class, 'edit'])->name('admin.maintenances.schedules.edit');
    Route::put('schedules/{schedule}', [MaintenanceScheduleController::class, 'update'])->name('admin.maintenances.schedules.update');
    Route::delete('schedules/{schedule}', [MaintenanceScheduleController::class, 'destroy'])->name('admin.maintenances.schedules.destroy');
});
Route::get('maintenance-schedules', function () {
    $maintenance = \App\Models\Maintenance::first();
    if (!$maintenance) {
        abort(404, 'No hay mantenimientos registrados.');
    }
    return redirect()->route('admin.maintenances.schedules.index', $maintenance);
})->name('admin.maintenance-schedules.redirect');