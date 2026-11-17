<?php

namespace App\Models;

use App\Models\ElementoAlquimico;
use App\Models\Receta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Signo extends Model
{
    //
    protected $table = 'signos';
    public $timestamps = false;
    protected $fillable = ['nombre', 'id_elemento_alquimico', 'planeta_regente'];

    public function elementoAlquimico(): BelongsTo
    {
        return $this->belongsTo(ElementoAlquimico::class, 'id_elemento_alquimico');
    }

    public function recetas(): HasMany
    {
        return $this->hasMany(Receta::class, 'id_signo');
    }
}
