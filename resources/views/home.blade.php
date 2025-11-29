@extends('layouts.app')

@section('title', 'Inicio')

@section('content')
<div class="row g-4">
    <!-- COLUMNA IZQUIERDA: Perfil y Accesos -->
    <div class="col-md-3">
        @include('partials.sidebar-profile')
        @include('partials.quick-links')
    </div>

    <!-- COLUMNA CENTRAL: Feed Principal -->
    <div class="col-md-6">
        
        <!-- 1. ANIMACIÓN: "Sigue aprendiendo" -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-body p-0 text-center bg-white">
                <div class="py-3">
                    <img src="https://th.bing.com/th/id/R.5f7ed509f8557dcb7fec81340f132da2?rik=0X5pNy3bu%2bjAnw&pid=ImgRaw&r=0" 
                         alt="Estudiando" 
                         class="img-fluid rounded" 
                         style="max-height: 200px; width: auto;">
                    <div class="mt-3 px-3">
                        <h5 class="fw-bold text-primary mb-1">¡Sigue aprendiendo!</h5>
                        <p class="text-muted small mb-0">Cada día es una nueva oportunidad para crecer.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. CAJA DE PUBLICACIÓN -->
        @include('partials.create-post')
        
        <!-- Alertas de Feedback -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-pill mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-pill mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        
        <!-- Listado de Publicaciones -->
        @forelse($posts as $post)
            @include('partials.post', ['post' => $post])
        @empty
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-inbox fa-4x text-muted mb-3 opacity-25"></i>
                <h5 class="text-muted fw-bold">No hay publicaciones aún</h5>
                <p class="text-muted mb-0">¡Sé el primero en compartir algo interesante!</p>
            </div>
        </div>
        @endforelse
        
        <!-- SECCIÓN DE PAGINACIÓN ELIMINADA -->
        
    </div>

    <!-- COLUMNA DERECHA: Eventos y Amigos -->
    <div class="col-md-3">
        @include('partials.upcoming-events')
        @include('partials.online-friends')
    </div>
</div>
@endsection 