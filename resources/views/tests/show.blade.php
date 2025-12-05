@extends('layouts.app')

@section('title', $test->name)

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            {{-- ENCABEZADO --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold text-dark mb-1">{{ $test->name }}</h2>
                    <p class="text-muted mb-0">
                        <span class="badge bg-secondary me-2">Nivel Avanzado</span>
                        Generado el {{ $test->created_at->format('d/m/Y') }}
                    </p>
                </div>
                <a href="{{ route('tests.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left me-1" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                    </svg>
                    Volver
                </a>
            </div>

            @php
                // Usamos data_get para evitar errores si el JSON viene incompleto
                $data = $test->quiz_structure ?? [];
                $type = data_get($data, 'type');
                $isPractice = $type === 'practice';
                
                // Fallback inteligente: si no tiene tipo pero tiene escenario, es práctica
                if (!$isPractice && data_get($data, 'scenario')) {
                    $isPractice = true;
                }
            @endphp

            @if($isPractice)
                {{-- VISTA DE PRÁCTICA (Desafío) --}}
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-dark text-white p-4">
                        <h4 class="fw-bold mb-0">
                            <i class="fas fa-briefcase me-2 text-warning"></i>Desafío Profesional
                        </h4>
                    </div>
                    <div class="card-body p-5">
                        
                        <div class="alert alert-light border-start border-4 border-warning shadow-sm mb-5">
                            <h5 class="fw-bold text-dark mb-2"><i class="fas fa-industry me-2"></i>Escenario Laboral</h5>
                            <p class="mb-0 text-secondary" style="font-size: 1.1rem; line-height: 1.6;">
                                {{ data_get($data, 'scenario', 'Sin escenario definido. La IA no generó contexto.') }}
                            </p>
                        </div>

                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-light shadow-sm">
                                    <div class="card-body">
                                        <h6 class="fw-bold text-dark mb-3">🎯 Tareas a Ejecutar</h6>
                                        <ul class="list-group list-group-flush bg-transparent">
                                            @forelse(data_get($data, 'tasks', []) as $task)
                                                <li class="list-group-item bg-transparent px-0 py-2 d-flex align-items-start">
                                                    <i class="fas fa-check-square text-primary mt-1 me-2"></i>
                                                    {{ $task }}
                                                </li>
                                            @empty
                                                <li class="text-muted small">No se especificaron tareas.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-light shadow-sm">
                                    <div class="card-body">
                                        <h6 class="fw-bold text-dark mb-3">📦 Entregables Esperados</h6>
                                        <p class="text-muted small mb-4">
                                            {{ data_get($data, 'deliverables', 'No especificado por la IA.') }}
                                        </p>
                                        
                                        <h6 class="fw-bold text-dark mb-2">⚖️ Criterios de Éxito</h6>
                                        <ul class="list-unstyled">
                                            @forelse(data_get($data, 'evaluation_criteria', []) as $criteria)
                                                <li class="mb-2 small d-flex align-items-start">
                                                    <i class="fas fa-star text-warning mt-1 me-2"></i> {{ $criteria }}
                                                </li>
                                            @empty
                                                <li class="text-muted small">Sin criterios definidos.</li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @else
                {{-- VISTA DE EXAMEN INTERACTIVO --}}
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden" id="quizContainer">
                    <div class="card-header bg-indigo-600 text-white p-4 d-flex justify-content-between align-items-center" style="background-color: #4f46e5;">
                        <h5 class="fw-bold mb-0 text-white"><i class="fas fa-pen-alt me-2"></i>Evaluación Teórica</h5>
                        {{-- Badge de puntuación (oculto al inicio) --}}
                        <span class="badge bg-white text-dark rounded-pill px-3 fs-6" id="scoreBadge" style="display: none;">
                            Calificación: <span id="finalScore">0</span>/100
                        </span>
                    </div>
                    
                    <div class="card-body p-0">
                        @php
                            $questions = data_get($data, 'questions', []);
                        @endphp

                        @if(count($questions) > 0)
                            <form id="quizForm">
                                <div class="accordion accordion-flush" id="accordionQuiz">
                                    @foreach($questions as $index => $q)
                                        <div class="accordion-item question-item" data-correct="{{ data_get($q, 'correct_answer') }}">
                                            <h2 class="accordion-header" id="heading{{ $index }}">
                                                <button class="accordion-button collapsed fw-bold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}">
                                                    <span class="badge bg-light text-dark border me-3">P{{ $index + 1 }}</span> 
                                                    {{ data_get($q, 'question') }}
                                                    {{-- Iconos de resultado (ocultos) --}}
                                                    <i class="fas fa-check-circle text-success ms-auto result-icon correct-icon d-none fa-lg"></i>
                                                    <i class="fas fa-times-circle text-danger ms-auto result-icon wrong-icon d-none fa-lg"></i>
                                                </button>
                                            </h2>
                                            <div id="collapse{{ $index }}" class="accordion-collapse collapse show" aria-labelledby="heading{{ $index }}">
                                                <div class="accordion-body bg-white p-4">
                                                    
                                                    {{-- Opciones de Respuesta --}}
                                                    <div class="list-group mb-4 options-group gap-2">
                                                        @foreach(data_get($q, 'options', []) as $optIndex => $option)
                                                            <label class="list-group-item list-group-item-action border rounded d-flex align-items-center cursor-pointer p-3">
                                                                <input class="form-check-input me-3 option-input" type="radio" name="question_{{ $index }}" value="{{ $option }}">
                                                                <div class="w-100 d-flex justify-content-between align-items-center">
                                                                    <span class="option-text">{{ $option }}</span>
                                                                    {{-- Badges de resultado (ocultos) --}}
                                                                    <span class="badge bg-success ms-2 status-badge correct-badge d-none">CORRECTA</span>
                                                                    <span class="badge bg-danger ms-2 status-badge wrong-badge d-none">TU RESPUESTA</span>
                                                                </div>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                    
                                                    {{-- Retroalimentación Mejorada --}}
                                                    <div class="feedback-box alert d-none rounded-3 border-0 shadow-sm">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <i class="fas fa-info-circle fs-4 me-2 feedback-icon"></i>
                                                            <strong class="feedback-title fs-5"></strong>
                                                        </div>
                                                        <hr class="my-2">
                                                        <div class="mb-2 correct-answer-text d-none">
                                                            <small class="text-uppercase fw-bold text-muted">La respuesta correcta es:</small><br>
                                                            <span class="fw-bold fs-6">{{ data_get($q, 'correct_answer') }}</span>
                                                        </div>
                                                        <div class="mt-2">
                                                            <small class="text-uppercase fw-bold text-muted">Explicación:</small><br>
                                                            <span class="text-dark">{{ data_get($q, 'explanation') }}</span>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Botón de Acción --}}
                                <div class="p-4 bg-light text-end border-top sticky-bottom">
                                    <button type="button" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm fw-bold" onclick="gradeQuiz()" id="btnGrade">
                                        <i class="fas fa-clipboard-check me-2"></i> Finalizar y Calificar
                                    </button>
                                    <a href="{{ route('tests.create') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-5 d-none" id="btnRetry">
                                        <i class="fas fa-redo me-2"></i> Crear Nuevo Examen
                                    </a>
                                </div>
                            </form>
                        @else
                            <div class="p-5 text-center">
                                <div class="mb-3 text-muted"><i class="fas fa-exclamation-circle fa-2x"></i></div>
                                <p class="text-muted">No se pudieron cargar las preguntas. La estructura generada no es válida o está vacía.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

