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
Route::get('vehicles/images/{id}', [VehicleController::class, 'getImages']);
Route::resource('types', TypeController::class)->names('admin.types');
Route::resource('vehicles', VehicleController::class)->names('admin.vehicles');