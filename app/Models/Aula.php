<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre', 'capacidad'])]
class Aula extends Model
{
    protected $table = 'aulas';

    public function grupos(): HasMany
    {
        return $this->hasMany(Grupo::class, 'aula_id');
    }
}
