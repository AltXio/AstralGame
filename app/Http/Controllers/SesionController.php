<?php

namespace App\Http\Controllers;

use App\Models\Sesion;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Registro de partidas de cualquier juego (tabla sesiones).
 * Una fila = una partida de un juego (o de un nivel de ese juego).
 */
class SesionController extends Controller
{
    /** Al empezar una partida: id_usuario, id_juego, id_nivel (si hay) y fecha_hora_inicio. */
    public function iniciar(Request $request): Response
    {
        // TODO: validar id_juego / id_nivel y crear la sesión del usuario autenticado.
        return response()->noContent();
    }

    /** Al terminar la partida: duracion_segundos, puntuacion, completada y contadores. */
    public function finalizar(Request $request, Sesion $sesion): Response
    {
        // TODO: comprobar que la sesión es del usuario y guardar las métricas.
        return response()->noContent();
    }
}
