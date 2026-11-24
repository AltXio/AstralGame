<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use App\Models\Constelaciones;
use App\Models\ElementoAlquimico;
use Illuminate\Http\Request;

class ControllerConstelaciones extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $constelaciones = Constelaciones::with([
            'nivel',
            'cuerposCelestes' => function ($query) {
                $query->orderBy('id');
            },
            'cuerposCelestes.elementosQuimicos.elementoAlquimico',
        ])->orderBy('id')->get()->shuffle();

        $elementosAlquimicos = ElementoAlquimico::orderBy('id')->get();

        return view('games.constelacion.constelaciones', compact('constelaciones', 'elementosAlquimicos'));
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
