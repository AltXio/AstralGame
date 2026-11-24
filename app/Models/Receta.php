<?php

namespace App\Models;

use App\Models\ElementoQuimico;
use App\Models\Nivel;
use App\Models\Signo;
use App\Models\UsuarioReceta;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Receta extends Model
{
    //
    protected $table = 'recetas';
    public $timestamps = false;
    protected $fillable = ['id_signo', 'id_elemento_quimico', 'id_nivel', 'titulo', 'descripcion'];

    public function elementoQuimico(): BelongsTo
    {
        return $this->belongsTo(ElementoQuimico::class, 'id_elemento_quimico');
    }

    public function signo(): BelongsTo
    {
        return $this->belongsTo(Signo::class, 'id_signo');
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class, 'id_nivel');
    }

    public function usuarioRecetas(): HasMany
    {
        return $this->hasMany(UsuarioReceta::class, 'id_receta');
    }
}
