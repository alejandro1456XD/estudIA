@extends('layouts.app')

@section('title', 'Eventos y Actividades - estudIA')

@section('content')
<div class="container-fluid py-4">

    <!-- ENCABEZADO -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Eventos y Actividades</h2>
            <p class="text-muted mb-0">Descubre, aprende y conecta con la comunidad.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('events.my') }}" class="btn btn-outline-primary rounded-pill fw-bold">
                <i class="fas fa-check-circle me-1"></i> Mis Eventos
            </a>
            <button class="btn btn-primary rounded-pill fw-bold shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#createEventModal">
                <i class="fas fa-plus me-1"></i> Crear Evento
            </button>
        </div>
    </div>

    <!-- 1. MENSAJES DE FEEDBACK -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-pill mb-4" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
        <div class="d-flex align-items-center mb-2">
            <i class="fas fa-exclamation-triangle me-2 fa-lg"></i>
            <h6 class="mb-0 fw-bold">No se pudo crear el evento:</h6>
        </div>
        <ul class="mb-0 small">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- LISTA DE EVENTOS PRÓXIMOS -->
    <h5 class="fw-bold mb-3 text-warning"><i class="fas fa-clock me-2"></i>Próximos Eventos</h5>
    
    <div class="row g-4 mb-5">
        @forelse($upcomingEvents as $event)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 border-0 shadow-sm rounded-4 hover-shadow transition">
                <div class="card-body p-4 d-flex flex-column">
                    <!-- Badge de Tipo -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill border border-primary border-opacity-10">
                            {{ $event->type }}
                        </span>
                        
                        <!-- Cupos Restantes -->
                        @if($event->max_attendees)
                            <span class="badge {{ $event->spots_left > 0 ? 'bg-success' : 'bg-danger' }} rounded-pill">
                                {{ $event->spots_left === '∞' ? 'Ilimitado' : $event->spots_left . ' cupos libres' }}
                            </span>
                        @endif
                    </div>

                    <h5 class="fw-bold text-dark mb-2">{{ $event->title }}</h5>
                    <p class="text-muted small flex-grow-1">{{ Str::limit($event->description, 80) }}</p>

                    <!-- Info con Hora Local -->
                    <div class="bg-light p-3 rounded-3 mb-3">
                        <div class="d-flex align-items-center mb-2 text-muted small">
                            <i class="fas fa-calendar-day me-2 text-primary" style="width: 20px;"></i>
                            <!-- Fecha en UTC para procesar con JS -->
                            <strong class="local-date" data-utc="{{ $event->start_time->toIso8601String() }}">
                                {{ $event->start_time->format('d M Y') }}
                            </strong>
                        </div>
                        <div class="d-flex align-items-center mb-2 text-muted small">
                            <i class="fas fa-clock me-2 text-warning" style="width: 20px;"></i>
                            <!-- Hora en UTC para procesar con JS -->
                            <span class="local-time" data-utc="{{ $event->start_time->toIso8601String() }}">
                                {{ $event->start_time->format('h:i A') }} (UTC)
                            </span>
                        </div>
                        <div class="d-flex align-items-center text-muted small">
                            <i class="fas fa-map-marker-alt me-2 text-danger" style="width: 20px;"></i>
                            {{ $event->location }} {{ $event->is_virtual ? '(Virtual)' : '' }}
                        </div>
                    </div>

                    <!-- LÓGICA DE BOTONES -->
                    @if($event->user_id == Auth::id())
                        <!-- CASO 1: SOY EL CREADOR -->
                        <div class="d-grid">
                            <button type="button" class="btn btn-light text-muted rounded-pill fw-bold border" disabled style="opacity: 1; cursor: default;">
                                <i class="fas fa-hourglass-half me-2"></i> Esperando al día del evento
                            </button>
                        </div>
                    @else
                        <!-- CASO 2: SOY INVITADO -->
                        <form action="{{ route('events.attend', $event->id) }}" method="POST" class="d-grid">
                            @csrf
                            @if($event->is_attending)
                                <button type="button" class="btn btn-outline-danger rounded-pill fw-bold" onclick="this.closest('form').submit()">
                                    <i class="fas fa-times me-1"></i> Cancelar Asistencia
                                </button>
                            @elseif($event->spots_left === 0)
                                <button type="button" class="btn btn-secondary rounded-pill fw-bold" disabled>
                                    <i class="fas fa-ban me-1"></i> Agotado
                                </button>
                            @else
                                <button type="submit" class="btn btn-primary rounded-pill fw-bold shadow-sm">
                                    <i class="fas fa-ticket-alt me-1"></i> Confirmar Asistencia
                                </button>
                            @endif
                        </form>
                    @endif

                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="mb-3"><i class="fas fa-calendar-times fa-4x text-muted opacity-25"></i></div>
            <h5 class="text-muted">No hay eventos próximos.</h5>
            <p class="text-muted">¡Sé el primero en organizar uno!</p>
        </div>
        @endforelse
    </div>

    <!-- HISTORIAL -->
    @if($pastEvents->count() > 0)
    <h5 class="fw-bold mb-3 text-muted"><i class="fas fa-history me-2"></i>Eventos Anteriores</h5>
    <div class="list-group shadow-sm rounded-4 overflow-hidden mb-4">
        @foreach($pastEvents as $event)
        <div class="list-group-item border-0 p-3 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="mb-1 fw-bold text-muted">{{ $event->title }}</h6>
                <small class="text-muted">
                    <span class="local-date" data-utc="{{ $event->start_time->toIso8601String() }}">
                        {{ $event->start_time->format('d M Y') }}
                    </span> 
                    • {{ $event->attendees->count() }} asistentes
                </small>
            </div>
            <button class="btn btn-sm btn-light rounded-pill px-3">Ver Resumen</button>
        </div>
        @endforeach
    </div>
    @endif

