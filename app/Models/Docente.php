<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'nombre',
    'apellido_paterno',
    'apellido_materno',
    'correo',
    'telefono',
    'especialidad',
])]
class Docente extends Model
{
    protected $table = 'docentes';

    protected function nombreCompleto(): Attribute
    {
        return Attribute::get(
            fn (): string => trim("{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}")
        );
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class, 'docente_id');
    }

    /**
     * Relación muchos-a-muchos con materias (tabla pivote docente_materia).
     */
    public function materias(): BelongsToMany
    {
        return $this->belongsToMany(Materia::class, 'docente_materia', 'docente_id', 'materia_id')
            ->withTimestamps();
    }
}
