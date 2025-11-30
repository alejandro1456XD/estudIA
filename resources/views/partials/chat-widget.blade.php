<!-- ==========================================
     TARJETA DE ACCESO AL CHAT (DASHBOARD)
     ========================================== -->
<div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary text-white overflow-hidden" 
     style="cursor: pointer; transition: transform 0.2s;"
     onmouseover="this.style.transform='scale(1.02)'"
     onmouseout="this.style.transform='scale(1)'"
     data-bs-toggle="modal" 
     data-bs-target="#chatSelectionModal">
    
    <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);"></div>

    <div class="card-body d-flex align-items-center justify-content-between position-relative">
        <div class="d-flex align-items-center">
            <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3">
                <i class="fas fa-comments fa-lg text-white"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0">Chat</h5>
                <small class="text-white-50">
                    {{ auth()->user()->conversations()->count() }} chats activos
                </small>
            </div>
        </div>
        <div>
            <i class="fas fa-chevron-right"></i>
        </div>
    </div>
</div>

<!-- ==========================================
     MODAL MULTI-FUNCIONAL (3 PESTAÑAS)
     ========================================== -->
<div class="modal fade" id="chatSelectionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            
            <!-- Encabezado con 3 Pestañas -->
            <div class="modal-header border-bottom-0 pb-0 bg-light rounded-top-4">
                <ul class="nav nav-tabs border-bottom-0 w-100" id="chatTabs" role="tablist">
                    <!-- Pestaña 1: Amigos (Privado) -->
                    <li class="nav-item flex-fill text-center" role="presentation">
                        <button class="nav-link active fw-bold w-100" id="private-tab" data-bs-toggle="tab" data-bs-target="#private-content" type="button" role="tab">
                            <i class="fas fa-user me-1"></i>Amigos
                        </button>
                    </li>
                    <!-- Pestaña 2: Mis Grupos -->
                    <li class="nav-item flex-fill text-center" role="presentation">
                        <button class="nav-link fw-bold w-100" id="mygroups-tab" data-bs-toggle="tab" data-bs-target="#mygroups-content" type="button" role="tab">
                            <i class="fas fa-users me-1"></i>Mis Grupos
                        </button>
                    </li>
                    <!-- Pestaña 3: Crear Grupo -->
                    <li class="nav-item flex-fill text-center" role="presentation">
                        <button class="nav-link fw-bold w-100" id="create-group-tab" data-bs-toggle="tab" data-bs-target="#create-group-content" type="button" role="tab">
                            <i class="fas fa-plus-circle me-1"></i>Crear
                        </button>
                    </li>
                </ul>
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4 pt-3">
                <div class="tab-content" id="chatTabsContent">
                    
                    <!-- VISTA 1: CHAT PRIVADO (AMIGOS) -->
                    <div class="tab-pane fade show active" id="private-content" role="tabpanel">
                        <div class="input-group mb-3">
                            <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" id="searchFriendInput" class="form-control border-start-0 ps-0" placeholder="Buscar amigo..." onkeyup="filterBootstrapFriends()">
                        </div>

                        <div class="list-group list-group-flush overflow-auto custom-scroll" style="max-height: 300px;" id="friendsList">
                            @forelse(auth()->user()->all_friends as $friend)
                                <form action="{{ route('chat.private') }}" method="POST" class="friend-item-bs w-100">
                                    @csrf
                                    <input type="hidden" name="recipient_id" value="{{ $friend->id }}">
                                    <button type="submit" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 d-flex align-items-center p-2 hover-bg-light">
                                        <div class="position-relative me-3">
                                            <img src="{{ $friend->profile_picture }}" class="rounded-circle object-fit-cover" width="40" height="40" alt="">
                                            <span class="position-absolute bottom-0 start-100 translate-middle p-1 bg-success border border-light rounded-circle"></span>
                                        </div>
                                        <div class="flex-grow-1 text-start">
                                            <h6 class="mb-0 fw-bold text-dark friend-name">{{ $friend->name }}</h6>
                                            <small class="text-muted">Iniciar conversación</small>
                                        </div>
                                        <i class="fas fa-comment text-primary opacity-50"></i>
                                    </button>
                                </form>
                            @empty
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-user-friends fa-2x mb-3 opacity-25"></i>
                                    <p class="mb-0">No tienes amigos agregados aún.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- VISTA 2: MIS GRUPOS (ACTUALIZADO CON FOTOS) -->
                    <div class="tab-pane fade" id="mygroups-content" role="tabpanel">
                        <div class="list-group list-group-flush overflow-auto custom-scroll" style="max-height: 300px;">
                            @php
                                $myGroups = auth()->user()->conversations->where('is_group', true);
                            @endphp

                            @forelse($myGroups as $group)
                                <a href="{{ route('chat.show', $group->id) }}" class="list-group-item list-group-item-action border-0 rounded-3 mb-1 d-flex align-items-center p-2">
                                    <div class="position-relative me-3">
                                        <!-- AQUI ESTÁ EL CAMBIO: Usamos la imagen real del grupo -->
                                        <img src="{{ $group->image }}" class="rounded-circle object-fit-cover shadow-sm" width="40" height="40" alt="Grupo">
                                    </div>
                                    <div class="flex-grow-1 text-start">
                                        <h6 class="mb-0 fw-bold text-dark">{{ $group->title }}</h6>
                                        <small class="text-muted">{{ $group->participants->count() }} participantes</small>
                                    </div>
                                    <span class="badge bg-light text-dark rounded-pill"><i class="fas fa-chevron-right"></i></span>
                                </a>
                            @empty
                                <div class="text-center py-5 text-muted">
                                    <i class="fas fa-users-slash fa-2x mb-3 opacity-25"></i>
                                    <p class="mb-2">No perteneces a ningún grupo.</p>
                                    <button class="btn btn-sm btn-primary" onclick="new bootstrap.Tab(document.querySelector('#create-group-tab')).show()">
                                        Crear uno ahora
                                    </button>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- VISTA 3: CREAR GRUPO -->
                    <div class="tab-pane fade" id="create-group-content" role="tabpanel">
                        <form action="{{ route('chat.group') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted">NOMBRE DEL GRUPO</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="fas fa-pen text-muted"></i></span>
                                    <input type="text" name="name" class="form-control" placeholder="Ej: Proyecto Final" required>
                                </div>
                            </div>

                            <label class="form-label small fw-bold text-muted mb-2">SELECCIONAR PARTICIPANTES</label>
                            <div class="border rounded-3 p-2 overflow-auto mb-3 bg-light custom-scroll" style="max-height: 180px;">
                                @forelse(auth()->user()->all_friends as $friend)
                                    <div class="form-check d-flex align-items-center mb-2 p-2 hover-bg-white rounded">
                                        <input class="form-check-input me-3 mt-0" type="checkbox" name="users[]" value="{{ $friend->id }}" id="group_friend_{{ $friend->id }}" style="width: 1.2em; height: 1.2em;">
                                        <label class="form-check-label d-flex align-items-center w-100 cursor-pointer" for="group_friend_{{ $friend->id }}">
                                            <img src="{{ $friend->profile_picture }}" class="rounded-circle me-2 object-fit-cover" width="30" height="30" alt="">
                                            <span class="fw-medium">{{ $friend->name }}</span>
                                        </label>
                                    </div>
                                @empty
                                    <p class="text-center text-muted small my-2">Necesitas agregar amigos primero.</p>
                                @endforelse
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary fw-bold py-2">
                                    <i class="fas fa-plus-circle me-2"></i>Crear Grupo
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function filterBootstrapFriends() {
        var input = document.getElementById("searchFriendInput");
        var filter = input.value.toUpperCase();
        var container = document.getElementById("friendsList");
        var items = container.getElementsByClassName("friend-item-bs");

        for (var i = 0; i < items.length; i++) {
            var nameElement = items[i].getElementsByClassName("friend-name")[0];
            if (nameElement) {
                var txtValue = nameElement.textContent || nameElement.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    items[i].style.display = "";
                } else {
                    items[i].style.display = "none";
                }
            }
        }
    }
</script>

<style>
    /* Scrollbar sutil para las listas */
    .custom-scroll::-webkit-scrollbar { width: 5px; }
    .custom-scroll::-webkit-scrollbar-track { background: #f1f1f1; }
    .custom-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    .hover-bg-light:hover { background-color: #f8f9fa; }
    .hover-bg-white:hover { background-color: #ffffff; }
    
    .nav-tabs .nav-link { color: #64748b; border: none; border-bottom: 2px solid transparent; }
    .nav-tabs .nav-link.active { color: #0d6efd; background: transparent; border-bottom: 2px solid #0d6efd; }
    .nav-tabs .nav-link:hover:not(.active) { color: #334155; border-bottom: 2px solid #e2e8f0; }
</style>