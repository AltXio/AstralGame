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
        'descripcion',
        'id_constelacion',
        'ascension_recta',
        'declinacion',
        'magnitud',
        'anyo_descubierta',
        'descubridor'
    ];

    protected $casts = [
        'temperatura'      => 'integer',
        'ascension_recta'  => 'float',
        'declinacion'      => 'float',
        'magnitud'         => 'float',
        'anyo_descubierta' => 'integer',
    ];


    //constelacion1_ cuerposceleste N
    public function constelaciones(): BelongsTo
    {
        return $this->belongsTo(Constelaciones::class, 'id_constelacion');
    }

    //CUERPOSCELESTES m-- elementoquimico N
    public function elementosQuimicos(): BelongsToMany
    {
        return $this->belongsToMany(ElementoQuimico::class, 'cuerpos_celestes_elementos_quimicos', 'id_cuerpo_celeste', 'id_elemento_quimico')
            ->withPivot('orden')
            ->orderBy('cuerpos_celestes_elementos_quimicos.orden');
    }
}
