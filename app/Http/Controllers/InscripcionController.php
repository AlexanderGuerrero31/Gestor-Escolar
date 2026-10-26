<?php

namespace App\Http\Controllers;

use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\Inscripcion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InscripcionController extends Controller
{
    public function index(): View
    {
        return $this->vista(null);
    }

    public function edit(Inscripcion $inscripcion): View
    {
        return $this->vista($inscripcion);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validarDatos($request);

        if ($mensaje = $this->motivoDeRechazo($datos)) {
            return back()->withInput()->withErrors(['grupo_id' => $mensaje]);
        }

        Inscripcion::create($datos);

        return redirect()->route('inscripciones.index')->with('exito', 'Inscripción registrada correctamente.');
    }

    public function update(Request $request, Inscripcion $inscripcion): RedirectResponse
    {
        $datos = $this->validarDatos($request);

        if ($mensaje = $this->motivoDeRechazo($datos, $inscripcion)) {
            return back()->withInput()->withErrors(['grupo_id' => $mensaje]);
        }

        $inscripcion->update($datos);

        return redirect()->route('inscripciones.index')->with('exito', 'Inscripción actualizada correctamente.');
    }

    public function destroy(Inscripcion $inscripcion): RedirectResponse
    {
        $inscripcion->delete();

        return redirect()->route('inscripciones.index')->with('exito', 'Inscripción eliminada correctamente.');
    }

    private function vista(?Inscripcion $inscripcion): View
    {
        return view('inscripciones.index', [
            'inscripciones' => Inscripcion::with(['alumno', 'grupo.grado', 'grupo.cicloEscolar'])
                ->orderByDesc('fecha_inscripcion')
                ->orderByDesc('id')
                ->get(),
            'alumnos' => Alumno::orderBy('apellido_paterno')->orderBy('nombre')->get(),
            'grupos' => Grupo::with(['grado', 'cicloEscolar'])->orderByDesc('ciclo_escolar_id')->orderBy('grado_id')->orderBy('nombre')->get(),
            'inscripcion' => $inscripcion,
        ]);
    }

    /**
     * Reglas de negocio: un alumno solo puede estar en un grupo por ciclo
     * y el grupo no puede pasar de la capacidad de su aula.
     *
     * @param  array<string, mixed>  $datos
     */
    private function motivoDeRechazo(array $datos, ?Inscripcion $actual = null): ?string
    {
        $grupo = Grupo::with('aula')->findOrFail($datos['grupo_id']);

        $yaInscritoEnElCiclo = Inscripcion::where('alumno_id', $datos['alumno_id'])
            ->whereHas('grupo', fn ($consulta) => $consulta->where('ciclo_escolar_id', $grupo->ciclo_escolar_id))
            ->when($actual, fn ($consulta) => $consulta->where('id', '!=', $actual->id))
            ->exists();

        if ($yaInscritoEnElCiclo) {
            return 'El alumno ya está inscrito en un grupo de ese ciclo escolar.';
        }

        if ($grupo->aula) {
            $inscritos = Inscripcion::where('grupo_id', $grupo->id)
                ->when($actual, fn ($consulta) => $consulta->where('id', '!=', $actual->id))
                ->count();

            if ($inscritos >= $grupo->aula->capacidad) {
                return "El grupo está lleno: el aula {$grupo->aula->nombre} tiene capacidad para {$grupo->aula->capacidad} alumnos.";
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function validarDatos(Request $request): array
    {
        return $request->validate([
            'alumno_id' => ['required', 'integer', 'exists:alumnos,id'],
            'grupo_id' => ['required', 'integer', 'exists:grupos,id'],
            'fecha_inscripcion' => ['required', 'date'],
        ], [
            'alumno_id.required' => 'Selecciona un alumno.',
            'grupo_id.required' => 'Selecciona un grupo.',
            'fecha_inscripcion.required' => 'La fecha de inscripción es obligatoria.',
            'fecha_inscripcion.date' => 'La fecha de inscripción no es válida.',
        ]);
    }
}
