<?php

use App\Http\Controllers\Games\ControllerConstelaciones;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;

//auth de prueba
// Route::Middleware(['auth'])-> group(function() { 

Route::get('/', function () {
    return view('landing_page.welcome');
})->name('home');

Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::get('/register', [LoginController::class, 'showRegister'])->name('register');
Route::post('/register', [LoginController::class, 'register'])->name('register.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::get('/games/musical', function () {
    return view('games.musical.musica');
})->name('game.musica');

Route::get('/games/constelacion',[ControllerConstelaciones::class, 'index']);



//});