</div>

<!-- MODAL CREAR EVENTO -->
<div class="modal fade" id="createEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-calendar-plus me-2"></i>Crear Nuevo Evento</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('events.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Título del Evento</label>
                        <input type="text" name="title" class="form-control rounded-pill px-3" placeholder="Ej: Clase de Repaso Matemáticas" value="{{ old('title') }}" required>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold">Tipo</label>
                            <select name="type" class="form-select rounded-pill px-3" required>
                                <option value="" disabled {{ old('type') ? '' : 'selected' }}>Seleccionar...</option>
                                @foreach(['Clase en Vivo', 'Taller', 'Webinar', 'Hackathon', 'Conferencia', 'Reunión de Estudio', 'Tutoría', 'Seminario', 'Meetup', 'Curso Intensivo', 'Panel', 'Otro'] as $option)
                                    <option value="{{ $option }}" {{ old('type') == $option ? 'selected' : '' }}>{{ $option }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold">Cupos</label>
                            <input type="number" name="max_attendees" class="form-control rounded-pill px-3" placeholder="Opcional (Ilimitado)" value="{{ old('max_attendees') }}">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Fecha y Hora</label>
                        <input type="datetime-local" name="start_time" class="form-control rounded-pill px-3" value="{{ old('start_time') }}" required>
                        <div class="form-text text-muted small">La fecha y hora se guardará en tu zona horaria local.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Ubicación</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start-pill bg-light border-end-0"><i class="fas fa-map-marker-alt"></i></span>
                            <input type="text" name="location" class="form-control rounded-end-pill border-start-0" placeholder="Ej: Zoom / Aula 101" value="{{ old('location') }}" required>
                        </div>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_virtual" id="is_virtual" {{ old('is_virtual') ? 'checked' : '' }}>
                            <label class="form-check-label small" for="is_virtual">Es un evento virtual</label>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="description" class="form-control rounded-3" rows="3" placeholder="¿De qué trata el evento?">{{ old('description') }}</textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill fw-bold py-2">Publicar Evento</button>
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

<!-- SCRIPTS -->
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // 1. FUNCIÓN PARA CONVERTIR HORA UTC A LOCAL
        function convertToLocalTime() {
            // Fechas (Ej: 29 Nov 2025)
            document.querySelectorAll('.local-date').forEach(el => {
                const utcDate = el.getAttribute('data-utc');
                if(utcDate) {
                    const date = new Date(utcDate);
                    // Usamos 'undefined' para que detecte el idioma del navegador automáticamente
                    el.textContent = date.toLocaleDateString(undefined, { 
                        weekday: 'short', 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric' 
                    });
                }
            });

            // Horas (Ej: 04:00 PM)
            document.querySelectorAll('.local-time').forEach(el => {
                const utcDate = el.getAttribute('data-utc');
                if(utcDate) {
                    const date = new Date(utcDate);
                    el.textContent = date.toLocaleTimeString(undefined, { 
                        hour: '2-digit', 
                        minute: '2-digit', 
                        hour12: true 
                    });
                }
            });
        }

        // Ejecutar conversión
        convertToLocalTime();

        // 2. REABRIR MODAL SI HAY ERRORES
        @if($errors->any())
            var myModal = new bootstrap.Modal(document.getElementById('createEventModal'));
            myModal.show();
        @endif
    });
</script>
@endpush

@endsection