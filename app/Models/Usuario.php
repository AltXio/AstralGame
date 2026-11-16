<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/** Usuario del login (tabla usuarios). Sustituye a App\Models\User, que ya no se usa. */
class Usuario extends Authenticatable
{
    use Notifiable;

    protected $table = 'usuarios';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class, 'role_id');
    }

    /** Elementos químicos que el jugador ha descubierto (Juego 1). */
    public function inventario(): BelongsToMany
    {
        return $this->belongsToMany(
            ElementoQuimico::class,
            'inventario_usuario_elementos_quimicos',
            'id_usuario',
            'id_elemento_quimico'
        );
    }

    /** Planetas que el jugador ha creado en el laboratorio (Juego 1). */
    public function planetasCreados(): HasMany
    {
        return $this->hasMany(PlanetaCreado::class, 'id_usuario');
    }

    /** Progreso por juego: fecha_desbloqueo y fecha_completado. */
    public function juegos(): BelongsToMany
    {
        return $this->belongsToMany(Juego::class, 'usuarios_juegos', 'id_usuario', 'id_juego')
            ->withPivot('fecha_desbloqueo', 'fecha_completado');
    }

    public function sesiones(): HasMany
    {
        return $this->hasMany(Sesion::class, 'id_usuario');
    }
}
