<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\TramiteController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\NotarioController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FormController;

//Ruta para el login
Route::post('/login', [AuthController::class, 'login']);

// Ruta para el preform.
Route::post('/pre-form', [FormController::class, 'store']);
Route::post('/cliente/pagos', [FormController::class, 'pagosPreform']);
Route::get('/cliente/dashboard', [ClienteController::class, 'dashboard']);


// Rutas protegidas por autenticación.
Route::middleware(['auth:sanctum'])->group(function () {

    // Rutas protegidas para el cliente.
    Route::get('/cliente/{id}', [ClienteController::class, 'show']);
    Route::put('/cliente/{id}', [ClienteController::class, 'update']);
    Route::delete('/cliente/{id}', [ClienteController::class, 'destroy']);
    Route::post('/cliente/minuta', [ClienteController::class, 'minuta']);

    // Rutas protegidas para el documento.
    Route::get('/documento', [DocumentoController::class, 'index']);
    Route::get('/documento/{id}', [DocumentoController::class, 'show']);
    Route::put('/documento/{id}', [DocumentoController::class, 'update']);
    Route::delete('/documento/{id}', [DocumentoController::class, 'destroy']);
    Route::post('/documento/store', [DocumentoController::class, 'store']);

    // Rutas protegidas para el notario.
    Route::get('/notario', [NotarioController::class, 'index']);
    Route::get('/notario/{id}', [NotarioController::class, 'show']);
    Route::put('/notario/{id}', [NotarioController::class, 'update']);
    Route::delete('/notario/{id}', [NotarioController::class, 'destroy']);
    Route::post('/notario/store', [NotarioController::class, 'store']);

    // Rutas protegidas para el admin.
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
    Route::put('/admin/cambiarNombre', [AdminController::class, 'CambioNombreEmpresa']);
    Route::get('/admin/clientes', [AdminController::class, 'clientes']);
    Route::get('/admin/notarios', [AdminController::class, 'notarios']);
    Route::get('/admin/tramites', [AdminController::class, 'tramites']);
    Route::get('/admin/pagos', [AdminController::class, 'pagos']);
    Route::get('/admin/reportes', [AdminController::class, 'reportes']);
    Route::get('/admin/configuracion', [AdminController::class, 'configuracion']);
    Route::get('/admin/store', [AdminController::class, 'store']);

    


    // Ruta para cerrar sesión.
    Route::post('/logout', [AuthController::class, 'logout']);
});
