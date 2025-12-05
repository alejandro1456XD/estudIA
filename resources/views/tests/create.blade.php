@extends('layouts.app')

@section('title', 'Nueva Actividad')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            
            {{-- Encabezado con Botón Volver --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">
                        <i class="fas fa-rocket text-primary me-2"></i>Nueva Actividad
                    </h2>
                    <p class="text-muted mb-0">Describe el tema y la IA generará tu evaluación.</p>
                </div>
                <a href="{{ route('tests.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-1" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                    </svg>
                    Volver
                </a>
            </div>

            {{-- Zona de Errores --}}
            @if ($errors->any())
                <div class="alert alert-danger shadow-sm rounded-3 mb-4">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">
                    
                    <form action="{{ route('tests.store') }}" method="POST" id="createTestForm">
                        @csrf
                        
                        {{-- MODO FIJO: PROMPT (Texto) --}}
                        <input type="hidden" name="mode" value="prompt">

                        {{-- 1. Título --}}
                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold text-dark">1. ¿Sobre qué tema es la actividad?</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0 rounded-start-pill ps-3">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="gray" class="bi bi-tag" viewBox="0 0 16 16">
                                        <path d="M6 4.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm-1 0a.5.5 0 1 0-1 0 .5.5 0 0 0 1 0z"/>
                                        <path d="M2 1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 1 6.586V2a1 1 0 0 1 1-1zm0 5.586 7 7L13.586 9l-7-7H2v4.586z"/>
                                    </svg>
                                </span>
                                <input type="text" class="form-control border-start-0 rounded-end-pill py-2 bg-light" id="name" name="name" placeholder="Ej: Historia de Roma, React JS, Marketing..." required>
                            </div>
                        </div>

                        {{-- 2. Tipo de Evaluación --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark mb-3">2. ¿Qué tipo de desafío buscas?</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="test_type" id="type_exam" value="exam" checked>
                                    <label class="card selection-card h-100 p-3 text-center rounded-3 border-2" for="type_exam">
                                        <div class="mb-2 text-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-card-checklist" viewBox="0 0 16 16">
                                                <path d="M14.5 3a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h13zm-13-1A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-13z"/>
                                                <path d="M7 5.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0zM7 9.5a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5zm-1.496-.854a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0z"/>
                                            </svg>
                                        </div>
                                        <span class="d-block fw-bold text-dark">Examen Teórico</span>
                                        <small class="text-muted">Preguntas y respuestas</small>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="test_type" id="type_practice" value="practice">
                                    <label class="card selection-card h-100 p-3 text-center rounded-3 border-2" for="type_practice">
                                        <div class="mb-2 text-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-briefcase" viewBox="0 0 16 16">
                                                <path d="M6.5 1A1.5 1.5 0 0 0 5 2.5V3H1.5A1.5 1.5 0 0 0 0 4.5v8A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-8A1.5 1.5 0 0 0 14.5 3H11v-.5A1.5 1.5 0 0 0 9.5 1h-3zm0 1h3a.5.5 0 0 1 .5.5V3H6v-.5a.5.5 0 0 1 .5-.5zm1.886 6.914L8.414 9H14.5A.5.5 0 0 1 15 9.5V10h-6.5a.5.5 0 0 1-.333-.146.5.5 0 0 1-.18-.34zM1 9.5A.5.5 0 0 1 1.5 9h6.086l-2.414 2.414a.5.5 0 0 1-.707 0L1.707 9.5a.5.5 0 0 1-.707 0z"/>
                                            </svg>
                                        </div>
                                        <span class="d-block fw-bold text-dark">Práctica Real / Tarea</span>
                                        <small class="text-muted">Casos prácticos y entregables</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Área de Texto --}}
                        <div class="mb-4">
                            <label for="prompt_input" class="form-label fw-bold text-dark mb-2">3. Escribe el tema, instrucciones o pega tus apuntes:</label>
                            <textarea class="form-control bg-light border-0 rounded-4 p-3" name="prompt_input" id="prompt_input" rows="8" placeholder="Ej: Quiero un examen de 5 preguntas sobre las capitales de Europa... o pega aquí el contenido de tu clase." required></textarea>
                            <div class="text-end mt-2">
                                <small class="text-muted">Mínimo 50 caracteres</small>
                            </div>
                        </div>

                        {{-- Botón Enviar --}}
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill shadow fw-bold py-3 hover-shadow transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-stars me-2" viewBox="0 0 16 16">
                                    <path d="M7.657 6.247c.11-.33.576-.33.686 0l.645 1.937a2.89 2.89 0 0 0 1.829 1.828l1.936.645c.33.11.33.576 0 .686l-1.937.645a2.89 2.89 0 0 0-1.828 1.829l-.645 1.936a.361.361 0 0 1-.686 0l-.645-1.937a2.89 2.89 0 0 0-1.828-1.828l-1.937-.645a.361.361 0 0 1 0-.686l1.937-.645a2.89 2.89 0 0 0 1.828-1.828l.645-1.937zM3.794 1.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387A1.734 1.734 0 0 0 4.593 5.69l-.387 1.162a.217.217 0 0 1-.412 0L3.407 5.69A1.734 1.734 0 0 0 2.31 4.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387A1.734 1.734 0 0 0 3.407 2.31l.387-1.162zM10.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732L9.1 2.137a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L10.863.1z"/>
                                </svg>
                                Crear Actividad con IA
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .selection-card { cursor: pointer; transition: all 0.2s ease; border-color: #e9ecef; }
    .selection-card:hover { border-color: #0d6efd; background-color: #f8f9fa; transform: translateY(-2px); }
    .btn-check:checked + .selection-card { border-color: #0d6efd; background-color: #e7f1ff; color: #0d6efd; }
    .hover-shadow:hover { box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important; }
    .transition-all { transition: all 0.3s ease; }
</style>
@endpush
@endsection