@extends('layouts.app')

@section('title', 'Mis Eventos - estudIA')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('events') }}" class="btn btn-light rounded-circle shadow-sm me-3"><i class="fas fa-arrow-left"></i></a>
        <h2 class="fw-bold text-dark mb-0">Mis Eventos</h2>
    </div>

    <div class="row g-4">
        @forelse($myEvents as $event)
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between">
                        <div>
                            <span class="badge bg-success bg-opacity-10 text-success mb-2">Inscrito</span>
                            <h5 class="fw-bold">{{ $event->title }}</h5>
                            <p class="text-muted small mb-0"><i class="fas fa-calendar me-1"></i> {{ $event->start_time->format('d M Y, h:i A') }}</p>
                            <p class="text-muted small"><i class="fas fa-map-marker-alt me-1"></i> {{ $event->location }}</p>
                        </div>
                        <div class="text-end">
                            <form action="{{ route('events.attend', $event->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill">
                                    Cancelar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <p class="text-muted">No te has inscrito a ningún evento aún.</p>
            <a href="{{ route('events') }}" class="btn btn-primary rounded-pill">Explorar Eventos</a>
        </div>
        @endforelse
    </div>
</div>
@endsection