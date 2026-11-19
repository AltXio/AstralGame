<?php

namespace App\Services;

use App\Models\ElementoQuimico;
use App\Models\PlanetaCreado;
use App\Models\PlanetaOrbita;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Prepara los datos que muestran las vistas del Juego 1 (colores, iconos, posiciones, símbolos),
 * para que las vistas Blade solo recorran y pinten arrays ya listos.
 * Los datos de presentación salen de config/astralquimium.php.
 */
class PresentacionAstralquimium
{
    /** Elementos alquímicos: nombre, color y svg, indexados por slug. */
    public function alquimicos(): array
    {
        return config('astralquimium.alquimicos');
    }

    /** Un elemento químico listo para pintar. */
    public function elemento(ElementoQuimico $e): array
    {
        $k = Str::slug($e->elementoAlquimico?->nombre ?? '');
        $alq = $this->alquimicos()[$k] ?? null;

        return [
            'id' => $e->id,
            'nombre' => $e->nombre,
            'simbolo' => config('astralquimium.simbolos.' . Str::slug($e->nombre), $e->nombre),
            'porque' => $e->descripcion,
            'dato' => $e->dato_curioso,
            'alq' => $k,
            'alqNombre' => $alq['nombre'] ?? '',
            'color' => $alq['color'] ?? '#d3dbea',
            'alqSvg' => $alq['svg'] ?? null,
        ];
    }

    /** Inventario del jugador (barra superior y laboratorio). */
    public function inventario(Collection $elementos): Collection
    {
        return $elementos->map(fn ($e) => $this->elemento($e))->values();
    }

    /** Sistema solar: el Sol y los planetas que orbitan. */
    public function sistema(Collection $planetas): array
    {
        $todos = $planetas->map(fn ($p) => $this->planeta($p));

        return [
            'sol' => $todos->firstWhere('slug', 'sol'),
            'orbitantes' => $todos->filter(fn ($p) => $p['orbita'])->values(),
        ];
    }

    /** Un planeta del sistema solar con su presentación. */
    public function planeta(PlanetaOrbita $p): array
    {
        $slug = Str::slug($p->nombre);
        $cfg = config("astralquimium.planetas.$slug", []);

        return [
            'id' => $p->id,
            'nombre' => $p->nombre,
            'descripcion' => $p->descripcion,
            'slug' => $slug,
            'url' => route('game.astralquimium.planeta', $p),
            'tinte' => $cfg['tinte'] ?? '#7a8bd0',
            'capa' => $cfg['capa'] ?? null,
            'glifo' => $cfg['glifo'] ?? '',
            'orbita' => $cfg['orbita'] ?? null,
        ];
    }

    /**
     * Ficha de un planeta: sus 4 elementos colocados en el dial y el recuento de cada elemento alquímico.
     *
     * @param  array<int>  $encontrados  ids de los elementos que el jugador ya tiene
     */
    public function ficha(PlanetaOrbita $planeta, array $encontrados): array
    {
        $angulos = [-90, 0, 90, 180];     // arriba, derecha, abajo, izquierda (sin orden especial)
        $equilibrio = collect($this->alquimicos())->map(fn ($a, $k) => $a + ['clave' => $k, 'cuenta' => 0])->all();

        $elementos = $planeta->elementosQuimicos->take(4)->values()->map(function ($e, $i) use ($angulos, $encontrados, &$equilibrio) {
            $d = $this->elemento($e);
            if (isset($equilibrio[$d['alq']])) {
                $equilibrio[$d['alq']]['cuenta']++;
            }
            $rad = deg2rad($angulos[$i]);

            return $d + [
                'pos' => sprintf('left:%.2f%%;top:%.2f%%', 50 + 40 * cos($rad), 50 + 40 * sin($rad)),
                'encontrado' => in_array($e->id, $encontrados),
                'urlDescubrir' => route('game.astralquimium.elemento.descubrir', $e),
            ];
        });

        return $this->planeta($planeta) + [
            'elementos' => $elementos,
            'equilibrio' => array_values($equilibrio),
        ];
    }

    /** Planetas creados por el jugador, con estado y elemento dominante. */
    public function creados(Collection $planetas): Collection
    {
        return $planetas->map(function (PlanetaCreado $p) {
            $k = Str::slug($p->elementoDominante?->nombre ?? '');
            $alq = $this->alquimicos()[$k] ?? null;

            return [
                'id' => $p->id,
                'nombre' => $p->nombre,
                'estado' => $p->habitabilidad ? 'Habitable' : 'No habitable',
                'color' => $alq['color'] ?? null,
                'svg' => $alq['svg'] ?? null,
            ];
        })->values();
    }

    /** Texto de progreso hacia el desbloqueo de los juegos 2 y 3. */
    public function progreso(int $creados): string
    {
        $umbral = config('astralquimium.umbral_desbloqueo');

        return $creados >= $umbral
            ? 'Los Juegos 2 y 3 están desbloqueados.'
            : 'Crea ' . ($umbral - $creados) . ' más para desbloquear los Juegos 2 y 3.';
    }
}
