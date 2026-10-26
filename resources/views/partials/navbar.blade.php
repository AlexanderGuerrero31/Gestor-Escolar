<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">Gestor Escolar</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal"
                aria-controls="menuPrincipal" aria-expanded="false" aria-label="Mostrar menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('alumnos.*', 'tutores.*', 'docentes.*') ? 'active' : '' }}"
                       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Personas</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('alumnos.index') }}">Alumnos</a></li>
                        <li><a class="dropdown-item" href="{{ route('tutores.index') }}">Tutores</a></li>
                        <li><a class="dropdown-item" href="{{ route('docentes.index') }}">Docentes</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('grados.*', 'ciclos.*', 'aulas.*', 'materias.*') ? 'active' : '' }}"
                       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Catálogos</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('grados.index') }}">Grados</a></li>
                        <li><a class="dropdown-item" href="{{ route('ciclos.index') }}">Ciclos escolares</a></li>
                        <li><a class="dropdown-item" href="{{ route('aulas.index') }}">Aulas</a></li>
                        <li><a class="dropdown-item" href="{{ route('materias.index') }}">Materias</a></li>
                    </ul>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('grupos.*', 'inscripciones.*') ? 'active' : '' }}"
                       href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Escolar</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('grupos.index') }}">Grupos</a></li>
                        <li><a class="dropdown-item" href="{{ route('inscripciones.index') }}">Inscripciones</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