<script>
function gradeQuiz() {
    let score = 0;
    let total = 0;
    const questions = document.querySelectorAll('.question-item');

    questions.forEach(q => {
        total++;
        // Limpiamos espacios para evitar errores de coincidencia
        const correctAnswer = q.getAttribute('data-correct').trim();
        
        const selectedInput = q.querySelector('input[type="radio"]:checked');
        
        // Elementos UI
        const feedbackBox = q.querySelector('.feedback-box');
        const feedbackTitle = q.querySelector('.feedback-title');
        const feedbackIcon = q.querySelector('.feedback-icon');
        const correctIcon = q.querySelector('.correct-icon');
        const wrongIcon = q.querySelector('.wrong-icon');
        const correctAnswerText = q.querySelector('.correct-answer-text');
        const options = q.querySelectorAll('.list-group-item');

        // Reset visual
        options.forEach(opt => {
            opt.classList.remove('list-group-item-success', 'list-group-item-danger', 'border-success', 'border-danger');
            opt.querySelector('.correct-badge').classList.add('d-none');
            opt.querySelector('.wrong-badge').classList.add('d-none');
        });
        
        feedbackBox.classList.remove('d-none', 'alert-success', 'alert-danger', 'alert-warning');
        correctAnswerText.classList.add('d-none');

        if (selectedInput) {
            const userAnswer = selectedInput.value.trim();
            const selectedLabel = selectedInput.closest('label');
            
            // Comparación
            if (userAnswer === correctAnswer) {
                score++;
                // ACIERTO
                selectedLabel.classList.add('list-group-item-success', 'border-success');
                selectedLabel.querySelector('.correct-badge').classList.remove('d-none');
                
                feedbackBox.classList.add('alert-success');
                feedbackTitle.textContent = "¡Correcto!";
                feedbackIcon.className = "fas fa-check-circle fs-4 me-2 text-success";
                correctIcon.classList.remove('d-none');
            } else {
                // FALLO
                selectedLabel.classList.add('list-group-item-danger', 'border-danger');
                selectedLabel.querySelector('.wrong-badge').classList.remove('d-none');
                
                feedbackBox.classList.add('alert-danger');
                feedbackTitle.textContent = "Incorrecto";
                feedbackIcon.className = "fas fa-times-circle fs-4 me-2 text-danger";
                wrongIcon.classList.remove('d-none');
                correctAnswerText.classList.remove('d-none');

                // Buscar y marcar la correcta visualmente también
                options.forEach(opt => {
                    const optText = opt.querySelector('.option-text').textContent.trim();
                    if (optText === correctAnswer) {
                        opt.classList.add('list-group-item-success', 'border-success');
                        opt.querySelector('.correct-badge').classList.remove('d-none');
                    }
                });
            }
        } else {
            // SIN RESPONDER
            feedbackBox.classList.add('alert-warning');
            feedbackTitle.textContent = "No respondida";
            feedbackIcon.className = "fas fa-exclamation-triangle fs-4 me-2 text-warning";
            wrongIcon.classList.remove('d-none');
            correctAnswerText.classList.remove('d-none');
            
            // Mostrar la correcta
            options.forEach(opt => {
                const optText = opt.querySelector('.option-text').textContent.trim();
                if (optText === correctAnswer) {
                    opt.classList.add('list-group-item-success', 'border-success');
                    opt.querySelector('.correct-badge').classList.remove('d-none');
                }
            });
        }

        // Bloquear inputs
        q.querySelectorAll('input').forEach(input => input.disabled = true);
    });

    // Calcular nota
    const finalScore = total === 0 ? 0 : Math.round((score / total) * 100);
    
    // Mostrar nota final
    const scoreBadge = document.getElementById('scoreBadge');
    const scoreText = document.getElementById('finalScore');
    scoreText.innerText = finalScore;
    
    scoreBadge.classList.remove('bg-danger', 'bg-warning', 'bg-success', 'bg-dark');
    if(finalScore < 60) scoreBadge.classList.add('bg-danger');
    else if(finalScore < 80) scoreBadge.classList.add('bg-warning', 'text-dark');
    else scoreBadge.classList.add('bg-success');
    
    scoreBadge.style.display = 'inline-block';

    // Scroll arriba
    window.scrollTo({ top: 0, behavior: 'smooth' });

    // Cambiar botones
    document.getElementById('btnGrade').classList.add('d-none');
    document.getElementById('btnRetry').classList.remove('d-none');
}
</script>

<style>
    .cursor-pointer { cursor: pointer; }
    .accordion-button:not(.collapsed) {
        background-color: #f8f9fa;
        color: #212529;
        box-shadow: inset 0 -1px 0 rgba(0,0,0,.125);
    }
    .accordion-button:focus { box-shadow: none; border-color: rgba(0,0,0,.125); }
    
    .list-group-item {
        transition: all 0.2s ease;
        border: 1px solid #dee2e6;
        margin-bottom: 0.5rem;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
    
    .list-group-item-success {
        background-color: #d1e7dd !important;
        color: #0f5132 !important;
        border-color: #badbcc !important;
    }
    .list-group-item-danger {
        background-color: #f8d7da !important;
        color: #842029 !important;
        border-color: #f5c2c7 !important;
    }
</style>
@endsection