@extends('layouts.app')

@section('titulo', 'Gestión de Tutores')

@section('contenido')
    @php
        $alumnosSeleccionados = collect(old('alumnos', $tutor?->alumnos->pluck('id')->all() ?? []))->map(fn ($id) => (int) $id);
    @endphp

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ $tutor ? 'Editar Tutor' : 'Registrar Nuevo Tutor' }}</h5>

            <form method="POST" action="{{ $tutor ? route('tutores.update', $tutor) : route('tutores.store') }}">
                @csrf
                @if ($tutor)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="nombre" class="form-label">Nombre(s)</label>
                        <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $tutor?->nombre) }}">
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="apellido_paterno" class="form-label">Apellido paterno</label>
                        <input type="text" id="apellido_paterno" name="apellido_paterno"
                               class="form-control @error('apellido_paterno') is-invalid @enderror"
                               value="{{ old('apellido_paterno', $tutor?->apellido_paterno) }}">
                        @error('apellido_paterno') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="apellido_materno" class="form-label">Apellido materno</label>
                        <input type="text" id="apellido_materno" name="apellido_materno"
                               class="form-control @error('apellido_materno') is-invalid @enderror"
                               value="{{ old('apellido_materno', $tutor?->apellido_materno) }}">
                        @error('apellido_materno') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" id="telefono" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                               value="{{ old('telefono', $tutor?->telefono) }}">
                        @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="correo" class="form-label">Correo (opcional)</label>
                        <input type="email" id="correo" name="correo" class="form-control @error('correo') is-invalid @enderror"
                               value="{{ old('correo', $tutor?->correo) }}">
                        @error('correo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Alumnos a su cargo</label>
                    @forelse ($alumnos as $alumnoItem)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="alumnos[]" id="alumno_{{ $alumnoItem->id }}"
                                   value="{{ $alumnoItem->id }}" @checked($alumnosSeleccionados->contains($alumnoItem->id))>
                            <label class="form-check-label" for="alumno_{{ $alumnoItem->id }}">{{ $alumnoItem->matricula }} - {{ $alumnoItem->nombre_completo }}</label>
                        </div>
                    @empty
                        <div class="text-muted">Aún no hay alumnos registrados.</div>
                    @endforelse
                    <div class="form-text">El parentesco se indica desde la pantalla de Alumnos.</div>
                </div>

                <button type="submit" class="btn btn-primary">Guardar Tutor</button>
                @if ($tutor)
                    <a href="{{ route('tutores.index') }}" class="btn btn-secondary">Cancelar</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Lista de Tutores Existentes</h5>

            <table class="table table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Teléfono</th>
                        <th>Alumnos a su cargo</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tutores as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->nombre_completo }}</td>
                            <td>{{ $item->telefono }}</td>
                            <td>
                                @forelse ($item->alumnos as $a)
                                    <span class="badge text-bg-info">{{ $a->nombre_completo }}</span>
                                @empty
                                    <span class="text-muted">Sin alumnos</span>
                                @endforelse
                            </td>
                            <td class="text-end">
                                <a href="{{ route('tutores.edit', $item) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('tutores.destroy', $item) }}" class="d-inline"
                                      onsubmit="return confirm(@js('¿Eliminar al tutor ' . $item->nombre_completo . '?'))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">No hay tutores registrados todavía.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
