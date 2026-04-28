<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asiento extends Model
{
    protected $table = 'asientos';
    protected $primaryKey = 'id';
    public $timestamps = true;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = [];

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class, 'sectorId');
    }

    public function partido(): BelongsTo
    {
        return $this->belongsTo(Partido::class, 'partidoId');
    }
}
