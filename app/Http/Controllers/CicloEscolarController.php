<?php

namespace App\Http\Controllers;

use App\Models\CicloEscolar;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CicloEscolarController extends Controller
{
    public function index(): View
    {
        return view('ciclos.index', [
            'ciclos' => CicloEscolar::orderByDesc('fecha_inicio')->get(),
            'ciclo' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $ciclo = CicloEscolar::create($this->validarDatos($request));
        $this->mantenerUnSoloCicloActivo($ciclo);

        return redirect()->route('ciclos.index')->with('exito', 'Ciclo escolar registrado correctamente.');
    }

    public function edit(CicloEscolar $ciclo): View
    {
        return view('ciclos.index', [
            'ciclos' => CicloEscolar::orderByDesc('fecha_inicio')->get(),
            'ciclo' => $ciclo,
        ]);
    }

    public function update(Request $request, CicloEscolar $ciclo): RedirectResponse
    {
        $ciclo->update($this->validarDatos($request, $ciclo));
        $this->mantenerUnSoloCicloActivo($ciclo);

        return redirect()->route('ciclos.index')->with('exito', 'Ciclo escolar actualizado correctamente.');
    }

    public function destroy(CicloEscolar $ciclo): RedirectResponse
    {
        if ($ciclo->grupos()->exists()) {
            return redirect()->route('ciclos.index')
                ->with('error', 'No se puede eliminar el ciclo porque tiene grupos asociados.');
        }

        $ciclo->delete();

        return redirect()->route('ciclos.index')->with('exito', 'Ciclo escolar eliminado correctamente.');
    }

    /**
     * Si el ciclo quedó marcado como activo, se desactivan los demás.
     */
    private function mantenerUnSoloCicloActivo(CicloEscolar $ciclo): void
    {
        if ($ciclo->activo) {
            CicloEscolar::where('id', '!=', $ciclo->id)->update(['activo' => false]);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function validarDatos(Request $request, ?CicloEscolar $ciclo = null): array
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:20', Rule::unique('ciclos_escolares', 'nombre')->ignore($ciclo)],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after:fecha_inicio'],
        ], [
            'nombre.required' => 'El nombre del ciclo es obligatorio (por ejemplo 2026-2027).',
            'nombre.max' => 'El nombre no puede tener más de 20 caracteres.',
            'nombre.unique' => 'Ya existe un ciclo con ese nombre.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date' => 'La fecha de inicio no es válida.',
            'fecha_fin.required' => 'La fecha de fin es obligatoria.',
            'fecha_fin.date' => 'La fecha de fin no es válida.',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
        ]);

        // Un checkbox sin marcar no se envía en el formulario, por eso se lee aparte.
        $datos['activo'] = $request->boolean('activo');

        return $datos;
    }
}
