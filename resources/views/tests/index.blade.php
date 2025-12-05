@extends('layouts.app')

@section('title', 'Mis Evaluaciones')

@section('content')
<div class="row g-4">
    <!-- 1. COLUMNA IZQUIERDA -->
    <div class="col-md-3 d-none d-md-block">
        @include('partials.sidebar-profile')
        @include('partials.quick-links')
    </div>

    <!-- 2. COLUMNA CENTRAL -->
    <div class="col-md-9">
        
        <!-- ENCABEZADO -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold text-dark mb-1">
                        <i class="fas fa-graduation-cap text-primary me-2"></i>Mis Evaluaciones
                    </h4>
                    <p class="text-muted small mb-0">Gestiona y revisa tus exámenes generados por IA.</p>
                </div>
                @if($tests->count() > 0)
                    <a href="{{ route('tests.create') }}" class="btn btn-primary rounded-pill px-4 fw-bold d-flex align-items-center">
                        <i class="fas fa-plus me-2"></i>Crear Nuevo
                    </a>
                @endif
            </div>
        </div>

        <!-- ALERTAS -->
        @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('status') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- CONTENIDO PRINCIPAL -->
        @if($tests->count() > 0)
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach ($tests as $test)
                    <div class="col">
                        <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift transition-all">
                            <!-- Estado (Badge) -->
                            <div class="card-header border-0 bg-white pt-3 pb-0 d-flex justify-content-between">
                                <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-primary" style="width: 40px; height: 40px;">
                                    <i class="fas fa-brain"></i>
                                </div>
                                @if ($test->status == 'generated')
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">LISTO</span>
                                @elseif ($test->status == 'pending')
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2">
                                        <i class="fas fa-spinner fa-spin me-1"></i> PROCESANDO
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-2">FALLÓ</span>
                                @endif
                            </div>

                            <div class="card-body">
                                <h5 class="card-title fw-bold text-dark text-truncate mb-1" title="{{ $test->name }}">
                                    {{ $test->name }}
                                </h5>
                                <p class="card-text text-muted small">
                                    <i class="far fa-calendar-alt me-1"></i> {{ $test->created_at->diffForHumans() }}
                                </p>
                            </div>

                            {{-- FOOTER CON BOTONES DE ACCIÓN --}}
                            <div class="card-footer bg-white border-0 pb-3 pt-0 d-flex gap-2">
                                
                                {{-- Botón Principal --}}
                                @if ($test->status == 'generated')
                                    <a href="{{ route('tests.show', $test) }}" class="btn btn-outline-primary flex-grow-1 rounded-pill fw-bold">
                                        Ver Resultados
                                    </a>
                                @elseif ($test->status == 'failed')
                                    <button class="btn btn-outline-secondary flex-grow-1 rounded-pill fw-bold" disabled>
                                        Error
                                    </button>
                                @else
                                    <button class="btn btn-light flex-grow-1 rounded-pill text-muted" disabled>
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </button>
                                @endif

                                {{-- Botón Eliminar (ACTIVADOR DEL MODAL) --}}
                                <button type="button" 
                                        class="btn btn-light text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm hover-danger" 
                                        style="width: 38px; height: 38px;" 
                                        title="Eliminar"
                                        onclick="confirmDelete('{{ route('tests.destroy', $test) }}')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- ESTADO VACÍO -->
            <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="fas fa-robot fa-3x"></i>
                        </div>
                    </div>
                    
                    <h3 class="fw-bold text-dark mb-2">¡Tu Evaluador Inteligente está listo!</h3>
                    <p class="text-muted mx-auto mb-5" style="max-width: 500px;">
                        Aún no tienes evaluaciones. Sube tus apuntes y deja que la IA te ponga a prueba.
                    </p>

                    <a href="{{ route('tests.create') }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow fw-bold d-inline-flex align-items-center">
                        <i class="fas fa-magic me-2"></i> Crear mi Primera Prueba
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- MODAL DE CONFIRMACIÓN DE ELIMINACIÓN --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="mb-3 text-danger">
                    <i class="fas fa-trash-alt fa-3x opacity-50"></i>
                </div>
                <h5 class="fw-bold text-dark">¿Estás seguro?</h5>
                <p class="text-muted mb-0">Esta evaluación se eliminará permanentemente y no podrás recuperarla.</p>
            </div>
            <div class="modal-footer border-0 bg-light p-3 justify-content-center">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancelar</button>
                
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                        Sí, eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-lift { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .hover-lift:hover { transform: translateY(-5px); box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important; }
    
    .hover-danger:hover {
        background-color: #dc3545 !important;
        color: white !important;
    }

    .bg-primary-subtle { background-color: #cfe2ff; }
    .bg-success-subtle { background-color: #d1e7dd; }
    .bg-warning-subtle { background-color: #fff3cd; }
    .bg-danger-subtle { background-color: #f8d7da; }
</style>

<script>
    function confirmDelete(url) {
        // Establecer la acción del formulario con la URL de borrado correcta
        document.getElementById('deleteForm').action = url;
        // Abrir el modal usando Bootstrap 5
        var myModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        myModal.show();
    }
</script>
@endsection