<?php

namespace App\Services;

use App\Models\Usuario;

/**
 * Qué juegos tiene desbloqueados cada usuario (tabla usuarios_juegos).
 * Astralquimium (juego 1) siempre está abierto; el resto se desbloquea al terminar el anterior.
 * Los administradores (rol distinto de jugador) lo ven todo desbloqueado.
 */
class ProgresoJuegos
{
    private const JUGADOR = 1;

    public function desbloqueado(?Usuario $user, int $idJuego): bool
    {
        if ($idJuego === config('astralquimium.id_juego')) {
            return true;
        }
        if (! $user) {
            return false;
        }
        if ((int) $user->role_id !== self::JUGADOR) {
            return true;
        }

        return $user->juegos()->where('juegos.id', $idJuego)->exists();
    }

    /** @return array<int,bool>  id_juego => desbloqueado, para los 4 juegos */
    public function estado(?Usuario $user): array
    {
        $estado = [];
        foreach ([1, 2, 3, 4] as $id) {
            $estado[$id] = $this->desbloqueado($user, $id);
        }

        return $estado;
    }

    /** Desbloquea juegos para siempre, sin tocar los que ya lo estaban. */
    public function desbloquear(Usuario $user, array $idsJuego): void
    {
        $ya = $user->juegos()->whereIn('juegos.id', $idsJuego)->pluck('juegos.id')->all();
        $nuevos = collect(array_diff($idsJuego, $ya))->mapWithKeys(fn ($id) => [$id => ['fecha_desbloqueo' => now()]]);
        if ($nuevos->isNotEmpty()) {
            $user->juegos()->attach($nuevos->all());
        }
    }

    /** Marca un juego como terminado (solo la primera vez). */
    public function completar(Usuario $user, int $idJuego): void
    {
        $this->desbloquear($user, [$idJuego]);
        $user->juegos()->wherePivotNull('fecha_completado')->updateExistingPivot($idJuego, ['fecha_completado' => now()]);
    }
}
