@extends('layouts.app')

@section('titulo', 'Gestión de Horarios')

@section('contenido')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ $horario ? 'Editar Horario' : 'Registrar Nuevo Horario' }}</h5>

            <form method="POST" action="{{ $horario ? route('horarios.update', $horario) : route('horarios.store') }}">
                @csrf
                @if ($horario)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="grupo_id" class="form-label">Grupo</label>
                        <select id="grupo_id" name="grupo_id" class="form-select @error('grupo_id') is-invalid @enderror">
                            <option value="">Selecciona...</option>
                            @foreach ($grupos as $g)
                                <option value="{{ $g->id }}" @selected(old('grupo_id', $horario?->grupo_id) == $g->id)>{{ $g->grado->nombre }} {{ $g->nombre }} ({{ $g->cicloEscolar->nombre }})</option>
                            @endforeach
                        </select>
                        @error('grupo_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="materia_id" class="form-label">Materia</label>
                        <select id="materia_id" name="materia_id" class="form-select @error('materia_id') is-invalid @enderror">
                            <option value="">Selecciona...</option>
                            @foreach ($materias as $m)
                                <option value="{{ $m->id }}" @selected(old('materia_id', $horario?->materia_id) == $m->id)>{{ $m->nombre }}</option>
                            @endforeach
                        </select>
                        @error('materia_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="docente_id" class="form-label">Docente</label>
                        <select id="docente_id" name="docente_id" class="form-select @error('docente_id') is-invalid @enderror">
                            <option value="">Selecciona...</option>
                            @foreach ($docentes as $d)
                                <option value="{{ $d->id }}" @selected(old('docente_id', $horario?->docente_id) == $d->id)>{{ $d->nombre_completo }}</option>
                            @endforeach
                        </select>
                        @error('docente_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="dia_semana" class="form-label">Día</label>
                        <select id="dia_semana" name="dia_semana" class="form-select @error('dia_semana') is-invalid @enderror">
                            <option value="">Selecciona...</option>
                            @foreach ($dias as $dia)
                                <option value="{{ $dia }}" @selected(old('dia_semana', $horario?->dia_semana) === $dia)>{{ $dia }}</option>
                            @endforeach
                        </select>
                        @error('dia_semana') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="hora_inicio" class="form-label">Hora de inicio</label>
                        <input type="time" id="hora_inicio" name="hora_inicio" class="form-control @error('hora_inicio') is-invalid @enderror"
                               value="{{ old('hora_inicio', $horario ? substr($horario->hora_inicio, 0, 5) : '') }}">
                        @error('hora_inicio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="hora_fin" class="form-label">Hora de fin</label>
                        <input type="time" id="hora_fin" name="hora_fin" class="form-control @error('hora_fin') is-invalid @enderror"
                               value="{{ old('hora_fin', $horario ? substr($horario->hora_fin, 0, 5) : '') }}">
                        @error('hora_fin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Guardar Horario</button>
                @if ($horario)
                    <a href="{{ route('horarios.index') }}" class="btn btn-secondary">Cancelar</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Lista de Horarios Existentes</h5>

            <table class="table table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Grupo</th>
                        <th>Día</th>
                        <th>Horario</th>
                        <th>Materia</th>
                        <th>Docente</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($horarios as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->grupo->grado->nombre }} {{ $item->grupo->nombre }}</td>
                            <td>{{ $item->dia_semana }}</td>
                            <td>{{ substr($item->hora_inicio, 0, 5) }} - {{ substr($item->hora_fin, 0, 5) }}</td>
                            <td>{{ $item->materia->nombre }}</td>
                            <td>{{ $item->docente->nombre_completo }}</td>
                            <td class="text-end">
                                <a href="{{ route('horarios.edit', $item) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('horarios.destroy', $item) }}" class="d-inline"
                                      onsubmit="return confirm(@js('¿Eliminar este horario de ' . $item->materia->nombre . '?'))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">No hay horarios registrados todavía.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
