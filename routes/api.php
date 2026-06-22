<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\FipeYearController;
use App\Http\Controllers\Api\MaintenanceCategoryController;
use App\Http\Controllers\Api\MaintenanceController;
use App\Http\Controllers\Api\MaintenanceTypeController;
use App\Http\Controllers\Api\RouteDestinationController;
use App\Http\Controllers\Api\VehicleController;
use App\Http\Controllers\Api\VehicleModelController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Vehicles
    Route::apiResource('vehicles', VehicleController::class);

    // Brands & Models & Years
    Route::get('/brands', [BrandController::class, 'index']);
    Route::get('/brands/{brand}/models', [VehicleModelController::class, 'index']);
    Route::get('/brands/{brand}/models/{vehicle_model}/years', [FipeYearController::class, 'index']);

    // Maintenance Categories & Types
    Route::get('/maintenance-categories', [MaintenanceCategoryController::class, 'index']);
    Route::get('/maintenance-types', [MaintenanceTypeController::class, 'index']);
    Route::get('/maintenance-categories/{category}/types', [MaintenanceTypeController::class, 'byCategory']);

    // Maintenances
    Route::apiResource('maintenances', MaintenanceController::class);

    // Destinations
    Route::apiResource('destinations', RouteDestinationController::class);
    Route::post('/destinations/calculate-route', [RouteDestinationController::class, 'calculateRoute']);
    Route::get('/destinations/{destination}/route', [RouteDestinationController::class, 'route']);
});
