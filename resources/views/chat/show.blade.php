@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="height: 85vh;">
                
                <!-- 1. HEADER -->
                <div class="card-header bg-white p-3 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('home') }}" class="btn btn-light btn-sm rounded-circle me-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                            <i class="fas fa-arrow-left text-muted"></i>
                        </a>

                        <div class="position-relative">
                            <img src="{{ $conversation->image }}" class="rounded-circle object-fit-cover" width="45" height="45" alt="Avatar">
                            @if($conversation->is_group)
                                <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-primary border border-white p-1" style="font-size: 0.6rem;">G</span>
                            @endif
                        </div>
                        <div class="ms-3">
                            <h5 class="mb-0 fw-bold text-dark">{{ $conversation->title }}</h5>
                            @if($conversation->is_group)
                                <small class="text-muted clickable" data-bs-toggle="modal" data-bs-target="#groupMembersModal" style="cursor: pointer;">
                                    {{ $conversation->participants->count() }} participantes <i class="fas fa-chevron-right fa-xs ms-1"></i>
                                </small>
                            @else
                                <small class="text-success fw-bold" style="font-size: 0.8rem;"><i class="fas fa-circle fa-xs me-1"></i>En línea</small>
                            @endif
                        </div>
                    </div>
                    
                    <!-- BOTONES DE ACCIÓN (DERECHA) -->
                    <div class="d-flex align-items-center gap-2">
                        
                        <!-- BOTÓN DE WHATSAPP (NUEVO) -->
                        <!-- Solo aparece si NO es grupo y si el otro usuario tiene teléfono -->
                        @if(!$conversation->is_group)
                            @php
                                // Buscamos al otro participante que no soy yo
                                $otherUser = $conversation->participants->where('id', '!=', auth()->id())->first();
                            @endphp

                            @if($otherUser && $otherUser->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $otherUser->phone) }}" target="_blank" 
                                   class="btn btn-success text-white rounded-pill px-3 py-1 shadow-sm d-flex align-items-center gap-2"
                                   style="background-color: #25D366; border: none; text-decoration: none;"
                                   title="Abrir chat en WhatsApp">
                                    <i class="fab fa-whatsapp fa-lg"></i> 
                                    <span class="d-none d-md-inline fw-bold small">WhatsApp</span>
                                </a>
                            @endif
                        @endif

                        <!-- MENÚ DE 3 PUNTOS (OPCIONES) -->
                        <div class="dropdown">
                            <button class="btn btn-light rounded-circle text-muted border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4">
                                
                                @if($conversation->is_group)
                                    @php
                                        $me = $conversation->participants->where('id', auth()->id())->first();
                                        $isCreator = $conversation->admin_id == auth()->id();
                                        $isAdmin = $isCreator || ($me && $me->pivot->is_admin);
                                    @endphp

                                    <li><h6 class="dropdown-header">Grupo</h6></li>
                                    <li>
                                        <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#groupMembersModal">
                                            <i class="fas fa-users me-2 text-primary"></i>Ver Miembros
                                        </button>
                                    </li>

                                    @if($isAdmin)
                                        <li><hr class="dropdown-divider"></li>
                                        <li><h6 class="dropdown-header">Administración</h6></li>
                                        <li>
                                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#changePhotoModal">
                                                <i class="fas fa-camera me-2 text-secondary"></i>Cambiar Foto
                                            </button>
                                        </li>
                                        <li>
                                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#addMembersModal">
                                                <i class="fas fa-user-plus me-2 text-secondary"></i>Añadir Personas
                                            </button>
                                        </li>
                                    @endif

                                    <li><hr class="dropdown-divider"></li>

                                    @if(!$isCreator)
                                        <!-- Opción Salir -->
                                        <li>
                                            <button class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#leaveGroupModal">
                                                <i class="fas fa-sign-out-alt me-2"></i>Abandonar Grupo
                                            </button>
                                        </li>
                                    @endif

                                    @if($isCreator)
                                        <!-- Opción Eliminar -->
                                        <li>
                                            <button class="dropdown-item text-danger fw-bold bg-danger-subtle" data-bs-toggle="modal" data-bs-target="#deleteGroupModal">
                                                <i class="fas fa-trash-alt me-2"></i>Eliminar Grupo
                                            </button>
                                        </li>
                                    @endif
                                @else
                                    <!-- Opciones para chat privado -->
                                    <li><a class="dropdown-item" href="#">Ver Perfil</a></li>
                                    <li><a class="dropdown-item text-danger" href="#">Eliminar Chat</a></li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- 2. BODY (MENSAJES) -->
                <div class="card-body bg-light overflow-auto p-4" id="messagesContainer" style="background-color: #f8f9fa;">
                    @foreach($conversation->messages as $message)
                        @php $isMe = $message->user_id == auth()->id(); @endphp
                        <div class="d-flex mb-3 {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
                            @if(!$isMe && $conversation->is_group)
                                <img src="{{ $message->user->profile_picture }}" class="rounded-circle me-2 align-self-end mb-1" width="30" height="30">
                            @endif
                            <div class="d-flex flex-column {{ $isMe ? 'align-items-end' : 'align-items-start' }}" style="max-width: 75%;">
                                @if(!$isMe && $conversation->is_group)
                                    <small class="text-muted mb-1 ms-1" style="font-size: 0.75rem;">
                                        {{ $message->user->name }}
                                        @php 
                                            $msgUser = $conversation->participants->where('id', $message->user_id)->first();
                                        @endphp
                                        @if($conversation->admin_id == $message->user_id)
                                            <span class="badge bg-warning text-dark" style="font-size: 0.6rem;">Creador</span>
                                        @elseif($msgUser && $msgUser->pivot->is_admin)
                                            <span class="badge bg-info text-dark" style="font-size: 0.6rem;">Admin</span>
                                        @endif
                                    </small>
                                @endif
                                <div class="p-3 shadow-sm position-relative text-break {{ $isMe ? 'bg-primary text-white rounded-4 rounded-bottom-end-0' : 'bg-white text-dark rounded-4 rounded-bottom-start-0' }}">
                                    @if($message->type == 'file')
                                        <div class="mb-2 bg-white bg-opacity-25 p-2 rounded">
                                            <a href="{{ asset('storage/' . $message->attachment) }}" target="_blank" class="text-decoration-none {{ $isMe ? 'text-white' : 'text-primary' }} d-flex align-items-center">
                                                <i class="fas fa-file-download me-2"></i><span>Ver adjunto</span>
                                            </a>
                                        </div>
                                    @endif
                                    <p class="mb-0">{{ $message->body }}</p>
                                </div>
                                <small class="text-muted mt-1 mx-1 opacity-75" style="font-size: 0.7rem;">{{ $message->created_at->format('H:i') }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- 3. FOOTER -->
                <div class="card-footer bg-white p-3 border-top">
                    <form action="{{ route('chat.send', $conversation->id) }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                        @csrf
                        <div class="position-relative">
                            <input type="file" name="attachment" id="fileInput" class="d-none" onchange="this.nextElementSibling.classList.add('text-primary')">
                            <label for="fileInput" class="btn btn-light text-secondary rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="fas fa-paperclip"></i>
                            </label>
                        </div>
                        <input type="text" name="body" class="form-control rounded-pill bg-light border-0 py-2 px-4" placeholder="Escribe un mensaje..." autocomplete="off">
                        <button type="submit" class="btn btn-primary rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: GESTIONAR MIEMBROS -->
<div class="modal fade" id="groupMembersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Miembros del Grupo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="list-group list-group-flush">
                    @foreach($conversation->participants as $participant)
                        <div class="list-group-item d-flex align-items-center justify-content-between p-3 border-0">
                            <div class="d-flex align-items-center">
                                <img src="{{ $participant->profile_picture }}" class="rounded-circle me-3" width="40" height="40">
                                <div>
                                    <h6 class="mb-0 fw-bold">
                                        {{ $participant->name }}
                                        @if($participant->id == auth()->id()) (Tú) @endif
                                    </h6>
                                    @if($conversation->admin_id == $participant->id)
                                        <span class="badge bg-warning text-dark" style="font-size: 0.7rem;">Creador</span>
                                    @elseif($participant->pivot->is_admin)
                                        <span class="badge bg-info text-dark" style="font-size: 0.7rem;">Admin</span>
                                    @else
                                        <small class="text-muted">Miembro</small>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Botones de Acción (Solo visibles para Admins) -->
                            @if(isset($isAdmin) && $isAdmin && $participant->id != auth()->id())
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light rounded-circle" data-bs-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        @if($participant->id != $conversation->admin_id)
                                            <!-- Hacer Admin (Solo si no es ya admin) -->
                                            @if(!$participant->pivot->is_admin)
                                                <li>
                                                    <form action="{{ route('chat.makeAdmin', ['id' => $conversation->id, 'user' => $participant->id]) }}" method="POST">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item"><i class="fas fa-crown me-2 text-warning"></i>Hacer Admin</button>
                                                    </form>
                                                </li>
                                            @endif
                                            <!-- Expulsar -->
                                            <li>
                                                <form action="{{ route('chat.removeMember', ['id' => $conversation->id, 'user' => $participant->id]) }}" method="POST" onsubmit="return confirm('¿Expulsar a este usuario?');">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item text-danger"><i class="fas fa-user-times me-2"></i>Expulsar</button>
                                                </form>
                                            </li>
                                        @else
                                            <li><span class="dropdown-item disabled text-muted">Es el creador</span></li>
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: CAMBIAR FOTO -->
<div class="modal fade" id="changePhotoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Cambiar Foto de Grupo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('chat.photo', $conversation->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3 text-center">
                        <img src="{{ $conversation->image }}" class="rounded-circle mb-3 shadow-sm" width="100" height="100">
                        <input type="file" name="icon" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: AÑADIR MIEMBROS -->
<div class="modal fade" id="addMembersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-bottom-0">
                <h5 class="modal-title fw-bold">Añadir Miembros</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('chat.addMembers', $conversation->id) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-muted small fw-bold">SELECCIONA AMIGOS</label>
                        <div class="border rounded p-2 overflow-auto" style="max-height: 200px;">
                            @foreach(auth()->user()->all_friends as $friend)
                                @if(!$conversation->participants->contains($friend->id))
                                    <div class="form-check p-2">
                                        <input class="form-check-input me-2" type="checkbox" name="users[]" value="{{ $friend->id }}" id="add_user_{{ $friend->id }}">
                                        <label class="form-check-label d-flex align-items-center" for="add_user_{{ $friend->id }}">
                                            <img src="{{ $friend->profile_picture }}" class="rounded-circle me-2" width="30" height="30">
                                            {{ $friend->name }}
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100 rounded-pill">Añadir al Grupo</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: CONFIRMAR ABANDONAR GRUPO -->
<div class="modal fade" id="leaveGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-sign-out-alt fa-2x"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-2">¿Salir del grupo?</h5>
                <p class="text-muted mb-4 small">
                    ¿Estás seguro de que quieres abandonar <strong>{{ $conversation->title }}</strong>?<br>
                    Ya no podrás leer los mensajes ni participar en la conversación.
                </p>
                
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    
                    <form action="{{ route('chat.leave', $conversation->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Sí, salir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: CONFIRMAR ELIMINAR GRUPO -->
<div class="modal fade" id="deleteGroupModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-2">¿Eliminar este grupo?</h5>
                <p class="text-muted mb-4 small">
                    Esta acción <strong>borrará permanentemente</strong> el grupo y todos sus mensajes para todos los participantes. No se puede deshacer.
                </p>
                
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    
                    <form action="{{ route('chat.delete', $conversation->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Sí, eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var container = document.getElementById('messagesContainer');
        if(container) { container.scrollTop = container.scrollHeight; }
    });
</script>
@endsection