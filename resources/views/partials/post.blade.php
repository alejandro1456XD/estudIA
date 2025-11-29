<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <!-- ENCABEZADO: Usuario y Menú -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <!-- Foto de perfil -->
                <div class="flex-shrink-0">
                    @if($post->user && $post->user->profile_photo_path)
                        <img src="{{ asset('storage/' . $post->user->profile_photo_path) }}" 
                             class="rounded-circle" 
                             alt="{{ $post->user->name }}"
                             style="width: 45px; height: 45px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center fw-bold" 
                             style="width: 45px; height: 45px; font-size: 18px;">
                            {{ substr($post->user->name ?? 'U', 0, 1) }}
                        </div>
                    @endif
                </div>
                <div>
                    <!-- Usamos variables en INGLÉS: name, created_at, type -->
                    <h6 class="fw-bold mb-0 text-dark">{{ $post->user->name ?? 'Usuario' }}</h6>
                    <small class="text-muted" style="font-size: 0.8rem;">
                        {{ $post->created_at->diffForHumans() }}
                        @if($post->type == 'photo') <i class="fas fa-camera ms-1"></i> @endif
                        @if($post->type == 'document') <i class="fas fa-file-alt ms-1"></i> @endif
                    </small>
                </div>
            </div>

            <!-- MENÚ DE 3 PUNTOS: Solo para el dueño (Usamos user_id) -->
            @if(Auth::id() == $post->user_id)
            <div class="dropdown">
                <button class="btn btn-light btn-sm rounded-circle text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-h"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-sm rounded-3">
                    <li>
                        <!-- ELIMINAR: Abre el modal único usando el ID del post -->
                        <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deletePostModal{{ $post->id }}">
                            <i class="fas fa-trash-alt me-2"></i> Eliminar publicación
                        </button>
                    </li>
                </ul>
            </div>
            @endif
        </div>

        <!-- CONTENIDO DE TEXTO (Variable: content) -->
        @if($post->content)
            <p class="card-text text-dark mb-3">{{ $post->content }}</p>
        @endif

        <!-- ARCHIVOS ADJUNTOS (Variable: file_path y type) -->
        @if($post->file_path)
            <div class="mb-3">
                @if($post->type == 'photo')
                    <img src="{{ asset('storage/' . $post->file_path) }}" class="img-fluid rounded-3 w-100 shadow-sm" style="max-height: 500px; object-fit: cover;">
                @elseif($post->type == 'video')
                    <div class="alert alert-warning small">Video no disponible.</div>
                @else
                    <!-- Documentos -->
                    <div class="d-flex align-items-center p-3 bg-light rounded-3 border">
                        <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                        <div class="flex-grow-1 overflow-hidden">
                            <h6 class="mb-0 text-truncate">Archivo adjunto</h6>
                            <a href="{{ asset('storage/' . $post->file_path) }}" target="_blank" class="text-decoration-none small fw-bold">Descargar</a>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- PIE DE TARJETA -->
        <hr class="my-2 opacity-10">
        <div class="d-flex justify-content-between align-items-center">
            <button class="btn btn-sm btn-light text-muted rounded-pill px-3"><i class="far fa-heart me-1"></i> Me gusta</button>
            <button class="btn btn-sm btn-light text-muted rounded-pill px-3"><i class="far fa-comment me-1"></i> Comentar</button>
            <button class="btn btn-sm btn-light text-muted rounded-pill px-3"><i class="fas fa-share me-1"></i> Compartir</button>
        </div>
    </div>
</div>

<!-- MODAL DE CONFIRMACIÓN ÚNICO -->
<!-- El ID del modal incluye {{ $post->id }} para que sea único por publicación -->
@if(Auth::id() == $post->user_id)
<div class="modal fade" id="deletePostModal{{ $post->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-body p-4 text-center">
                <div class="mb-3 text-danger bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3">
                    <i class="fas fa-trash-alt fa-2x"></i>
                </div>
                <h5 class="fw-bold mb-2 text-dark">¿Eliminar publicación?</h5>
                <p class="text-muted mb-4 small">Esta acción no se puede deshacer.</p>
                
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    
                    <!-- Formulario que apunta a la ruta destroy -->
                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">Sí, eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif  