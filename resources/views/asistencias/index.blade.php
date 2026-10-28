@extends('layouts.app')

@section('titulo', 'Gestión de Asistencias')

@section('contenido')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ $asistencia ? 'Editar Asistencia' : 'Registrar Nueva Asistencia' }}</h5>

            <form method="POST" action="{{ $asistencia ? route('asistencias.update', $asistencia) : route('asistencias.store') }}">
                @csrf
                @if ($asistencia)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="alumno_id" class="form-label">Alumno</label>
                        <select id="alumno_id" name="alumno_id" class="form-select @error('alumno_id') is-invalid @enderror">
                            <option value="">Selecciona...</option>
                            @foreach ($alumnos as $a)
                                <option value="{{ $a->id }}" @selected(old('alumno_id', $asistencia?->alumno_id) == $a->id)>{{ $a->matricula }} - {{ $a->nombre_completo }}</option>
                            @endforeach
                        </select>
                        @error('alumno_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="materia_id" class="form-label">Materia</label>
                        <select id="materia_id" name="materia_id" class="form-select @error('materia_id') is-invalid @enderror">
                            <option value="">Selecciona...</option>
                            @foreach ($materias as $m)
                                <option value="{{ $m->id }}" @selected(old('materia_id', $asistencia?->materia_id) == $m->id)>{{ $m->nombre }}</option>
                            @endforeach
                        </select>
                        @error('materia_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" id="fecha" name="fecha" max="{{ now()->format('Y-m-d') }}"
                               class="form-control @error('fecha') is-invalid @enderror"
                               value="{{ old('fecha', $asistencia?->fecha?->format('Y-m-d') ?? now()->format('Y-m-d')) }}">
                        @error('fecha') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror">
                            @foreach ($estados as $clave => $etiqueta)
                                <option value="{{ $clave }}" @selected(old('estado', $asistencia?->estado ?? 'presente') === $clave)>{{ $etiqueta }}</option>
                            @endforeach
                        </select>
                        @error('estado') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-8 mb-3">
                        <label for="observaciones" class="form-label">Observaciones (opcional)</label>
                        <input type="text" id="observaciones" name="observaciones" class="form-control @error('observaciones') is-invalid @enderror"
                               value="{{ old('observaciones', $asistencia?->observaciones) }}" placeholder="Ej. Presentó justificante médico">
                        @error('observaciones') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Guardar Asistencia</button>
                @if ($asistencia)
                    <a href="{{ route('asistencias.index') }}" class="btn btn-secondary">Cancelar</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Lista de Asistencias Existentes</h5>

            <table class="table table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Alumno</th>
                        <th>Materia</th>
                        <th>Estado</th>
                        <th>Observaciones</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($asistencias as $item)
                        @php
                            $colorEstado = match ($item->estado) {
                                'presente' => 'text-bg-success',
                                'retardo' => 'text-bg-warning',
                                'justificada' => 'text-bg-info',
                                default => 'text-bg-danger',
                            };
                        @endphp
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->fecha->format('d/m/Y') }}</td>
                            <td>{{ $item->alumno->nombre_completo }}</td>
                            <td>{{ $item->materia?->nombre ?? 'Sin materia' }}</td>
                            <td><span class="badge {{ $colorEstado }}">{{ $estados[$item->estado] ?? $item->estado }}</span></td>
                            <td>{{ $item->observaciones }}</td>
                            <td class="text-end">
                                <a href="{{ route('asistencias.edit', $item) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('asistencias.destroy', $item) }}" class="d-inline"
                                      onsubmit="return confirm(@js('¿Eliminar esta asistencia de ' . $item->alumno->nombre_completo . '?'))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">No hay asistencias registradas todavía.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
