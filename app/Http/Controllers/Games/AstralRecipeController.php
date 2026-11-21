<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use App\Models\ElementoQuimico;
use App\Models\Nivel;
use App\Models\Signo;
use Illuminate\Http\Request;

class AstralRecipeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $niveles = Nivel::where('id_juego', 3)->get();
        $signos = Signo::with('elementoAlquimico')->get();

        // planeta regente => [símbolo, nombre del metal en elementos_quimicos]
        $planetas = [
            'Marte' => ['♂', 'Hierro'],
            'Sol' => ['☉', 'Oro'],
            'Luna' => ['☽', 'Plata'],
            'Venus'=> ['♀', 'Cobre'],
            'Mercurio' => ['☿', 'Mercurio'],
            'Júpiter' => ['♃', 'Estaño'],
            'Saturno' => ['♄', 'Plomo'],
        ];

        // Consulta con todos los metales, indexados por nombre
        $metales = ElementoQuimico::whereIn('nombre', array_column($planetas, 1))
            ->get()
            ->keyBy('nombre');

        foreach ($signos as $signo) {
            [$simbolo, $nombreMetal] = $planetas[$signo->planeta_regente] ?? [null, null];

            $signo->metal = $nombreMetal
                ? "{$simbolo} {$nombreMetal} / {$signo->planeta_regente}"
                : '';
        }

        return view('games.astral_recipe.astral', compact('niveles', 'signos'));
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