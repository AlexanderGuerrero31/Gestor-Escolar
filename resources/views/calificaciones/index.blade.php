@extends('layouts.app')

@section('titulo', 'Gestión de Calificaciones')

@section('contenido')
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">{{ $calificacion ? 'Editar Calificación' : 'Registrar Nueva Calificación' }}</h5>

            <form method="POST" action="{{ $calificacion ? route('calificaciones.update', $calificacion) : route('calificaciones.store') }}">
                @csrf
                @if ($calificacion)
                    @method('PUT')
                @endif

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="alumno_id" class="form-label">Alumno</label>
                        <select id="alumno_id" name="alumno_id" class="form-select @error('alumno_id') is-invalid @enderror">
                            <option value="">Selecciona...</option>
                            @foreach ($alumnos as $a)
                                <option value="{{ $a->id }}" @selected(old('alumno_id', $calificacion?->alumno_id) == $a->id)>{{ $a->matricula }} - {{ $a->nombre_completo }}</option>
                            @endforeach
                        </select>
                        @error('alumno_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="materia_id" class="form-label">Materia</label>
                        <select id="materia_id" name="materia_id" class="form-select @error('materia_id') is-invalid @enderror">
                            <option value="">Selecciona...</option>
                            @foreach ($materias as $m)
                                <option value="{{ $m->id }}" @selected(old('materia_id', $calificacion?->materia_id) == $m->id)>{{ $m->nombre }}</option>
                            @endforeach
                        </select>
                        @error('materia_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-2 mb-3">
                        <label for="unidad_id" class="form-label">Unidad</label>
                        <select id="unidad_id" name="unidad_id" class="form-select @error('unidad_id') is-invalid @enderror"></select>
                        @error('unidad_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="calificacion" class="form-label">Calificación (0 a 10)</label>
                        <input type="number" step="0.1" min="0" max="10" id="calificacion" name="calificacion"
                               class="form-control @error('calificacion') is-invalid @enderror"
                               value="{{ old('calificacion', $calificacion?->calificacion) }}" placeholder="Ej. 8.5">
                        @error('calificacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div id="aviso-sin-unidades" class="alert alert-warning d-none">
                    Esta materia todavía no tiene unidades. Agrégalas desde la pantalla de Materias (botón Editar).
                </div>

                <button type="submit" class="btn btn-primary">Guardar Calificación</button>
                @if ($calificacion)
                    <a href="{{ route('calificaciones.index') }}" class="btn btn-secondary">Cancelar</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Lista de Calificaciones Existentes</h5>

            <table class="table table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Alumno</th>
                        <th>Materia</th>
                        <th>Unidad</th>
                        <th>Calificación</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($calificaciones as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->alumno->nombre_completo }}</td>
                            <td>{{ $item->materia->nombre }}</td>
                            <td>{{ $item->unidad?->nombre ?? 'Sin unidad' }}</td>
                            <td>
                                <span class="badge {{ $item->calificacion >= 6 ? 'text-bg-success' : 'text-bg-danger' }}">{{ number_format((float) $item->calificacion, 1) }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('calificaciones.edit', $item) }}" class="btn btn-sm btn-outline-primary">Editar</a>
                                <form method="POST" action="{{ route('calificaciones.destroy', $item) }}" class="d-inline"
                                      onsubmit="return confirm(@js('¿Eliminar la calificación de ' . $item->alumno->nombre_completo . '?'))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">No hay calificaciones registradas todavía.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const unidadesPorMateria = @js($unidadesPorMateria);
            const unidadSeleccionada = @js((string) old('unidad_id', $calificacion?->unidad_id));
            const selectMateria = document.getElementById('materia_id');
            const selectUnidad = document.getElementById('unidad_id');
            const aviso = document.getElementById('aviso-sin-unidades');

            function cargarUnidades(conservarSeleccion) {
                const unidades = unidadesPorMateria[selectMateria.value] ?? [];
                selectUnidad.innerHTML = '';

                const primera = document.createElement('option');
                primera.value = '';
                primera.textContent = selectMateria.value ? 'Selecciona...' : 'Elige una materia';
                selectUnidad.appendChild(primera);

                unidades.forEach((unidad) => {
                    const opcion = document.createElement('option');
                    opcion.value = unidad.id;
                    opcion.textContent = unidad.nombre;
                    opcion.selected = conservarSeleccion && String(unidad.id) === unidadSeleccionada;
                    selectUnidad.appendChild(opcion);
                });

                aviso.classList.toggle('d-none', ! selectMateria.value || unidades.length > 0);
            }

            selectMateria.addEventListener('change', () => cargarUnidades(false));
            cargarUnidades(true);
        });
    </script>
@endsection
