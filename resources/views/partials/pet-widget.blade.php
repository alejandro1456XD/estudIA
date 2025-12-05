<!-- Widget de Mascota (EstuPet) -->
<div class="card shadow-sm mb-4 border-0 overflow-hidden position-relative">
    <!-- Fondo decorativo -->
    <div class="position-absolute top-0 start-0 w-100 h-100" 
         style="background: linear-gradient(135deg, var(--bs-primary-bg-subtle, #f0f9ff) 0%, var(--bs-body-bg, #ffffff) 100%); z-index: 0;">
    </div>

    <div class="card-body text-center position-relative" style="z-index: 1;">
        
        @if(Auth::user()->pet_type)
            <!-- MODO: MASCOTA SELECCIONADA -->
            <h6 class="text-muted text-uppercase fw-bold small mb-2">Tu Compañero de Estudio</h6>
            
            <!-- CONTENEDOR DE LA ESCENA (Mascota + Stickers) -->
            <div class="pet-scene position-relative d-inline-block mt-2 mb-3" style="width: 160px; height: 160px;">
                
                <!-- 1. El Aura (Fondo) -->
                <div class="position-absolute top-50 start-50 translate-middle bg-white rounded-circle shadow-sm" 
                     style="width: 120px; height: 120px; opacity: 0.8; z-index: 0;"></div>
                
                <!-- 2. La Mascota (Centro) -->
                <div class="position-absolute top-50 start-50 translate-middle" style="z-index: 10;">
                    @switch(Auth::user()->pet_type)
                        @case('dog') <i class="fas fa-dog text-primary pet-bounce" style="font-size: 5rem;"></i> @break
                        @case('cat') <i class="fas fa-cat text-warning pet-float" style="font-size: 5rem;"></i> @break
                        @case('dolphin') <i class="fas fa-fish text-info pet-swim" style="font-size: 5rem; transform: rotate(-45deg);"></i> @break
                        @case('shark') <i class="fas fa-shark text-danger pet-float" style="font-size: 5rem;"></i> @break
                    @endswitch
                </div>

                <!-- 3. STICKERS DECORATIVOS (Flotando alrededor) -->
                @php $stickerIndex = 0; @endphp
                @foreach(Auth::user()->items as $uItem)
                    @if($uItem->is_equipped)
                        @php 
                            $stickerIndex++; 
                            $posClass = match($stickerIndex) {
                                1 => 'sticker-top-right',
                                2 => 'sticker-bottom-left',
                                3 => 'sticker-top-left',
                                4 => 'sticker-bottom-right',
                                default => 'sticker-float' 
                            };
                        @endphp

                        <div class="pet-sticker {{ $posClass }}" title="{{ $uItem->item->name }}">
                            @if($uItem->item->category == 'hat') <i class="fas fa-hat-wizard text-primary fa-lg"></i>
                            @elseif($uItem->item->category == 'glasses') <i class="fas fa-glasses text-dark fa-lg"></i>
                            @elseif($uItem->item->category == 'body') <i class="fas fa-shield-alt text-info fa-lg"></i>
                            @elseif(str_contains($uItem->item->name, 'Galleta')) <i class="fas fa-cookie-bite text-warning fa-lg"></i>
                            @else <i class="fas fa-star text-warning fa-lg"></i> @endif
                        </div>
                    @endif
                @endforeach
            </div>
            
            <!-- NOMBRE DE LA MASCOTA CON EDICIÓN -->
            <div class="mt-1 d-flex justify-content-center align-items-center gap-2">
                <span class="fw-bold text-primary fs-5">
                    {{ Auth::user()->pet_name ?? 'Tu Mascota' }}
                </span>
                <!-- Botón de Lápiz para Editar -->
                <button class="btn btn-sm btn-link text-muted p-0 opacity-50 hover-opacity-100" 
                        data-bs-toggle="modal" 
                        data-bs-target="#renamePetModal"
                        title="Cambiar nombre">
                    <i class="fas fa-pencil-alt small"></i>
                </button>
            </div>

            <!-- Estadísticas -->
            <div class="d-flex justify-content-center gap-3 mt-2">
                <div class="badge bg-warning text-dark shadow-sm p-2 rounded-pill">
                    <i class="fas fa-coins me-1"></i> {{ Auth::user()->coins ?? 0 }}
                </div>
                <div class="badge bg-success shadow-sm p-2 rounded-pill">
                    <i class="fas fa-level-up-alt me-1"></i> Lvl {{ Auth::user()->pet_level ?? 1 }}
                </div>
            </div>

            <!-- Barra XP -->
            <div class="px-3 mt-3">
                <div class="d-flex justify-content-between small fw-bold text-muted mb-1">
                    <span>Progreso</span>
                    <span>{{ Auth::user()->pet_xp ?? 0 }} / {{ Auth::user()->pet_xp_next_level ?? 100 }} XP</span>
                </div>
                <div class="progress bg-secondary-subtle" style="height: 8px; border-radius: 10px;">
                    @php
                        $xp = Auth::user()->pet_xp ?? 0;
                        $next = Auth::user()->pet_xp_next_level ?? 100;
                        $percentage = ($next > 0) ? ($xp / $next) * 100 : 0;
                    @endphp
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage }}%;"></div>
                </div>
            </div>

            <!-- Botones -->
            <div class="mt-4 d-grid gap-2">
                <a href="{{ route('shop.index') }}" class="btn btn-sm btn-primary rounded-pill shadow-sm">
                    <i class="fas fa-shapes me-1"></i> Tienda de Stickers
                </a>
                <button class="btn btn-sm btn-outline-secondary rounded-pill" data-bs-toggle="modal" data-bs-target="#choosePetModal">
                    <i class="fas fa-sync-alt me-1"></i> Cambiar Mascota
                </button>
            </div>

        @else
            <!-- SELECCIÓN PENDIENTE -->
            <div class="py-4">
                <div class="mb-3 text-warning display-4"><i class="fas fa-egg fa-shake"></i></div>
                <h5>¡Adopta un compañero!</h5>
                <button class="btn btn-primary w-100 pulse-button" data-bs-toggle="modal" data-bs-target="#choosePetModal">
                    Elegir Mascota
                </button>
            </div>
        @endif
    </div>
