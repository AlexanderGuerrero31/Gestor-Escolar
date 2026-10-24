@extends('layouts.app')

@section('titulo', 'Gestión de Aulas')

@section('contenido')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ $aula ? 'Editar Aula' : 'Registrar Nueva Aula' }}</h5>

            <form method="POST" action="{{ $aula ? route('aulas.update', $aula) : route('aulas.store') }}">
                @csrf
                @if ($aula)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="nombre" class="form-label">Nombre del aula</label>
                        <input type="text" id="nombre" name="nombre"
                               class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $aula?->nombre) }}"
                               placeholder="Ej. Aula 101, Laboratorio de cómputo...">
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="capacidad" class="form-label">Capacidad (alumnos)</label>
                        <input type="number" id="capacidad" name="capacidad" min="1"
                               class="form-control @error('capacidad') is-invalid @enderror"
                               value="{{ old('capacidad', $aula?->capacidad) }}"
                               placeholder="Ej. 30">
                        @error('capacidad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Guardar Aula</button>
                @if ($aula)
                    <a href="{{ route('aulas.index') }}" class="btn btn-secondary">Cancelar</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Lista de Aulas Existentes</h5>

            <table class="table table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Aula</th>
                        <th>Capacidad</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($aulas as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->nombre }}</td>
                            <td>{{ $item->capacidad }}</td>
                            <td class="text-end">
                                <a href="{{ route('aulas.edit', $item) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('aulas.destroy', $item) }}" class="d-inline"
                                      onsubmit="return confirm(@js('¿Eliminar el aula ' . $item->nombre . '?'))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3">No hay aulas registradas todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
