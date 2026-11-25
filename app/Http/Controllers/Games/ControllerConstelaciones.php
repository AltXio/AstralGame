<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use App\Models\Constelaciones;
use App\Models\ElementoAlquimico;
use App\Services\SesionJuego;
use Illuminate\Http\Request;

class ControllerConstelaciones extends Controller
{
    //usamos with para hacer una carga anticipada, para hacer  las consultas con  
    // todas las tablas que tengan relacion de las cuales coloco
    public function index(Request $request, SesionJuego $sesiones)
    {
        $constelaciones = Constelaciones::with([
            'nivel',
            'cuerposCelestes' => fn($query) => $query->orderBy('id'),
            'cuerposCelestes.elementosQuimicos.elementoAlquimico',
        ])->get()->shuffle();

        $elementosAlquimicos = ElementoAlquimico::orderBy('id')->get();

        $idNivel = $constelaciones->first()?->id_nivel;

        [$sesion] = $sesiones->abrir($request->user(), 4, $idNivel);

        return view('games.constelacion.constelaciones', [
            'constelaciones'      => $constelaciones,
            'elementosAlquimicos' => $elementosAlquimicos,
            'sesionId'            => $sesion->id,
        ]);
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
    public function show(string $id)
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
