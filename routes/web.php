<?php

use App\Http\Controllers\Games\ControllerConstelaciones;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OrbitaController;

// Rutas públicas 
Route::get('/', function () {
    return view('landing_page.welcome');
})->name('home');

Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/register', [LoginController::class, 'showRegister'])->name('register');
Route::post('/register', [LoginController::class, 'register'])->name('register.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas protegidas (requieren sesión iniciada)
Route::middleware(['auth'])->group(function () {
    Route::get('/juegos/musica', [OrbitaController::class, 'index'])
        ->name('game.musica');

Route::get('/games/musical', function () {
    return view('games.musical.musica');
})->name('game.musica');

Route::post('/juegos/musica/guardar', [OrbitaController::class, 'guardarOrbitas'])
        ->name('orbitas.guardar');
        
Route::get('/games/constelacion',[ControllerConstelaciones::class, 'index']) ->name('game.constelaciones');;
});