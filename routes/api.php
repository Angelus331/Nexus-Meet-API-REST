<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\CirculoController;
use App\Http\Controllers\MiembroController;
use App\Http\Controllers\MensajeController;
use App\Http\Controllers\EventoController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\CalificacionController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\AdminController;


Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/google', [AuthController::class, 'loginGoogle']);

Route::middleware('auth:api')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::post('/auth/refresh', [AuthController::class, 'refresh']);	
});

Route::middleware('auth:api')->group(function () {

    // Usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index']);
    Route::get('/usuarios/{usuario}', [UsuarioController::class, 'show']);
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update']);
    Route::post('/usuarios/{usuario}/foto', [UsuarioController::class, 'subirFoto']);
    Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy']);

    // Círculos
    Route::get('/circulos', [CirculoController::class, 'index']);
    Route::post('/circulos', [CirculoController::class, 'store']);
    Route::post('/circulos/join', [CirculoController::class, 'join']);
    Route::get('/circulos/{circulo}', [CirculoController::class, 'show']);
    Route::put('/circulos/{circulo}', [CirculoController::class, 'update']);
    Route::delete('/circulos/{circulo}', [CirculoController::class, 'destroy']);
    Route::post('/circulos/{circulo}/leave', [CirculoController::class, 'leave']);

    // Miembros
    Route::get('/circulos/{circulo}/miembros', [MiembroController::class, 'index']);
    Route::post('/circulos/{circulo}/miembros', [MiembroController::class, 'store']);
    Route::put('/circulos/{circulo}/miembros/{usuario}', [MiembroController::class, 'update']);
    Route::delete('/circulos/{circulo}/miembros/{usuario}', [MiembroController::class, 'destroy']);

    // Chat
    Route::get('/circulos/{circulo}/mensajes', [MensajeController::class, 'index']);
    Route::post('/circulos/{circulo}/mensajes', [MensajeController::class, 'store']);
    Route::put('/mensajes/{mensaje}', [MensajeController::class, 'update']);
    Route::delete('/mensajes/{mensaje}', [MensajeController::class, 'destroy']);
    Route::post('/mensajes/{mensaje}/reportar', [MensajeController::class, 'reportar']);

    // Eventos
    Route::get('/circulos/{circulo}/eventos', [EventoController::class, 'index']);
    Route::post('/circulos/{circulo}/eventos', [EventoController::class, 'store']);
    Route::get('/eventos/{evento}', [EventoController::class, 'show']);
    Route::put('/eventos/{evento}', [EventoController::class, 'update']);
    Route::delete('/eventos/{evento}', [EventoController::class, 'destroy']);

    // Materiales
    Route::get('/circulos/{circulo}/materiales', [MaterialController::class, 'index']);
    Route::post('/circulos/{circulo}/materiales', [MaterialController::class, 'store']);
    Route::delete('/materiales/{material}', [MaterialController::class, 'destroy']);

    // Gastos
    Route::get('/circulos/{circulo}/gastos', [GastoController::class, 'index']);
    Route::post('/circulos/{circulo}/gastos', [GastoController::class, 'store']);
    Route::get('/gastos/{gasto}', [GastoController::class, 'show']);
    Route::put('/gastos/{gasto}', [GastoController::class, 'update']);
    Route::delete('/gastos/{gasto}', [GastoController::class, 'destroy']);
    Route::put('/gastos/{gasto}/detalle/{usuario}/pagar', [GastoController::class, 'marcarPagado']);

    // Calificaciones
    Route::get('/circulos/{circulo}/calificaciones', [CalificacionController::class, 'index']);
    Route::post('/circulos/{circulo}/calificaciones', [CalificacionController::class, 'store']);

    // Notificaciones
    Route::get('/notificaciones', [NotificacionController::class, 'index']);
    Route::put('/notificaciones/{notificacion}/leer', [NotificacionController::class, 'leer']);
    Route::put('/notificaciones/leer-todas', [NotificacionController::class, 'leerTodas']);

    // Admin (Rodolfo — panel web)
    Route::prefix('admin')->group(function () {
        Route::get('/metrics', [AdminController::class, 'metrics']);
        Route::get('/circulos', [AdminController::class, 'circulos']);
        Route::put('/circulos/{circulo}/moderar', [AdminController::class, 'moderarCirculo']);
        Route::get('/usuarios', [AdminController::class, 'usuarios']);
        Route::put('/usuarios/{usuario}/suspender', [AdminController::class, 'suspenderUsuario']);
        Route::get('/mensajes/reportados', [AdminController::class, 'mensajesReportados']);
        Route::put('/mensajes/{mensaje}/resolver', [AdminController::class, 'resolverReporte']);
    });
});
