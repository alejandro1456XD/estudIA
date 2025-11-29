@extends('layouts.app')

@section('title', 'Recursos de Estudio - estudIA')

@section('content')
<div class="container-fluid py-4">

    <!-- ENCABEZA Y BUSCADOR -->
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h2 class="fw-bold text-dark mb-0">
                <i class="fas fa-book-reader me-2 text-primary"></i>Recursos de Estudio
            </h2>
            <p class="text-muted mb-0">Comparte conocimiento y encuentra material de calidad.</p>
        </div>
        <div class="col-md-6">
            <div class="d-flex gap-2 justify-content-md-end mt-3 mt-md-0">
                <!-- Buscador -->
                <form action="{{ route('resources') }}" method="GET" class="d-flex flex-grow-1" style="max-width: 300px;">
                    <input type="text" name="search" class="form-control rounded-pill" placeholder="Buscar recurso..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary rounded-pill ms-1 px-3">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <!-- Botón Subir -->
                <button class="btn btn-success rounded-pill fw-bold shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#uploadResourceModal">
                    <i class="fas fa-cloud-upload-alt me-2"></i> Subir
                </button>
            </div>
        </div>
    </div>

    <!-- FILTROS DE CATEGORÍA (Estilo Botones) -->
    <div class="d-flex gap-2 mb-5 overflow-auto pb-2">
        <a href="{{ route('resources') }}" class="btn {{ !request('category') ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-4 fw-bold">Todos</a>
        <a href="{{ route('resources', ['category' => 'Programación']) }}" class="btn {{ request('category') == 'Programación' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3">💻 Programación</a>
        <a href="{{ route('resources', ['category' => 'Matemáticas']) }}" class="btn {{ request('category') == 'Matemáticas' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3">📐 Matemáticas</a>
        <a href="{{ route('resources', ['category' => 'Diseño']) }}" class="btn {{ request('category') == 'Diseño' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3">🎨 Diseño</a>
        <a href="{{ route('resources', ['category' => 'Idiomas']) }}" class="btn {{ request('category') == 'Idiomas' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3">🌍 Idiomas</a>
        <a href="{{ route('resources', ['category' => 'Ciencias']) }}" class="btn {{ request('category') == 'Ciencias' ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill px-3">🔬 Ciencias</a>
    </div>

    <!-- SECCIÓN TOP 3 (DESTACADOS) -->
    @if(count($topResources) > 0 && !request('search') && !request('category'))
    <div class="mb-5">
        <h4 class="fw-bold mb-4"><i class="fas fa-star text-warning me-2"></i>Recursos Destacados (Top 3)</h4>
        <div class="row g-4 justify-content-center">
            @foreach($topResources as $index => $res)
                @php
                    // Estilos según el ranking
                    $borderClass = '';
                    $badgeIcon = '';
                    $badgeColor = '';
                    $cardScale = '';
                    
                    if($index == 0) { // 1er Lugar (Oro)
                        $borderClass = 'border-warning border-3';
                        $badgeIcon = 'fa-crown';
                        $badgeColor = 'bg-warning text-dark';
                        $cardScale = 'transform: scale(1.05); z-index: 10;'; 
                    } elseif($index == 1) { // 2do Lugar (Plata)
                        $borderClass = 'border-secondary border-2';
                        $badgeIcon = 'fa-medal';
                        $badgeColor = 'bg-secondary text-white';
                    } else { // 3er Lugar (Bronce)
                        $borderClass = 'border-danger border-2'; // Usamos danger como tono cobrizo
                        $badgeIcon = 'fa-ribbon';
                        $badgeColor = 'bg-danger text-white'; // O un color custom 'bronze'
                    }
                @endphp

                <div class="col-md-4" style="{{ $cardScale }}">
                    <div class="card h-100 shadow {{ $borderClass }} position-relative rounded-4 overflow-hidden">
                        <!-- Badge de Ranking -->
                        <div class="position-absolute top-0 end-0 m-0 px-3 py-1 rounded-bottom-start fw-bold shadow-sm {{ $badgeColor }}">
                            <i class="fas {{ $badgeIcon }} me-1"></i> #{{ $index + 1 }}
                        </div>

                        <div class="card-body text-center p-4">
                            <!-- Icono Grande según tipo -->
                            <div class="mb-3">
                                @if($res->file_type == 'pdf')
                                    <i class="fas fa-file-pdf fa-4x text-danger"></i>
                                @elseif($res->file_type == 'image')
                                    <i class="fas fa-file-image fa-4x text-success"></i>
                                @elseif($res->file_type == 'video')
                                    <i class="fas fa-file-video fa-4x text-primary"></i>
                                @else
                                    <i class="fas fa-file-archive fa-4x text-warning"></i>
                                @endif
                            </div>

                            <h5 class="fw-bold text-truncate" title="{{ $res->title }}">{{ $res->title }}</h5>
                            <p class="text-muted small mb-2">{{ $res->user->name }} • {{ $res->category }}</p>
                            
                            <div class="d-flex justify-content-center align-items-center gap-1 mb-3">
                                <span class="fw-bold text-warning">{{ $res->average_rating }}</span>
                                <i class="fas fa-star text-warning"></i>
                                <span class="text-muted small">({{ $res->ratings_count }} votos)</span>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="{{ route('resources.download', $res->id) }}" class="btn btn-outline-dark btn-sm rounded-pill">
                                    <i class="fas fa-download me-1"></i> Descargar ({{ $res->downloads }})
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- LISTA GENERAL DE RECURSOS -->
    <h4 class="fw-bold mb-3">
        @if(request('search')) Resultados de búsqueda @elseif(request('category')) Categoría: {{ request('category') }} @else Explorar Todo @endif
    </h4>

    <div class="row g-4">
        @forelse($resources as $res)
        <div class="col-md-3 col-sm-6">
            <div class="card h-100 border-0 shadow-sm rounded-4 hover-shadow transition">
                <div class="card-body p-3">
                    <div class="d-flex align-items-start gap-3">
                        <!-- Icono Tipo -->
                        <div class="flex-shrink-0">
                            @if($res->file_type == 'pdf')
                                <div class="bg-danger bg-opacity-10 p-3 rounded-3 text-danger"><i class="fas fa-file-pdf fa-2x"></i></div>
                            @elseif($res->file_type == 'video')
                                <div class="bg-primary bg-opacity-10 p-3 rounded-3 text-primary"><i class="fas fa-video fa-2x"></i></div>
                            @elseif($res->file_type == 'image')
                                <div class="bg-success bg-opacity-10 p-3 rounded-3 text-success"><i class="fas fa-image fa-2x"></i></div>
                            @else
                                <div class="bg-warning bg-opacity-10 p-3 rounded-3 text-warning"><i class="fas fa-file fa-2x"></i></div>
                            @endif
                        </div>
                        
                        <div class="flex-grow-1 overflow-hidden">
                            <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $res->title }}">{{ $res->title }}</h6>
                            <span class="badge bg-light text-dark border">{{ $res->file_size }}</span>
                            <span class="badge bg-light text-muted border">{{ $res->category }}</span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted"><i class="fas fa-download me-1"></i> {{ $res->downloads }}</small>
                            <small class="text-warning fw-bold"><i class="fas fa-star"></i> {{ $res->average_rating }}</small>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <a href="{{ route('resources.download', $res->id) }}" class="btn btn-primary btn-sm flex-grow-1 rounded-pill">
                                <i class="fas fa-download"></i>
                            </a>
                            <!-- Botón Calificar (Dropdown) -->
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm rounded-pill" data-bs-toggle="dropdown">
                                    <i class="fas fa-star"></i>
                                </button>
                                <ul class="dropdown-menu shadow border-0 p-2 text-center" style="min-width: 150px;">
                                    <li><h6 class="dropdown-header">Calificar</h6></li>
                                    <li>
                                        <form action="{{ route('resources.rate', $res->id) }}" method="POST" class="d-flex justify-content-center gap-1">
                                            @csrf
                                            @for($i=1; $i<=5; $i++)
                                                <button type="submit" name="rating" value="{{ $i }}" class="btn btn-sm p-0 text-warning" style="font-size: 1.2rem;">
                                                    <i class="{{ $res->current_user_rating >= $i ? 'fas' : 'far' }} fa-star"></i>
                                                </button>
                                            @endfor
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="mb-3"><i class="fas fa-folder-open fa-4x text-muted opacity-25"></i></div>
            <h5 class="text-muted">No se encontraron recursos.</h5>
            <p class="text-muted">¡Sé el primero en subir uno!</p>
        </div>
        @endforelse
    </div>

    <div class="mt-4 d-flex justify-content-center">
        {{ $resources->links() }}
    </div>

</div>

<!-- MODAL SUBIR RECURSO -->
<div class="modal fade" id="uploadResourceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-cloud-upload-alt me-2"></i>Compartir Recurso</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('resources.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Título del Recurso</label>
                        <input type="text" name="title" class="form-control rounded-pill px-3" placeholder="Ej: Guía de Python 2024" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Categoría</label>
                        <select name="category" class="form-select rounded-pill px-3" required>
                            <option value="" disabled selected>Selecciona una...</option>
                            <option value="Programación">Programación</option>
                            <option value="Matemáticas">Matemáticas</option>
                            <option value="Diseño">Diseño</option>
                            <option value="Idiomas">Idiomas</option>
                            <option value="Ciencias">Ciencias</option>
                            <option value="Otros">Otros</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Archivo</label>
                        <input type="file" name="file" class="form-control" required>
                        <div class="form-text">PDF, Imágenes, Videos o ZIP. Máx 50MB.</div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill fw-bold py-2">
                            Publicar Recurso
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .transition { transition: all 0.3s ease; }
</style>
@endsection