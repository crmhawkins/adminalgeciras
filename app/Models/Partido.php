<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partido extends Model
{
    protected $table = 'partidos';
    protected $primaryKey = 'id';
    public $timestamps = true;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = ['equipoLocal', 'equipoVisitante', 'fecha', 'hora', 'competicion', 'jornada', 'estadio', 'activo'];

    protected $casts = [
        'fecha' => 'date',
    ];
}
