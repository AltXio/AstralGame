<?php

namespace App\Services;

use App\Models\Sesion;
use App\Models\Usuario;

/**
 * Sesión (partida) de un usuario en un juego.
 * El id de la sesión se guarda en la sesión de Laravel: al cerrar sesión o volver a iniciarla
 * se empieza una partida nueva, y también tras 10 minutos sin actividad.
 * La duración se actualiza con cada aviso de actividad (SesionController::finalizar).
 */
class SesionJuego
{
    /**
     * Devuelve la sesión vigente o abre una nueva.
     *
     * @param  array  $inicial  valores de partida de las métricas del juego (p. ej. contadores a 0)
     * @return array{0: Sesion, 1: bool}  la sesión y si es nueva
     */
    public function abrir(Usuario $user, int $idJuego, ?int $idNivel = null, array $inicial = []): array
    {
        $existente = $this->actual($user, $idJuego, $idNivel);
        if ($existente) {
            return [$existente, false];
        }

        $sesion = Sesion::create($inicial + [
            'id_usuario' => $user->id,
            'id_juego' => $idJuego,
            'id_nivel' => $idNivel,
            'fecha_hora_inicio' => now(),
            'duracion_segundos' => 0,
            'completada' => false,
        ]);
        session([$this->clave($idJuego, $idNivel) => $sesion->id]);

        return [$sesion, true];
    }

    /** Sesión vigente del usuario en ese juego, sin crear ninguna. */
    public function actual(Usuario $user, int $idJuego, ?int $idNivel = null): ?Sesion
    {
        $id = session($this->clave($idJuego, $idNivel));
        $sesion = $id ? Sesion::find($id) : null;

        if (! $sesion || (int) $sesion->id_usuario !== (int) $user->id) {
            return null;
        }

        // último aviso de actividad = inicio + duración
        $ultimo = $sesion->fecha_hora_inicio->timestamp + (int) $sesion->duracion_segundos;

        return now()->timestamp - $ultimo < config('astralquimium.inactividad_segundos') ? $sesion : null;
    }

    private function clave(int $idJuego, ?int $idNivel): string
    {
        return "sesion_juego.$idJuego" . ($idNivel ? ".$idNivel" : '');
    }
}
