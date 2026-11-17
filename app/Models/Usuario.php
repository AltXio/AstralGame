<?php

namespace App\Models;

use App\Models\ElementoQuimico;
use App\Models\Juego;
use App\Models\PlanetaCreado;
use App\Models\Rol;
use App\Models\Sesion;
use App\Models\UsuarioReceta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/** Usuario del login (tabla usuarios). Sustituye a App\Models\User, que ya no se usa. */
class Usuario extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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

    /** Orbitas para el juego 2 (musical) */
    public function partituraOrbitas()
    {
        return $this->hasMany(PartituraOrbita::class);
    }

    /** recetas para el juego 3 (cartas) */
    public function usuarioRecetas(): HasMany
    {
        return $this->hasMany(UsuarioReceta::class, 'id_usuario');
    }
    
     //usuarrio M-- constelacion N
    public function Constelaciones(): BelongsToMany
    {
        return $this->BelongsToMany(Constelaciones::class, 'constelaciones', 'id_constelacion', 'id_usuario');
    }
}
