@extends('layouts.app')

@section('titulo', 'Gestión de Materias')

@section('contenido')
    @php
        $docentesSeleccionados = collect(old('docentes', $materia?->docentes->pluck('id')->all() ?? []))->map(fn ($id) => (int) $id);
    @endphp

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ $materia ? 'Editar Materia' : 'Registrar Nueva Materia' }}</h5>

            <form method="POST" action="{{ $materia ? route('materias.update', $materia) : route('materias.store') }}">
                @csrf
                @if ($materia)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="clave" class="form-label">Clave</label>
                        <input type="text" id="clave" name="clave" class="form-control @error('clave') is-invalid @enderror"
                               value="{{ old('clave', $materia?->clave) }}" placeholder="Ej. MAT-01">
                        @error('clave') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="nombre" class="form-label">Nombre de la materia</label>
                        <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $materia?->nombre) }}" placeholder="Ej. Matemáticas, Historia...">
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="horas_semana" class="form-label">Horas por semana</label>
                        <input type="number" id="horas_semana" name="horas_semana" min="1"
                               class="form-control @error('horas_semana') is-invalid @enderror"
                               value="{{ old('horas_semana', $materia?->horas_semana ?? 3) }}">
                        @error('horas_semana') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Docentes que imparten esta materia</label>
                    @forelse ($docentes as $docenteItem)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="docentes[]" id="docente_{{ $docenteItem->id }}"
                                   value="{{ $docenteItem->id }}" @checked($docentesSeleccionados->contains($docenteItem->id))>
                            <label class="form-check-label" for="docente_{{ $docenteItem->id }}">{{ $docenteItem->nombre_completo }}</label>
                        </div>
                    @empty
                        <div class="text-muted">Aún no hay docentes registrados.</div>
                    @endforelse
                </div>

                <button type="submit" class="btn btn-primary">Guardar Materia</button>
                @if ($materia)
                    <a href="{{ route('materias.index') }}" class="btn btn-secondary">Cancelar</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Lista de Materias Existentes</h5>

            <table class="table table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Clave</th>
                        <th>Materia</th>
                        <th>Horas/sem</th>
                        <th>Docentes</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($materias as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->clave }}</td>
                            <td>{{ $item->nombre }}</td>
                            <td>{{ $item->horas_semana }}</td>
                            <td>
                                @forelse ($item->docentes as $d)
                                    <span class="badge text-bg-info">{{ $d->nombre_completo }}</span>
                                @empty
                                    <span class="text-muted">Sin docentes</span>
                                @endforelse
                            </td>
                            <td class="text-end">
                                <a href="{{ route('materias.edit', $item) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('materias.destroy', $item) }}" class="d-inline"
                                      onsubmit="return confirm(@js('¿Eliminar la materia ' . $item->nombre . '?'))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No hay materias registradas todavía.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
