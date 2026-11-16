<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Juego extends Model
{
    protected $table = 'juegos';

    public $timestamps = false;

    protected $fillable = ['nombre'];

    public function niveles(): HasMany
    {
        return $this->hasMany(Nivel::class, 'id_juego');
    }

    public function sesiones(): HasMany
    {
        return $this->hasMany(Sesion::class, 'id_juego');
    }

    /** Progreso por usuario: fecha_desbloqueo y fecha_completado. */
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'usuarios_juegos', 'id_juego', 'id_usuario')
            ->withPivot('fecha_desbloqueo', 'fecha_completado');
    }
}
