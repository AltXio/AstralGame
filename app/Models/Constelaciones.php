<?php

namespace App\Models;

use App\Models\Nivel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Constelaciones extends Model
{
    protected $table = 'constelaciones';
    public $timestamps = false;
    protected $primaryKey = 'id';

    protected $fillable = ['nombre', 'alias', 'descripcion_cientifica', 'descripcion_mitologica'];

    //constelacion1_ cuerposceleste N
    public function CuerposCelestes(): HasMany
    {
        return $this->hasMany(CuerposCelestes::class, 'id_constelacion');
    }

    //usuarrio M-- constelacion N
    public function Usuario(): BelongsToMany
    {
        return $this->belongsToMany(Usuario::class, 'usuarios_constelaciones', 'id_constelacion', 'id_usuario');
    }


    public function Nivel(): BelongsTo
    {
        return $this->belongsTo(Nivel::class, 'id_nivel');
    }
}
