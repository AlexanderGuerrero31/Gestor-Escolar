<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">Gestor Escolar</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal"
                aria-controls="menuPrincipal" aria-expanded="false" aria-label="Mostrar menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('grados.*') ? 'active' : '' }}"
                       href="{{ route('grados.index') }}">Grados</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('ciclos.*') ? 'active' : '' }}"
                       href="{{ route('ciclos.index') }}">Ciclos escolares</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('aulas.*') ? 'active' : '' }}"
                       href="{{ route('aulas.index') }}">Aulas</a>
                </li>
            </ul>
        </div>
    </div>
</nav>
