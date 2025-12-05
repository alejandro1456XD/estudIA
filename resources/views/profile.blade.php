@extends('layouts.app')

@section('title', 'Mi Perfil - estudIA')

@section('content')
<div class="row">
    <!-- Sidebar izquierda -->
    <div class="col-md-3">
        @include('partials.sidebar-profile')
        @include('partials.quick-links')
    </div>

    <!-- Contenido central -->
    <div class="col-md-6">
        
        <!-- 1. MENSAJES DE ÉXITO -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- 2. MENSAJES DE ERROR -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-exclamation-triangle me-1"></i> ¡Ups! Algo salió mal:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Header del perfil -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body text-center p-4">
                
                <!-- FOTO DE PERFIL -->
                <div class="mb-3 position-relative d-inline-block">
                    @if($user->profile_photo_path)
                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" 
                             class="rounded-circle shadow-sm" 
                             alt="Foto de perfil" 
                             style="width: 120px; height: 120px; object-fit: cover; border: 4px solid #fff;">
                    @else
                        <!-- Avatar generado si no hay foto -->
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm" 
                             style="width: 120px; height: 120px; font-size: 48px; border: 4px solid #fff;">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                </div>

                <h4 class="fw-bold mb-1">{{ $user->name }}</h4>
                <p class="text-muted mb-3">{{ $user->bio ?? 'Estudiante en estudIA' }}</p>
                
                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-primary btn-sm rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="fas fa-edit me-1"></i> Editar Perfil
                    </button>
                </div>
            </div>
        </div>

        <!-- Información del perfil -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h6 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2 text-primary"></i>Información Personal</h6>
            </div>
            <div class="card-body pt-0">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="p-3 bg-light rounded-3">
                            <p class="mb-2"><strong><i class="fas fa-user-tag me-2 text-muted"></i>Bio:</strong> {{ $user->bio ?? 'Sin biografía definida' }}</p>
                            <p class="mb-2"><strong><i class="fas fa-envelope me-2 text-muted"></i>Email:</strong> {{ $user->email }}</p>
                            
                            <!-- MOSTRAR TELÉFONO SI EXISTE -->
                            @if($user->phone)
                                <p class="mb-2"><strong><i class="fab fa-whatsapp me-2 text-success"></i>WhatsApp:</strong> {{ $user->phone }}</p>
                            @endif

                            <p class="mb-2"><strong><i class="fas fa-calendar me-2 text-muted"></i>Miembro desde:</strong> {{ $user->created_at->format('d/m/Y') }}</p>
                            
                            <!-- Contamos amigos de forma segura -->
                            <p class="mb-0"><strong><i class="fas fa-users me-2 text-muted"></i>Amigos:</strong> 
                                {{ method_exists($user, 'friends') ? $user->friends()->count() : 0 }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PUBLICACIONES DEL USUARIO (REALES) -->
        <h5 class="fw-bold mb-3 px-1">Publicaciones de {{ $user->name }}</h5>

        @if($user->posts && $user->posts->count() > 0)
            @foreach($user->posts as $post)
                @include('partials.post', ['post' => $post])
            @endforeach
        @else
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-muted mb-3 opacity-25"></i>
                    <p class="text-muted mb-0">Este usuario aún no ha publicado nada.</p>
                </div>
            </div>
        @endif
        
    </div>

    <!-- Sidebar derecha -->
    <div class="col-md-3">
        @include('partials.upcoming-events')
        @include('partials.online-friends')
    </div>
</div>


<!-- MODAL EDITAR PERFIL -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Editar Perfil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="modal-body">
                    <!-- Nombre -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Nombre Completo</label>
                        <input type="text" class="form-control rounded-pill px-3" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <!-- Teléfono WhatsApp (NUEVO CAMPO) -->
                    <div class="mb-3">
                        <label for="phone" class="form-label fw-bold">WhatsApp (Con código país)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-success text-white border-0 rounded-start-pill ps-3"><i class="fab fa-whatsapp"></i></span>
                            <input type="text" class="form-control rounded-end-pill" id="phone" name="phone" 
                                   value="{{ old('phone', $user->phone) }}" 
                                   placeholder="Ej: 59170707070">
                        </div>
                        <div class="form-text text-muted ms-2 small">Necesario para habilitar el botón de chat rápido.</div>
                    </div>

                    <!-- Bio -->
                    <div class="mb-3">
                        <label for="bio" class="form-label fw-bold">Biografía / Carrera</label>
                        <input type="text" class="form-control rounded-pill px-3" id="bio" name="bio" value="{{ old('bio', $user->bio) }}" placeholder="Ej: Ingeniería de Sistemas">
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Correo Electrónico</label>
                        <input type="email" class="form-control rounded-pill px-3" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>

                    <!-- Foto -->
                    <div class="mb-3">
                        <label for="photo" class="form-label fw-bold">Foto de Perfil</label>
                        <input type="file" class="form-control rounded-pill" id="photo" name="photo" accept="image/*">
                        <div class="form-text text-muted ms-2">JPG o PNG. Máximo 2MB.</div>
                    </div>
                </div>
                
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- SCRIPT: Si hay errores, volver a abrir el modal automáticamente -->
@if($errors->any())
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var myModal = new bootstrap.Modal(document.getElementById('editProfileModal'));
        myModal.show();
    });
</script>
@endif

@endsection