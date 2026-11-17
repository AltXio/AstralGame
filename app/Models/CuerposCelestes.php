<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Database\Eloquent\Model;

class CuerposCelestes extends Model
{
    protected $table = 'cuerpos_celestes';
    public $timestamps = false;
    public $primaryKey = 'id';
    public $incrementing = true;
    public $nombre = 'nombre';
    public $temperatura = 'temperatura';
    public $descripcion = 'descripcion';


    //constelacion1_ cuerposceleste N
    public function constelaciones(): BelongsTo
    {
        return $this->BelongsTo(Constelaciones::class, 'id');
    }

    //CUERPOSCELESTES m-- elementoquimico N
    public function ElementoQuimico(): BelongsToMany
    {
        return $this->BelongsToMany(ElementoQuimico::class, 'elementos_quimicos', 'id_elemento_quimico', 'id_cuerpo_celeste');
    }
}
