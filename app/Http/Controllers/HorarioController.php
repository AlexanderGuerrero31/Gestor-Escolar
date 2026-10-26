<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Materia;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HorarioController extends Controller
{
    public const DIAS = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];

    public function index(): View
    {
        return $this->vista(null);
    }

    public function edit(Horario $horario): View
    {
        return $this->vista($horario);
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validarDatos($request);

        if ($mensaje = $this->motivoDeRechazo($datos)) {
            return back()->withInput()->withErrors(['hora_inicio' => $mensaje]);
        }

        Horario::create($datos);

        return redirect()->route('horarios.index')->with('exito', 'Horario registrado correctamente.');
    }

    public function update(Request $request, Horario $horario): RedirectResponse
    {
        $datos = $this->validarDatos($request);

        if ($mensaje = $this->motivoDeRechazo($datos, $horario)) {
            return back()->withInput()->withErrors(['hora_inicio' => $mensaje]);
        }

        $horario->update($datos);

        return redirect()->route('horarios.index')->with('exito', 'Horario actualizado correctamente.');
    }

    public function destroy(Horario $horario): RedirectResponse
    {
        $horario->delete();

        return redirect()->route('horarios.index')->with('exito', 'Horario eliminado correctamente.');
    }

    private function vista(?Horario $horario): View
    {
        return view('horarios.index', [
            'horarios' => Horario::with(['grupo.grado', 'grupo.cicloEscolar', 'materia', 'docente'])
                ->orderBy('grupo_id')
                ->orderBy('dia_semana')
                ->orderBy('hora_inicio')
                ->get()
                ->sortBy(fn (Horario $h) => [$h->grupo_id, array_search($h->dia_semana, self::DIAS), $h->hora_inicio])
                ->values(),
            'grupos' => Grupo::with(['grado', 'cicloEscolar'])->orderByDesc('ciclo_escolar_id')->orderBy('grado_id')->orderBy('nombre')->get(),
            'materias' => Materia::orderBy('nombre')->get(),
            'docentes' => Docente::orderBy('apellido_paterno')->orderBy('nombre')->get(),
            'dias' => self::DIAS,
            'horario' => $horario,
        ]);
    }

    /**
     * Reglas de negocio: el docente debe impartir la materia, no puede estar
     * en dos grupos a la vez y el grupo no puede tener clases encimadas.
     *
     * @param  array<string, mixed>  $datos
     */
    private function motivoDeRechazo(array $datos, ?Horario $actual = null): ?string
    {
        $docente = Docente::findOrFail($datos['docente_id']);

        if (! $docente->materias()->whereKey($datos['materia_id'])->exists()) {
            return 'Ese docente no imparte la materia seleccionada. Asígnala primero desde la pantalla de Docentes.';
        }

        $encimados = fn () => Horario::where('dia_semana', $datos['dia_semana'])
            ->where('hora_inicio', '<', $datos['hora_fin'])
            ->where('hora_fin', '>', $datos['hora_inicio'])
            ->when($actual, fn ($consulta) => $consulta->where('id', '!=', $actual->id));

        if ($encimados()->where('docente_id', $datos['docente_id'])->exists()) {
            return 'El docente ya tiene otra clase en ese día y horario.';
        }

        if ($encimados()->where('grupo_id', $datos['grupo_id'])->exists()) {
            return 'El grupo ya tiene otra clase en ese día y horario.';
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function validarDatos(Request $request): array
    {
        $datos = $request->validate([
            'grupo_id' => ['required', 'integer', 'exists:grupos,id'],
            'materia_id' => ['required', 'integer', 'exists:materias,id'],
            'docente_id' => ['required', 'integer', 'exists:docentes,id'],
            'dia_semana' => ['required', 'in:'.implode(',', self::DIAS)],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
        ], [
            'grupo_id.required' => 'Selecciona un grupo.',
            'materia_id.required' => 'Selecciona una materia.',
            'docente_id.required' => 'Selecciona un docente.',
            'dia_semana.required' => 'Selecciona el día de la semana.',
            'dia_semana.in' => 'El día seleccionado no es válido.',
            'hora_inicio.required' => 'La hora de inicio es obligatoria.',
            'hora_inicio.date_format' => 'La hora de inicio no es válida.',
            'hora_fin.required' => 'La hora de fin es obligatoria.',
            'hora_fin.date_format' => 'La hora de fin no es válida.',
            'hora_fin.after' => 'La hora de fin debe ser posterior a la hora de inicio.',
        ]);

        // Se guardan con segundos para que todas las horas tengan el mismo formato (HH:MM:SS).
        $datos['hora_inicio'] .= ':00';
        $datos['hora_fin'] .= ':00';

        return $datos;
    }
}
