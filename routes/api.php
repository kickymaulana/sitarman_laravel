<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ThermalShockController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Route Terproteksi (Harus Login)
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class)->names('api.users');
    Route::get('thermal-shocks/references', [ThermalShockController::class, 'references'])->name('api.thermal-shocks.references');
    Route::apiResource('thermal-shocks', ThermalShockController::class)->names('api.thermal-shocks');
    
    // Route khusus untuk mengelola detail secara satuan (Master-Detail UX)
    Route::post('thermal-shocks/{thermal_shock}/details', [ThermalShockController::class, 'storeDetail'])->name('api.thermal-shocks.details.store');
    Route::delete('thermal-shocks/{thermal_shock}/details/{detail_id}', [ThermalShockController::class, 'destroyDetail'])->name('api.thermal-shocks.details.destroy');

    Route::post('/logout', [AuthController::class, 'logout']);
});
