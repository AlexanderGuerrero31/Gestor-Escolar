<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Materia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MateriaController extends Controller
{
    public function index(): View
    {
        return $this->vista(null);
    }

    public function edit(Materia $materia): View
    {
        return $this->vista($materia->load('docentes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $materia = Materia::create($this->validarDatos($request));
        $materia->docentes()->sync($request->input('docentes', []));

        return redirect()->route('materias.index')->with('exito', 'Materia registrada correctamente.');
    }

    public function update(Request $request, Materia $materia): RedirectResponse
    {
        $materia->update($this->validarDatos($request, $materia));
        $materia->docentes()->sync($request->input('docentes', []));

        return redirect()->route('materias.index')->with('exito', 'Materia actualizada correctamente.');
    }

    public function destroy(Materia $materia): RedirectResponse
    {
        $materia->delete();

        return redirect()->route('materias.index')->with('exito', 'Materia eliminada correctamente.');
    }

    private function vista(?Materia $materia): View
    {
        return view('materias.index', [
            'materias' => Materia::with('docentes')->orderBy('nombre')->get(),
            'docentes' => Docente::orderBy('apellido_paterno')->orderBy('nombre')->get(),
            'materia' => $materia,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validarDatos(Request $request, ?Materia $materia = null): array
    {
        $request->validate([
            'docentes' => ['nullable', 'array'],
            'docentes.*' => ['integer', 'exists:docentes,id'],
        ]);

        return $request->validate([
            'clave' => ['required', 'string', 'max:20', Rule::unique('materias', 'clave')->ignore($materia)],
            'nombre' => ['required', 'string', 'max:100'],
            'horas_semana' => ['required', 'integer', 'min:1', 'max:40'],
        ], [
            'clave.required' => 'La clave es obligatoria.',
            'clave.unique' => 'Ya existe una materia con esa clave.',
            'nombre.required' => 'El nombre de la materia es obligatorio.',
            'horas_semana.required' => 'Las horas por semana son obligatorias.',
            'horas_semana.integer' => 'Las horas deben ser un número entero.',
            'horas_semana.min' => 'Debe tener al menos 1 hora por semana.',
            'horas_semana.max' => 'No puede pasar de 40 horas por semana.',
        ]);
    }
}
