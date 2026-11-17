<?php

namespace App\Models;

use App\Models\ElementoQuimico;
use App\Models\Signo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Fuego, Agua, Tierra y Aire.
 */
class ElementoAlquimico extends Model
{
    protected $table = 'elementos_alquimicos';

    public $timestamps = false;

    protected $fillable = ['nombre', 'descripcion'];

    /** Cada elemento químico pertenece a un único elemento alquímico (la FK está en el químico). */
    public function elementosQuimicos(): HasMany
    {
        return $this->hasMany(ElementoQuimico::class, 'id_elemento_alquimico');
    }

    public function signos(): HasMany
    {
        return $this->hasMany(Signo::class, 'id_elemento_alquimico');
    }
        
}
