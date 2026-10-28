<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Asistencia;
use App\Models\Materia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AsistenciaController extends Controller
{
    public const ESTADOS = [
        'presente' => 'Presente',
        'falta' => 'Falta',
        'retardo' => 'Retardo',
        'justificada' => 'Falta justificada',
    ];

    public function index(): View
    {
        return $this->vista(null);
    }

    public function edit(Asistencia $asistencia): View
    {
        return $this->vista($asistencia);
    }

    public function store(Request $request): RedirectResponse
    {
        Asistencia::create($this->validarDatos($request));

        return redirect()->route('asistencias.index')->with('exito', 'Asistencia registrada correctamente.');
    }

    public function update(Request $request, Asistencia $asistencia): RedirectResponse
    {
        $asistencia->update($this->validarDatos($request, $asistencia));

        return redirect()->route('asistencias.index')->with('exito', 'Asistencia actualizada correctamente.');
    }

    public function destroy(Asistencia $asistencia): RedirectResponse
    {
        $asistencia->delete();

        return redirect()->route('asistencias.index')->with('exito', 'Asistencia eliminada correctamente.');
    }

    private function vista(?Asistencia $asistencia): View
    {
        return view('asistencias.index', [
            'asistencias' => Asistencia::with(['alumno', 'materia'])->orderByDesc('fecha')->orderBy('alumno_id')->get(),
            'alumnos' => Alumno::orderBy('apellido_paterno')->orderBy('nombre')->get(),
            'materias' => Materia::orderBy('nombre')->get(),
            'estados' => self::ESTADOS,
            'asistencia' => $asistencia,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validarDatos(Request $request, ?Asistencia $asistencia = null): array
    {
        return $request->validate([
            'alumno_id' => ['required', 'integer', 'exists:alumnos,id'],
            'materia_id' => ['required', 'integer', 'exists:materias,id'],
            'fecha' => [
                'required', 'date', 'before_or_equal:today',
                Rule::unique('asistencias', 'fecha')
                    ->where('alumno_id', $request->input('alumno_id'))
                    ->where('materia_id', $request->input('materia_id'))
                    ->ignore($asistencia),
            ],
            'estado' => ['required', 'in:'.implode(',', array_keys(self::ESTADOS))],
            'observaciones' => ['nullable', 'string', 'max:255'],
        ], [
            'alumno_id.required' => 'Selecciona un alumno.',
            'materia_id.required' => 'Selecciona la materia de la asistencia.',
            'fecha.required' => 'La fecha es obligatoria.',
            'fecha.date' => 'La fecha no es válida.',
            'fecha.before_or_equal' => 'No se puede registrar asistencia de una fecha futura.',
            'fecha.unique' => 'Ese alumno ya tiene asistencia registrada en esa materia y fecha.',
            'estado.required' => 'Selecciona el estado.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'observaciones.max' => 'Las observaciones no pueden tener más de 255 caracteres.',
        ]);
    }
}
