@extends('layouts.app')

@section('content')
<!-- Estilos específicos para el Aula -->
<style>
    /* Aseguramos que tape toda la interfaz de Laravel */
    #classroom-wrapper {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: #202124;
        color: white;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    /* --- 1. LOBBY (SALA DE ESPERA) --- */
    #lobby-screen {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: #202124;
        z-index: 2001;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        transition: opacity 0.5s ease;
    }

    .preview-container {
        position: relative;
        width: 90%;
        max-width: 700px;
        aspect-ratio: 16/9;
        background: #3c4043;
        border-radius: 16px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }

    video {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Mantiene la proporción llenando el espacio */
        transform: scaleX(-1); /* Efecto espejo natural */
    }

    .lobby-controls {
        position: absolute;
        bottom: 20px;
        display: flex;
        gap: 20px;
        z-index: 10;
    }

    .control-btn-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: none;
        font-size: 20px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }

    .btn-on { background: rgba(255,255,255,0.9); color: #202124; }
    .btn-on:hover { background: #fff; transform: scale(1.05); }
    
    .btn-off { background: #ea4335; color: white; border: none; }
    .btn-off:hover { background: #d93025; transform: scale(1.05); }

    /* --- 2. SALA PRINCIPAL --- */
    .main-container {
        flex: 1;
        display: flex;
        overflow: hidden;
        position: relative;
    }

    .video-grid {
        flex: 1;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
        gap: 20px;
        padding: 20px;
        align-content: center;
        justify-content: center;
        background: #202124;
    }
    
    .participant-card {
        background: #3c4043;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        aspect-ratio: 16/9;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid transparent;
        transition: border-color 0.2s;
        max-height: 100%;
        width: 100%;
        max-width: 800px; /* Limite para que no se estire demasiado */
    }

    .participant-name {
        position: absolute;
        bottom: 15px;
        left: 15px;
        background: rgba(0,0,0,0.6);
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 14px;
        font-weight: 500;
        color: white;
        backdrop-filter: blur(4px);
    }

    .participant-mic-status {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(0,0,0,0.6);
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        backdrop-filter: blur(4px);
    }
    
    /* Avatar Placeholder */
    .no-camera-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: #5f6368;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        color: white;
        font-weight: bold;
        text-transform: uppercase;
        border: 2px solid #fff;
    }

    /* --- 3. BARRA INFERIOR --- */
    .bottom-bar {
        height: 80px;
        background: #202124;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 24px;
        border-top: 1px solid #3c4043;
    }

    .action-btn {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        border: 1px solid #5f6368;
        background: #3c4043;
        color: white;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
        margin: 0 5px;
    }
    .action-btn:hover { background: #45474a; }
    .action-btn.active { background: #8ab4f8; color: #202124; border-color: #8ab4f8; }
    
    .btn-call-end {
        background: #ea4335;
        width: 60px;
        border-radius: 30px;
        border: none;
    }
    .btn-call-end:hover { background: #d93025; }

    /* --- 4. SIDEBAR (CHAT) --- */
    .sidebar {
        width: 360px;
        background: white;
        color: #202124;
        display: none;
        flex-direction: column;
        border-left: 1px solid #ddd;
        height: 100%;
    }
    .sidebar.open { display: flex; }
    .sidebar-header { padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
    .sidebar-content { flex: 1; overflow-y: auto; padding: 15px; background: #f8f9fa; }
    .sidebar-footer { padding: 15px; border-top: 1px solid #eee; background: white; }

    .chat-bubble {
        background: #e8f0fe;
        color: #1967d2;
        padding: 10px 15px;
        border-radius: 15px;
        margin-bottom: 10px;
        display: inline-block;
        max-width: 85%;
        font-size: 14px;
    }
    .chat-meta { font-size: 10px; color: #5f6368; margin-bottom: 2px; margin-left: 5px; }

    .profile-img-preview { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
</style>

<div id="classroom-wrapper">
    
    <!-- ================== PANTALLA DE ESPERA (LOBBY) ================== -->
    <div id="lobby-screen">
        <h2 class="mb-4 fw-light text-white">¿Listo para unirte a la clase?</h2>
        
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
                <button class="control-btn-circle btn-on" id="btnToggleMicPreview" onclick="toggleMic()" title="Micrófono">
                    <i class="fas fa-microphone"></i>
                </button>
                <button class="control-btn-circle btn-on" id="btnToggleCamPreview" onclick="toggleCam()" title="Cámara">
                    <i class="fas fa-video"></i>
                </button>
            </div>
        </div>

        <div class="mt-5 d-flex gap-4 align-items-center bg-dark p-3 rounded-4 px-5 border border-secondary border-opacity-25">
            <div class="text-start border-end pe-4 border-secondary">
                <h5 class="m-0 fw-bold">{{ auth()->user()->name }}</h5>
                <small class="text-success" id="statusText">Cámara HD lista</small>
            </div>
            <button class="btn btn-primary rounded-pill px-5 py-2 fw-bold fs-5" onclick="joinClassroom()">
                Unirse ahora
            </button>
        </div>
    </div>

    <!-- ================== SALA PRINCIPAL ================== -->
    <div class="main-container">
        <div class="video-grid" id="videoGrid">
            <!-- MI VIDEO -->
            <div class="participant-card">
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

        <!-- SIDEBAR CHAT -->
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

    <!-- ================== BARRA DE CONTROLES ================== -->
    <div class="bottom-bar">
        <div class="d-none d-md-block text-white" style="width: 200px;">
            <div class="fw-bold" id="clock">00:00</div>
            <small class="text-white-50">Sala #{{ $id }}</small>
        </div>
        <div class="d-flex justify-content-center flex-grow-1">
            <button class="action-btn" id="btnToggleMic" onclick="toggleMic()"><i class="fas fa-microphone"></i></button>
            <button class="action-btn" id="btnToggleCam" onclick="toggleCam()"><i class="fas fa-video"></i></button>
            <button class="action-btn" onclick="shareScreen()"><i class="fas fa-desktop"></i></button>
            <button class="action-btn btn-call-end" onclick="showExitModal()"><i class="fas fa-phone-slash"></i></button>
        </div>
        <div class="d-flex justify-content-end" style="width: 200px;">
            <button class="action-btn" onclick="toggleSidebar()"><i class="fas fa-comment-alt"></i></button>
            <button class="action-btn"><i class="fas fa-users"></i><span class="badge bg-secondary rounded-pill position-absolute top-0 start-100 translate-middle" style="font-size: 10px;">1</span></button>
        </div>
    </div>
</div>

<!-- ================== MODAL DE SALIDA PERSONALIZADO ================== -->
<div class="modal fade" id="exitClassModal" tabindex="-1" aria-hidden="true" style="z-index: 10000;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border border-secondary">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-door-open fa-2x"></i>
                    </div>
                </div>
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

<!-- ================== SCRIPTS ================== -->
<script>
    let localStream;
    let isMicOn = true;
    let isCamOn = true;

    // Reloj
    setInterval(() => {
        const now = new Date();
        document.getElementById('clock').innerText = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    }, 1000);

    // INICIALIZAR CON CALIDAD HD
    document.addEventListener('DOMContentLoaded', async () => {
        try {
            // SOLICITUD HD: width 1280, height 720
            localStream = await navigator.mediaDevices.getUserMedia({ 
                video: { width: { ideal: 1280 }, height: { ideal: 720 } }, 
                audio: true 
            });
            
            document.getElementById('localPreview').srcObject = localStream;
            document.getElementById('localVideo').srcObject = localStream;
            
        } catch (error) {
            console.error("Error:", error);
            document.getElementById('statusText').innerText = "Error de dispositivos";
            document.getElementById('statusText').classList.replace('text-success', 'text-danger');
            alert("No pudimos acceder a la cámara en alta definición o al micrófono.");
        }
    });

    function joinClassroom() {
        const lobby = document.getElementById('lobby-screen');
        lobby.style.opacity = "0";
        setTimeout(() => { lobby.style.display = 'none'; }, 500);
        updateButtonUI('Mic', isMicOn);
        updateButtonUI('Cam', isCamOn);
    }

    // MODAL DE SALIDA
    function showExitModal() {
        const modal = new bootstrap.Modal(document.getElementById('exitClassModal'));
        modal.show();
    }

    function confirmExit() {
        // Apagar cámara/luz de hardware
        if(localStream) {
            localStream.getTracks().forEach(track => track.stop());
        }
        
        // CERRAR VENTANA ACTUAL
        window.close();
        
        // Fallback: Si el navegador bloquea el cierre (p.ej. si no se abrió con JS), redirigir
        setTimeout(() => {
            if(!window.closed) {
                window.location.href = "{{ route('courses') }}";
            }
        }, 500);
    }

    // TOGGLE MIC
    function toggleMic() {
        isMicOn = !isMicOn;
        if(localStream) localStream.getAudioTracks()[0].enabled = isMicOn;
        updateButtonUI('Mic', isMicOn);
    }

    // TOGGLE CAM
    function toggleCam() {
        isCamOn = !isCamOn;
        if(localStream) localStream.getVideoTracks()[0].enabled = isCamOn;
        
        const els = {
            preview: document.getElementById('localPreview'),
            main: document.getElementById('localVideo'),
            noCamPrev: document.getElementById('noCamPreview'),
            noCamMain: document.getElementById('localNoCam')
        };

        if (isCamOn) {
            els.preview.style.opacity = 1;
            els.main.style.opacity = 1;
            els.noCamPrev.style.display = 'none';
            els.noCamMain.style.display = 'none';
        } else {
            els.preview.style.opacity = 0;
            els.main.style.opacity = 0;
            els.noCamPrev.style.display = 'flex';
            els.noCamMain.style.display = 'flex';
        }
        updateButtonUI('Cam', isCamOn);
    }

    function updateButtonUI(type, isOn) {
        const iconClass = type === 'Mic' ? (isOn ? 'fa-microphone' : 'fa-microphone-slash') : (isOn ? 'fa-video' : 'fa-video-slash');
        
        const btnLobby = document.getElementById(`btnToggle${type}Preview`);
        if(btnLobby) {
            btnLobby.innerHTML = `<i class="fas ${iconClass}"></i>`;
            btnLobby.className = `control-btn-circle ${isOn ? 'btn-on' : 'btn-off'}`;
        }

        const btnMain = document.getElementById(`btnToggle${type}`);
        if(btnMain) {
            btnMain.innerHTML = `<i class="fas ${iconClass}"></i>`;
            btnMain.classList.toggle('active', !isOn);
            btnMain.style.backgroundColor = isOn ? '#3c4043' : '#ea4335';
            btnMain.style.borderColor = isOn ? '#5f6368' : '#ea4335';
        }

        if(type === 'Mic') {
            document.getElementById('localMicIcon').style.display = isOn ? 'none' : 'block';
            document.getElementById('localMicOnIcon').style.display = isOn ? 'block' : 'none';
        }
    }

    async function shareScreen() {
        try {
            const screenStream = await navigator.mediaDevices.getDisplayMedia({ cursor: true });
            const videoSender = document.getElementById('localVideo');
            videoSender.srcObject = screenStream;
            screenStream.getVideoTracks()[0].onended = function () {
                videoSender.srcObject = localStream;
            };
        } catch (err) {}
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.style.display = (sidebar.style.display === 'flex') ? 'none' : 'flex';
    }

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
    
    document.getElementById('chatInput').addEventListener('keypress', function (e) {
        if (e.key === 'Enter') sendMessage();
    });
</script>
@endsection