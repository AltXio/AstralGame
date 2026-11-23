<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Melodia extends Model
{
    protected $table = 'melodias';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'nombre',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'user_id');
    }

    public function orbitas(): HasMany
    {
        return $this->hasMany(PartituraOrbita::class, 'id_melodia');
    }
}