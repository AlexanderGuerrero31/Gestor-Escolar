<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use App\Models\CicloEscolar;
use App\Models\Grado;
use App\Models\Grupo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class GrupoController extends Controller
{
    public function index(): View
    {
        return $this->vista(null);
    }

    public function edit(Grupo $grupo): View
    {
        return $this->vista($grupo);
    }

    public function store(Request $request): RedirectResponse
    {
        Grupo::create($this->validarDatos($request));

        return redirect()->route('grupos.index')->with('exito', 'Grupo registrado correctamente.');
    }

    public function update(Request $request, Grupo $grupo): RedirectResponse
    {
        $grupo->update($this->validarDatos($request, $grupo));

        return redirect()->route('grupos.index')->with('exito', 'Grupo actualizado correctamente.');
    }

    public function destroy(Grupo $grupo): RedirectResponse
    {
        if ($grupo->inscripciones()->exists()) {
            return redirect()->route('grupos.index')
                ->with('error', 'No se puede eliminar el grupo porque tiene alumnos inscritos.');
        }

        $grupo->delete();

        return redirect()->route('grupos.index')->with('exito', 'Grupo eliminado correctamente.');
    }

    private function vista(?Grupo $grupo): View
    {
        return view('grupos.index', [
            'grupos' => Grupo::with(['grado', 'cicloEscolar', 'aula'])
                ->withCount('inscripciones')
                ->orderByDesc('ciclo_escolar_id')
                ->orderBy('grado_id')
                ->orderBy('nombre')
                ->get(),
            'grados' => Grado::orderBy('nombre')->get(),
            'ciclos' => CicloEscolar::orderByDesc('fecha_inicio')->get(),
            'aulas' => Aula::orderBy('nombre')->get(),
            'grupo' => $grupo,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validarDatos(Request $request, ?Grupo $grupo = null): array
    {
        return $request->validate([
            'grado_id' => ['required', 'integer', 'exists:grados,id'],
            'ciclo_escolar_id' => ['required', 'integer', 'exists:ciclos_escolares,id'],
            'aula_id' => ['nullable', 'integer', 'exists:aulas,id'],
            'nombre' => [
                'required', 'string', 'max:10',
                Rule::unique('grupos', 'nombre')
                    ->where('grado_id', $request->input('grado_id'))
                    ->where('ciclo_escolar_id', $request->input('ciclo_escolar_id'))
                    ->ignore($grupo),
            ],
        ], [
            'grado_id.required' => 'Selecciona un grado.',
            'ciclo_escolar_id.required' => 'Selecciona un ciclo escolar.',
            'nombre.required' => 'El nombre del grupo es obligatorio (por ejemplo A).',
            'nombre.max' => 'El nombre no puede tener más de 10 caracteres.',
            'nombre.unique' => 'Ya existe un grupo con ese nombre en ese grado y ciclo.',
        ]);
    }
}
