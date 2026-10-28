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
        $materias = Materia::with('unidades')->orderBy('nombre')->get();

        return view('calificaciones.index', [
            'calificaciones' => Calificacion::with(['alumno', 'materia', 'unidad'])
                ->orderBy('alumno_id')
                ->orderBy('materia_id')
                ->orderBy('unidad_id')
                ->get(),
            'alumnos' => Alumno::orderBy('apellido_paterno')->orderBy('nombre')->get(),
            'materias' => $materias,
            // Unidades de cada materia, para que el formulario muestre solo las de la materia elegida.
            'unidadesPorMateria' => $materias->mapWithKeys(fn (Materia $materia) => [
                $materia->id => $materia->unidades->map(fn ($unidad) => ['id' => $unidad->id, 'nombre' => $unidad->nombre])->values(),
            ]),
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
            'unidad_id' => [
                'required', 'integer',
                Rule::exists('unidades', 'id')->where('materia_id', $request->input('materia_id')),
                Rule::unique('calificaciones', 'unidad_id')
                    ->where('alumno_id', $request->input('alumno_id'))
                    ->ignore($calificacion),
            ],
            'calificacion' => ['required', 'numeric', 'between:0,10'],
        ], [
            'alumno_id.required' => 'Selecciona un alumno.',
            'materia_id.required' => 'Selecciona una materia.',
            'unidad_id.required' => 'Selecciona la unidad.',
            'unidad_id.exists' => 'La unidad no pertenece a la materia seleccionada.',
            'unidad_id.unique' => 'Ese alumno ya tiene calificación en esa unidad.',
            'calificacion.required' => 'La calificación es obligatoria.',
            'calificacion.numeric' => 'La calificación debe ser un número.',
            'calificacion.between' => 'La calificación debe estar entre 0 y 10.',
        ]);
    }
}
