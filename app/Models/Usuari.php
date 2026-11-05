<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Usuari Extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $table = 'users';

    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'email',
        'password'
    ];

    protected $casts = [
        'juego1' => 'bool',
        'juego2' => 'bool',
        'juego3' => 'bool',
        'juego4' => 'bool',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
 
}
