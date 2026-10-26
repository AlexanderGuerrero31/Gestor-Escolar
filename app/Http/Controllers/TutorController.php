<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Tutor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TutorController extends Controller
{
    public function index(): View
    {
        return $this->vista(null);
    }

    public function edit(Tutor $tutor): View
    {
        return $this->vista($tutor->load('alumnos'));
    }

    public function store(Request $request): RedirectResponse
    {
        $tutor = Tutor::create($this->validarDatos($request));
        $tutor->alumnos()->sync($request->input('alumnos', []));

        return redirect()->route('tutores.index')->with('exito', 'Tutor registrado correctamente.');
    }

    public function update(Request $request, Tutor $tutor): RedirectResponse
    {
        $tutor->update($this->validarDatos($request));
        $tutor->alumnos()->sync($request->input('alumnos', []));

        return redirect()->route('tutores.index')->with('exito', 'Tutor actualizado correctamente.');
    }

    public function destroy(Tutor $tutor): RedirectResponse
    {
        $tutor->delete();

        return redirect()->route('tutores.index')->with('exito', 'Tutor eliminado correctamente.');
    }

    private function vista(?Tutor $tutor): View
    {
        return view('tutores.index', [
            'tutores' => Tutor::with('alumnos')->orderBy('apellido_paterno')->orderBy('nombre')->get(),
            'alumnos' => Alumno::orderBy('apellido_paterno')->orderBy('nombre')->get(),
            'tutor' => $tutor,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validarDatos(Request $request): array
    {
        $request->validate([
            'alumnos' => ['nullable', 'array'],
            'alumnos.*' => ['integer', 'exists:alumnos,id'],
        ]);

        return $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'telefono' => ['required', 'string', 'max:20'],
            'correo' => ['nullable', 'email', 'max:255'],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'correo.email' => 'El correo no tiene un formato válido.',
        ]);
    }
}
