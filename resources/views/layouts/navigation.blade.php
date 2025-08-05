<nav class="navbar navbar-expand-md navbar-light bg-white border-bottom">
    <div class="container">
        <!-- Logo Principal -->
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <!-- Usamos Vite::asset para cargar nuestro logo corporativo -->
            <img src="{{ Vite::asset('resources/images/cilindrogas_transp.png') }}" alt="Logo Gas Comunal" style="height: 40px;">
        </a>

        <!-- Botón Hamburguesa para móviles -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Enlaces de Navegación a la Izquierda -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        Dashboard
                    </a>
                </li>
                
                @can('create movements')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('movements.*') ? 'active' : '' }}" href="{{ route('movements.index') }}">
                            Movimientos
                        </a>
                    </li>
                @endcan
                
                @can('perform daily closing')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('daily-closing.*') ? 'active' : '' }}" href="{{ route('daily-closing.index') }}">
                            Cierres Diarios
                        </a>
                    </li>
                @endcan
                
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
                            Reportes
                        </a>
                    </li>
                

                <!-- Enlace a Gestión de Usuarios (SOLO para Admins) -->
                @can('manage users')
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            Gestionar Usuarios
                        </a>
                    </li>
                @endcan
            </ul>

            <!-- Menú Desplegable del Usuario a la Derecha -->
            <ul class="navbar-nav ms-auto">
                @auth
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                Perfil
                            </a>
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                                Cerrar Sesión
                            </a>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endauth
            </ul>
        </div>
    </div>
</nav>