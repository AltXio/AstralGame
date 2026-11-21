<?php

namespace App\Models;

use App\Models\Juego;
use App\Models\Sesion;
use App\Models\UsuariosReceta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nivel extends Model
{
    protected $table = 'niveles';

    public $timestamps = false;

    protected $fillable = ['id_juego', 'nombre'];

    public function juego(): BelongsTo
    {
        return $this->belongsTo(Juego::class, 'id_juego');
    }

    public function sesiones(): HasMany
    {
        return $this->hasMany(Sesion::class, 'id_nivel');
    }

    public function usuarioRecetas(): HasMany
    {
        return $this->hasMany(UsuariosReceta::class, 'id_nivel');
    }


    public function Constelaciones(): HasMany
    {
        return $this->hasMany(Constelaciones::class, 'id_nivel');
    }
}
