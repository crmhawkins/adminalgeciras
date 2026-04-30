<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grada extends Model
{
    protected $table = 'gradas';
    protected $primaryKey = 'id';
    public $timestamps = true;
    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';

    protected $fillable = ['nombre', 'descripcion', 'imagen'];

    public function sectores(): HasMany
    {
        return $this->hasMany(Sector::class, 'gradaId');
    }
}
