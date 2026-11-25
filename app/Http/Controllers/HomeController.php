<?php

namespace App\Http\Controllers;

use App\Services\ProgresoJuegos;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    /** Landing: indica qué juegos están bloqueados para el usuario (id_juego => bool). */
    public function index(Request $request, ProgresoJuegos $progreso): View
    {
        return view('landing_page.welcome', [
            'bloqueado' => array_map(fn ($abierto) => ! $abierto, $progreso->estado($request->user())),
            'esAdmin' => (bool) $request->user()?->esAdmin(),
        ]);
    }
}
