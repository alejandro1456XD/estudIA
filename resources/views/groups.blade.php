@extends('layouts.app')

@section('title', 'Grupos - estudIA')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            
            <!-- Mensajes Flash -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- ENCABEZADO MEJORADO -->
            <div class="text-center mb-5">
                <h1 class="fw-bold text-dark mb-3"><i class="fas fa-users me-2 text-primary"></i>Grupos de Estudio</h1>
                <p class="text-muted fs-5">Conecta, colabora y aprende con otros estudiantes.</p>
            </div>

            <!-- TARJETA DE CREAR GRUPO (Diseño tuyo mejorado) -->
            <div class="card border-0 shadow-sm mb-5">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0" 
                             style="width: 60px; height: 60px; font-size: 24px;">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1">Crear un nuevo grupo</h5>
                            <p class="text-muted mb-0">Comparte conocimientos y conecta con otros estudiantes.</p>
                        </div>
                        <button class="btn btn-dark fw-bold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#createGroupModal">
                            <i class="fas fa-plus me-1"></i> Crear Grupo
                        </button>
                    </div>
                </div>
            </div>

            <!-- SELECTOR DE VISTA (Tu diseño) -->
            <div class="d-flex justify-content-center mb-4">
                <div class="btn-group shadow-sm" role="group">
                    <input type="radio" class="btn-check" name="group_view" id="view_my_groups" autocomplete="off" checked onclick="switchGroupView('my-groups')">
                    <label class="btn btn-outline-primary fw-bold" for="view_my_groups">
                        <i class="fas fa-user-friends me-1"></i> Mis Grupos
                    </label>

                    <input type="radio" class="btn-check" name="group_view" id="view_discover" autocomplete="off" onclick="switchGroupView('discover')">
                    <label class="btn btn-outline-primary fw-bold" for="view_discover">
                        <i class="fas fa-compass me-1"></i> Descubrir
                    </label>
                </div>
            </div>

            <!-- VISTA 1: MIS GRUPOS (Fusión de diseños) -->
            <div id="my-groups-view">
                @if($myGroups->count() > 0)
                    <div class="row g-4">
                        @foreach($myGroups as $group)
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100 group-card">
                                <!-- Header con fondo del grupo (Mi diseño) -->
                                <div class="card-header border-0 p-0 position-relative" style="height: 100px;">
                                    @if($group->cover_photo_path)
                                        <img src="{{ asset('storage/' . $group->cover_photo_path) }}" 
                                             class="w-100 h-100" 
                                             style="object-fit: cover; border-radius: 12px 12px 0 0;">
                                    @else
                                        <div class="w-100 h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px 12px 0 0;"></div>
                                    @endif
                                    
                                    <!-- Foto del grupo superpuesta (Mi diseño) -->
                                    <div class="position-absolute top-100 start-4 translate-middle-y">
                                        <div class="bg-white p-1 rounded-circle shadow-sm">
                                            @if($group->group_photo_path)
                                                <img src="{{ asset('storage/' . $group->group_photo_path) }}" 
                                                     class="rounded-circle" 
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px; font-size: 20px;">
                                                    <i class="fas fa-hashtag"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Card Body (Tu diseño mejorado) -->
                                <div class="card-body pt-5">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="fw-bold text-dark mb-0">{{ $group->name }}</h5>
                                        @if($group->admin_id == Auth::id())
                                            <span class="badge bg-warning text-dark border small">Admin</span>
                                        @else
                                            <span class="badge bg-light text-muted border small">Miembro</span>
                                        @endif
                                    </div>
                                    
                                    <p class="text-muted small mb-3">{{ Str::limit($group->description, 80) }}</p>
                                    
                                    <!-- Información de miembros (Tu diseño) -->
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="d-flex me-2">
                                            @foreach($group->members->take(3) as $member)
                                                <div class="rounded-circle border border-2 border-white bg-secondary d-flex align-items-center justify-content-center text-white small" 
                                                     style="width: 28px; height: 28px; margin-right: -8px; font-size: 10px;">
                                                    {{ substr($member->name, 0, 1) }}
                                                </div>
                                            @endforeach
                                            @if($group->members->count() > 3)
                                                <div class="rounded-circle border border-2 border-white bg-light text-muted d-flex align-items-center justify-content-center small fw-bold" 
                                                     style="width: 28px; height: 28px; margin-right: -8px; font-size: 10px;">
                                                    +{{ $group->members->count() - 3 }}
                                                </div>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $group->members->count() }} miembros</small>
                                    </div>
                                    
                                    <!-- Botones (Tu diseño) -->
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('groups.show', $group->id) }}" class="btn btn-primary w-100 fw-bold rounded-pill">
                                            <i class="fas fa-door-open me-1"></i> Entrar
                                        </a>
                                        
                                        @if($group->admin_id != Auth::id())
                                            <button type="button" class="btn btn-outline-danger rounded-circle" title="Salir del grupo" 
                                                    onclick="confirmLeave('{{ route('groups.leave', $group->id) }}', '{{ $group->name }}')">
                                                <i class="fas fa-sign-out-alt"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 bg-white rounded-3 shadow-sm">
                        <img src="https://cdn-icons-png.flaticon.com/512/1256/1256650.png" width="100" class="mb-3 opacity-50" alt="Grupos vacíos">
                        <h4 class="text-muted fw-bold">No perteneces a ningún grupo</h4>
                        <p class="text-muted">¡Únete a uno existente o crea tu propia comunidad!</p>
                        <button class="btn btn-outline-primary mt-2" onclick="document.getElementById('view_discover').click()">
                            Explorar Grupos
                        </button>
                    </div>
                @endif
            </div>

            <!-- VISTA 2: DESCUBRIR (Fusión de diseños) -->
            <div id="discover-view" style="display: none;">
                @if($otherGroups->count() > 0)
                    <div class="row g-4">
                        @foreach($otherGroups as $group)
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-0 shadow-sm h-100 group-card">
                                <!-- Header con fondo del grupo (Mi diseño) -->
                                <div class="card-header border-0 p-0 position-relative" style="height: 100px;">
                                    @if($group->cover_photo_path)
                                        <img src="{{ asset('storage/' . $group->cover_photo_path) }}" 
                                             class="w-100 h-100" 
                                             style="object-fit: cover; border-radius: 12px 12px 0 0;">
                                    @else
                                        <div class="w-100 h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px 12px 0 0;"></div>
                                    @endif
                                    
                                    <!-- Foto del grupo superpuesta (Mi diseño) -->
                                    <div class="position-absolute top-100 start-4 translate-middle-y">
                                        <div class="bg-white p-1 rounded-circle shadow-sm">
                                            @if($group->group_photo_path)
                                                <img src="{{ asset('storage/' . $group->group_photo_path) }}" 
                                                     class="rounded-circle" 
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px; font-size: 20px;">
                                                    @if($group->category == 'Tecnología') <i class="fas fa-laptop-code"></i>
                                                    @elseif($group->category == 'Idiomas') <i class="fas fa-language"></i>
                                                    @else <i class="fas fa-users"></i> @endif
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Card Body (Tu diseño mejorado) -->
                                <div class="card-body pt-5">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="fw-bold text-dark mb-0">{{ $group->name }}</h5>
                                        <span class="badge bg-light text-dark border small">{{ $group->category ?? 'General' }}</span>
                                    </div>
                                    
                                    <p class="text-muted small mb-3">{{ Str::limit($group->description, 80) }}</p>
                                    
                                    <!-- Información del admin (Tu diseño) -->
                                    <div class="d-flex justify-content-between align-items-center mb-3 text-muted small">
                                        <span><i class="fas fa-user-shield me-1"></i> {{ $group->admin->name }}</span>
                                        <span><i class="fas fa-users me-1"></i> {{ $group->members->count() }}</span>
                                    </div>
                                    
                                    <!-- Botón con efecto puerta (Tu diseño) -->
                                    <form action="{{ route('groups.join', $group->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-success w-100 fw-bold rounded-pill btn-door">
                                            <i class="fas fa-door-closed me-2 door-icon"></i> Unirme al Grupo
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 bg-white rounded-3 shadow-sm">
                        <i class="fas fa-search fa-4x text-muted mb-3 opacity-25"></i>
                        <h4 class="text-muted fw-bold">No hay grupos nuevos</h4>
                        <p class="text-muted">Parece que ya estás en todos los grupos o no hay ninguno disponible.</p>
                        <button class="btn btn-dark mt-2" data-bs-toggle="modal" data-bs-target="#createGroupModal">
                            Crear el Primero
                        </button>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>

