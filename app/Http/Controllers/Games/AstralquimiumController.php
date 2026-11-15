<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use App\Models\ElementoQuimico;
use App\Models\PlanetaCreado;
use App\Models\PlanetaOrbita;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

/**
 * Juego 1: Astralquimium.
 *   - sistema solar (vista 1), ficha de planeta (vista 2) y laboratorio (vista 3)
 *   - inventario de elementos descubiertos y planetas creados por el jugador
 */
class AstralquimiumController extends Controller
{
    /** Vista 1: sistema solar con el Sol y los planetas. */
    public function index(): View
    {
        return view('games.astralquimium.index', [
            'planetas' => PlanetaOrbita::all(),
        ]);
    }

    /** Vista 2: ficha de un planeta con sus 4 elementos químicos. */
    public function planeta(PlanetaOrbita $planeta): View
    {
        return view('games.astralquimium.planeta', [
            'planeta' => $planeta->load('elementosQuimicos.elementoAlquimico'),
        ]);
    }

    /** Vista 3: laboratorio (inventario del jugador y sus planetas creados). */
    public function laboratorio(Request $request): View
    {
        $user = $request->user();

        return view('games.astralquimium.laboratorio', [
            'inventario' => $user ? $user->inventario()->with('elementoAlquimico')->get() : collect(),
            'planetasCreados' => $user ? $user->planetasCreados()->with('elementoDominante')->get() : collect(),
        ]);
    }

    /** Guarda en el inventario un elemento descubierto (id_usuario + id_elemento_quimico). */
    public function descubrirElemento(Request $request, ElementoQuimico $elemento): Response
    {
        // TODO: $request->user()->inventario()->syncWithoutDetaching([$elemento->id]);
        return response()->noContent();
    }

    /** Crea un planeta: planetas_creados + planetas_creados_elementos_quimicos (con partes). */
    public function guardarPlaneta(Request $request): Response
    {
        // TODO: validar nombre y mezcla (máximo 12 partes), calcular habitabilidad y elemento dominante.
        return response()->noContent();
    }

    /** Elimina un planeta creado del jugador autenticado. */
    public function eliminarPlaneta(Request $request, PlanetaCreado $planetaCreado): Response
    {
        // TODO: comprobar que pertenece al usuario antes de borrar.
        return response()->noContent();
    }
}