</div>

<!-- ESTILOS CSS -->
<style>
    .pet-sticker {
        position: absolute;
        background: white;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        border: 2px solid #fff;
        z-index: 20;
        animation: popIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .sticker-top-right { top: 10px; right: 10px; transform: rotate(15deg); }
    .sticker-bottom-left { bottom: 10px; left: 10px; transform: rotate(-10deg); }
    .sticker-top-left { top: 10px; left: 10px; transform: rotate(-15deg); }
    .sticker-bottom-right { bottom: 10px; right: 10px; transform: rotate(10deg); }
    .sticker-float { top: -20px; left: 50%; transform: translateX(-50%); }
    .hover-opacity-100:hover { opacity: 1 !important; cursor: pointer; }

    @keyframes popIn { from { transform: scale(0); } to { transform: scale(1); } }
    .pet-bounce { animation: bounce 2s infinite; }
    @keyframes bounce { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-10px); } }
    .pet-float { animation: float 3s ease-in-out infinite; }
    @keyframes float { 0% { transform: translate(-50%, 0px); } 50% { transform: translate(-50%, -10px); } 100% { transform: translate(-50%, 0px); } }
    .pet-swim { animation: swim 3s ease-in-out infinite; }
    @keyframes swim { 0% { transform: rotate(-45deg) translateX(0); } 50% { transform: rotate(-40deg) translateX(5px); } 100% { transform: rotate(-45deg) translateX(0); } }
</style>

<!-- MODAL 1: SELECCIÓN DE MASCOTA -->
<div class="modal fade" id="choosePetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title"><i class="fas fa-paw me-2"></i>Elige tu Compañero</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <form action="{{ route('pet.select') }}" method="POST">
                    @csrf
                    <div class="row g-4 justify-content-center">
                        <div class="col-6 col-md-3">
                            <input type="radio" class="btn-check" name="pet_type" id="pet_dolphin" value="dolphin" required>
                            <label class="btn btn-outline-light text-dark h-100 border-0 shadow-sm p-3 card-hover-effect w-100" for="pet_dolphin">
                                <div class="text-info mb-3"><i class="fas fa-fish fa-3x"></i></div>
                                <h6 class="fw-bold">El Delfín</h6>
                            </label>
                        </div>
                        <div class="col-6 col-md-3">
                            <input type="radio" class="btn-check" name="pet_type" id="pet_shark" value="shark">
                            <label class="btn btn-outline-light text-dark h-100 border-0 shadow-sm p-3 card-hover-effect w-100" for="pet_shark">
                                <div class="text-danger mb-3"><i class="fas fa-bookmark fa-3x"></i></div> 
                                <h6 class="fw-bold">El Tiburón</h6>
                            </label>
                        </div>
                        <div class="col-6 col-md-3">
                            <input type="radio" class="btn-check" name="pet_type" id="pet_dog" value="dog">
                            <label class="btn btn-outline-light text-dark h-100 border-0 shadow-sm p-3 card-hover-effect w-100" for="pet_dog">
                                <div class="text-primary mb-3"><i class="fas fa-dog fa-3x"></i></div>
                                <h6 class="fw-bold">El Perro</h6>
                            </label>
                        </div>
                        <div class="col-6 col-md-3">
                            <input type="radio" class="btn-check" name="pet_type" id="pet_cat" value="cat">
                            <label class="btn btn-outline-light text-dark h-100 border-0 shadow-sm p-3 card-hover-effect w-100" for="pet_cat">
                                <div class="text-warning mb-3"><i class="fas fa-cat fa-3x"></i></div>
                                <h6 class="fw-bold">El Gato</h6>
                            </label>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow">Confirmar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODAL 2: RENOMBRAR MASCOTA -->
<div class="modal fade" id="renamePetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold">Renombrar Mascota</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('pet.rename') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="petNameInput" class="form-label small text-muted">Nuevo nombre:</label>
                        <input type="text" class="form-control" id="petNameInput" name="name" 
                               value="{{ Auth::user()->pet_name }}" 
                               maxlength="15" required placeholder="Ej: Firulais">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-sm rounded-pill">Guardar Nombre</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>