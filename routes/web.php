<?php

use App\Http\Controllers\Games\AstralRecipeController;
use App\Http\Controllers\Games\ControllerConstelaciones;
use App\Http\Controllers\Games\ControllerInventario;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrbitaController;
use App\Http\Controllers\SesionController;
use App\Http\Controllers\Games\AstralquimiumController;

// Rutas públicas 
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/register', [LoginController::class, 'showRegister'])->name('register');
Route::post('/register', [LoginController::class, 'register'])->name('register.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Juego 1: Astralquimium (hay que iniciar sesión; si no, redirige a /login)
Route::middleware('auth')
    ->prefix('games/astralquimium')
    ->name('game.astralquimium.')
    ->controller(AstralquimiumController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/planetas/{planeta}', 'planeta')->name('planeta');
        Route::get('/laboratorio', 'laboratorio')->name('laboratorio');
        Route::post('/elementos/{elemento}/descubrir', 'descubrirElemento')->name('elemento.descubrir');
        Route::post('/evaluar', 'evaluar')->name('evaluar');
        Route::post('/planetas-creados', 'guardarPlaneta')->name('planeta.guardar');
        Route::delete('/planetas-creados/{planetaCreado}', 'eliminarPlaneta')->name('planeta.eliminar');
    });

// Sesiones (partidas) de todos los juegos
Route::middleware('auth')->prefix('sesiones')->name('sesiones.')->group(function () {
    Route::post('/', [SesionController::class, 'iniciar'])->name('iniciar');
    Route::patch('/{sesion}', [SesionController::class, 'finalizar'])->name('finalizar');
});

// Rutas protegidas (requieren sesión iniciada)
// Cada juego, salvo Astralquimium, se abre al completar el anterior (middleware juego:ID)
Route::middleware(['auth'])->group(function () {
    // Orbital Music (juego 2)
    Route::middleware('juego:2')->group(function () {
        Route::get('/juegos/musica', [OrbitaController::class, 'index'])
            ->name('game.musica');

        Route::post('/juegos/musica/guardar', [OrbitaController::class, 'guardarOrbitas'])
            ->name('orbitas.guardar');
    });

    // Astral Recipe (juego 3)
    Route::middleware('juego:3')->group(function () {
        Route::get('/games/astral', [AstralRecipeController::class, 'index'])->name('game.astral');
    });

    // Constelaciones (juego 4)
    Route::middleware('juego:4')->group(function () {
        Route::get('/games/constelacion', [ControllerConstelaciones::class, 'index'])->name('game.constelaciones');
        Route::get('/games/constelacion/inventario', [ControllerInventario::class, 'index']);
    });
});
