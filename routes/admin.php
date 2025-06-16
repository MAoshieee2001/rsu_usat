<?php

use App\Http\Controllers\admin\zones\ZoneCoordController;
use App\Http\Controllers\admin\zones\ZonesController;
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
Route::resource('zones', ZonesController::class)->names('admin.zones');
Route::resource('zonescoords', ZoneCoordController::class)->names('admin.zonescoords');
Route::resource('employeetypes', EmployeeTypeController::class)->names('admin.employeetypes');
# RUTA DE GESTION DE PROGRAMACION
Route::resource('schedules', ScheduleController::class)->names('admin.schedules');



//? RUTAS PARAMETRIZADAS PARA ASISTENCIAS 
Route::get('/attendances/buscar', [AttendanceController::class, 'buscar'])->name('admin.attendances.buscar');
#* RUTAS PARAMETRIZADAS PARA ZONAS
Route::get('maps', [ZonesController::class, 'getAllZones'])->name('admin.zones.all');
Route::get('coords/{id}', [ZonesController::class, 'getCoords'])->name('admin.zones.getCoords');

//? RUTAS PARAMETRIZADAS PARA VEHICLES
// Rutas para gestión de imágenes de vehículos
Route::get('models-by-brand/{brand_id}', [VehicleController::class, 'getModelsByBrand'])->name('admin.models.byBrand');
// Ruta para obtener las iamgenes de los vehiculos
Route::get('vehicles/images/{id}', [VehicleController::class, 'getImages']);
// Ruta  para actualizar las imagenes vehiculos desde el modals
Route::post('vehicles/images/{image}/set-profile', [VehicleController::class, 'setProfileImage'])->name('admin.vehicles.images.set-profile');
// Ruta para eliminar una imagene
Route::delete('vehicles/images/{image}', [VehicleController::class, 'deleteImage'])->name('admin.vehicles.images.delete');

