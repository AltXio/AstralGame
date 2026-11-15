<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Una fila = una partida de un juego (o de un nivel de ese juego).
 * Las métricas que un juego no mide quedan en NULL.
 */
class Sesion extends Model
{
    protected $table = 'sesiones';

    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_juego',
        'id_nivel',
        'fecha_hora_inicio',
        'duracion_segundos',
        'puntuacion',
        'completada',
        'intentos',
        'errores',
        'ayudas',
    ];

    protected function casts(): array
    {
        return [
            'fecha_hora_inicio' => 'datetime',
            'completada' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function juego(): BelongsTo
    {
        return $this->belongsTo(Juego::class, 'id_juego');
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class, 'id_nivel');
    }
}
