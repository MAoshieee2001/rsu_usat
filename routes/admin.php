<?php

use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\BrandController;
use App\Http\Controllers\admin\BrandModelController;
use App\Http\Controllers\admin\TypeController;
use App\Http\Controllers\admin\VehicleController;
use Illuminate\Support\Facades\Route;

Route::resource('/', AdminController::class)->names('admin');
// Registrar todas las rutas del recurso BrandController
Route::resource('brands', BrandController::class)->names('admin.brands');
Route::resource('models', BrandmodelController::class)->names('admin.models');
Route::get('models-by-brand/{brand_id}', [VehicleController::class, 'getModelsByBrand'])->name('admin.models.byBrand');

Route::resource('types', TypeController::class)->names('admin.types');
Route::resource('vehicles', VehicleController::class)->names('admin.vehicles');

// Rutas para gestión de imágenes de vehículos
Route::get('vehicles/images/{id}', [VehicleController::class, 'getImages']);
Route::post('vehicles/images/{image}/set-profile', [VehicleController::class, 'setProfileImage'])->name('admin.vehicles.images.set-profile');
Route::delete('vehicles/images/{image}', [VehicleController::class, 'deleteImage'])->name('admin.vehicles.images.delete');