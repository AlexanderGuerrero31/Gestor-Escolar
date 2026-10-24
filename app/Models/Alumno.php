<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'matricula',
    'nombre',
    'apellido_paterno',
    'apellido_materno',
    'fecha_nacimiento',
    'correo',
    'telefono',
])]
class Alumno extends Model
{
    protected $table = 'alumnos';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'fecha_nacimiento' => 'date',
        ];
    }

    protected function nombreCompleto(): Attribute
    {
        return Attribute::get(
            fn (): string => trim("{$this->nombre} {$this->apellido_paterno} {$this->apellido_materno}")
        );
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class, 'alumno_id');
    }

    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class, 'alumno_id');
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class, 'alumno_id');
    }

    /**
     * Relación muchos-a-muchos con tutores (tabla pivote alumno_tutor).
     */
    public function tutores(): BelongsToMany
    {
        return $this->belongsToMany(Tutor::class, 'alumno_tutor', 'alumno_id', 'tutor_id')
            ->withPivot('parentesco')
            ->withTimestamps();
    }
}
