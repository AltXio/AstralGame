<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Calcula los indicadores y la habitabilidad de una mezcla de elementos (Juego 1).
 * Reglas permisivas: la habitabilidad baja poco a poco según lo que falte o sobre.
 */
class EvaluadorPlaneta
{
    /**
     * @param  array<int,int>  $partes     id_elemento_quimico => número de partes
     * @param  Collection      $elementos  ElementoQuimico (con elementoAlquimico) indexados por id
     * @return array{total:int, ind:array, alq:array, alqTotal:int, dom:?string, habit:int, avisos:array}
     */
    public function evaluar(array $partes, Collection $elementos): array
    {
        $simbolos = config('astralquimium.simbolos');
        $p = [];                                            // símbolo => partes
        $alq = ['fuego' => 0, 'agua' => 0, 'tierra' => 0, 'aire' => 0];

        foreach ($partes as $id => $n) {
            $el = $elementos->get($id);
            if (! $el || $n < 1) {
                continue;
            }
            $sim = $simbolos[Str::slug($el->nombre)] ?? null;
            if ($sim) {
                $p[$sim] = ($p[$sim] ?? 0) + $n;
            }
            if ($el->elementoAlquimico) {
                $alq[Str::slug($el->elementoAlquimico->nombre)] += $n;
            }
        }

        $g = fn (string $s) => $p[$s] ?? 0;
        $total = array_sum($p);
        $alqTotal = array_sum($alq);
        $calor = $alqTotal ? ($alq['fuego'] + $alq['aire']) / $alqTotal : 0.5;   // fuego y aire calientan
        $ligeros = $g('H') + $g('He');

        // Atmósfera: gases que se quedan. H y He se escapan; el azufre la vuelve ácida.
        $atmosfera = $this->sat($g('N') + $g('O') + 0.7 * $g('C'), 2.5);
        $atmosfera *= 1 - min(0.7, 0.15 * max(0, $ligeros - 2));
        $atmosfera *= 1 - min(0.4, 0.1 * max(0, $g('S') - 1));

        // Agua: hacen falta hidrógeno y oxígeno a la vez (H2O).
        $agua = $this->sat(min($g('H'), $g('O')), 1.5);

        // Suelo: rocas y metales.
        $suelo = $this->sat($g('Si') + 0.8 * ($g('Mg') + $g('Al')) + $g('Fe'), 2);

        // Temperatura: lo mejor es un reparto templado entre lo caliente y lo frío.
        $temperatura = $alqTotal ? $this->clamp(100 - 250 * max(0, abs($calor - 0.5) - 0.15)) : 0;

        // Energía: un oxidante (oxígeno) junto a algo que reaccione con él.
        $reductor = $g('C') + $g('S') + $g('Fe') + $g('Mg') + 0.5 * $g('H');
        $energia = $this->sat(min($g('O'), $reductor), 1.5);

        $ind = array_map(fn ($v) => (int) round($this->clamp($v)), [
            'atmosfera' => $atmosfera, 'agua' => $agua, 'suelo' => $suelo,
            'temperatura' => $temperatura, 'energia' => $energia,
        ]);

        // Ingredientes de la vida: C, H, O y N. Con menos de cuatro, la nota baja.
        $chon = count(array_filter(['C', 'H', 'O', 'N'], fn ($s) => $g($s) > 0));
        $multVida = [0.4, 0.4, 0.65, 0.85, 1][$chon];

        // Gigante gaseoso: demasiado gas ligero y no hay superficie.
        $gigante = $total > 0 && $ligeros / $total > 0.5;

        $media = array_sum($ind) / count($ind);
        $habit = $total ? (int) round($media * $multVida * ($gigante ? 0.5 : 1)) : 0;

        $avisos = [];
        if (! $total) $avisos[] = 'Añade elementos con + para empezar a construir tu planeta.';
        if ($gigante) $avisos[] = 'Demasiado hidrógeno y helio: sin suelo sólido, esto sería un gigante gaseoso.';
        if ($total && min($g('H'), $g('O')) === 0) $avisos[] = 'Sin hidrógeno y oxígeno juntos no se forma agua líquida.';
        if ($total && ! $g('N')) $avisos[] = 'Falta nitrógeno: lo necesitan las proteínas y el ADN.';
        if ($total && ! $g('C')) $avisos[] = 'Falta carbono: la química de la vida se construye sobre él.';
        if ($total && $ind['suelo'] < 35) $avisos[] = 'Casi no tiene suelo: prueba con silicio, magnesio o hierro.';
        if ($total && $alqTotal && $ind['temperatura'] < 60) {
            $avisos[] = $calor > 0.5
                ? 'Demasiado caliente: suma elementos de Agua o de Tierra.'
                : 'Demasiado frío: suma elementos de Fuego o de Aire.';
        }
        if ($g('S') >= 3) $avisos[] = 'Mucho azufre: la atmósfera será ácida, como la de Venus.';
        if ($habit >= 80 && ! $avisos) $avisos[] = 'Un mundo con todas las condiciones para la vida.';

        return [
            'total' => $total,
            'ind' => $ind,
            'alq' => $alq,
            'alqTotal' => $alqTotal,
            'dom' => $this->dominante($alq),
            'habit' => $habit,
            'avisos' => array_slice($avisos, 0, 2),
        ];
    }

    private function dominante(array $alq): ?string
    {
        $mejor = null;
        $max = 0;
        foreach (['fuego', 'agua', 'tierra', 'aire'] as $k) {
            if ($alq[$k] > $max) {
                $max = $alq[$k];
                $mejor = $k;
            }
        }
        return $mejor;
    }

    private function sat(float $x, float $k): float
    {
        return 100 * (1 - exp(-$x / $k));
    }

    private function clamp(float $x, float $a = 0, float $b = 100): float
    {
        return max($a, min($b, $x));
    }
}
