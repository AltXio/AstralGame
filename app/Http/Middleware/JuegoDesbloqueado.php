<?php

namespace App\Http\Middleware;

use App\Services\ProgresoJuegos;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Uso: ->middleware('juego:3'). Si el usuario aún no tiene ese juego desbloqueado, vuelve al inicio. */
class JuegoDesbloqueado
{
    public function __construct(private ProgresoJuegos $progreso) {}

    public function handle(Request $request, Closure $next, int $idJuego): Response
    {
        if (! $this->progreso->desbloqueado($request->user(), $idJuego)) {
            return redirect()->route('home')->with('bloqueado', 'Este juego aún está bloqueado: completa antes el juego anterior.');
        }

        return $next($request);
    }
}
