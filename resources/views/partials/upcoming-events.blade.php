<div class="card border-0 shadow-sm rounded-4 mb-4">
    <!-- Encabezado del Widget -->
    <div class="card-header bg-info text-white border-0 rounded-top-4 py-3 position-relative overflow-hidden">
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-white opacity-10" style="transform: skewX(-20deg);"></div>
        <h6 class="mb-0 fw-bold position-relative"><i class="fas fa-calendar-check me-2"></i>Eventos Próximos</h6>
    </div>

    <div class="card-body p-0">
        @php
            // LÓGICA: Buscar eventos a los que ASISTIRÁ (attendingEvents) y que sean FUTUROS
            // Usamos la misma tolerancia de horario que en el controlador
            $myUpcomingEvents = Auth::user()
                ->attendingEvents()
                ->where('start_time', '>=', now()->subDay()->startOfDay()) 
                ->orderBy('start_time', 'asc')
                ->take(3) // Mostramos solo los 3 más cercanos
                ->get();
        @endphp

        @if($myUpcomingEvents->count() > 0)
            <!-- CASO 1: TIENE EVENTOS CONFIRMADOS -->
            <div class="list-group list-group-flush">
                @foreach($myUpcomingEvents as $event)
                    <div class="list-group-item border-0 p-3 hover-bg-light transition">
                        <!-- Fecha Relativa -->
                        <small class="text-info fw-bold text-uppercase mb-1 d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                            @if($event->start_time->isToday()) HOY
                            @elseif($event->start_time->isTomorrow()) MAÑANA
                            @else {{ $event->start_time->format('d M') }}
                            @endif
                        </small>
                        
                        <!-- Título -->
                        <h6 class="fw-bold text-dark mb-1 text-truncate" title="{{ $event->title }}">
                            {{ $event->title }}
                        </h6>
                        
                        <!-- Detalles (Hora y Lugar) -->
                        <div class="d-flex align-items-center text-muted small">
                            <i class="far fa-clock me-1 text-warning"></i> 
                            <!-- Hora local automática -->
                            <span class="local-time" data-utc="{{ $event->start_time->toIso8601String() }}">
                                {{ $event->start_time->format('h:i A') }}
                            </span>
                            <span class="mx-2">•</span>
                            <i class="fas {{ $event->is_virtual ? 'fa-video' : 'fa-map-marker-alt' }} me-1 text-secondary"></i> 
                            {{ $event->is_virtual ? 'Virtual' : Str::limit($event->location, 10) }}
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Botón Ver Todos -->
            <div class="p-3 text-center border-top bg-light rounded-bottom-4">
                <a href="{{ route('events.my') }}" class="btn btn-sm btn-outline-info rounded-pill fw-bold w-100 border-2">
                    Ver mi agenda completa
                </a>
            </div>

        @else
            <!-- CASO 2: NO HA CONFIRMADO NADA (Tu solicitud) -->
            <div class="text-center p-4">
                <div class="mb-3 text-muted opacity-25">
                    <i class="fas fa-calendar-plus fa-3x"></i>
                </div>
                <h6 class="fw-bold text-dark mb-2">Sin planes aún</h6>
                <p class="text-muted small mb-3 px-2">
                    Recuerda confirmar los eventos a los que quieras asistir para verlos aquí.
                </p>
                <a href="{{ route('events') }}" class="btn btn-sm btn-info text-white rounded-pill fw-bold shadow-sm px-4">
                    Explorar Eventos
                </a>
            </div>
        @endif
    </div>
</div>

<style>
    .hover-bg-light:hover { background-color: #f8f9fa; }
    .transition { transition: all 0.2s ease; }
</style>