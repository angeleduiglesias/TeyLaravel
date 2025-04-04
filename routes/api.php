<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\TramiteController;

// Route::post('/cliente/store', [ClienteController::class, 'store']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/cliente/store', [ClienteController::class, 'store']);

// Ruta para registrar un nuevo cliente.
Route::post('/cliente/store', [ClienteController::class, 'store']);

// Ruta para registrar un tramite.
Route::post('/tramite/store', [TramiteController::class, 'store']);

// Rutas protegidas por autenticación.
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

});