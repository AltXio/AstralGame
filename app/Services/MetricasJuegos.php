<?php

namespace App\Services;

use App\Models\Juego;
use App\Models\Sesion;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Métricas de los juegos para el panel de administración, calculadas sobre la tabla sesiones.
 * Las métricas que un juego no mide están en NULL y se muestran como "—".
 */
class MetricasJuegos
{
    /** Resumen por juego (incluye los juegos que aún no tienen sesiones). */
    public function resumen(): Collection
    {
        $datos = $this->agregados('id_juego')->groupBy('id_juego')->get()->keyBy('id_juego');

        return Juego::orderBy('id')->get()->map(function ($j) use ($datos) {
            $d = $datos->get($j->id);

            return ['id' => $j->id, 'nombre' => $j->nombre] + $this->formatear($d);
        });
    }

    /** Resumen por nivel de un juego (solo los juegos con niveles). */
    public function porNivel(int $idJuego): Collection
    {
        $datos = $this->agregados('id_nivel')->where('id_juego', $idJuego)->whereNotNull('id_nivel')->groupBy('id_nivel')->get()->keyBy('id_nivel');

        return DB::table('niveles')->where('id_juego', $idJuego)->orderBy('id')->get()->map(function ($n) use ($datos) {
            return ['id' => $n->id, 'nombre' => $n->nombre] + $this->formatear($datos->get($n->id));
        });
    }

    /** Sesiones (partidas) más recientes, con filtro opcional por juego y nivel. */
    public function sesiones(?int $idJuego, ?int $idNivel, int $porPagina = 15): LengthAwarePaginator
    {
        return Sesion::with(['user:id,name', 'juego:id,nombre', 'nivel:id,nombre'])
            ->when($idJuego, fn ($q) => $q->where('id_juego', $idJuego))
            ->when($idNivel, fn ($q) => $q->where('id_nivel', $idNivel))
            ->orderByDesc('id')
            ->paginate($porPagina)
            ->withQueryString()
            ->through(fn (Sesion $s) => [
                'id' => $s->id,
                'usuario' => $s->user?->name ?? '—',
                'juego' => $s->juego?->nombre ?? '—',
                'nivel' => $s->nivel?->nombre ?? '—',
                'fecha' => $s->fecha_hora_inicio?->format('d/m/Y H:i'),
                'duracion' => $this->duracion($s->duracion_segundos),
                'puntuacion' => $this->numero($s->puntuacion),
                'completada' => (bool) $s->completada,
                'intentos' => $s->intentos ?? '—',
                'errores' => $s->errores ?? '—',
                'ayudas' => $s->ayudas ?? '—',
            ]);
    }

    /**
     * Sesiones de un juego en bruto, listas para el pipeline de Python (Pyodide).
     * Los nombres son los del enunciado de métricas. No se limpia nada aquí:
     * los NULL y los valores raros se dejan tal cual para que la fase de limpieza los trate.
     *
     * @return array<int, array<string, mixed>>
     */
    public function dataset(int $idJuego): array
    {
        return DB::table('sesiones')
            ->join('usuarios', 'usuarios.id', '=', 'sesiones.id_usuario')
            ->where('sesiones.id_juego', $idJuego)
            ->orderBy('sesiones.id')
            ->get([
                'usuarios.name as username',
                'sesiones.id as session_id',
                'sesiones.fecha_hora_inicio as date_time',
                'sesiones.duracion_segundos as session_length',
                'sesiones.puntuacion as final_score',
                'sesiones.intentos as n_attempts',
                'sesiones.errores as errors_made',
                'sesiones.ayudas as help_clicks',
                'sesiones.completada as completed',
            ])
            ->map(fn ($f) => [
                'username' => $f->username,
                'session_id' => (int) $f->session_id,
                'date_time' => $f->date_time,
                'session_length' => $f->session_length === null ? null : (int) $f->session_length,
                'final_score' => $f->final_score === null ? null : (float) $f->final_score,
                'n_attempts' => $f->n_attempts === null ? null : (int) $f->n_attempts,
                'errors_made' => $f->errors_made === null ? null : (int) $f->errors_made,
                'help_clicks' => $f->help_clicks === null ? null : (int) $f->help_clicks,
                'completed' => (int) $f->completed,
            ])
            ->all();
    }

    private function agregados(string $columna)
    {
        return DB::table('sesiones')->select([
            $columna,
            DB::raw('count(*) as sesiones'),
            DB::raw('count(distinct id_usuario) as jugadores'),
            DB::raw('avg(cast(duracion_segundos as float)) as duracion'),
            DB::raw('avg(cast(puntuacion as float)) as puntuacion'),
            DB::raw('sum(cast(completada as int)) as completadas'),
            DB::raw('avg(cast(intentos as float)) as intentos'),
            DB::raw('avg(cast(errores as float)) as errores'),
            DB::raw('avg(cast(ayudas as float)) as ayudas'),
        ]);
    }

    /** Pasa una fila agregada (o null si no hay sesiones) a valores listos para mostrar. */
    private function formatear(?object $d): array
    {
        $sesiones = (int) ($d->sesiones ?? 0);

        return [
            'sesiones' => $sesiones,
            'jugadores' => (int) ($d->jugadores ?? 0),
            'duracion' => $this->duracion($d->duracion ?? null),
            'puntuacion' => $this->numero($d->puntuacion ?? null),
            'completadas' => $sesiones ? round(100 * ($d->completadas ?? 0) / $sesiones) . ' %' : '—',
            'intentos' => $this->numero($d->intentos ?? null),
            'errores' => $this->numero($d->errores ?? null),
            'ayudas' => $this->numero($d->ayudas ?? null),
        ];
    }

    private function duracion(null|int|float|string $segundos): string
    {
        if ($segundos === null) {
            return '—';
        }
        $s = (int) round((float) $segundos);

        return intdiv($s, 60) . ' min ' . str_pad((string) ($s % 60), 2, '0', STR_PAD_LEFT) . ' s';
    }

    private function numero(null|int|float|string $valor): string
    {
        return $valor === null ? '—' : rtrim(rtrim(number_format((float) $valor, 1, ',', '.'), '0'), ',');
    }
}
