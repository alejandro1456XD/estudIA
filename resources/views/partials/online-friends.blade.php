@php
    // Truco de seguridad: Si la variable $friends no llega desde el controlador (ej. en el Perfil),
    // la buscamos nosotros mismos usando el usuario autenticado para que no de error.
    if (!isset($friends)) {
        $friends = auth()->user() ? auth()->user()->all_friends : collect();
    }
@endphp

<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-success text-white py-3">
        <h6 class="mb-0 d-flex align-items-center">
            <i class="fas fa-circle text-light me-2" style="font-size: 0.6rem;"></i>
            Amigos
            <!-- Contador real de amigos -->
            <span class="badge bg-white text-success ms-auto rounded-pill">{{ $friends->count() }}</span>
        </h6>
    </div>
    <div class="card-body p-3">
        @if($friends->count() > 0)
            <!-- Mostramos solo los primeros 5 amigos -->
            @foreach($friends->take(5) as $friend)
            <div class="d-flex align-items-center mb-3">
                <!-- Avatar Dinámico (Foto real o Iniciales) -->
                <div class="position-relative me-2">
                    @if($friend->profile_photo_path)
                        <img src="{{ asset('storage/' . $friend->profile_photo_path) }}" 
                             class="rounded-circle border border-2 border-white shadow-sm" 
                             style="width: 40px; height: 40px; object-fit: cover;"
                             alt="{{ $friend->name }}">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($friend->name) }}&background=random&color=fff" 
                             class="rounded-circle border border-2 border-white shadow-sm" 
                             style="width: 40px; height: 40px;"
                             alt="{{ $friend->name }}">
                    @endif
                    <!-- Puntito verde de "En línea" -->
                    <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle" 
                          style="width: 10px; height: 10px;"></span>
                </div>
                
                <div class="overflow-hidden">
                    <span class="d-block fw-bold text-dark text-truncate" style="max-width: 140px; font-size: 0.95rem;">
                        {{ $friend->name }}
                    </span>
                    <small class="d-block text-muted" style="font-size: 0.75rem;">
                        <i class="fas fa-laptop-code me-1"></i>En línea
                    </small>
                </div>
            </div>
            @endforeach
            
            @if($friends->count() > 5)
                <div class="text-center mt-3 border-top pt-2">
                    <a href="{{ route('friends') }}" class="text-decoration-none text-success small fw-bold">
                        Ver todos ({{ $friends->count() }})
                    </a>
                </div>
            @endif
        @else
            <!-- Estado vacío (cuando no tienes amigos) -->
            <div class="text-center py-4 text-muted">
                <i class="fas fa-user-friends fa-2x mb-2 opacity-50"></i>
                <p class="small mb-0">Aún no tienes amigos.</p>
                <a href="{{ route('friends') }}" class="btn btn-sm btn-outline-success mt-3 rounded-pill">
                    Buscar Amigos
                </a>
            </div>
        @endif
    </div>
</div>