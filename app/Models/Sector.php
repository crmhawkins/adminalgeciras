<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sector extends Model
{
    protected $table = 'sectores';
    protected $primaryKey = 'id';
    public $timestamps = true;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = [];

    protected $casts = [
        'precio' => 'decimal:2',
        'capacidad' => 'integer',
        'activo' => 'boolean',
    ];

    public function grada(): BelongsTo
    {
        return $this->belongsTo(Grada::class, 'gradaId');
    }

    public function asientos(): HasMany
    {
        return $this->hasMany(Asiento::class, 'sectorId');
    }
}
