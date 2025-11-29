@extends('layouts.app')

@section('title', 'Eventos - estudIA')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card post-card shadow-sm">
            <div class="card-header bg-info text-white">
                <h4 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Eventos y Actividades</h4>
            </div>
            <div class="card-body">
                <!-- Eventos próximos -->
                <h5 class="mb-3"><i class="fas fa-clock me-2 text-warning"></i>Próximos Eventos</h5>
                <div class="row">
                    @foreach([
                        ['title' => 'Webinar: Introducción a la IA', 'date' => 'MAÑANA', 'time' => '4:00 PM', 'location' => 'Virtual', 'type' => 'Webinar'],
                        ['title' => 'Hackathon estudIA 2024', 'date' => '15 DIC', 'time' => '9:00 AM', 'location' => 'Campus Central', 'type' => 'Hackathon'],
                        ['title' => 'Taller de Machine Learning', 'date' => '20 DIC', 'time' => '2:00 PM', 'location' => 'Laboratorio 5', 'type' => 'Taller'],
                        ['title' => 'Charla: Futuro de la Educación', 'date' => '5 ENE', 'time' => '6:00 PM', 'location' => 'Auditorio Principal', 'type' => 'Conferencia']
                    ] as $event)
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 border-{{ $event['type'] == 'Webinar' ? 'primary' : ($event['type'] == 'Hackathon' ? 'success' : 'warning') }}">
                            <div class="card-body">
                                <span class="badge bg-{{ $event['type'] == 'Webinar' ? 'primary' : ($event['type'] == 'Hackathon' ? 'success' : 'warning') }} mb-2">
                                    {{ $event['type'] }}
                                </span>
                                <h6>{{ $event['title'] }}</h6>
                                <p class="mb-1">
                                    <i class="fas fa-calendar me-1 text-muted"></i>
                                    <strong>{{ $event['date'] }}</strong> a las {{ $event['time'] }}
                                </p>
                                <p class="mb-2">
                                    <i class="fas fa-map-marker-alt me-1 text-muted"></i>
                                    {{ $event['location'] }}
                                </p>
                                <button class="btn btn-outline-primary btn-sm w-100">
                                    <i class="fas fa-calendar-plus me-1"></i>Asistiré
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Eventos pasados -->
                <h5 class="mb-3 mt-4"><i class="fas fa-history me-2 text-secondary"></i>Eventos Anteriores</h5>
                <div class="list-group">
                    @foreach([
                        ['title' => 'Workshop de Python', 'date' => '15 NOV', 'attendees' => 45],
                        ['title' => 'Charla: Inteligencia Artificial', 'date' => '8 NOV', 'attendees' => 78],
                        ['title' => 'Meetup: Desarrollo Web', 'date' => '1 NOV', 'attendees' => 32]
                    ] as $pastEvent)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">{{ $pastEvent['title'] }}</h6>
                                <small class="text-muted">{{ $pastEvent['date'] }} • {{ $pastEvent['attendees'] }} asistentes</small>
                            </div>
                            <button class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-eye me-1"></i>Ver Resumen
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection