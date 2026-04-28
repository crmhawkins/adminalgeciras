<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Abono extends Model
{
    protected $table = 'abonos';
    protected $primaryKey = 'id';
    public $timestamps = true;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = [];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuarioId');
    }

    public function asiento(): BelongsTo
    {
        return $this->belongsTo(Asiento::class, 'asientoId');
    }

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class, 'sectorId');
    }
}
