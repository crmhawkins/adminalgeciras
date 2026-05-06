<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jugador extends Model
{
    protected $table = 'jugadores';

    protected $fillable = [
        'sofascoreId',
        'nombre',
        'nombreCorto',
        'posicion',
        'dorsal',
        'foto',
        'edad',
        'nacionalidad',
        'altura',
        'piePref',
        'valorMercado',
    ];

    protected $casts = [
        'dorsal'       => 'integer',
        'edad'         => 'integer',
        'altura'       => 'integer',
        'valorMercado' => 'integer',
    ];
}
