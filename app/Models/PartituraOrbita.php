<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartituraOrbita extends Model
{
    protected $table = 'partitura_orbitas';

    protected $fillable = [
        'user_id',
        'id_planeta_orbita',
        'numero_orbita',
        'angulo',
    ];

    public function planeta(): BelongsTo
    {
        return $this->belongsTo(PlanetaOrbita::class, 'id_planeta_orbita');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
}