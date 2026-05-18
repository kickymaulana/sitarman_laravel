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
    Route::apiResource('thermal-shocks', ThermalShockController::class)->names('api.thermal-shocks');

    Route::post('/logout', [AuthController::class, 'logout']);
});
