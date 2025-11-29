<div class="card">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0"><i class="fas fa-rocket me-2"></i>Accesos Rápidos</h6>
    </div>
    <div class="list-group list-group-flush">
        @foreach([
            ['icon' => 'home', 'text' => 'Inicio', 'color' => 'primary', 'route' => 'home'],
            ['icon' => 'user-friends', 'text' => 'Amigos', 'color' => 'success', 'route' => 'friends'],
            ['icon' => 'users', 'text' => 'Grupos de Estudio', 'color' => 'info', 'route' => 'groups'],
            ['icon' => 'graduation-cap', 'text' => 'Mis Cursos', 'color' => 'warning', 'route' => 'courses'],
            ['icon' => 'user', 'text' => 'Mi Perfil', 'color' => 'secondary', 'route' => 'profile'],
            ['icon' => 'calendar', 'text' => 'Eventos', 'color' => 'danger', 'route' => 'events'],
            ['icon' => 'book', 'text' => 'Recursos', 'color' => 'dark', 'route' => 'resources']
        ] as $link)
        <a href="{{ route($link['route']) }}" class="list-group-item list-group-item-action">
            <i class="fas fa-{{ $link['icon'] }} me-2 text-{{ $link['color'] }}"></i>{{ $link['text'] }}
        </a>
        @endforeach
    </div>
</div>