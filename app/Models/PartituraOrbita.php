<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PartituraOrbita extends Model
{
    protected $table = 'partitura_orbitas';

    // la tabla no tiene created_at / updated_at
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'id_melodia',
        'id_planeta_creado',
        'numero_orbita',
        'angulo',
    ];

    public function planeta(): BelongsTo
    {
        return $this->belongsTo(PlanetaCreado::class, 'id_planeta_creado');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }
    
    public function melodia(): BelongsTo
    {
        return $this->belongsTo(Melodia::class, 'id_melodia');
    }
}