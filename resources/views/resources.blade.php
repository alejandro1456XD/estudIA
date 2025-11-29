@extends('layouts.app')

@section('title', 'Recursos - estudIA')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card post-card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h4 class="mb-0"><i class="fas fa-book me-2"></i>Recursos de Estudio</h4>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="d-flex gap-2 flex-wrap">
                            <button class="btn btn-primary btn-sm">Todos</button>
                            <button class="btn btn-outline-primary btn-sm">Documentos</button>
                            <button class="btn btn-outline-primary btn-sm">Videos</button>
                            <button class="btn btn-outline-primary btn-sm">Código</button>
                            <button class="btn btn-outline-primary btn-sm">Presentaciones</button>
                        </div>
                    </div>
                </div>

                <!-- Recursos destacados -->
                <h5 class="mb-3"><i class="fas fa-star me-2 text-warning"></i>Recursos Destacados</h5>
                <div class="row">
                    @foreach([
                        ['title' => 'Guía Completa de Python', 'type' => 'PDF', 'size' => '2.4 MB', 'downloads' => 156, 'icon' => 'file-pdf', 'color' => 'danger'],
                        ['title' => 'Tutorial de Machine Learning', 'type' => 'Video', 'size' => '45 min', 'downloads' => 89, 'icon' => 'video', 'color' => 'primary'],
                        ['title' => 'Ejercicios de SQL', 'type' => 'Archivo', 'size' => '1.1 MB', 'downloads' => 203, 'icon' => 'database', 'color' => 'info'],
                        ['title' => 'Presentación: IA en 2024', 'type' => 'PPT', 'size' => '5.2 MB', 'downloads' => 67, 'icon' => 'file-powerpoint', 'color' => 'warning']
                    ] as $resource)
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="me-3">
                                        <i class="fas fa-{{ $resource['icon'] }} fa-2x text-{{ $resource['color'] }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6>{{ $resource['title'] }}</h6>
                                        <p class="mb-1">
                                            <span class="badge bg-{{ $resource['color'] }}">{{ $resource['type'] }}</span>
                                            <small class="text-muted ms-2">{{ $resource['size'] }}</small>
                                        </p>
                                        <p class="mb-2">
                                            <small class="text-muted">
                                                <i class="fas fa-download me-1"></i>{{ $resource['downloads'] }} descargas
                                            </small>
                                        </p>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-primary btn-sm">
                                                <i class="fas fa-download me-1"></i>Descargar
                                            </button>
                                            <button class="btn btn-outline-secondary btn-sm">
                                                <i class="fas fa-eye me-1"></i>Vista Previa
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Recursos por categoría -->
                <h5 class="mb-3 mt-4"><i class="fas fa-folder me-2 text-success"></i>Por Categoría</h5>
                <div class="row">
                    @foreach([
                        ['category' => 'Programación', 'count' => 24, 'icon' => 'code', 'color' => 'success'],
                        ['category' => 'Machine Learning', 'count' => 18, 'icon' => 'robot', 'color' => 'primary'],
                        ['category' => 'Base de Datos', 'count' => 12, 'icon' => 'database', 'color' => 'info'],
                        ['category' => 'Matemáticas', 'count' => 15, 'icon' => 'calculator', 'color' => 'warning'],
                        ['category' => 'Estadística', 'count' => 8, 'icon' => 'chart-bar', 'color' => 'danger'],
                        ['category' => 'Desarrollo Web', 'count' => 21, 'icon' => 'globe', 'color' => 'dark']
                    ] as $category)
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-{{ $category['icon'] }} fa-2x text-{{ $category['color'] }} mb-2"></i>
                                <h6>{{ $category['category'] }}</h6>
                                <p class="text-muted">{{ $category['count'] }} recursos</p>
                                <button class="btn btn-outline-{{ $category['color'] }} btn-sm w-100">
                                    Explorar
                                </button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection