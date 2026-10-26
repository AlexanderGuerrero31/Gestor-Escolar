@extends('layouts.app')

@section('titulo', 'Gestión de Alumnos')

@section('contenido')
    @php
        $idsTutores = $alumno?->tutores->pluck('id')->all() ?? [];
        $tutoresSeleccionados = collect(old('tutores', $idsTutores))->map(fn ($id) => (int) $id);
    @endphpgit checkout --ours routes/web.php resources/views/partials/navbar.blade.php

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ $alumno ? 'Editar Alumno' : 'Registrar Nuevo Alumno' }}</h5>

            <form method="POST" action="{{ $alumno ? route('alumnos.update', $alumno) : route('alumnos.store') }}">
                @csrf
                @if ($alumno)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="matricula" class="form-label">Matrícula</label>
                        <input type="text" id="matricula" name="matricula" class="form-control @error('matricula') is-invalid @enderror"
                               value="{{ old('matricula', $alumno?->matricula) }}" placeholder="Ej. 2026001">
                        @error('matricula') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="nombre" class="form-label">Nombre(s)</label>
                        <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $alumno?->nombre) }}">
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="apellido_paterno" class="form-label">Apellido paterno</label>
                        <input type="text" id="apellido_paterno" name="apellido_paterno"
                               class="form-control @error('apellido_paterno') is-invalid @enderror"
                               value="{{ old('apellido_paterno', $alumno?->apellido_paterno) }}">
                        @error('apellido_paterno') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="apellido_materno" class="form-label">Apellido materno</label>
                        <input type="text" id="apellido_materno" name="apellido_materno"
                               class="form-control @error('apellido_materno') is-invalid @enderror"
                               value="{{ old('apellido_materno', $alumno?->apellido_materno) }}">
                        @error('apellido_materno') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"
                               class="form-control @error('fecha_nacimiento') is-invalid @enderror"
                               value="{{ old('fecha_nacimiento', $alumno?->fecha_nacimiento?->format('Y-m-d')) }}">
                        @error('fecha_nacimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="correo" class="form-label">Correo (opcional)</label>
                        <input type="email" id="correo" name="correo" class="form-control @error('correo') is-invalid @enderror"
                               value="{{ old('correo', $alumno?->correo) }}">
                        @error('correo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="telefono" class="form-label">Teléfono (opcional)</label>
                        <input type="text" id="telefono" name="telefono" class="form-control @error('telefono') is-invalid @enderror"
                               value="{{ old('telefono', $alumno?->telefono) }}">
                        @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tutores del alumno</label>
                    @forelse ($tutores as $tutorItem)
                        @php
                            $parentescoActual = old("parentescos.{$tutorItem->id}", $alumno?->tutores->firstWhere('id', $tutorItem->id)?->pivot->parentesco);
                        @endphp
                        <div class="row align-items-center mb-1">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" name="tutores[]" id="tutor_{{ $tutorItem->id }}"
                                           value="{{ $tutorItem->id }}" @checked($tutoresSeleccionados->contains($tutorItem->id))>
                                    <label class="form-check-label" for="tutor_{{ $tutorItem->id }}">{{ $tutorItem->nombre_completo }}</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <input type="text" name="parentescos[{{ $tutorItem->id }}]" class="form-control form-control-sm"
                                       value="{{ $parentescoActual }}" placeholder="Parentesco (Ej. Madre, Padre, Abuelo)">
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">Aún no hay tutores registrados. Registra primero un tutor para poder asignarlo.</div>
                    @endforelse
                </div>

                <button type="submit" class="btn btn-primary">Guardar Alumno</button>
                @if ($alumno)
                    <a href="{{ route('alumnos.index') }}" class="btn btn-secondary">Cancelar</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Lista de Alumnos Existentes</h5>

            <table class="table table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Matrícula</th>
                        <th>Nombre</th>
                        <th>Nacimiento</th>
                        <th>Tutores</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($alumnos as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->matricula }}</td>
                            <td>{{ $item->nombre_completo }}</td>
                            <td>{{ $item->fecha_nacimiento->format('d/m/Y') }}</td>
                            <td>
                                @forelse ($item->tutores as $t)
                                    <span class="badge text-bg-info">{{ $t->nombre_completo }}@if ($t->pivot->parentesco) ({{ $t->pivot->parentesco }})@endif</span>
                                @empty
                                    <span class="text-muted">Sin tutores</span>
                                @endforelse
                            </td>
                            <td class="text-end">
                                <a href="{{ route('alumnos.edit', $item) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('alumnos.destroy', $item) }}" class="d-inline"
                                      onsubmit="return confirm(@js('¿Eliminar al alumno ' . $item->nombre_completo . '?'))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No hay alumnos registrados todavía.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
