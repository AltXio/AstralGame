<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Constelaciones extends Model
{
    protected $table = 'constelaciones';
    public $timestamps = false;
    protected $primaryKey = 'id';
    public $incrementing = true;
    public $descripcionCientifica = 'descripcion_cientifica';
    public $descripcionMitologica = 'descripcion_mitologica';

    //constelacion1_ cuerposceleste N
    public function CuerposCelestes(): HasMany
    {
        return $this->HasMany(CuerposCelestes::class, 'id');
    }

    //usuarrio M-- constelacion N
     public function Usuario(): BelongsToMany
    {
        return $this->BelongsToMany(Usuario::class, 'usuarios', 'id_usuario','id_constelacion');
    }
}
