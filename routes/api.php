<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\FipeYearController;
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

    // Destinations
    Route::apiResource('destinations', RouteDestinationController::class);
    Route::post('/destinations/calculate-route', [RouteDestinationController::class, 'calculateRoute']);
});
