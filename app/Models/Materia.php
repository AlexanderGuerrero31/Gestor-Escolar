<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['clave', 'nombre', 'horas_semana'])]
class Materia extends Model
{
    protected $table = 'materias';

    public function unidades(): HasMany
    {
        return $this->hasMany(Unidad::class, 'materia_id')->orderBy('id');
    }

    public function calificaciones(): HasMany
    {
        return $this->hasMany(Calificacion::class, 'materia_id');
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class, 'materia_id');
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class, 'materia_id');
    }

    /**
     * Relación muchos-a-muchos con docentes (tabla pivote docente_materia).
     */
    public function docentes(): BelongsToMany
    {
        return $this->belongsToMany(Docente::class, 'docente_materia', 'materia_id', 'docente_id')
            ->withTimestamps();
    }
}
