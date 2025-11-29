@extends('layouts.app')

@section('title', $group->name . ' - estudIA')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-11">
            
            <!-- Mensajes de Feedback -->
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

            <!-- ENCABEZADO DEL GRUPO -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <!-- ZONA DE PORTADA (FONDO) -->
                <div class="card-header border-0 p-0 position-relative" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    
                    <!-- Imagen de fondo real (si existe) -->
                    @if($group->cover_photo_path)
                        <img src="{{ asset('storage/' . $group->cover_photo_path) }}" 
                             class="w-100 h-100" 
                             style="object-fit: cover;">
                    @endif
                    
                    <!-- BOTÓN DE ENGRANAJE (SOLO ADMIN) -->
                    @if($isAdmin)
                    <div class="position-absolute top-0 end-0 m-3">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle shadow-lg" 
                                    type="button" 
                                    data-bs-toggle="dropdown" 
                                    aria-expanded="false"
                                    style="width: 40px; height: 40px;">
                                <i class="fas fa-cog fa-lg text-secondary"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3 mt-2">
                                <li><h6 class="dropdown-header">Configuración del Grupo</h6></li>
                                <li>
                                    <button class="dropdown-item py-2" onclick="triggerCoverPhotoInput()">
                                        <i class="fas fa-image me-2 text-primary"></i> Cambiar Portada
                                    </button>
                                </li>
                                <li>
                                    <button class="dropdown-item py-2" onclick="triggerGroupPhotoInput()">
                                        <i class="fas fa-camera me-2 text-success"></i> Cambiar Foto de Perfil
                                    </button>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button class="dropdown-item py-2" onclick="openEditGroupModal()">
                                        <i class="fas fa-edit me-2 text-warning"></i> Editar Nombre/Info
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- INFO DEL GRUPO (PERFIL Y NOMBRE) -->
                <div class="card-body p-4 position-relative">
                    <div class="d-flex align-items-end" style="margin-top: -80px;">
                        
                        <!-- Foto de Perfil del Grupo -->
                        <div class="bg-white p-2 rounded-circle shadow-sm me-3 position-relative">
                            @if($group->group_photo_path)
                                <img src="{{ asset('storage/' . $group->group_photo_path) }}" 
                                     class="rounded-circle" 
                                     style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 120px; height: 120px; font-size: 50px;">
                                    <i class="fas fa-hashtag"></i>
                                </div>
                            @endif

                            <!-- Botón rápido de cámara (Opcional, también está en el engranaje) -->
                            @if($isAdmin)
                            <button class="btn btn-light btn-sm rounded-circle position-absolute bottom-0 end-0 shadow-sm border"
                                    style="width: 35px; height: 35px;"
                                    onclick="triggerGroupPhotoInput()"
                                    title="Cambiar foto de perfil">
                                <i class="fas fa-camera text-muted"></i>
                            </button>
                            @endif
                        </div>

                        <!-- Texto: Nombre y Miembros -->
                        <div class="mb-3">
                            <h2 class="fw-bold mb-1 text-dark">{{ $group->name }}</h2>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-light text-dark border">{{ $group->category ?? 'General' }}</span>
                                <span class="text-muted small"><i class="fas fa-users"></i> {{ $group->members->count() }} miembros</span>
                            </div>
                        </div>
                        
                        <!-- Botón Salir (Visible para todos MENOS el admin) -->
                        <div class="ms-auto mb-3 d-none d-md-block">
                            @if(!$isAdmin)
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill fw-bold px-4"
                                        onclick="confirmLeave('{{ route('groups.leave', $group->id) }}', '{{ $group->name }}')">
                                    <i class="fas fa-sign-out-alt me-1"></i> Salir
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- COLUMNA IZQUIERDA: MURO / CHAT -->
                <div class="col-lg-8">
                    
                    <!-- CAJA PARA PUBLICAR -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <form action="{{ route('groups.message', $group->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="d-flex gap-3">
                                    <div class="flex-shrink-0">
                                        @if(Auth::user()->profile_photo_path)
                                            <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;">
                                                {{ substr(Auth::user()->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="w-100">
                                        <textarea name="content" class="form-control border-0 bg-light mb-2" rows="2" placeholder="Comparte algo con el grupo..."></textarea>
                                        
                                        <!-- Previsualización de archivo -->
                                        <div id="file-preview" class="mb-2 text-primary small fw-bold" style="display:none;">
                                            <i class="fas fa-paperclip me-1"></i> <span id="file-name"></span>
                                            <button type="button" class="btn btn-link btn-sm text-danger p-0 ms-2" onclick="clearFile()"><i class="fas fa-times"></i></button>
                                        </div>

                                        <input type="file" name="file" id="masterFile" class="d-none" onchange="showFileName()">

                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <button type="button" class="btn btn-light btn-sm text-muted rounded-circle" onclick="triggerFileInput('image')"><i class="fas fa-image"></i></button>
                                                <button type="button" class="btn btn-light btn-sm text-muted rounded-circle" onclick="triggerFileInput('general')"><i class="fas fa-paperclip"></i></button>
                                            </div>
                                            <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">Publicar</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- LISTA DE MENSAJES -->
                    @forelse($messages as $msg)
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="flex-shrink-0">
                                        <!-- Avatar del autor del mensaje -->
                                        @if($msg->user->profile_photo_path)
                                            <img src="{{ asset('storage/' . $msg->user->profile_photo_path) }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">{{ substr($msg->user->name, 0, 1) }}</div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-0">{{ $msg->user->name }}</h6>
                                        <small class="text-muted">{{ $msg->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                @if($isAdmin || $msg->user_id === Auth::id())
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-circle text-muted" type="button" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-h"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                        <li><button type="button" class="dropdown-item text-danger" onclick="confirmDeleteMessage('{{ route('groups.deleteMessage', ['group' => $group->id, 'message' => $msg->id]) }}')"><i class="fas fa-trash me-2"></i> Eliminar</button></li>
                                        @if($isAdmin && $msg->user_id !== Auth::id())
                                        <li><button type="button" class="dropdown-item text-warning" onclick="confirmExpelMember('{{ route('groups.expelMember', ['group' => $group->id, 'user' => $msg->user_id]) }}', '{{ $msg->user->name }}')"><i class="fas fa-user-slash me-2"></i> Expulsar</button></li>
                                        @endif
                                    </ul>
                                </div>
                                @endif
                            </div>
                            
                            @if($msg->content)<p class="card-text">{{ $msg->content }}</p>@endif
                            
                            @if($msg->file_path)
                                <div class="mt-3">
                                    @if($msg->type == 'image')
                                        <img src="{{ asset('storage/' . $msg->file_path) }}" class="img-fluid rounded" style="max-height: 300px;">
                                    @else
                                        <div class="p-3 bg-light rounded border d-flex align-items-center">
                                            <i class="fas fa-file-alt fa-2x text-primary me-3"></i>
                                            <a href="{{ asset('storage/' . $msg->file_path) }}" target="_blank" class="text-decoration-none">Ver archivo adjunto</a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-comments fa-3x mb-3 opacity-25"></i>
                        <p>No hay mensajes aún.</p>
                    </div>
                    @endforelse
                </div>

                <!-- COLUMNA DERECHA -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Sobre este grupo</h6>
                            <p class="text-muted small">{{ $group->description ?? 'Sin descripción.' }}</p>
                            <hr>
                            <div class="d-flex align-items-center gap-2 text-muted small"><i class="fas fa-globe"></i> {{ $group->is_private ? 'Privado' : 'Público' }}</div>
                        </div>
                    </div>
                    
                    <!-- Lista de Miembros -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white border-0 py-3"><h6 class="fw-bold mb-0">Miembros ({{ $group->members->count() }})</h6></div>
                        <div class="card-body pt-0">
                            <div class="list-group list-group-flush">
                                @foreach($group->members->take(5) as $member)
                                <div class="list-group-item px-0 border-0 d-flex align-items-center gap-3">
                                    <div class="flex-shrink-0">
                                        @if($member->profile_photo_path)
                                            <img src="{{ asset('storage/' . $member->profile_photo_path) }}" class="rounded-circle" style="width: 35px; height: 35px; object-fit: cover;">
                                        @else
                                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; font-size: 12px;">{{ substr($member->name, 0, 1) }}</div>
                                        @endif
                                    </div>
                                    <div>
                                        <h6 class="mb-0 small fw-bold">{{ $member->name }}</h6>
                                        <small class="text-muted">{{ $member->pivot->role == 'admin' ? 'Admin' : 'Miembro' }}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODALES (ocultos) -->

<!-- 1. Modal para Editar Grupo (Contiene los inputs de archivos ocultos) -->
<div class="modal fade" id="editGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">Editar Grupo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editGroupForm" action="{{ route('groups.update', $group->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre</label>
                        <input type="text" name="name" class="form-control" value="{{ $group->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="description" class="form-control" rows="3">{{ $group->description }}</textarea>
                    </div>
                    
                    <!-- INPUTS DE ARCHIVO OCULTOS (Se activan con el engranaje) -->
                    <input type="file" name="group_photo" id="groupPhotoInput" class="d-none" accept="image/*">
                    <input type="file" name="cover_photo" id="coverPhotoInput" class="d-none" accept="image/*">
                    
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 2. Modal Salir -->
<div class="modal fade" id="leaveGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 text-center">
            <div class="modal-body p-5">
                <h3 class="fw-bold mb-3">¿Salir del grupo?</h3>
                <p class="text-muted mb-4">Perderás acceso al chat y archivos.</p>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <form id="leave-form" action="" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger rounded-pill px-4">Sí, salir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. Modales de Moderación (Eliminar/Expulsar) -->
<div class="modal fade" id="deleteMessageModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow-lg p-4 text-center"><h5 class="fw-bold mb-3">¿Eliminar mensaje?</h5><form id="deleteMessageForm" method="POST">@csrf @method('DELETE')<button type="submit" class="btn btn-danger rounded-pill px-4">Eliminar</button></form></div></div></div>
<div class="modal fade" id="expelMemberModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content border-0 shadow-lg p-4 text-center"><h5 class="fw-bold mb-3">¿Expulsar miembro?</h5><form id="expelMemberForm" method="POST">@csrf<button type="submit" class="btn btn-warning rounded-pill px-4">Expulsar</button></form></div></div></div>

<script>
    // --- LÓGICA DE ARCHIVOS DEL MURO ---
    const masterFile = document.getElementById('masterFile');
    const fileNameSpan = document.getElementById('file-name');
    const previewDiv = document.getElementById('file-preview');

    function triggerFileInput(type) {
        masterFile.value = null; 
        masterFile.setAttribute('accept', type === 'image' ? 'image/*' : '');
        masterFile.click();
    }
    
    function showFileName() {
        if(masterFile.files.length > 0) {
            fileNameSpan.textContent = masterFile.files[0].name;
            previewDiv.style.display = 'block';
        }
    }
    function clearFile() {
        masterFile.value = null;
        previewDiv.style.display = 'none';
    }
    masterFile.addEventListener('change', showFileName);

    // --- LÓGICA DEL ENGRANAJE (FOTOS DE PERFIL Y PORTADA) ---
    const groupPhotoInput = document.getElementById('groupPhotoInput');
    const coverPhotoInput = document.getElementById('coverPhotoInput');
    const editGroupForm = document.getElementById('editGroupForm');

    function triggerGroupPhotoInput() { groupPhotoInput.click(); }
    function triggerCoverPhotoInput() { coverPhotoInput.click(); }
    function openEditGroupModal() { new bootstrap.Modal(document.getElementById('editGroupModal')).show(); }

    // Al seleccionar una foto, enviar el formulario automáticamente
    groupPhotoInput.addEventListener('change', function() { if(this.files.length > 0) editGroupForm.submit(); });
    coverPhotoInput.addEventListener('change', function() { if(this.files.length > 0) editGroupForm.submit(); });

    // --- MODALES AUXILIARES ---
    function confirmLeave(url) { document.getElementById('leave-form').action = url; new bootstrap.Modal(document.getElementById('leaveGroupModal')).show(); }
    function confirmDeleteMessage(url) { document.getElementById('deleteMessageForm').action = url; new bootstrap.Modal(document.getElementById('deleteMessageModal')).show(); }
    function confirmExpelMember(url) { document.getElementById('expelMemberForm').action = url; new bootstrap.Modal(document.getElementById('expelMemberModal')).show(); }
</script>
@endsection