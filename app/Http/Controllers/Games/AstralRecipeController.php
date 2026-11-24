<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use App\Models\Nivel;
use App\Models\Receta;
use App\Models\Signo;
use Illuminate\Http\Request;

class AstralRecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $descripciones = [
            'Aprendiz' => 'Modo clásico: tienes el elemento y el metal visibles.',
            'Alquimista' => 'El elemento está oculto. Dedúcelo leyendo la receta y su metal principal.',
            'Maestro' => 'Sin pistas de metal en las cartas. ¡Demuestra tu maestría!',
        ];

        $niveles = Nivel::where('id_juego', 3)->get()->map(function ($nivel) use ($descripciones) {
            $nivel->descripcion = $nivel->descripcion ?? ($descripciones[$nivel->nombre] ?? 'Selecciona este nivel');
            return $nivel;
        });

        $nivelActualId = $request->query('nivel', $niveles->first()->id ?? 1);

        // 1. Carreguem els signes amb el seu element alquímic (Foc, Aigua, Terra, Aire)
        $signos = Signo::with('elementoAlquimico')->get();

        $planetas = [
            'Marte' => ['♂', 'Hierro'],
            'Sol' => ['☉', 'Oro'],
            'Luna' => ['☽', 'Plata'],
            'Venus' => ['♀', 'Cobre'],
            'Mercurio' => ['☿', 'Mercurio'],
            'Júpiter' => ['♃', 'Estaño'],
            'Saturno' => ['♄', 'Plomo'],
        ];

        foreach ($signos as $signo) {
            [$simbolo, $nombreMetal] = $planetas[$signo->planeta_regente] ?? ['?', 'Desconocido'];
            $signo->simbolo_metal = $simbolo;
            $signo->nombre_metal = $nombreMetal;
            $signo->metal = "{$simbolo} {$nombreMetal} / {$signo->planeta_regente}";
        }

        // 2. Carreguem les receptes del nivell seleccionat amb les seves relacions
        $recetas = Receta::with(['signo.elementoAlquimico', 'elementoQuimico'])
            ->where('id_nivel', $nivelActualId)
            ->get();

        return view('games.astral_recipe.astral', compact('niveles', 'signos', 'recetas', 'nivelActualId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
