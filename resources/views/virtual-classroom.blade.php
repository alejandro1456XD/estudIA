@extends('layouts.app')

@section('content')
<!-- Importar PeerJS -->
<script src="https://unpkg.com/peerjs@1.5.2/dist/peerjs.min.js"></script>

<style>
    /* Estilos del Aula Virtual (Igual que antes) */
    #classroom-wrapper {
        position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
        background-color: #202124; color: white; z-index: 9999;
        display: flex; flex-direction: column; overflow: hidden;
    }
    #lobby-screen {
        position: absolute; top: 0; left: 0; width: 100%; height: 100%;
        background: #202124; z-index: 2001; display: flex;
        align-items: center; justify-content: center; flex-direction: column;
        transition: opacity 0.5s ease;
    }
    .preview-container {
        position: relative; width: 90%; max-width: 700px; aspect-ratio: 16/9;
        background: #3c4043; border-radius: 16px; overflow: hidden;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    video { width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1); }
    .lobby-controls {
        position: absolute; bottom: 20px; display: flex; gap: 20px; z-index: 10;
    }
    .control-btn-circle {
        width: 50px; height: 50px; border-radius: 50%; border: none; font-size: 20px;
        cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }
    .btn-on { background: rgba(255,255,255,0.9); color: #202124; }
    .btn-on:hover { background: #fff; transform: scale(1.05); }
    .btn-off { background: #ea4335; color: white; border: none; }
    .btn-off:hover { background: #d93025; transform: scale(1.05); }
    .main-container { flex: 1; display: flex; overflow: hidden; position: relative; }
    .video-grid {
        flex: 1; display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 15px; padding: 20px; align-content: center; justify-content: center;
        background: #202124; width: 100%;
    }
    .participant-card {
        background: #3c4043; border-radius: 12px; overflow: hidden; position: relative;
        aspect-ratio: 16/9; display: flex; align-items: center; justify-content: center;
        border: 2px solid transparent; transition: border-color 0.2s;
        max-height: 100%; width: 100%;
    }
    .participant-name {
        position: absolute; bottom: 15px; left: 15px; background: rgba(0,0,0,0.6);
        padding: 4px 12px; border-radius: 4px; font-size: 14px; font-weight: 500;
        color: white; backdrop-filter: blur(4px);
    }
    .participant-mic-status {
        position: absolute; top: 15px; right: 15px; background: rgba(0,0,0,0.6);
        width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center;
        justify-content: center; font-size: 14px; backdrop-filter: blur(4px);
    }
    .no-camera-avatar {
        width: 100px; height: 100px; border-radius: 50%; background: #5f6368;
        display: flex; align-items: center; justify-content: center; font-size: 35px;
        color: white; font-weight: bold; text-transform: uppercase; border: 2px solid #fff;
    }
    .profile-img-preview { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
    .bottom-bar {
        height: 80px; background: #202124; display: flex; align-items: center;
        justify-content: space-between; padding: 0 24px; border-top: 1px solid #3c4043;
    }
    .action-btn {
        width: 48px; height: 48px; border-radius: 50%; border: 1px solid #5f6368;
        background: #3c4043; color: white; font-size: 18px; display: flex;
        align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;
        margin: 0 5px;
    }
    .action-btn:hover { background: #45474a; }
    .action-btn.active { background: #8ab4f8; color: #202124; border-color: #8ab4f8; }
    .btn-call-end { background: #ea4335; width: 60px; border-radius: 30px; border: none; }
    .btn-call-end:hover { background: #d93025; }
    .sidebar {
        width: 360px; background: white; color: #202124; display: none;
        flex-direction: column; border-left: 1px solid #ddd; height: 100%;
    }
    .sidebar.open { display: flex; }
    .sidebar-header { padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
    .sidebar-content { flex: 1; overflow-y: auto; padding: 15px; background: #f8f9fa; }
    .sidebar-footer { padding: 15px; border-top: 1px solid #eee; background: white; }
    .chat-bubble {
        background: #e8f0fe; color: #1967d2; padding: 10px 15px; border-radius: 15px;
        margin-bottom: 10px; display: inline-block; max-width: 85%; font-size: 14px;
    }
    .chat-meta { font-size: 10px; color: #5f6368; margin-bottom: 2px; margin-left: 5px; }
</style>

<div id="classroom-wrapper">
    <!-- LOBBY -->
    <div id="lobby-screen">
        <h2 class="mb-4 fw-light text-white">Sala de Espera: {{ $course->title }}</h2>
        <div class="preview-container">
            <video id="localPreview" autoplay muted playsinline></video>
            <div id="noCamPreview" class="no-camera-avatar" style="display:none; position:absolute;">
                @if(auth()->user()->profile_photo_path)
                    <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" class="profile-img-preview">
                @else
                    {{ substr(auth()->user()->name, 0, 1) }}
                @endif
            </div>
            <div class="lobby-controls">
                <button class="control-btn-circle btn-on" id="btnToggleMicPreview" onclick="toggleMic()" title="Micrófono"><i class="fas fa-microphone"></i></button>
                <button class="control-btn-circle btn-on" id="btnToggleCamPreview" onclick="toggleCam()" title="Cámara"><i class="fas fa-video"></i></button>
            </div>
        </div>
        <div class="mt-5 d-flex gap-4 align-items-center bg-dark p-3 rounded-4 px-5 border border-secondary border-opacity-25">
            <div class="text-start border-end pe-4 border-secondary">
                <h5 class="m-0 fw-bold">{{ auth()->user()->name }}</h5>
                <small class="text-success" id="statusText">Cámara HD lista</small>
                <!-- Mostrar ID para depuración -->
                <div class="text-white-50 small mt-1" id="myIdDisplay">Conectando...</div>
            </div>
            <button class="btn btn-primary rounded-pill px-5 py-2 fw-bold fs-5" onclick="joinClassroom()">Unirse a Clase</button>
        </div>
    </div>

    <!-- SALA PRINCIPAL -->
    <div class="main-container">
        <div class="video-grid" id="videoGrid">
            <!-- Mi Video -->
            <div class="participant-card" id="localVideoContainer">
                <video id="localVideo" autoplay muted playsinline></video>
                <div id="localNoCam" class="no-camera-avatar" style="display:none; position:absolute;">
                    @if(auth()->user()->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" class="profile-img-preview">
                    @else
                        {{ substr(auth()->user()->name, 0, 1) }}
                    @endif
                </div>
                <div class="participant-name">{{ auth()->user()->name }} (Tú)</div>
                <div class="participant-mic-status">
                    <i class="fas fa-microphone-slash text-danger" id="localMicIcon" style="display:none;"></i>
                    <i class="fas fa-microphone text-white" id="localMicOnIcon"></i>
                </div>
            </div>
        </div>
        
        <!-- Sidebar Chat -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h5 class="m-0 fw-bold">Mensajes</h5>
                <button class="btn-close" onclick="toggleSidebar()"></button>
            </div>
            <div class="sidebar-content" id="sidebarContent">
                <div class="text-center text-muted mt-4"><small>Chat de la clase</small></div>
            </div>
            <div class="sidebar-footer">
                <div class="input-group">
                    <input type="text" id="chatInput" class="form-control bg-light border-end-0" placeholder="Enviar mensaje..." style="border-radius: 20px 0 0 20px;">
                    <button class="btn btn-primary border-start-0" onclick="sendMessage()" style="border-radius: 0 20px 20px 0;"><i class="fas fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de Controles -->
    <div class="bottom-bar">
        <div class="d-none d-md-block text-white" style="width: 200px;">
            <div class="fw-bold" id="clock">00:00</div>
            <small class="text-white-50">{{ Str::limit($course->title, 20) }}</small>
        </div>
        <div class="d-flex justify-content-center flex-grow-1">
            <button class="action-btn" id="btnToggleMic" onclick="toggleMic()"><i class="fas fa-microphone"></i></button>
            <button class="action-btn" id="btnToggleCam" onclick="toggleCam()"><i class="fas fa-video"></i></button>
            <button class="action-btn" onclick="shareScreen()"><i class="fas fa-desktop"></i></button>
            <button class="action-btn btn-call-end" onclick="showExitModal()"><i class="fas fa-phone-slash"></i></button>
        </div>
        <div class="d-flex justify-content-end" style="width: 200px;">
            <button class="action-btn" onclick="toggleSidebar()"><i class="fas fa-comment-alt"></i></button>
            <!-- BOTÓN PARA RECONEXIÓN (Si no eres profesor) -->
            @if(auth()->user()->id != $course->user_id)
            <button class="action-btn" onclick="connectToTeacher()" title="Reconectar Profesor"><i class="fas fa-sync-alt"></i></button>
            @endif
            <!-- Botón de Participantes (Simple) -->
            <button class="action-btn" title="Participantes"><i class="fas fa-users"></i></button>
        </div>
    </div>
</div>

<!-- Modal Salida -->
<div class="modal fade" id="exitClassModal" tabindex="-1" aria-hidden="true" style="z-index: 10000;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border border-secondary">
            <div class="modal-body p-4 text-center">
                <div class="mb-3"><div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;"><i class="fas fa-door-open fa-2x"></i></div></div>
                <h4 class="fw-bold mb-2">¿Salir de la clase?</h4>
                <p class="text-white-50 mb-4">La llamada finalizará y esta ventana se cerrará.</p>
                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger rounded-pill px-4 fw-bold" onclick="confirmExit()">Salir ahora</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Variables globales
    let localStream;
    let isMicOn = true;
    let isCamOn = true;
    let peer = null;
    
    // DATOS DINÁMICOS DESDE LARAVEL
    const myUserId = "{{ auth()->id() }}";
    const courseId = "{{ $course->id }}";
    const teacherId = "{{ $course->user_id }}"; // ID del creador del curso
    
    // Construimos los IDs predecibles
    const myPeerId = `estudia-c${courseId}-u${myUserId}`;
    const teacherPeerId = `estudia-c${courseId}-u${teacherId}`;
    const isTeacher = (myUserId === teacherId.toString()); // Comparación segura de string/int

    // Reloj
    setInterval(() => {
        const now = new Date();
        document.getElementById('clock').innerText = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    }, 1000);

    // INICIO
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            // 1. Obtener Medios (Cámara HD)
            localStream = await navigator.mediaDevices.getUserMedia({ 
                video: { width: { ideal: 1280 }, height: { ideal: 720 } }, 
                audio: true 
            });
            document.getElementById('localPreview').srcObject = localStream;
            document.getElementById('localVideo').srcObject = localStream;

            // 2. Iniciar PeerJS con mi ID predecible
            peer = new Peer(myPeerId, { host: '0.peerjs.com', port: 443, path: '/' });

            peer.on('open', id => {
                console.log('Mi Peer ID:', id);
                document.getElementById('myIdDisplay').innerText = `ID: ${id}`;
                document.getElementById('connectionStatus').innerText = isTeacher ? "Anfitrión: Esperando alumnos" : "Listo para conectar";
                document.getElementById('connectionStatus').classList.add('text-success');
            });

            // 3. Manejar llamadas entrantes (Si soy el profesor, recibo alumnos)
            peer.on('call', call => {
                console.log("Llamada entrante de:", call.peer);
                call.answer(localStream); // Responder con mi video
                
                const video = document.createElement('video');
                call.on('stream', userVideoStream => {
                    addVideoStream(video, userVideoStream, call.peer);
                });
                call.on('close', () => video.parentElement.remove());
            });

            peer.on('error', err => {
                if(err.type === 'unavailable-id') {
                    alert("Ya tienes esta clase abierta en otra pestaña.");
                } else {
                    console.error("PeerJS Error:", err);
                }
            });

        } catch (error) {
            console.error("Error medios:", error);
            alert("Error al acceder a la cámara.");
        }
    });

    function joinClassroom() {
        // Ocultar Lobby
        const lobby = document.getElementById('lobby-screen');
        lobby.style.opacity = "0";
        setTimeout(() => { lobby.style.display = 'none'; }, 500);
        
        updateButtonUI('Mic', isMicOn);
        updateButtonUI('Cam', isCamOn);

        // Si soy alumno, llamo automáticamente al profesor
        if (!isTeacher) {
            connectToTeacher();
        }
    }

    // --- CONEXIÓN AUTOMÁTICA AL PROFESOR (PARA ESTUDIANTES) ---
    function connectToTeacher() {
        if(!peer) return;
        if(isTeacher) return;

        console.log("Intentando conectar con el profesor:", teacherPeerId);
        document.getElementById('connectionStatus').innerText = "Intentando conectar...";

        const call = peer.call(teacherPeerId, localStream);
        
        // Si el profesor responde
        if(call) {
            const video = document.createElement('video');
            call.on('stream', teacherStream => {
                console.log("¡Conectado con el profesor!");
                addVideoStream(video, teacherStream, teacherPeerId, true);
                document.getElementById('connectionStatus').innerText = "Conectado";
            });
            
            call.on('close', () => {
                video.parentElement.remove();
                alert("El profesor ha finalizado la llamada.");
            });

            call.on('error', (err) => {
                console.error("Error al llamar al profesor:", err);
                document.getElementById('connectionStatus').innerText = "Profesor no disponible (Reintentar)";
            });

        } else {
            document.getElementById('connectionStatus').innerText = "Profesor no encontrado (Esperando...)";
        }
    }

    // Agregar video al Grid
    function addVideoStream(video, stream, userId, isTeacherStream = false) {
        // Evitar duplicados
        if(document.getElementById('video-' + userId)) return;

        video.srcObject = stream;
        video.addEventListener('loadedmetadata', () => video.play());
        video.style.width = '100%';
        video.style.height = '100%';
        video.style.objectFit = 'cover';
        video.style.transform = 'scaleX(-1)';

        const card = document.createElement('div');
        card.className = 'participant-card';
        card.id = 'video-' + userId;
        
        // Borde especial para el profesor
        if(isTeacherStream) {
            card.style.borderColor = '#0d6efd'; // Azul
            card.style.boxShadow = '0 0 15px rgba(13, 110, 253, 0.3)';
        }

        const label = document.createElement('div');
        label.className = 'participant-name';
        label.innerText = isTeacherStream ? `${teacherPeerId.split('-u')[1]} (Profesor)` : `Participante ${userId.split('-u')[1]}`;
        
        card.appendChild(video);
        card.appendChild(label);
        document.getElementById('videoGrid').appendChild(card);
    }

    // --- CONTROLES Y CHAT ---
    function showExitModal() { new bootstrap.Modal(document.getElementById('exitClassModal')).show(); }
    
    function confirmExit() {
        if(localStream) localStream.getTracks().forEach(track => track.stop());
        if(peer) peer.destroy();
        window.close();
        setTimeout(() => { if(!window.closed) window.location.href = "{{ route('courses') }}"; }, 500);
    }

    function toggleMic() {
        isMicOn = !isMicOn;
        if(localStream) localStream.getAudioTracks()[0].enabled = isMicOn;
        updateButtonUI('Mic', isMicOn);
    }

    function toggleCam() {
        isCamOn = !isCamOn;
        if(localStream) localStream.getVideoTracks()[0].enabled = isCamOn;
        const els = { preview: document.getElementById('localPreview'), main: document.getElementById('localVideo'), noCamPrev: document.getElementById('noCamPreview'), noCamMain: document.getElementById('localNoCam') };
        if (isCamOn) { els.preview.style.opacity = 1; els.main.style.opacity = 1; els.noCamPrev.style.display = 'none'; els.noCamMain.style.display = 'none'; } 
        else { els.preview.style.opacity = 0; els.main.style.opacity = 0; els.noCamPrev.style.display = 'flex'; els.noCamMain.style.display = 'flex'; }
        updateButtonUI('Cam', isCamOn);
    }

    function updateButtonUI(type, isOn) {
        const iconClass = type === 'Mic' ? (isOn ? 'fa-microphone' : 'fa-microphone-slash') : (isOn ? 'fa-video' : 'fa-video-slash');
        const btnLobby = document.getElementById(`btnToggle${type}Preview`);
        if(btnLobby) { btnLobby.innerHTML = `<i class="fas ${iconClass}"></i>`; btnLobby.className = `control-btn-circle ${isOn ? 'btn-on' : 'btn-off'}`; }
        const btnMain = document.getElementById(`btnToggle${type}`);
        if(btnMain) { btnMain.innerHTML = `<i class="fas ${iconClass}"></i>`; btnMain.classList.toggle('active', !isOn); btnMain.style.backgroundColor = isOn ? '#3c4043' : '#ea4335'; btnMain.style.borderColor = isOn ? '#5f6368' : '#ea4335'; }
        if(type === 'Mic') { document.getElementById('localMicIcon').style.display = isOn ? 'none' : 'block'; document.getElementById('localMicOnIcon').style.display = isOn ? 'block' : 'none'; }
    }

    async function shareScreen() {
        try {
            const screenStream = await navigator.mediaDevices.getDisplayMedia({ cursor: true });
            const videoTrack = screenStream.getVideoTracks()[0];
            const localVid = document.getElementById('localVideo');
            const originalStream = localVid.srcObject;
            localVid.srcObject = screenStream;
            
            // Aquí deberías reemplazar la pista para todos los peers conectados
            // (La implementación es más compleja sin un framework de signaling, pero la base está)
            
            videoTrack.onended = function () {
                localVid.srcObject = originalStream;
            };
        } catch (err) { console.log("Compartir cancelado"); }
    }

    function toggleSidebar() { const sb = document.getElementById('sidebar'); sb.style.display = (sb.style.display === 'flex') ? 'none' : 'flex'; }
    function sendMessage() {
        const input = document.getElementById('chatInput');
        const text = input.value.trim();
        if (text) {
            const content = document.getElementById('sidebarContent');
            const now = new Date();
            const time = now.getHours() + ":" + (now.getMinutes()<10?'0':'') + now.getMinutes();
            content.innerHTML += `<div style="text-align: right; margin-bottom: 10px;"><div class="chat-meta">Tú • ${time}</div><div class="chat-bubble" style="background: #d2e3fc; color: #174ea6;">${text}</div></div>`;
            input.value = '';
            content.scrollTop = content.scrollHeight;
        }
    }
    document.getElementById('chatInput').addEventListener('keypress', function (e) { if (e.key === 'Enter') sendMessage(); });
</script>
@endsection