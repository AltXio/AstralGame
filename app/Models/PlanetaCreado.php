<?php

namespace App\Models;

use App\Models\ElementoAlquimico;
use App\Models\ElementoQuimico;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Planeta creado por un jugador en el laboratorio (Juego 1).
 * Lo reutilizan los demás juegos.
 *   habitabilidad: bit calculado por el juego (1 = habitable). La BD no lo valida.
 *   id_elemento_alquimico: elemento dominante de la mezcla (NULL si solo hay metales sueltos).
 */
class PlanetaCreado extends Model
{
    protected $table = 'planetas_creados';

    public $timestamps = false;

    protected $fillable = ['id_usuario', 'nombre', 'habitabilidad', 'id_elemento_alquimico'];

    protected function casts(): array
    {
        return ['habitabilidad' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function elementoDominante(): BelongsTo
    {
        return $this->belongsTo(ElementoAlquimico::class, 'id_elemento_alquimico');
    }

    /** Mezcla del planeta: cada elemento con su número de partes (máximo 12 en total). */
    public function elementosQuimicos(): BelongsToMany
    {
        return $this->belongsToMany(
            ElementoQuimico::class,
            'planetas_creados_elementos_quimicos',
            'id_planeta_creado',
            'id_elemento_quimico'
        )->withPivot('partes');
    }
}
