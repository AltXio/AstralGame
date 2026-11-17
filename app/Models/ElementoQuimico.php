<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Catálogo químico compartido por los cuatro juegos (incluye los metales sueltos).
 * id_elemento_alquimico es NULL en los metales sueltos.
 */
class ElementoQuimico extends Model
{
    protected $table = 'elementos_quimicos';

    public $timestamps = false;

    protected $fillable = ['nombre', 'descripcion', 'dato_curioso', 'id_elemento_alquimico'];

    public function elementoAlquimico(): BelongsTo
    {
        return $this->belongsTo(ElementoAlquimico::class, 'id_elemento_alquimico');
    }

    public function planetasOrbitas(): BelongsToMany
    {
        return $this->belongsToMany(
            PlanetaOrbita::class,
            'planetas_orbitas_elementos_quimicos',
            'id_elemento_quimico',
            'id_planeta_orbita'
        );
    }

    /** Usuarios que lo han descubierto (inventario). */
    public function usuarios(): BelongsToMany
    {
        return $this->belongsToMany(
            Usuario::class,
            'inventario_usuario_elementos_quimicos',
            'id_elemento_quimico',
            'id_usuario'
        );
    }

//CUERPOSCELESTES m-- elementoquimico N
    public function CuerposCelestes(): BelongsToMany
    {
        return $this->BelongsToMany(CuerposCelestes::class, 'cuerpos_celestes', 'id_cuerpo_celeste','id_elemento_quimico');
    }
}
