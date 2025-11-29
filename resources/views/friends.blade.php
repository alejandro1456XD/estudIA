@extends('layouts.app')

@section('title', 'Amigos - estudIA')

@section('content')
<div class="friends-container">
    
    <!-- Mensajes de Feedback -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Barra de búsqueda -->
    <div class="search-section">
        <div class="search-card">
            <h3 class="search-title">Buscar Amigos</h3>
            <div class="search-input-group">
                <input type="text" id="friendSearch" class="search-input" placeholder="Buscar por nombre o email...">
                <button class="search-button" id="searchButton">
                    <i class="fas fa-search"></i>
                </button>
            </div>
            <div id="searchResults" class="search-results"></div>
        </div>
    </div>

    <!-- Solicitudes pendientes -->
    @if($pendingRequests->count() > 0)
    <div class="requests-section">
        <div class="requests-card">
            <h3 class="section-title">
                Solicitudes de Amistad 
                <span class="badge bg-danger rounded-pill ms-2" style="font-size: 0.8rem;">{{ $pendingRequests->count() }}</span>
            </h3>
            @foreach($pendingRequests as $request)
            <div class="request-item">
                <div class="request-info">
                    <!-- MEJORA: Foto real o Iniciales -->
                    <div class="user-avatar-container">
                        @if($request->user->profile_photo_path)
                            <img src="{{ asset('storage/' . $request->user->profile_photo_path) }}" class="user-avatar-img" alt="{{ $request->user->name }}">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($request->user->name) }}&background=3498db&color=fff" class="user-avatar-img" alt="{{ $request->user->name }}">
                        @endif
                    </div>
                    <div class="request-details">
                        <h4 class="user-name">{{ $request->user->name }}</h4>
                        <p class="user-email">{{ $request->user->email }}</p>
                    </div>
                </div>
                <div class="request-actions">
                    <form action="{{ route('friends.accept', $request->user) }}" method="POST" class="action-form">
                        @csrf
                        <button type="submit" class="btn-accept">
                            <i class="fas fa-check"></i> Aceptar
                        </button>
                    </form>
                    <form action="{{ route('friends.reject', $request->user) }}" method="POST" class="action-form">
                        @csrf
                        <button type="submit" class="btn-reject">
                            <i class="fas fa-times"></i> Rechazar
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="friends-layout">
        <!-- Lista de amigos -->
        <div class="friends-main">
            <div class="friends-card">
                <h3 class="section-title">Mis Amigos ({{ $friends->count() }})</h3>
                
                @if($friends->count() > 0)
                <div class="friends-grid">
                    @foreach($friends as $friend)
                    <div class="friend-card">
                        <!-- MEJORA: Foto real o Iniciales -->
                        <div class="friend-avatar-container">
                            @if($friend->profile_photo_path)
                                <img src="{{ asset('storage/' . $friend->profile_photo_path) }}" class="friend-avatar-img" alt="{{ $friend->name }}">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($friend->name) }}&background=9b59b6&color=fff" class="friend-avatar-img" alt="{{ $friend->name }}">
                            @endif
                        </div>
                        
                        <div class="friend-info">
                            <h4 class="friend-name">{{ $friend->name }}</h4>
                            <p class="friend-email">{{ $friend->email }}</p>
                            <p class="friend-date">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Amigos desde: {{ $friend->pivot->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                        <div class="friend-status">
                            <span class="status-online">
                                <i class="fas fa-circle"></i> Conectado
                            </span>
                        </div>
                        
                        <!-- MEJORA: Confirmación antes de eliminar -->
                        <form action="{{ route('friends.remove', $friend) }}" method="POST" class="friend-action-form" onsubmit="return confirm('¿Estás seguro de que quieres eliminar a {{ $friend->name }} de tus amigos?');">
                            @csrf
                            <button type="submit" class="btn-remove" title="Eliminar amigo">
                                <i class="fas fa-user-times"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-state">
                    <i class="fas fa-user-friends empty-icon"></i>
                    <h4>Aún no tienes amigos</h4>
                    <p>Comienza agregando amigos desde las sugerencias o buscando por nombre.</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Sugerencias de amigos -->
        <div class="friends-sidebar">
            <div class="suggestions-card">
                <h3 class="section-title">Personas que quizás conozcas</h3>
                
                @foreach($suggestedFriends as $suggested)
                <div class="suggestion-item">
                    <div class="suggestion-info">
                        <div class="user-avatar-container small">
                            @if($suggested->profile_photo_path)
                                <img src="{{ asset('storage/' . $suggested->profile_photo_path) }}" class="user-avatar-img" alt="{{ $suggested->name }}">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($suggested->name) }}&background=random&color=fff" class="user-avatar-img" alt="{{ $suggested->name }}">
                            @endif
                        </div>
                        <div class="suggestion-details">
                            <h5 class="user-name">{{ $suggested->name }}</h5>
                            <p class="user-email text-truncate" style="max-width: 140px;">{{ $suggested->email }}</p>
                        </div>
                    </div>
                    <form action="{{ route('friends.send', $suggested) }}" method="POST" class="suggestion-form">
                        @csrf
                        <button type="submit" class="btn-add">
                            <i class="fas fa-user-plus"></i>
                        </button>
                    </form>
                </div>
                @endforeach

                @if($suggestedFriends->count() === 0)
                <div class="empty-suggestions">
                    <i class="fas fa-check-circle"></i>
                    <p>No hay más sugerencias por ahora</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.friends-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

