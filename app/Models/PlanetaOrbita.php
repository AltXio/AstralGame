<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Sol y planetas del sistema solar del Juego 1.
 */
class PlanetaOrbita extends Model
{
    protected $table = 'planetas_orbitas';

    public $timestamps = false;

    protected $fillable = ['user_id', 'nombre', 'descripcion'];

    /** Los 4 elementos químicos más abundantes del planeta (sin orden). */
    public function elementosQuimicos(): BelongsToMany
    {
        return $this->belongsToMany(
            ElementoQuimico::class,
            'planetas_orbitas_elementos_quimicos',
            'id_planeta_orbita',
            'id_elemento_quimico'
        );
    }
}
