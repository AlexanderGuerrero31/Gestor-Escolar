<?php

use App\Http\Controllers\AulaController;
use App\Http\Controllers\CicloEscolarController;
use App\Http\Controllers\GradoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/grados');

Route::resource('grados', GradoController::class)->except(['show', 'create']);
Route::resource('ciclos', CicloEscolarController::class)->except(['show', 'create']);
Route::resource('aulas', AulaController::class)->except(['show', 'create']);
