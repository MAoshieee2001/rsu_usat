<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\BrandModelController;
use App\Http\Controllers\admin\EmployeeContractController;
use App\Http\Controllers\admin\EmployeeController;
use App\Http\Controllers\admin\TypeController;
use App\Http\Controllers\admin\VacationChangeController;
use App\Http\Controllers\admin\VacationController;
use App\Http\Controllers\admin\VehicleController;
use App\Http\Controllers\admin\ColorController;
use Illuminate\Support\Facades\Route;

Route::resource('/', AdminController::class)->names('admin');
// Registrar todas las rutas del recurso BrandController
Route::resource('brands', BrandController::class)->names('admin.brands');
Route::resource('models', BrandmodelController::class)->names('admin.models');

Route::resource('types', TypeController::class)->names('admin.types');
Route::resource('vehicles', VehicleController::class)->names('admin.vehicles');
Route::resource('employees', EmployeeController::class)->names('admin.employees');
Route::resource('contracts', EmployeeContractController::class)->names('admin.contracts');
Route::resource('vacations', VacationController::class)->names('admin.vacations');
Route::resource('colors', ColorController::class)->names('admin.colors');

// Rutas para gestión de imágenes de vehículos
Route::get('models-by-brand/{brand_id}', [VehicleController::class, 'getModelsByBrand'])->name('admin.models.byBrand');
// Ruta para obtener las iamgenes de los vehiculos
Route::get('vehicles/images/{id}', [VehicleController::class, 'getImages']);
// Ruta  para actualizar las imagenes vehiculos desde el modals
Route::post('vehicles/images/{image}/set-profile', [VehicleController::class, 'setProfileImage'])->name('admin.vehicles.images.set-profile');
// Ruta para eliminar una imagene
Route::delete('vehicles/images/{image}', [VehicleController::class, 'deleteImage'])->name('admin.vehicles.images.delete');

