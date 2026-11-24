<?php

namespace App\Http\Controllers;

use App\Models\Melodia;
use App\Models\PlanetaCreado;
use App\Models\PartituraOrbita;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrbitaController extends Controller
{
    /**
     * Carga la vista del Juego 2 con los planetas del usuario
     * y todas sus melodías guardadas (con sus órbitas y planetas).
     */
    public function index()
    {
        $userId = Auth::id();

        // 1. Planetas creados por el usuario en el laboratorio (Juego 1)
        $planetasDisponibles = PlanetaCreado::with(['elementoDominante', 'elementosQuimicos'])
            ->where('id_usuario', $userId)
            ->get();

        // 2. Melodías guardadas por este usuario con sus órbitas y planetas asociados
        $melodiasGuardadas = Melodia::with([
                'orbitas.planeta.elementoDominante',
                'orbitas.planeta.elementosQuimicos'
            ])
            ->where('user_id', $userId)
            ->orderByDesc('id')
            ->get();

        return view('games.musical.musica', compact('planetasDisponibles', 'melodiasGuardadas'));
    }

    /**
     * Guarda una nueva melodía (o actualiza una existente si se envía id_melodia)
     * junto con todas sus órbitas y planetas en la base de datos.
     */
    public function guardarOrbitas(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'id_melodia' => 'nullable|integer|exists:melodias,id',
            'nombre_melodia' => 'required|string|max:100',
            'orbitas' => 'required|array|min:1',
            'orbitas.*.id_planeta_creado' => 'required|exists:planetas_creados,id',
            'orbitas.*.numero_orbita' => 'required|integer|min:1|max:15',
            'orbitas.*.angulo' => 'required|numeric',
        ]);

        $userId = Auth::id();

        $melodia = DB::transaction(function () use ($validated, $userId) {
            if (!empty($validated['id_melodia'])) {
                // Actualizar melodía existente del usuario
                $melodia = Melodia::where('id', $validated['id_melodia'])
                    ->where('user_id', $userId)
                    ->firstOrFail();

                $melodia->update([
                    'nombre' => $validated['nombre_melodia'],
                ]);

                // Reemplazar sus órbitas anteriores
                PartituraOrbita::where('id_melodia', $melodia->id)->delete();
            } else {
                // Crear una nueva melodía
                $melodia = Melodia::create([
                    'user_id' => $userId,
                    'nombre' => $validated['nombre_melodia'],
                ]);
            }

            foreach ($validated['orbitas'] as $orbita) {
                PartituraOrbita::create([
                    'user_id' => $userId,
                    'id_melodia' => $melodia->id,
                    'id_planeta_creado' => $orbita['id_planeta_creado'],
                    'numero_orbita' => $orbita['numero_orbita'],
                    'angulo' => $orbita['angulo'],
                ]);
            }

            return $melodia->load([
                'orbitas.planeta.elementoDominante',
                'orbitas.planeta.elementosQuimicos'
            ]);
        });

        return response()->json([
            'status' => 'ok',
            'message' => 'Melodía guardada correctamente.',
            'melodia' => $melodia,
        ]);
    }
}