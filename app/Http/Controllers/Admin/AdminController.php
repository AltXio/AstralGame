<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Juego;
use App\Models\Nivel;
use App\Services\MetricasJuegos;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Panel de administración: métricas de los juegos (administrador y superadministrador). */
class AdminController extends Controller
{
    /** Sesiones en bruto (JSON) que carga el pipeline de Python en el navegador. Por defecto, el Juego 1. */
    public function datos(Request $request, MetricasJuegos $metricas): JsonResponse
    {
        $idJuego = $request->integer('juego') ?: config('astralquimium.id_juego');

        return response()->json($metricas->dataset($idJuego));
    }

    public function index(Request $request, MetricasJuegos $metricas): View
    {
        $idJuego = $request->integer('juego') ?: null;
        $idNivel = $request->integer('nivel') ?: null;

        return view('admin.metricas', [
            'pyodideUrl' => config('metricas.pyodide_url'),
            'detalle' => config('metricas.detalle'),
            'resumen' => $metricas->resumen(),
            'porNivel' => $idJuego ? $metricas->porNivel($idJuego) : collect(),
            'sesiones' => $metricas->sesiones($idJuego, $idNivel),
            'juegos' => Juego::orderBy('id')->get(['id', 'nombre']),
            'niveles' => $idJuego ? Nivel::where('id_juego', $idJuego)->orderBy('id')->get(['id', 'nombre']) : collect(),
            'idJuego' => $idJuego,
            'idNivel' => $idNivel,
        ]);
    }
}
