<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    public $timestamps = true;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $guarded = [];
    protected $hidden = ['password'];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function abonos(): HasMany
    {
        return $this->hasMany(Abono::class, 'usuarioId');
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class, 'usuarioId');
    }
}
