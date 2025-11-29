<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Eventos Próximos</h6>
    </div>
    <div class="card-body">
        @foreach([
            ['date' => 'MAÑANA', 'title' => 'Webinar: Introducción a la IA', 'time' => '4:00 PM • Virtual'],
            ['date' => '15 DIC', 'title' => 'Hackathon estudIA', 'time' => '9:00 AM • Campus Central']
        ] as $event)
        <div class="mb-3">
            <small class="text-primary">{{ $event['date'] }}</small>
            <a href="{{ route('events') }}" class="text-decoration-none">
                <p class="mb-1 fw-bold text-dark">{{ $event['title'] }}</p>
            </a>
            <small class="text-muted">{{ $event['time'] }}</small>
        </div>
        @endforeach
        <div class="text-center">
            <a href="{{ route('events') }}" class="btn btn-outline-info btn-sm w-100">
                <i class="fas fa-calendar-plus me-1"></i>Ver Todos
            </a>
        </div>
    </div>
</div>