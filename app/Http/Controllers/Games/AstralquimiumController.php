<?php

namespace App\Http\Controllers\Games;

use App\Http\Controllers\Controller;
use App\Models\ElementoAlquimico;
use App\Models\ElementoQuimico;
use App\Models\PlanetaCreado;
use App\Models\PlanetaOrbita;
use App\Models\Sesion;
use App\Models\Usuario;
use App\Services\EvaluadorPlaneta;
use App\Services\PresentacionAstralquimium;
use App\Services\ProgresoJuegos;
use App\Services\SesionJuego;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Juego 1: Astralquimium.
 *   - sistema solar (vista 1), ficha de planeta (vista 2) y laboratorio (vista 3)
 *   - inventario de elementos descubiertos y planetas creados por el jugador
 * Todas las rutas exigen sesión iniciada (middleware auth).
 */
class AstralquimiumController extends Controller
{
    public function __construct(private SesionJuego $sesiones, private ProgresoJuegos $progreso) {}

    /** Vista 1: sistema solar con el Sol y los planetas. */
    public function index(Request $request, PresentacionAstralquimium $vista): View
    {
        $sesion = $this->entrar($request->user());

        return view('games.astralquimium.index', $vista->sistema(PlanetaOrbita::orderBy('id')->get()) + [
            'sesionId' => $sesion->id,
            'inventario' => $vista->inventario($this->inventario($request->user())),
        ]);
    }

    /** Vista 2: ficha de un planeta con sus 4 elementos químicos. */
    public function planeta(Request $request, PlanetaOrbita $planeta, PresentacionAstralquimium $vista): View
    {
        $sesion = $this->entrar($request->user());
        $inventario = $this->inventario($request->user());
        $planeta->load('elementosQuimicos.elementoAlquimico');

        return view('games.astralquimium.planeta', [
            'ficha' => $vista->ficha($planeta, $inventario->pluck('id')->all()),
            'alquimicos' => $vista->alquimicos(),
            'sesionId' => $sesion->id,
            'inventario' => $vista->inventario($inventario),
        ]);
    }

    /** Vista 3: laboratorio (inventario del jugador y sus planetas creados). */
    public function laboratorio(Request $request, PresentacionAstralquimium $vista): View
    {
        $user = $request->user();
        $sesion = $this->entrar($user);
        $creados = $user->planetasCreados()->with('elementoDominante')->orderBy('id')->get();

        return view('games.astralquimium.laboratorio', [
            'inventario' => $vista->inventario($this->inventario($user)),
            'creados' => $vista->creados($creados),
            'progreso' => $vista->progreso($creados->count()),
            'alquimicos' => $vista->alquimicos(),
            'sesionId' => $sesion->id,
            'umbral' => config('astralquimium.umbral_desbloqueo'),
            'maxPartes' => config('astralquimium.max_partes'),
        ]);
    }

    /** Guarda en el inventario un elemento descubierto (id_usuario + id_elemento_quimico). */
    public function descubrirElemento(Request $request, ElementoQuimico $elemento): JsonResponse
    {
        $request->user()->inventario()->syncWithoutDetaching([$elemento->id]);

        // Cada vez que el jugador abre la ficha de un elemento cuenta como una ayuda consultada
        $this->sumarEnSesion($request->user(), 'ayudas');

        return response()->json(['id' => $elemento->id]);
    }

    /** Calcula en vivo los indicadores de una mezcla (no guarda nada). */
    public function evaluar(Request $request, EvaluadorPlaneta $evaluador): JsonResponse
    {
        $partes = $this->partesValidas($request);

        return response()->json($evaluador->evaluar($partes, $this->elementosDe($partes)));
    }

    /** Crea un planeta: planetas_creados + planetas_creados_elementos_quimicos (con partes). */
    public function guardarPlaneta(Request $request, EvaluadorPlaneta $evaluador): JsonResponse
    {
        $user = $request->user();
        $request->validate(['nombre' => ['nullable', 'string', 'max:24']]);
        $partes = $this->partesValidas($request);

        $propios = $user->inventario()->pluck('elementos_quimicos.id')->all();
        if (array_diff(array_keys($partes), $propios)) {
            return response()->json(['message' => 'Solo puedes usar elementos que hayas descubierto.'], 422);
        }

        $ev = $evaluador->evaluar($partes, $this->elementosDe($partes));
        $dominante = $ev['dom'] ? ElementoAlquimico::all()->first(fn ($a) => Str::slug($a->nombre) === $ev['dom']) : null;
        $nombre = trim((string) $request->input('nombre')) ?: 'Planeta ' . ($user->planetasCreados()->count() + 1);

        $planeta = DB::transaction(function () use ($user, $nombre, $ev, $dominante, $partes) {
            $planeta = PlanetaCreado::create([
                'id_usuario' => $user->id,
                'nombre' => $nombre,
                'habitabilidad' => $ev['habit'] >= config('astralquimium.umbral_habitable'),
                'id_elemento_alquimico' => $dominante?->id,
            ]);
            $planeta->elementosQuimicos()->attach(
                collect($partes)->map(fn ($n) => ['partes' => $n])->all()
            );

            return $planeta;
        });

        // Con el umbral de planetas el juego 1 queda terminado y se abren los siguientes
        $total = $user->planetasCreados()->count();
        $desbloqueados = $total >= config('astralquimium.umbral_desbloqueo');
        if ($desbloqueados) {
            $this->progreso->completar($user, config('astralquimium.id_juego'));
            $this->progreso->desbloquear($user, config('astralquimium.juegos_desbloqueados'));
        }
        $this->registrarEnSesion($user, $ev['habit'], $desbloqueados);

        return response()->json([
            'id' => $planeta->id,
            'nombre' => $planeta->nombre,
            'habitable' => $planeta->habitabilidad,
            'habitabilidad' => $ev['habit'],
            'dominante' => $ev['dom'],
            'total' => $total,
            'desbloqueados' => $desbloqueados,
        ], 201);
    }

