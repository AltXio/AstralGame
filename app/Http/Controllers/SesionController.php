<?php

namespace App\Http\Controllers;

use App\Models\Sesion;
use App\Services\SesionJuego;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Registro de partidas de cualquier juego (tabla sesiones).
 * Una fila = una partida de un juego (o de un nivel de ese juego).
 * La partida termina al cerrar sesión o tras 10 minutos sin actividad (ver SesionJuego):
 * la duración se actualiza con cada aviso de actividad.
 */
class SesionController extends Controller
{
    /** Al empezar: reutiliza la sesión vigente del usuario en ese juego o abre una nueva. */
    public function iniciar(Request $request, SesionJuego $sesiones): JsonResponse
    {
        $data = $request->validate([
            'id_juego' => ['required', 'integer', 'exists:juegos,id'],
            'id_nivel' => ['nullable', 'integer', 'exists:niveles,id'],
        ]);

        [$sesion] = $sesiones->abrir($request->user(), $data['id_juego'], $data['id_nivel'] ?? null);

        return response()->json(['id' => $sesion->id]);
    }

    /** Actividad o fin de la partida: actualiza duracion_segundos. */
    public function finalizar(Request $request, Sesion $sesion): Response
    {
        abort_unless((int) $sesion->id_usuario === (int) $request->user()->id, 403);

        $sesion->duracion_segundos = max(0, now()->timestamp - $sesion->fecha_hora_inicio->timestamp);
        $sesion->save();

        return response()->noContent();
    }
}
