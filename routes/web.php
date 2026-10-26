<?php

use App\Http\Controllers\AlumnoController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\AulaController;
use App\Http\Controllers\CalificacionController;
use App\Http\Controllers\CicloEscolarController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\GradoController;
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\HorarioController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\TutorController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/alumnos');

Route::resource('grados', GradoController::class)->except(['show', 'create']);
Route::resource('ciclos', CicloEscolarController::class)->except(['show', 'create']);
Route::resource('aulas', AulaController::class)->except(['show', 'create']);
Route::resource('materias', MateriaController::class)->except(['show', 'create']);
Route::resource('docentes', DocenteController::class)->except(['show', 'create']);
Route::resource('alumnos', AlumnoController::class)->except(['show', 'create']);
Route::resource('tutores', TutorController::class)
    ->parameters(['tutores' => 'tutor'])
    ->except(['show', 'create']);
Route::resource('grupos', GrupoController::class)->except(['show', 'create']);
Route::resource('inscripciones', InscripcionController::class)
    ->parameters(['inscripciones' => 'inscripcion'])
    ->except(['show', 'create']);
Route::resource('horarios', HorarioController::class)->except(['show', 'create']);
Route::resource('calificaciones', CalificacionController::class)
    ->parameters(['calificaciones' => 'calificacion'])
    ->except(['show', 'create']);
Route::resource('asistencias', AsistenciaController::class)->except(['show', 'create']);
