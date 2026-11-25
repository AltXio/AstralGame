<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use App\Models\Nivel;
use App\Models\Receta;
use App\Models\Sesion;
use App\Models\Signo;
use App\Models\UsuariosReceta;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
            $nivel->descripcion =$nivel->descripcion ?? ($descripciones[$nivel->nombre] ?? 'Selecciona este nivel');
            return $nivel;
        });

        $nivelActualId = $request->query('nivel',$niveles->first()->id ?? 1);

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

        foreach ($signos as$signo) {
            [$simbolo,$nombreMetal] = $planetas[$signo->planeta_regente] ?? ['?', 'Desconocido'];
            $signo->simbolo_metal =$simbolo;
            $signo->nombre_metal =$nombreMetal;
            $signo->metal = "{$simbolo} {$nombreMetal} / {$signo->planeta_regente}";
        }

        // 2. Carreguem les receptes del nivell seleccionat amb les seves relacions
        $recetas = Receta::with(['signo.elementoAlquimico', 'elementoQuimico'])
            ->where('id_nivel', $nivelActualId)
            ->get();

        return view('games.astral_recipe.astral', compact('niveles', 'signos', 'recetas', 'nivelActualId'));
    }

    public function completarReceta(Request $request)
    {
        $data =$request->validate([
            'id_receta' => ['required', 'integer', 'exists:recetas,id'],
            'id_nivel' => ['required', 'integer', Rule::exists('niveles', 'id')->where('id_juego', 3)],
        ]);

        // Guardar receptes completades per l'usuari
        UsuariosReceta::firstOrCreate(
            [
                'id_usuario' => $request->user()->id,
                'id_receta' => $data['id_receta'],
                'id_nivel' => $data['id_nivel'],
            ],
            ['fecha_completado' => now()]
        );

        return response()->json(['ok' => true]);
    }

    public function guardarResultado(Request $request, Sesion$sesion)
    {
        abort_unless((int) $sesion->id_usuario === (int)$request->user()->id, 403);
        abort_unless((int) $sesion->id_juego === 3, 403);

        $data =$request->validate([
            'puntuacion' => ['required', 'integer', 'min:0'],
            'errores' => ['required', 'integer', 'min:0'],
            'intentos' => ['required', 'integer', 'min:0'],
            'completada' => ['required', 'boolean'],
        ]);

        $sesion->puntuacion = $data['puntuacion'];$sesion->errores = $data['errores'];$sesion->intentos = $data['intentos'];$sesion->completada = $data['completada'];$sesion->duracion_segundos = max(0, now()->timestamp - $sesion->fecha_hora_inicio->timestamp);$sesion->save();

        return response()->noContent();
    }
}
