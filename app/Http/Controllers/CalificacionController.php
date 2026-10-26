<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Calificacion;
use App\Models\Materia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CalificacionController extends Controller
{
    public const PERIODOS = [1, 2, 3, 4, 5];

    public function index(): View
    {
        return $this->vista(null);
    }

    public function edit(Calificacion $calificacion): View
    {
        return $this->vista($calificacion);
    }

    public function store(Request $request): RedirectResponse
    {
        Calificacion::create($this->validarDatos($request));

        return redirect()->route('calificaciones.index')->with('exito', 'Calificación registrada correctamente.');
    }

    public function update(Request $request, Calificacion $calificacion): RedirectResponse
    {
        $calificacion->update($this->validarDatos($request, $calificacion));

        return redirect()->route('calificaciones.index')->with('exito', 'Calificación actualizada correctamente.');
    }

    public function destroy(Calificacion $calificacion): RedirectResponse
    {
        $calificacion->delete();

        return redirect()->route('calificaciones.index')->with('exito', 'Calificación eliminada correctamente.');
    }

    private function vista(?Calificacion $calificacion): View
    {
        return view('calificaciones.index', [
            'calificaciones' => Calificacion::with(['alumno', 'materia'])
                ->orderBy('alumno_id')
                ->orderBy('materia_id')
                ->orderBy('periodo')
                ->get(),
            'alumnos' => Alumno::orderBy('apellido_paterno')->orderBy('nombre')->get(),
            'materias' => Materia::orderBy('nombre')->get(),
            'periodos' => self::PERIODOS,
            'calificacion' => $calificacion,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validarDatos(Request $request, ?Calificacion $calificacion = null): array
    {
        return $request->validate([
            'alumno_id' => ['required', 'integer', 'exists:alumnos,id'],
            'materia_id' => ['required', 'integer', 'exists:materias,id'],
            'periodo' => [
                'required', 'integer', 'in:'.implode(',', self::PERIODOS),
                Rule::unique('calificaciones', 'periodo')
                    ->where('alumno_id', $request->input('alumno_id'))
                    ->where('materia_id', $request->input('materia_id'))
                    ->ignore($calificacion),
            ],
            'calificacion' => ['required', 'numeric', 'between:0,10'],
        ], [
            'alumno_id.required' => 'Selecciona un alumno.',
            'materia_id.required' => 'Selecciona una materia.',
            'periodo.required' => 'Selecciona el periodo.',
            'periodo.in' => 'El periodo seleccionado no es válido.',
            'periodo.unique' => 'Ese alumno ya tiene calificación en esa materia y periodo.',
            'calificacion.required' => 'La calificación es obligatoria.',
            'calificacion.numeric' => 'La calificación debe ser un número.',
            'calificacion.between' => 'La calificación debe estar entre 0 y 10.',
        ]);
    }
}
