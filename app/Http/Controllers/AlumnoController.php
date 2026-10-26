<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Tutor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AlumnoController extends Controller
{
    public function index(): View
    {
        return $this->vista(null);
    }

    public function edit(Alumno $alumno): View
    {
        return $this->vista($alumno->load('tutores'));
    }

    public function store(Request $request): RedirectResponse
    {
        $alumno = Alumno::create($this->validarDatos($request));
        $alumno->tutores()->sync($this->tutoresConParentesco($request));

        return redirect()->route('alumnos.index')->with('exito', 'Alumno registrado correctamente.');
    }

    public function update(Request $request, Alumno $alumno): RedirectResponse
    {
        $alumno->update($this->validarDatos($request, $alumno));
        $alumno->tutores()->sync($this->tutoresConParentesco($request));

        return redirect()->route('alumnos.index')->with('exito', 'Alumno actualizado correctamente.');
    }

    public function destroy(Alumno $alumno): RedirectResponse
    {
        $alumno->delete();

        return redirect()->route('alumnos.index')->with('exito', 'Alumno eliminado correctamente.');
    }

    private function vista(?Alumno $alumno): View
    {
        return view('alumnos.index', [
            'alumnos' => Alumno::with('tutores')->orderBy('apellido_paterno')->orderBy('nombre')->get(),
            'tutores' => Tutor::orderBy('apellido_paterno')->orderBy('nombre')->get(),
            'alumno' => $alumno,
        ]);
    }

    /**
     * Arma el arreglo [tutor_id => ['parentesco' => ...]] que espera sync().
     *
     * @return array<int, array<string, string|null>>
     */
    private function tutoresConParentesco(Request $request): array
    {
        $resultado = [];

        foreach ($request->input('tutores', []) as $tutorId) {
            $parentesco = trim((string) $request->input("parentescos.$tutorId", ''));
            $resultado[(int) $tutorId] = ['parentesco' => $parentesco !== '' ? $parentesco : null];
        }

        return $resultado;
    }

    /**
     * @return array<string, mixed>
     */
    private function validarDatos(Request $request, ?Alumno $alumno = null): array
    {
        $request->validate([
            'tutores' => ['nullable', 'array'],
            'tutores.*' => ['integer', 'exists:tutores,id'],
            'parentescos.*' => ['nullable', 'string', 'max:50'],
        ]);

        return $request->validate([
            'matricula' => ['required', 'string', 'max:20', Rule::unique('alumnos', 'matricula')->ignore($alumno)],
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'fecha_nacimiento' => ['required', 'date', 'before:today'],
            'correo' => ['nullable', 'email', 'max:255', Rule::unique('alumnos', 'correo')->ignore($alumno)],
            'telefono' => ['nullable', 'string', 'max:20'],
        ], [
            'matricula.required' => 'La matrícula es obligatoria.',
            'matricula.unique' => 'Ya existe un alumno con esa matrícula.',
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.date' => 'La fecha de nacimiento no es válida.',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'correo.email' => 'El correo no tiene un formato válido.',
            'correo.unique' => 'Ya existe un alumno con ese correo.',
        ]);
    }
}
