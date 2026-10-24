@extends('layouts.app')

@section('titulo', 'Gestión de Grados')

@section('contenido')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ $grado ? 'Editar Grado' : 'Registrar Nuevo Grado' }}</h5>

            <form method="POST" action="{{ $grado ? route('grados.update', $grado) : route('grados.store') }}">
                @csrf
                @if ($grado)
                    @method('PUT')
                @endif

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre del grado</label>
                    <input type="text" id="nombre" name="nombre"
                           class="form-control @error('nombre') is-invalid @enderror"
                           value="{{ old('nombre', $grado?->nombre) }}"
                           placeholder="Ej. Primero, Segundo, Tercero...">
                    @error('nombre')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Guardar Grado</button>
                @if ($grado)
                    <a href="{{ route('grados.index') }}" class="btn btn-secondary">Cancelar</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Lista de Grados Existentes</h5>

            <table class="table table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nombre del Grado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($grados as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->nombre }}</td>
                            <td class="text-end">
                                <a href="{{ route('grados.edit', $item) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('grados.destroy', $item) }}" class="d-inline"
                                      onsubmit="return confirm(@js('¿Eliminar el grado ' . $item->nombre . '?'))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-3">No hay grados registrados todavía.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
