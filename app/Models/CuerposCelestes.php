<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use Illuminate\Database\Eloquent\Model;

class CuerposCelestes extends Model
{
    protected $table = 'cuerpos_celestes';
    protected $primaryKey = 'id';
    public $incrementing = true;

    protected $fillable = [
        'nombre',
        'temperatura',
        'descripcion'
    ];

    //constelacion1_ cuerposceleste N
    public function constelaciones(): BelongsTo
    {
        return $this->belongsTo(Constelaciones::class, 'id_constelacion');
    }

    //CUERPOSCELESTES m-- elementoquimico N
    public function ElementoQuimico(): BelongsToMany
    {
        return $this->belongsToMany(ElementoQuimico::class, 'cuerpos_celestes_elementos_quimicos', 'id_cuerpo_celeste','id_elemento_quimico');
    }
}
