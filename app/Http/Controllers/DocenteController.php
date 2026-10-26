<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Materia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DocenteController extends Controller
{
    public function index(): View
    {
        return $this->vista(null);
    }

    public function edit(Docente $docente): View
    {
        return $this->vista($docente->load('materias'));
    }

    public function store(Request $request): RedirectResponse
    {
        $docente = Docente::create($this->validarDatos($request));
        $docente->materias()->sync($request->input('materias', []));

        return redirect()->route('docentes.index')->with('exito', 'Docente registrado correctamente.');
    }

    public function update(Request $request, Docente $docente): RedirectResponse
    {
        $docente->update($this->validarDatos($request, $docente));
        $docente->materias()->sync($request->input('materias', []));

        return redirect()->route('docentes.index')->with('exito', 'Docente actualizado correctamente.');
    }

    public function destroy(Docente $docente): RedirectResponse
    {
        $docente->delete();

        return redirect()->route('docentes.index')->with('exito', 'Docente eliminado correctamente.');
    }

    private function vista(?Docente $docente): View
    {
        return view('docentes.index', [
            'docentes' => Docente::with('materias')->orderBy('apellido_paterno')->orderBy('nombre')->get(),
            'materias' => Materia::orderBy('nombre')->get(),
            'docente' => $docente,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validarDatos(Request $request, ?Docente $docente = null): array
    {
        $request->validate([
            'materias' => ['nullable', 'array'],
            'materias.*' => ['integer', 'exists:materias,id'],
        ]);

        return $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido_paterno' => ['required', 'string', 'max:100'],
            'apellido_materno' => ['nullable', 'string', 'max:100'],
            'correo' => ['required', 'email', 'max:255', Rule::unique('docentes', 'correo')->ignore($docente)],
            'telefono' => ['nullable', 'string', 'max:20'],
            'especialidad' => ['nullable', 'string', 'max:100'],
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'apellido_paterno.required' => 'El apellido paterno es obligatorio.',
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El correo no tiene un formato válido.',
            'correo.unique' => 'Ya existe un docente con ese correo.',
        ]);
    }
}
