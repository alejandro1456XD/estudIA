@extends('layouts.app')

@section('title', 'Cursos - estudIA')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        
        <!-- Mensajes de Feedback -->
        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm border-0" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle fs-4 me-3 text-success"></i>
                <div>{{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 shadow-sm border-0" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle fs-4 me-3 text-danger"></i>
                <div>{{ session('error') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <!-- ENCABEZADO MODERNO -->
            <div class="card-header bg-white p-4 border-bottom">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                    
                    <!-- Título y Icono -->
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-25 p-3 rounded-circle me-3">
                            <i class="fas fa-graduation-cap fa-2x text-warning"></i>
                        </div>
                        <div>
                            <h3 class="mb-0 fw-bold text-dark">Cursos</h3>
                            <p class="text-muted small mb-0">Aprende, enseña y crece.</p>
                        </div>
                    </div>
                    
                    <!-- Navegación de Pestañas (Estilo Pills) -->
                    <div class="bg-light p-1 rounded-pill d-inline-flex shadow-sm">
                        <button class="btn btn-sm rounded-pill px-3 fw-bold btn-tab active" id="tab-student" onclick="switchView('student')">
                            <i class="fas fa-book-reader me-1"></i> Mis Cursos
                        </button>
                        <button class="btn btn-sm rounded-pill px-3 fw-bold btn-tab" id="tab-explore" onclick="switchView('explore')">
                            <i class="fas fa-search me-1"></i> Explorar
                        </button>
                        <button class="btn btn-sm rounded-pill px-3 fw-bold btn-tab" id="tab-instructor" onclick="switchView('instructor')">
                            <i class="fas fa-chalkboard-teacher me-1"></i> Gestionar
                        </button>
                    </div>

                    <!-- Botón Crear (Solo visible en pantallas grandes, en móviles flota) -->
                    <button class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm d-none d-md-block" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                        <i class="fas fa-plus me-2"></i>Crear
                    </button>
                </div>
            </div>
            
            <div class="card-body p-4 bg-light">
                
                <!-- ================= VISTA 1: APRENDIENDO (Cursos Inscritos) ================= -->
                <div id="student-view" class="view-section">
                    @if($enrolledCourses->count() > 0)
                        <div class="row row-cols-1 row-cols-md-2 g-4">
                            @foreach($enrolledCourses as $course)
                            <div class="col">
                                <div class="card h-100 border-0 shadow-sm course-card">
                                    <div class="card-body p-4 d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">
                                                {{ ucfirst($course->category) }}
                                            </span>
                                            
                                            @if($course->is_live_now)
                                                <span class="badge bg-danger animate-pulse shadow-sm">🔴 EN VIVO</span>
                                            @endif
                                        </div>

                                        <h5 class="fw-bold text-dark mb-1">{{ $course->name }}</h5>
                                        <p class="text-muted small mb-2">
                                            <i class="fas fa-user-circle me-1"></i> {{ $course->instructor->name }}
                                        </p>

                                        <!-- Mostrar horario si existe -->
                                        @if(isset($course->schedule['start_time']))
                                            <div class="alert alert-light border py-1 px-2 mb-3 d-inline-flex align-items-center gap-2 small text-muted">
                                                <i class="far fa-clock text-warning"></i>
                                                <span>Horario: {{ $course->schedule['start_time'] }} ({{ $course->schedule['duration'] }} min)</span>
                                            </div>
                                        @endif
                                        
                                        <div class="mt-auto">
                                            <div class="d-flex justify-content-between text-muted small mb-1">
                                                <span>Progreso</span>
                                                <span>{{ $course->pivot->progress ?? 0 }}%</span>
                                            </div>
                                            <div class="progress mb-3" style="height: 6px;">
                                                <div class="progress-bar bg-success rounded-pill" style="width: {{ $course->pivot->progress ?? 0 }}%"></div>
                                            </div>
                                            
                                            <!-- BOTONES INTELIGENTES -->
                                            @if($course->type == 'live' || $course->type == 'hybrid')
                                                @if($course->is_live_now)
                                                    <a href="{{ route('classroom', ['id' => $course->id]) }}" target="_blank" class="btn btn-danger w-100 fw-bold rounded-pill shadow-sm">
                                                        <i class="fas fa-video me-2"></i> Entrar a Clase
                                                    </a>
                                                @else
                                                    <button disabled class="btn btn-light w-100 fw-bold text-muted border rounded-pill">
                                                        <i class="fas fa-clock me-2"></i> Esperando al instructor...
                                                    </button>
                                                @endif
                                            @endif

                                            @if($course->type == 'recorded' || $course->type == 'hybrid')
                                                <button class="btn btn-outline-primary w-100 fw-bold rounded-pill mt-2" onclick="alert('Abriendo lista de videos...')">
                                                    <i class="fas fa-play me-2"></i> Ver Lecciones
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486747.png" width="120" alt="Vacio" class="opacity-50">
                            </div>
                            <h4 class="text-muted fw-bold">No estás inscrito en cursos</h4>
                            <p class="text-muted mb-4">Explora el catálogo y encuentra algo nuevo para aprender hoy.</p>
                            <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" onclick="switchView('explore')">
                                <i class="fas fa-search me-2"></i>Explorar Cursos
                            </button>
                        </div>
                    @endif
                </div>

                <!-- ================= VISTA 2: EXPLORAR (Comunidad) ================= -->
                <div id="explore-view" class="view-section" style="display: none;">
                    @if($availableCourses->count() > 0)
                        <div class="row row-cols-1 g-3">
                            @foreach($availableCourses as $course)
                            <div class="col">
                                <div class="card border-0 shadow-sm course-card p-2">
                                    <div class="card-body d-flex flex-column flex-md-row align-items-center gap-4">
                                        
                                        <!-- Icono Categoría -->
                                        <div class="rounded-4 bg-light p-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                            @if($course->category == 'tech') <i class="fas fa-laptop-code fa-2x text-primary"></i>
                                            @elseif($course->category == 'math') <i class="fas fa-calculator fa-2x text-danger"></i>
                                            @elseif($course->category == 'lang') <i class="fas fa-language fa-2x text-success"></i>
                                            @elseif($course->category == 'art') <i class="fas fa-palette fa-2x text-info"></i>
                                            @elseif($course->category == 'music') <i class="fas fa-music fa-2x text-secondary"></i>
                                            @elseif($course->category == 'game') <i class="fas fa-gamepad fa-2x text-dark"></i>
                                            @else <i class="fas fa-book fa-2x text-warning"></i> @endif
                                        </div>

                                        <!-- Info -->
                                        <div class="flex-grow-1 text-center text-md-start">
                                            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2 mb-1">
                                                <h5 class="fw-bold mb-0">{{ $course->name }}</h5>
                                                @if($course->type == 'live') <span class="badge bg-danger rounded-pill">En Vivo</span>
                                                @elseif($course->type == 'recorded') <span class="badge bg-secondary rounded-pill">Grabado</span>
                                                @else <span class="badge bg-primary rounded-pill">Híbrido</span> @endif
                                            </div>
                                            <p class="text-muted mb-1 small">
                                                Por <strong class="text-dark">{{ $course->instructor->name }}</strong> • {{ ucfirst($course->category) }} • {{ ucfirst($course->level) }}
                                            </p>
                                            <p class="text-muted small mb-0 d-none d-md-block">
                                                {{ Str::limit($course->description, 120) }}
                                            </p>
                                        </div>

                                        <!-- Botón -->
                                        <div style="min-width: 180px;">
                                            <form action="{{ route('courses.enroll', $course->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="btn btn-success w-100 rounded-pill fw-bold shadow-sm">
                                                    Inscribirme Gratis
                                                </button>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-search fa-3x mb-3 opacity-50"></i>
                            <h5>No hay cursos nuevos disponibles.</h5>
                            <p>¡Sé el primero en crear uno para la comunidad!</p>
                        </div>
                    @endif
                </div>

                <!-- ================= VISTA 3: GESTIONAR (Instructor) ================= -->
                <div id="instructor-view" class="view-section" style="display: none;">
                    
                    @if($myCreatedCourses->count() > 0)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark m-0">Mis Cursos Creados</h5>
                            <span class="badge bg-dark rounded-pill">{{ $myCreatedCourses->count() }} Cursos</span>
                        </div>

                        <div class="row row-cols-1 g-3">
                            @foreach($myCreatedCourses as $myCourse)
                            <div class="col">
                                <div class="card border-0 shadow-sm course-card overflow-hidden">
                                    <div class="card-body p-0 d-flex flex-column flex-md-row">
                                        
                                        <!-- Borde de estado -->
                                        <div class="bg-{{ $myCourse->is_live_now ? 'danger' : 'primary' }}" style="width: 6px;"></div>
                                        
                                        <div class="p-4 flex-grow-1 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                                            
                                            <!-- Info -->
                                            <div class="text-center text-md-start">
                                                <div class="d-flex align-items-center justify-content-center justify-content-md-start gap-2 mb-1">
                                                    <h5 class="fw-bold mb-0 text-dark">{{ $myCourse->name }}</h5>
                                                    @if($myCourse->is_live_now)
                                                        <span class="badge bg-danger animate-pulse">🔴 EN VIVO</span>
                                                    @endif
                                                </div>
                                                <div class="d-flex align-items-center gap-3 text-muted small">
                                                    <span><i class="fas fa-users me-1"></i> {{ $myCourse->students->count() }} alumnos</span>
                                                    <span><i class="fas fa-layer-group me-1"></i> {{ ucfirst($myCourse->category) }}</span>
                                                </div>
                                            </div>

                                            <!-- Acciones -->
                                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                                
                                                <!-- Control En Vivo -->
                                                @if($myCourse->type == 'live' || $myCourse->type == 'hybrid')
                                                    <form action="{{ route('courses.toggle-live', $myCourse->id) }}" method="POST">
                                                        @csrf
                                                        @if($myCourse->is_live_now)
                                                            <a href="{{ route('classroom', ['id' => $myCourse->id]) }}" target="_blank" class="btn btn-outline-danger btn-sm rounded-pill fw-bold me-1">
                                                                <i class="fas fa-external-link-alt me-1"></i> Entrar
                                                            </a>
                                                            <button type="submit" class="btn btn-dark btn-sm rounded-pill fw-bold">
                                                                <i class="fas fa-stop me-1"></i> Finalizar
                                                            </button>
                                                        @else
                                                            <button type="submit" class="btn btn-success btn-sm rounded-pill fw-bold shadow-sm" onclick="setTimeout(() => window.open('{{ route('classroom', ['id' => $myCourse->id]) }}', '_blank'), 1000)">
                                                                <i class="fas fa-video me-1"></i> Iniciar Clase
                                                            </button>
                                                        @endif
                                                    </form>
                                                @endif

                                                <!-- Material -->
                                                @if($myCourse->type == 'recorded' || $myCourse->type == 'hybrid')
                                                    <button class="btn btn-light border btn-sm rounded-pill fw-bold text-primary">
                                                        <i class="fas fa-upload me-1"></i> Material
                                                    </button>
                                                @endif

                                                <button class="btn btn-light border btn-sm rounded-circle" title="Editar">
                                                    <i class="fas fa-pen text-muted"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <span class="fa-stack fa-3x text-warning opacity-75">
                                    <i class="fas fa-circle fa-stack-2x"></i>
                                    <i class="fas fa-lightbulb fa-stack-1x fa-inverse"></i>
                                </span>
                            </div>
                            <h4 class="text-muted fw-bold">Conviértete en Instructor</h4>
                            <p class="text-muted mb-4">Comparte tu conocimiento y crea tu primer curso hoy mismo.</p>
                            <button class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                                <i class="fas fa-plus me-2"></i>Crear Curso
                            </button>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>

<!-- BOTÓN FLOTANTE PARA MÓVIL (CREAR) -->
<button class="btn btn-dark rounded-circle shadow-lg d-md-none position-fixed bottom-0 end-0 m-4 p-3" 
        style="z-index: 1000; width: 60px; height: 60px;"
        data-bs-toggle="modal" data-bs-target="#createCourseModal">
    <i class="fas fa-plus fa-lg"></i>
</button>

<!-- MODAL: CREAR CURSO -->
<div class="modal fade" id="createCourseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white p-4 border-0">
                <div>
                    <h5 class="modal-title fw-bold">Crear Nuevo Curso</h5>
                    <p class="mb-0 small text-white-50">Configura los detalles básicos de tu clase</p>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form action="{{ route('courses.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-light">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">NOMBRE DEL CURSO</label>
                            <!-- Placeholder eliminado como pediste -->
                            <input type="text" name="name" class="form-control form-control-lg border-0 shadow-sm" placeholder="" required>
                        </div>
                        
                        <!-- CATEGORÍA CON OPCIÓN "OTRO" -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">CATEGORÍA</label>
                            <select name="category" id="categorySelect" class="form-select form-select-lg border-0 shadow-sm cursor-pointer" onchange="checkCategory(this)">
                                <option value="tech">💻 Tecnología</option>
                                <option value="math">📐 Matemáticas</option>
                                <option value="lang">🗣️ Idiomas</option>
                                <option value="science">🧬 Ciencias</option>
                                <option value="art">🎨 Arte</option>
                                <option value="music">🎵 Música</option>
                                <option value="fitness">🏋️ Salud y Fitness</option>
                                <option value="business">💼 Negocios</option>
                                <option value="marketing">📈 Marketing</option>
                                <option value="photo">📷 Fotografía</option>
                                <option value="cooking">🍳 Cocina</option>
                                <option value="game">🎮 Videojuegos</option>
                                <option value="other">➕ Otro (Especificar)</option>
                            </select>
                            
                            <!-- Campo Oculto para "Otro" -->
                            <div id="customCategoryDiv" class="mt-2" style="display: none;">
                                <input type="text" id="customCategoryInput" class="form-control border-0 shadow-sm" placeholder="Escribe la categoría (ej: Jardinería)">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">IDIOMA</label>
                            <select name="language" class="form-select border-0 shadow-sm">
                                <option value="es">Español</option>
                                <option value="en">Inglés</option>
                                <option value="pt">Portugués</option>
                                <option value="fr">Francés</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">NIVEL</label>
                            <select name="level" class="form-select border-0 shadow-sm">
                                <option value="beginner">Principiante</option>
                                <option value="intermediate">Intermedio</option>
                                <option value="advanced">Avanzado</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small text-muted">DESCRIPCIÓN</label>
                            <textarea name="description" class="form-control border-0 shadow-sm" rows="3" placeholder="¿Qué aprenderán los estudiantes?"></textarea>
                        </div>

                        <div class="col-12 mt-4">
                            <label class="form-label fw-bold small text-muted mb-3">MODALIDAD DE ENSEÑANZA</label>
                            <div class="row text-center g-3">
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="course_type" id="t_rec" value="recorded" checked onclick="toggleSchedule(false)">
                                    <label class="btn btn-white border shadow-sm w-100 p-3 rounded-3" for="t_rec">
                                        <i class="fas fa-video fa-2x mb-2 text-danger"></i><br><span class="fw-bold">Grabado</span>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="course_type" id="t_live" value="live" onclick="toggleSchedule(true)">
                                    <label class="btn btn-white border shadow-sm w-100 p-3 rounded-3" for="t_live">
                                        <i class="fas fa-broadcast-tower fa-2x mb-2 text-success"></i><br><span class="fw-bold">En Vivo</span>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="course_type" id="t_hyb" value="hybrid" onclick="toggleSchedule(true)">
                                    <label class="btn btn-white border shadow-sm w-100 p-3 rounded-3" for="t_hyb">
                                        <i class="fas fa-sync-alt fa-2x mb-2 text-primary"></i><br><span class="fw-bold">Híbrido</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- SECCIÓN DE HORARIO (AGREGADA) -->
                        <div class="col-12 mt-3" id="scheduleSection" style="display: none;">
                            <div class="bg-white p-3 rounded-3 border shadow-sm">
                                <label class="form-label fw-bold small text-muted mb-2"><i class="far fa-calendar-alt me-1"></i> CONFIGURAR HORARIO EN VIVO</label>
                                
                                <div class="mb-3">
                                    <label class="form-label small mb-1">Días de clase:</label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="checkbox" class="btn-check" name="days[]" value="mon" id="d_mon"><label class="btn btn-outline-primary btn-sm" for="d_mon">L</label>
                                        <input type="checkbox" class="btn-check" name="days[]" value="tue" id="d_tue"><label class="btn btn-outline-primary btn-sm" for="d_tue">M</label>
                                        <input type="checkbox" class="btn-check" name="days[]" value="wed" id="d_wed"><label class="btn btn-outline-primary btn-sm" for="d_wed">M</label>
                                        <input type="checkbox" class="btn-check" name="days[]" value="thu" id="d_thu"><label class="btn btn-outline-primary btn-sm" for="d_thu">J</label>
                                        <input type="checkbox" class="btn-check" name="days[]" value="fri" id="d_fri"><label class="btn btn-outline-primary btn-sm" for="d_fri">V</label>
                                        <input type="checkbox" class="btn-check" name="days[]" value="sat" id="d_sat"><label class="btn btn-outline-primary btn-sm" for="d_sat">S</label>
                                        <input type="checkbox" class="btn-check" name="days[]" value="sun" id="d_sun"><label class="btn btn-outline-primary btn-sm" for="d_sun">D</label>
                                    </div>
                                </div>

                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small mb-1">Hora Inicio:</label>
                                        <input type="time" name="start_time" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small mb-1">Duración (min):</label>
                                        <input type="number" name="duration" class="form-control form-control-sm" placeholder="60">
                                    </div>
                                </div>
                                
                                <div class="mt-3 small text-muted">
                                    <i class="fas fa-video me-1"></i> Plataforma: <strong>Aula Virtual estudIA</strong>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer bg-white p-3 border-0">
                    <button type="button" class="btn btn-light rounded-pill fw-bold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success rounded-pill fw-bold px-5 shadow-sm">Crear Curso</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function switchView(mode) {
        document.querySelectorAll('.view-section').forEach(el => el.style.display = 'none');
        document.getElementById(mode + '-view').style.display = 'block';
        
        // Actualizar botones activos
        document.querySelectorAll('.btn-tab').forEach(btn => {
            btn.classList.remove('bg-white', 'text-dark', 'shadow-sm');
            btn.classList.add('text-muted');
        });
        const activeBtn = document.getElementById('tab-' + mode);
        activeBtn.classList.remove('text-muted');
        activeBtn.classList.add('bg-white', 'text-dark', 'shadow-sm');
    }

    function toggleSchedule(show) {
        const section = document.getElementById('scheduleSection');
        if (show) {
            section.style.display = 'block';
        } else {
            section.style.display = 'none';
        }
    }

    // NUEVO: Lógica para "Otro" en Categoría
    function checkCategory(select) {
        const customDiv = document.getElementById('customCategoryDiv');
        const customInput = document.getElementById('customCategoryInput');
        
        if (select.value === 'other') {
            customDiv.style.display = 'block';
            
            // Truco: Cambiamos el nombre del select para que NO se envíe
            // y le ponemos el nombre 'category' al input de texto.
            select.name = 'category_selector';
            customInput.name = 'category';
            customInput.required = true;
            customInput.focus();
        } else {
            customDiv.style.display = 'none';
            
            // Revertimos: El select vuelve a ser 'category'
            select.name = 'category';
            customInput.name = ''; // Quitamos el nombre al input para que no se envíe
            customInput.required = false;
        }
    }
    
    // Iniciar con la vista correcta
    switchView('student');
</script>

<style>
    .course-card { transition: transform 0.2s, box-shadow 0.2s; cursor: default; }
    .course-card:hover { transform: translateY(-3px); box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important; }
    
    .btn-tab { transition: all 0.2s; border: none; }
    .btn-tab:hover { background-color: rgba(255,255,255,0.5); }
    
    .btn-check:checked + label { background-color: #f8f9fa; border-color: #212529 !important; box-shadow: 0 0 0 2px #212529; }
    
    /* Animación pulso */
    @keyframes pulse-red { 0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); } 70% { box-shadow: 0 0 0 6px rgba(220, 53, 69, 0); } 100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); } }
    .animate-pulse { animation: pulse-red 2s infinite; }
</style>
@endsection