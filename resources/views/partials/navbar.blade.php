<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm transition-colors duration-300" id="mainNavbar">
    <div class="container">
        
        <a class="navbar-brand" href="{{ route('home') }}">
            <i class="fas fa-brain me-2"></i>
            estud<span class="logo-ia">IA</span>
        </a>

        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">
                        <i class="fas fa-home me-1"></i>Inicio
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('friends') }}">
                        <i class="fas fa-user-friends me-1"></i>Amigos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('groups') }}">
                        <i class="fas fa-users me-1"></i>Grupos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('courses') }}">
                        <i class="fas fa-graduation-cap me-1"></i>Cursos
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('profile') }}">
                        <i class="fas fa-user me-1"></i>Perfil
                    </a>
                </li>
            </ul>

            
            <div class="navbar-nav align-items-center">
                
                <li class="nav-item me-3">
                    <button id="theme-toggle" class="btn btn-link nav-link" style="text-decoration: none;" title="Cambiar modo claro/oscuro">
                        <i id="theme-toggle-dark-icon" class="fas fa-moon"></i>
                        <i id="theme-toggle-light-icon" class="fas fa-sun d-none"></i>
                    </button>
                </li>
                @auth
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                            @if(Auth::user()->profile_photo_path)
                                <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" 
                                     alt="Perfil" 
                                     class="rounded-circle border border-white"
                                     style="width: 32px; height: 32px; object-fit: cover;">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=EBF4FF&color=3182CE" 
                                     alt="Perfil" 
                                     class="rounded-circle border border-white"
                                     style="width: 32px; height: 32px;">
                            @endif
                            
                            <span class="d-none d-lg-inline">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li>
                                <div class="px-3 py-2 text-center border-bottom mb-2">
                                    <small class="text-muted d-block">Conectado como</small>
                                    <strong class="text-primary">{{ Auth::user()->name }}</strong>
                                </div>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('profile') }}">
                                <i class="fas fa-user me-2 text-muted"></i>Mi Perfil
                            </a></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-cog me-2 text-muted"></i>Configuración
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger" style="border: none; background: none; width: 100%; text-align: left;">
                                        <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="nav-link">
                        <i class="fas fa-sign-in-alt me-1"></i>Iniciar Sesión
                    </a>
                    <a href="{{ route('register') }}" class="nav-link">
                        <i class="fas fa-user-plus me-1"></i>Registrarse
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>