/* Tarjetas generales */
.search-card,
.requests-card,
.friends-card,
.suggestions-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    margin-bottom: 20px;
    border: 1px solid #e1e5e9;
}

/* NUEVOS ESTILOS PARA FOTOS DE PERFIL */
.user-avatar-container {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    border: 2px solid #f0f2f5;
}

.user-avatar-container.small {
    width: 40px;
    height: 40px;
}

.user-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.friend-avatar-container {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    margin: 0 auto 15px auto;
    border: 3px solid #f0f2f5;
}

.friend-avatar-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Búsqueda */
.search-title { color: #2c3e50; margin-bottom: 16px; font-size: 1.5rem; }
.search-input-group { display: flex; gap: 10px; }
.search-input { flex: 1; padding: 12px 16px; border: 2px solid #e1e5e9; border-radius: 8px; font-size: 14px; transition: border-color 0.3s; }
.search-input:focus { outline: none; border-color: #3498db; }
.search-button { padding: 12px 20px; background: #3498db; color: white; border: none; border-radius: 8px; cursor: pointer; transition: background 0.3s; }
.search-button:hover { background: #2980b9; }
.search-results { margin-top: 16px; }

/* Layout */
.friends-layout { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }

/* Solicitudes */
.section-title { color: #2c3e50; margin-bottom: 20px; font-size: 1.3rem; display: flex; align-items: center; }
.request-item { display: flex; justify-content: space-between; align-items: center; padding: 16px; border: 1px solid #e1e5e9; border-radius: 8px; margin-bottom: 12px; background: #f8f9fa; }
.request-info { display: flex; align-items: center; gap: 12px; }
.user-name { margin: 0; color: #2c3e50; font-size: 1.1rem; }
.user-email { margin: 4px 0 0 0; color: #7f8c8d; font-size: 0.9rem; }
.request-actions { display: flex; gap: 8px; }
.action-form { margin: 0; }

.btn-accept { padding: 8px 16px; background: #27ae60; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9rem; transition: background 0.3s; }
.btn-accept:hover { background: #219a52; }
.btn-reject { padding: 8px 16px; background: #e74c3c; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.9rem; transition: background 0.3s; }
.btn-reject:hover { background: #c0392b; }

/* Grid de amigos */
.friends-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 16px; }
.friend-card { position: relative; background: #f8f9fa; border: 1px solid #e1e5e9; border-radius: 8px; padding: 20px; transition: transform 0.2s, box-shadow 0.2s; text-align: center; }
.friend-card:hover { transform: translateY(-3px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }

.friend-name { margin: 0 0 4px 0; color: #2c3e50; font-size: 1.1rem; font-weight: 600; }
.friend-email { margin: 0 0 8px 0; color: #7f8c8d; font-size: 0.9rem; }
.friend-date { margin: 0; color: #95a5a6; font-size: 0.8rem; }
.status-online { display: inline-flex; align-items: center; gap: 6px; color: #27ae60; font-size: 0.8rem; font-weight: 500; margin-top: 5px; }
.status-online i { font-size: 0.6rem; }

.friend-action-form { position: absolute; top: 12px; right: 12px; }
.btn-remove { background: none; border: none; color: #e74c3c; cursor: pointer; padding: 6px; border-radius: 4px; transition: background 0.3s; opacity: 0.6; }
.btn-remove:hover { background: #fdf2f2; opacity: 1; }

/* Sugerencias */
.suggestion-item { display: flex; justify-content: space-between; align-items: center; padding: 12px; border: 1px solid #e1e5e9; border-radius: 8px; margin-bottom: 10px; background: white; }
.suggestion-info { display: flex; align-items: center; gap: 10px; }
.suggestion-form { margin: 0; }
.btn-add { padding: 6px 12px; background: #3498db; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.8rem; transition: background 0.3s; }
.btn-add:hover { background: #2980b9; }

/* Estados vacíos */
.empty-state { text-align: center; padding: 40px 20px; color: #7f8c8d; }
.empty-icon { font-size: 3rem; color: #bdc3c7; margin-bottom: 16px; }
.empty-state h4 { margin: 0 0 8px 0; color: #95a5a6; }
.empty-suggestions { text-align: center; padding: 20px; color: #95a5a6; }
.empty-suggestions i { font-size: 2rem; margin-bottom: 8px; color: #27ae60; }

@media (max-width: 768px) {
    .friends-layout { grid-template-columns: 1fr; }
    .friends-grid { grid-template-columns: 1fr; }
    .request-item { flex-direction: column; gap: 12px; text-align: center; }
    .request-info { flex-direction: column; }
    .request-actions { justify-content: center; }
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('friendSearch');
    const searchButton = document.getElementById('searchButton');
    const searchResults = document.getElementById('searchResults');

    function performSearch() {
        const query = searchInput.value.trim();
        
        if (query.length < 2) {
            searchResults.innerHTML = '';
            return;
        }

        // Mostrar indicador de carga
        searchResults.innerHTML = '<div class="text-center p-3 text-muted"><i class="fas fa-spinner fa-spin"></i> Buscando...</div>';

        fetch(`{{ route('friends.search') }}?query=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(users => {
                if (users.length > 0) {
                    let html = '<div class="search-results-header mb-2 text-muted small"><strong>Resultados de búsqueda:</strong></div>';
                    users.forEach(user => {
                        // MEJORA: Lógica de imagen en JS
                        let avatarHtml = '';
                        if (user.profile_photo_path) {
                            avatarHtml = `<img src="/storage/${user.profile_photo_path}" class="user-avatar-img" alt="${user.name}">`;
                        } else {
                            avatarHtml = `<img src="https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=3498db&color=fff" class="user-avatar-img" alt="${user.name}">`;
                        }

                        html += `
                        <div class="suggestion-item">
                            <div class="suggestion-info">
                                <div class="user-avatar-container small">
                                    ${avatarHtml}
                                </div>
                                <div class="suggestion-details">
                                    <h5 class="user-name mb-0" style="font-size: 0.95rem;">${user.name}</h5>
                                    <p class="user-email mb-0 small text-muted">${user.email}</p>
                                </div>
                            </div>
                            <form action="/friends/send-request/${user.id}" method="POST" class="suggestion-form">
                                @csrf
                                <button type="submit" class="btn-add" title="Agregar amigo">
                                    <i class="fas fa-user-plus"></i> Agregar
                                </button>
                            </form>
                        </div>`;
                    });
                    searchResults.innerHTML = html;
                } else {
                    searchResults.innerHTML = '<div class="empty-suggestions p-3"><p class="mb-0">No se encontraron usuarios.</p></div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                searchResults.innerHTML = '<div class="empty-suggestions p-3 text-danger"><p>Error en la búsqueda</p></div>';
            });
    }

    searchButton.addEventListener('click', performSearch);
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
});
</script>
@endpush
@endsection