<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsuarioReceta extends Model
{
    protected $table = 'usuarios_recetas';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_receta',
        'id_nivel',
        'fecha_completado',
    ];

    protected $casts = [
        'fecha_completado' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario');
    }

    public function receta(): BelongsTo
    {
        return $this->belongsTo(Receta::class, 'id_receta');
    }

    public function nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class, 'id_nivel');
    }
}
