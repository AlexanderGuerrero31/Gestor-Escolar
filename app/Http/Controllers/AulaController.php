<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AulaController extends Controller
{
    public function index(): View
    {
        return view('aulas.index', [
            'aulas' => Aula::orderBy('nombre')->get(),
            'aula' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Aula::create($this->validarDatos($request));

        return redirect()->route('aulas.index')->with('exito', 'Aula registrada correctamente.');
    }

    public function edit(Aula $aula): View
    {
        return view('aulas.index', [
            'aulas' => Aula::orderBy('nombre')->get(),
            'aula' => $aula,
        ]);
    }

    public function update(Request $request, Aula $aula): RedirectResponse
    {
        $aula->update($this->validarDatos($request, $aula));

        return redirect()->route('aulas.index')->with('exito', 'Aula actualizada correctamente.');
    }

    public function destroy(Aula $aula): RedirectResponse
    {
        // Los grupos que usaban esta aula se quedan sin aula (nullOnDelete en la migración).
        $aula->delete();

        return redirect()->route('aulas.index')->with('exito', 'Aula eliminada correctamente.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validarDatos(Request $request, ?Aula $aula = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:50', Rule::unique('aulas', 'nombre')->ignore($aula)],
            'capacidad' => ['required', 'integer', 'min:1', 'max:500'],
        ], [
            'nombre.required' => 'El nombre del aula es obligatorio.',
            'nombre.max' => 'El nombre no puede tener más de 50 caracteres.',
            'nombre.unique' => 'Ya existe un aula con ese nombre.',
            'capacidad.required' => 'La capacidad es obligatoria.',
            'capacidad.integer' => 'La capacidad debe ser un número entero.',
            'capacidad.min' => 'La capacidad debe ser de al menos 1 alumno.',
            'capacidad.max' => 'La capacidad no puede pasar de 500 alumnos.',
        ]);
    }
}
