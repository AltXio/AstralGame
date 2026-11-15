<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Roles: jugador, administrador, superadministrador.
 */
class Rol extends Model
{
    protected $table = 'roles';

    public $timestamps = false;

    protected $fillable = ['nombre'];

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class, 'role_id');
    }
}
