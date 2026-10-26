@extends('layouts.app')

@section('titulo', 'Gestión de Grupos')

@section('contenido')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ $grupo ? 'Editar Grupo' : 'Registrar Nuevo Grupo' }}</h5>

            <form method="POST" action="{{ $grupo ? route('grupos.update', $grupo) : route('grupos.store') }}">
                @csrf
                @if ($grupo)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="grado_id" class="form-label">Grado</label>
                        <select id="grado_id" name="grado_id" class="form-select @error('grado_id') is-invalid @enderror">
                            <option value="">Selecciona...</option>
                            @foreach ($grados as $g)
                                <option value="{{ $g->id }}" @selected(old('grado_id', $grupo?->grado_id) == $g->id)>{{ $g->nombre }}</option>
                            @endforeach
                        </select>
                        @error('grado_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="ciclo_escolar_id" class="form-label">Ciclo escolar</label>
                        <select id="ciclo_escolar_id" name="ciclo_escolar_id" class="form-select @error('ciclo_escolar_id') is-invalid @enderror">
                            <option value="">Selecciona...</option>
                            @foreach ($ciclos as $c)
                                <option value="{{ $c->id }}" @selected(old('ciclo_escolar_id', $grupo?->ciclo_escolar_id) == $c->id)>{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                        @error('ciclo_escolar_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="aula_id" class="form-label">Aula (opcional)</label>
                        <select id="aula_id" name="aula_id" class="form-select @error('aula_id') is-invalid @enderror">
                            <option value="">Sin aula</option>
                            @foreach ($aulas as $a)
                                <option value="{{ $a->id }}" @selected(old('aula_id', $grupo?->aula_id) == $a->id)>{{ $a->nombre }} (cap. {{ $a->capacidad }})</option>
                            @endforeach
                        </select>
                        @error('aula_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="nombre" class="form-label">Nombre del grupo</label>
                        <input type="text" id="nombre" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                               value="{{ old('nombre', $grupo?->nombre) }}" placeholder="Ej. A, B, C">
                        @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                @if ($grados->isEmpty() || $ciclos->isEmpty())
                    <div class="alert alert-warning">Para crear un grupo primero registra al menos un grado y un ciclo escolar.</div>
                @endif

                <button type="submit" class="btn btn-primary">Guardar Grupo</button>
                @if ($grupo)
                    <a href="{{ route('grupos.index') }}" class="btn btn-secondary">Cancelar</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Lista de Grupos Existentes</h5>

            <table class="table table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Grupo</th>
                        <th>Ciclo</th>
                        <th>Aula</th>
                        <th>Inscritos</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($grupos as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->grado->nombre }} {{ $item->nombre }}</td>
                            <td>{{ $item->cicloEscolar->nombre }}</td>
                            <td>{{ $item->aula?->nombre ?? 'Sin aula' }}</td>
                            <td>{{ $item->inscripciones_count }}@if ($item->aula) / {{ $item->aula->capacidad }}@endif</td>
                            <td class="text-end">
                                <a href="{{ route('grupos.edit', $item) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('grupos.destroy', $item) }}" class="d-inline"
                                      onsubmit="return confirm(@js('¿Eliminar el grupo ' . $item->grado->nombre . ' ' . $item->nombre . '?'))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No hay grupos registrados todavía.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
