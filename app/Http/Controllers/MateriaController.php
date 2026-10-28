<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Materia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class MateriaController extends Controller
{
    public function index(): View
    {
        return $this->vista(null);
    }

    public function edit(Materia $materia): View
    {
        return $this->vista($materia->load(['docentes', 'unidades']));
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validarDatos($request);
        $unidades = $this->unidadesEnviadas($request);

        DB::transaction(function () use ($datos, $request, $unidades) {
            $materia = Materia::create($datos);
            $materia->docentes()->sync($request->input('docentes', []));
            $this->guardarUnidades($materia, $unidades);
        });

        return redirect()->route('materias.index')->with('exito', 'Materia registrada correctamente.');
    }

    public function update(Request $request, Materia $materia): RedirectResponse
    {
        $datos = $this->validarDatos($request, $materia);
        $unidades = $this->unidadesEnviadas($request);

        $this->validarUnidadesQuitadas($materia, $unidades);

        DB::transaction(function () use ($datos, $request, $materia, $unidades) {
            $materia->update($datos);
            $materia->docentes()->sync($request->input('docentes', []));
            $this->guardarUnidades($materia, $unidades);
        });

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
            'materias' => Materia::with(['docentes', 'unidades'])->orderBy('nombre')->get(),
            'docentes' => Docente::orderBy('apellido_paterno')->orderBy('nombre')->get(),
            'materia' => $materia,
        ]);
    }

    /**
     * Unidades capturadas en el formulario, sin las filas vacías.
     *
     * @return Collection<int, array{id: int|null, nombre: string}>
     */
    private function unidadesEnviadas(Request $request): Collection
    {
        $unidades = collect($request->input('unidades', []))
            ->map(fn (array $unidad) => [
                'id' => ($unidad['id'] ?? '') !== '' ? (int) $unidad['id'] : null,
                'nombre' => trim((string) ($unidad['nombre'] ?? '')),
            ])
            ->filter(fn (array $unidad) => $unidad['nombre'] !== '')
            ->values();

        if ($unidades->pluck('nombre')->map(fn (string $nombre) => mb_strtolower($nombre))->duplicates()->isNotEmpty()) {
            throw ValidationException::withMessages(['unidades' => 'No puedes repetir el nombre de una unidad dentro de la misma materia.']);
        }

        return $unidades;
    }

    /**
     * Una unidad con calificaciones capturadas no se puede quitar, porque se perderían.
     *
     * @param  Collection<int, array{id: int|null, nombre: string}>  $unidades
     */
    private function validarUnidadesQuitadas(Materia $materia, Collection $unidades): void
    {
        $bloqueadas = $materia->unidades()
            ->whereNotIn('id', $unidades->pluck('id')->filter()->all())
            ->whereHas('calificaciones')
            ->pluck('nombre');

        if ($bloqueadas->isNotEmpty()) {
            throw ValidationException::withMessages([
                'unidades' => 'No se puede quitar la unidad '.$bloqueadas->implode(', ').' porque ya tiene calificaciones registradas.',
            ]);
        }
    }

    /**
     * Crea las unidades nuevas, renombra las existentes y elimina las que se quitaron.
     *
     * @param  Collection<int, array{id: int|null, nombre: string}>  $unidades
     */
    private function guardarUnidades(Materia $materia, Collection $unidades): void
    {
        $existentes = $materia->unidades()->get()->keyBy('id');
        $conservadas = [];

        foreach ($unidades as $unidad) {
            if ($unidad['id'] !== null && $existentes->has($unidad['id'])) {
                $existentes[$unidad['id']]->update(['nombre' => $unidad['nombre']]);
                $conservadas[] = $unidad['id'];
            } else {
                $conservadas[] = $materia->unidades()->create(['nombre' => $unidad['nombre']])->id;
            }
        }

        $materia->unidades()->whereNotIn('id', $conservadas)->delete();
    }

    /**
     * @return array<string, mixed>
     */
    private function validarDatos(Request $request, ?Materia $materia = null): array
    {
        $request->validate([
            'docentes' => ['nullable', 'array'],
            'docentes.*' => ['integer', 'exists:docentes,id'],
            'unidades' => ['nullable', 'array'],
            'unidades.*.id' => ['nullable', 'integer'],
            'unidades.*.nombre' => ['nullable', 'string', 'max:100'],
        ], [
            'unidades.*.nombre.max' => 'El nombre de una unidad no puede tener más de 100 caracteres.',
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
