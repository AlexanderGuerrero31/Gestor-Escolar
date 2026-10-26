@extends('layouts.app')

@section('titulo', 'Gestión de Docentes')

@section('contenido')
    @php
        $materiasSeleccionadas = collect(old('materias', $docente?->materias->pluck('id')->all() ?? []))->map(fn ($id) => (int) $id);
    @endphp

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ $docente ? 'Editar Docente' : 'Registrar Nuevo Docente' }}</h5>

            <form method="POST" action="{{ $docente ? route('docentes.update', $docente) : route('docentes.store') }}">
                @csrf
                @if ($docente)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="nombre" class="form-label">Nombre(s)</label>
                        <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $docente?->nombre) }}">
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="apellido_paterno" class="form-label">Apellido paterno</label>
                        <input type="text" id="apellido_paterno" name="apellido_paterno"
                               class="form-control @error('apellido_paterno') is-invalid @enderror"
                               value="{{ old('apellido_paterno', $docente?->apellido_paterno) }}">
                        @error('apellido_paterno') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="apellido_materno" class="form-label">Apellido materno</label>
                        <input type="text" id="apellido_materno" name="apellido_materno"
                               class="form-control @error('apellido_materno') is-invalid @enderror"
                               value="{{ old('apellido_materno', $docente?->apellido_materno) }}">
                        @error('apellido_materno') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="correo" class="form-label">Correo</label>
                        <input type="email" id="correo" name="correo" class="form-control @error('correo') is-invalid @enderror"
                               value="{{ old('correo', $docente?->correo) }}" placeholder="docente@escuela.mx">
                        @error('correo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                               value="{{ old('telefono', $docente?->telefono) }}">
                        @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="especialidad" class="form-label">Especialidad</label>
                        <input type="text" id="especialidad" name="especialidad"
                               class="form-control @error('especialidad') is-invalid @enderror"
                               value="{{ old('especialidad', $docente?->especialidad) }}" placeholder="Ej. Ciencias exactas">
                        @error('especialidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Materias que imparte</label>
                    @forelse ($materias as $materiaItem)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="materias[]" id="materia_{{ $materiaItem->id }}"
                                   value="{{ $materiaItem->id }}" @checked($materiasSeleccionadas->contains($materiaItem->id))>
                            <label class="form-check-label" for="materia_{{ $materiaItem->id }}">{{ $materiaItem->clave }} - {{ $materiaItem->nombre }}</label>
                        </div>
                    @empty
                        <div class="text-muted">Aún no hay materias registradas.</div>
                    @endforelse
                </div>

                <button type="submit" class="btn btn-primary">Guardar Docente</button>
                @if ($docente)
                    <a href="{{ route('docentes.index') }}" class="btn btn-secondary">Cancelar</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Lista de Docentes Existentes</h5>

            <table class="table table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Materias</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($docentes as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->nombre_completo }}</td>
                            <td>{{ $item->correo }}</td>
                            <td>
                                @forelse ($item->materias as $m)
                                    <span class="badge text-bg-info">{{ $m->nombre }}</span>
                                @empty
                                    <span class="text-muted">Sin materias</span>
                                @endforelse
                            </td>
                            <td class="text-end">
                                <a href="{{ route('docentes.edit', $item) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('docentes.destroy', $item) }}" class="d-inline"
                                      onsubmit="return confirm(@js('¿Eliminar al docente ' . $item->nombre_completo . '?'))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No hay docentes registrados todavía.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