<!-- MODAL CREAR GRUPO (Tu diseño - mejorado) -->
<div class="modal fade" id="createGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-users me-2"></i>Nuevo Grupo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('groups.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">NOMBRE DEL GRUPO</label>
                        <input type="text" name="name" class="form-control form-control-lg bg-light border-0" placeholder="Ej: Club de Python" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">CATEGORÍA</label>
                        <select name="category" class="form-select bg-light border-0">
                            <option value="Tecnología">Tecnología</option>
                            <option value="Matemáticas">Matemáticas</option>
                            <option value="Idiomas">Idiomas</option>
                            <option value="Ciencias">Ciencias</option>
                            <option value="Arte">Arte</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">DESCRIPCIÓN</label>
                        <textarea name="description" class="form-control bg-light border-0" rows="3" placeholder="¿Cuál es el objetivo del grupo?"></textarea>
                    </div>

                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_private" id="isPrivate">
                        <label class="form-check-label" for="isPrivate">Grupo Privado (Solo invitación)</label>
                    </div>
                </div>
                <div class="modal-footer p-3 border-0 bg-light rounded-bottom-4">
                    <button type="button" class="btn btn-light fw-bold text-muted" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-dark fw-bold px-4 rounded-pill">Crear Grupo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EMOCIONAL DE SALIDA (Tu diseño) -->
<div class="modal fade" id="leaveGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 text-center">
            <div class="modal-body p-5">
                
                <!-- ESTADO 1: PREGUNTA -->
                <div id="leave-step-1">
                    <div class="mb-4 text-warning">
                        <i class="fas fa-exclamation-circle fa-4x"></i>
                    </div>
                    <h3 class="fw-bold mb-3">¿Seguro que quieres salir?</h3>
                    <p class="text-muted mb-4">
                        Si sales de <strong id="group-name-placeholder" class="text-dark">este grupo</strong>, 
                        perderás acceso al chat y a los archivos compartidos.
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" onclick="showSadFace()">
                            Sí, salir del grupo
                        </button>
                    </div>
                </div>

                <!-- ESTADO 2: CARA TRISTE Y DESPEDIDA -->
                <div id="leave-step-2" style="display: none;">
                    <div class="mb-3 animate-sad">
                        <i class="fas fa-sad-tear fa-5x text-primary"></i>
                    </div>
                    <h3 class="fw-bold mb-2">¡Hasta pronto! 😢</h3>
                    <p class="text-muted">
                        Gracias por haber sido parte de esta comunidad.<br>
                        Esperamos verte de vuelta pronto.
                    </p>
                    <div class="mt-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Saliendo...</span>
                        </div>
                        <p class="small text-muted mt-2">Saliendo del grupo...</p>
                    </div>
                </div>

                <!-- Formulario Oculto -->
                <form id="leave-form" action="" method="POST">
                    @csrf
                </form>

            </div>
        </div>
    </div>
</div>

<script>
    function switchGroupView(view) {
        document.getElementById('my-groups-view').style.display = (view === 'my-groups') ? 'block' : 'none';
        document.getElementById('discover-view').style.display = (view === 'discover') ? 'block' : 'none';
    }

    function confirmLeave(url, groupName) {
        document.getElementById('group-name-placeholder').innerText = groupName;
        document.getElementById('leave-form').action = url;
        document.getElementById('leave-step-1').style.display = 'block';
        document.getElementById('leave-step-2').style.display = 'none';
        var myModal = new bootstrap.Modal(document.getElementById('leaveGroupModal'));
        myModal.show();
    }

    function showSadFace() {
        document.getElementById('leave-step-1').style.display = 'none';
        document.getElementById('leave-step-2').style.display = 'block';
        setTimeout(function() {
            document.getElementById('leave-form').submit();
        }, 2500);
    }
</script>

<style>
    .group-card { 
        transition: transform 0.2s, box-shadow 0.2s; 
        border-radius: 12px;
    }
    .group-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important; 
    }
    .btn-check:checked + .btn-outline-primary { 
        background-color: #0d6efd; 
        color: white; 
    }

    .btn-door { 
        position: relative; 
        overflow: hidden; 
        transition: all 0.3s ease; 
    }
    .btn-door:hover { 
        background-color: #198754; 
        color: white; 
        border-color: #198754; 
    }
    .door-icon { 
        display: inline-block; 
        transition: transform 0.4s ease-in-out; 
        transform-origin: left center; 
    }
    .btn-door:hover .door-icon { 
        transform: perspective(100px) rotateY(-60deg); 
    }

    @keyframes tear-drop {
        0% { transform: translateY(0); opacity: 1; }
        100% { transform: translateY(10px); opacity: 0.5; }
    }
    .animate-sad i { animation: tear-drop 2s infinite alternate; }
</style>
@endsection