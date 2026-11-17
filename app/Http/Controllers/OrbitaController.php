<?php

namespace App\Http\Controllers;

use App\Models\PlanetaCreado;
use App\Models\PartituraOrbita;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class OrbitaController extends Controller
{
    /**
     * Carga la vista del Juego 2 con los planetas creados por el usuario y sus órbitas guardadas.
     */
    public function index()
    {
        $userId = Auth::id();

        // 1. Planetas creados por el usuario en el laboratorio (Juego 1)
        $planetasDisponibles = PlanetaCreado::with(['elementoDominante', 'elementosQuimicos'])
            ->where('id_usuario', $userId)
            ->get();

        // 2. Órbitas ya guardadas por este usuario en el Juego 2
        $orbitasGuardadas = PartituraOrbita::with(['planeta.elementoDominante', 'planeta.elementosQuimicos'])
            ->where('user_id', $userId)
            ->get();

        return view('games.musical.musica', compact('planetasDisponibles', 'orbitasGuardadas'));
    }

    /**
     * Guarda o actualiza todas las órbitas del usuario en la base de datos.
     */
    public function guardarOrbitas(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'orbitas' => 'present|array',
            'orbitas.*.id_planeta_creado' => 'required|exists:planetas_creados,id',
            'orbitas.*.numero_orbita' => 'required|integer|min:1|max:15',
            'orbitas.*.angulo' => 'required|numeric',
        ]);

        $userId = Auth::id();

        PartituraOrbita::where('user_id', $userId)->delete();

        foreach ($validated['orbitas'] as $orbita) {
            PartituraOrbita::create([
                'user_id' => $userId,
                'id_planeta_creado' => $orbita['id_planeta_creado'],
                'numero_orbita' => $orbita['numero_orbita'],
                'angulo' => $orbita['angulo'],
            ]);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Órbitas guardadas correctamente en la base de datos.'
        ]);
    }
}