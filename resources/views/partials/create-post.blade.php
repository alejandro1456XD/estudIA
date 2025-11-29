<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <form action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="d-flex align-items-center mb-3">
                <!-- LÓGICA DE FOTO DE PERFIL DEL USUARIO -->
                <div class="me-3 flex-shrink-0">
                    @if(Auth::user()->profile_photo_path)
                        <img src="{{ asset('storage/' . Auth::user()->profile_photo_path) }}" 
                             class="rounded-circle" 
                             alt="{{ Auth::user()->name }}"
                             style="width: 45px; height: 45px; object-fit: cover; border: 2px solid #f0f2f5;">
                    @else
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" 
                             style="width: 45px; height: 45px; font-size: 18px;">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    @endif
                </div>
                
                <!-- Input de texto -->
                <textarea name="content" class="form-control border-0 bg-light rounded-pill px-3 py-2" 
                          placeholder="¿Qué estás aprendiendo hoy, {{ Auth::user()->name }}?" 
                          rows="1" 
                          style="resize: none; overflow: hidden; min-height: 45px; line-height: 30px;"></textarea>
            </div>

            <!-- Inputs ocultos -->
            <!-- Quitamos video/* del accept para que no puedan seleccionarlos -->
            <input type="file" name="file" id="fileInput" style="display: none;" accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt">
            <input type="hidden" name="type" id="postType" value="question">

            <!-- Botones de tipo de publicación (SIN VIDEO) -->
            <div class="d-flex justify-content-between border-top pt-3">
                <!-- Botón Video ELIMINADO -->
                
                <button type="button" class="btn btn-outline-success btn-sm rounded-pill px-3 border-0" onclick="selectPostType('photo')">
                    <i class="fas fa-image me-1"></i> Foto
                </button>
                <button type="button" class="btn btn-outline-warning btn-sm rounded-pill px-3 border-0" onclick="selectPostType('document')">
                    <i class="fas fa-file-alt me-1"></i> Documento
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 border-0" onclick="selectPostType('question')">
                    <i class="fas fa-question-circle me-1"></i> Pregunta
                </button>
                
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold shadow-sm">
                    Publicar
                </button>
            </div>
            
            <!-- Previsualización de archivo -->
            <div id="filePreview" class="mt-3" style="display: none;">
                <div class="alert alert-info d-flex justify-content-between align-items-center py-2 px-3 mb-0 small rounded-3">
                    <div>
                        <i class="fas fa-paperclip me-2"></i>
                        <span id="fileName" class="text-truncate fw-bold"></span>
                    </div>
                    <button type="button" class="btn-close btn-close-white small" onclick="removeFile()"></button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function selectPostType(type) {
    document.getElementById('postType').value = type;
    
    // Resaltar visualmente el botón seleccionado (opcional)
    // Lógica simple: Si no es pregunta, abrimos el selector de archivos
    if (type !== 'question') {
        document.getElementById('fileInput').click();
    }
}

document.getElementById('fileInput').addEventListener('change', function(e) {
    if (e.target.files.length > 0) {
        const file = e.target.files[0];
        document.getElementById('fileName').textContent = file.name;
        document.getElementById('filePreview').style.display = 'block';
    }
});

function removeFile() {
    document.getElementById('fileInput').value = '';
    document.getElementById('filePreview').style.display = 'none';
    document.getElementById('postType').value = 'question';
}
</script>
@endpush