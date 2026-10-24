<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['nombre', 'apellido_paterno', 'apellido_materno', 'telefono', 'correo'])]
class Tutor extends Model
{
    protected $table = 'tutores';

    protected function nombreCompleto(): Attribute
    {
        return Attribute::get(
            fn (): string => trim("{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}")
        );
    }

    /**
     * Relación muchos-a-muchos con alumnos (tabla pivote alumno_tutor).
     */
    public function alumnos(): BelongsToMany
    {
        return $this->belongsToMany(Alumno::class, 'alumno_tutor', 'tutor_id', 'alumno_id')
            ->withPivot('parentesco')
            ->withTimestamps();
    }
}
