@extends('layouts.app')

@section('titulo', 'Gestión de Inscripciones')

@section('contenido')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ $inscripcion ? 'Editar Inscripción' : 'Registrar Nueva Inscripción' }}</h5>

            <form method="POST" action="{{ $inscripcion ? route('inscripciones.update', $inscripcion) : route('inscripciones.store') }}">
                @csrf
                @if ($inscripcion)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="alumno_id" class="form-label">Alumno</label>
                        <select id="alumno_id" name="alumno_id" class="form-select @error('alumno_id') is-invalid @enderror">
                            <option value="">Selecciona...</option>
                            @foreach ($alumnos as $a)
                                <option value="{{ $a->id }}" @selected(old('alumno_id', $inscripcion?->alumno_id) == $a->id)>{{ $a->matricula }} - {{ $a->nombre_completo }}</option>
                            @endforeach
                        </select>
                        @error('alumno_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="grupo_id" class="form-label">Grupo</label>
                        <select id="grupo_id" name="grupo_id" class="form-select @error('grupo_id') is-invalid @enderror">
                            <option value="">Selecciona...</option>
                            @foreach ($grupos as $g)
                                <option value="{{ $g->id }}" @selected(old('grupo_id', $inscripcion?->grupo_id) == $g->id)>{{ $g->grado->nombre }} {{ $g->nombre }} ({{ $g->cicloEscolar->nombre }})</option>
                            @endforeach
                        </select>
                        @error('grupo_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="fecha_inscripcion" class="form-label">Fecha de inscripción</label>
                        <input type="date" id="fecha_inscripcion" name="fecha_inscripcion"
                               class="form-control @error('fecha_inscripcion') is-invalid @enderror"
                               value="{{ old('fecha_inscripcion', $inscripcion?->fecha_inscripcion?->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                        @error('fecha_inscripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                @if ($alumnos->isEmpty() || $grupos->isEmpty())
                    <div class="alert alert-warning">Para inscribir primero registra al menos un alumno y un grupo.</div>
                @endif

                <button type="submit" class="btn btn-primary">Guardar Inscripción</button>
                @if ($inscripcion)
                    <a href="{{ route('inscripciones.index') }}" class="btn btn-secondary">Cancelar</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Lista de Inscripciones Existentes</h5>

            <table class="table table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Alumno</th>
                        <th>Grupo</th>
                        <th>Ciclo</th>
                        <th>Fecha</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($inscripciones as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->alumno->nombre_completo }}</td>
                            <td>{{ $item->grupo->grado->nombre }} {{ $item->grupo->nombre }}</td>
                            <td>{{ $item->grupo->cicloEscolar->nombre }}</td>
                            <td>{{ $item->fecha_inscripcion->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('inscripciones.edit', $item) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('inscripciones.destroy', $item) }}" class="d-inline"
                                      onsubmit="return confirm(@js('¿Eliminar la inscripción de ' . $item->alumno->nombre_completo . '?'))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No hay inscripciones registradas todavía.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