    /** Elimina un planeta creado del jugador autenticado. */
    public function eliminarPlaneta(Request $request, PlanetaCreado $planetaCreado): JsonResponse
    {
        abort_unless((int) $planetaCreado->id_usuario === (int) $request->user()->id, 403);

        try {
            $planetaCreado->delete();
        } catch (QueryException $e) {
            // Otro juego (órbitas o partituras) ya usa este planeta
            return response()->json(['message' => 'Este planeta ya se usa en otro juego y no se puede quitar.'], 409);
        }

        return response()->json(['id' => $planetaCreado->id]);
    }

    /** Elementos descubiertos por el jugador, con su elemento alquímico. */
    private function inventario(Usuario $user)
    {
        return $user->inventario()->with('elementoAlquimico')->orderBy('elementos_quimicos.id')->get();
    }

    /** Valida la mezcla enviada: id_elemento => partes enteras, entre 1 y max_partes en total. */
    private function partesValidas(Request $request): array
    {
        $max = config('astralquimium.max_partes');
        $request->validate([
            'partes' => ['required', 'array', 'min:1'],
            'partes.*' => ['integer', 'min:1', 'max:' . $max],
        ]);

        $partes = [];
        foreach ($request->input('partes') as $id => $n) {
            $partes[(int) $id] = (int) $n;
        }
        abort_if(array_sum($partes) > $max, 422, "La mezcla no puede superar {$max} partes.");

        return $partes;
    }

    private function elementosDe(array $partes)
    {
        return ElementoQuimico::with('elementoAlquimico')->whereIn('id', array_keys($partes))->get()->keyBy('id');
    }

    /**
     * Entrada a cualquier vista del juego: abre la partida del usuario o reutiliza la vigente.
     * Al empezar una partida nueva (nuevo inicio de sesión o 10 minutos de inactividad)
     * el inventario se vacía; los planetas creados se conservan porque los usan los demás juegos.
     */
    private function entrar(Usuario $user): Sesion
    {
        // Las métricas de esta partida empiezan a 0 (y no a NULL) para poder promediarlas
        [$sesion, $nueva] = $this->sesiones->abrir($user, config('astralquimium.id_juego'), null, [
            'puntuacion' => 0, 'intentos' => 0, 'errores' => 0, 'ayudas' => 0,
        ]);
        if ($nueva) {
            $user->inventario()->detach();
        }

        return $sesion;
    }

    /**
     * Suma un planeta creado a las métricas de la sesión activa:
     *   intentos = planetas creados, errores = planetas no habitables,
     *   puntuacion = mejor habitabilidad conseguida, completada = se alcanzó el umbral de planetas.
     */
    private function registrarEnSesion(Usuario $user, int $habitabilidad, bool $completada): void
    {
        $sesion = $this->sesiones->actual($user, config('astralquimium.id_juego'));
        if (! $sesion) {
            return;
        }
        $sesion->intentos = ($sesion->intentos ?? 0) + 1;
        if ($habitabilidad < config('astralquimium.umbral_habitable')) {
            $sesion->errores = ($sesion->errores ?? 0) + 1;
        }
        $sesion->puntuacion = max((float) $sesion->puntuacion, $habitabilidad);
        $sesion->completada = $sesion->completada || $completada;
        $sesion->save();
    }

    /** Suma 1 a un contador (errores, ayudas...) de la sesión activa del juego. */
    private function sumarEnSesion(Usuario $user, string $campo): void
    {
        $sesion = $this->sesiones->actual($user, config('astralquimium.id_juego'));
        if ($sesion) {
            $sesion->{$campo} = ($sesion->{$campo} ?? 0) + 1;
            $sesion->save();
        }
    }
}
