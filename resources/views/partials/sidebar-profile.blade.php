<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body text-center py-4">
        <!-- FOTO DE PERFIL DINÁMICA -->
        <div class="mb-3 position-relative d-inline-block">
            @if(Auth::user()->profile_photo_path)
                <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" 
                     class="rounded-circle shadow-sm" 
                     alt="{{ Auth::user()->name }}"
                     style="width: 80px; height: 80px; object-fit: cover; border: 3px solid #fff;">
            @else
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm mx-auto" 
                     style="width: 80px; height: 80px; font-size: 32px; border: 3px solid #fff;">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            @endif
        </div>

        <!-- Nombre del usuario y bienvenida -->
        <h5 class="fw-bold mb-1">Hola, {{ Auth::user()->name }}</h5>
        <p class="text-muted small mb-3">Bienvenido a estudIA</p>
        
        <!-- Botón para ir al perfil -->
        <div class="d-grid gap-2 col-10 mx-auto">
            <a href="{{ route('profile') }}" class="btn btn-outline-primary btn-sm rounded-pill">
                <i class="fas fa-user-circle me-1"></i> Ir a mi Perfil
            </a>
        </div>
    </div>
</div>