<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre'])]
class Grado extends Model
{
    protected $table = 'grados';

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class, 'grado_id');
    }
}
