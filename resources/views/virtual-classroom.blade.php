<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aula Virtual - estudIA</title>
    <!-- Estilos (Bootstrap + Iconos) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #202124; color: white; height: 100vh; overflow: hidden; display: flex; flex-direction: column; margin: 0; }
        
        /* --- 1. LOBBY (SALA DE ESPERA) --- */
        #lobby-screen { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #202124; z-index: 2000; display: flex; align-items: center; justify-content: center; flex-direction: column; }
        .preview-container { position: relative; width: 90%; max-width: 700px; aspect-ratio: 16/9; background: #3c4043; border-radius: 16px; overflow: hidden; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 30px rgba(0,0,0,0.5); }
        .preview-video { width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1); }
        
        .lobby-controls { position: absolute; bottom: 20px; display: flex; gap: 20px; }
        .control-btn-circle { width: 56px; height: 56px; border-radius: 50%; border: none; font-size: 24px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
        .btn-on { background: rgba(255,255,255,0.9); color: #202124; }
        .btn-on:hover { background: #fff; transform: scale(1.05); }
        .btn-off { background: #ea4335; color: white; border: none; }
        .btn-off:hover { background: #d93025; transform: scale(1.05); }

        /* --- 2. SALA PRINCIPAL --- */
        .main-container { flex: 1; display: flex; overflow: hidden; position: relative; }
        .video-grid { flex: 1; display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 15px; padding: 20px; align-content: center; justify-content: center; }
        
        .participant-card { background: #3c4043; border-radius: 12px; overflow: hidden; position: relative; aspect-ratio: 16/9; display: flex; align-items: center; justify-content: center; border: 2px solid transparent; transition: border-color 0.2s; }
        .participant-card.speaking { border-color: #8ab4f8; }
        .participant-video { width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1); }
        .participant-name { position: absolute; bottom: 15px; left: 15px; background: rgba(0,0,0,0.6); padding: 4px 12px; border-radius: 20px; font-size: 13px; font-weight: 600; color: white; backdrop-filter: blur(4px); }
        .participant-mic { position: absolute; top: 15px; right: 15px; background: rgba(0,0,0,0.6); width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 14px; backdrop-filter: blur(4px); }
        
        /* Avatar Placeholder */
        .no-camera-avatar { width: 100px; height: 100px; border-radius: 50%; background: #5f6368; display: flex; align-items: center; justify-content: center; font-size: 40px; color: white; font-weight: bold; text-transform: uppercase; }

        /* --- 3. BARRA INFERIOR --- */
        .bottom-bar { height: 80px; background: #202124; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; border-top: 1px solid #3c4043; }
        .bar-left { width: 250px; display: flex; flex-direction: column; }
        .class-time { font-size: 18px; font-weight: bold; }
        .class-id { font-size: 12px; color: #9aa0a6; }
        
        .bar-center { display: flex; gap: 12px; }
        .action-btn { width: 48px; height: 48px; border-radius: 50%; border: none; background: #3c4043; color: white; font-size: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s; }
        .action-btn:hover { background: #45474a; transform: translateY(-2px); }
        .action-btn.active { background: #8ab4f8; color: #202124; }
        .btn-call-end { background: #ea4335; width: 70px; border-radius: 35px; }
        .btn-call-end:hover { background: #d93025; }

        .bar-right { width: 250px; display: flex; justify-content: flex-end; gap: 10px; }

        /* --- 4. SIDEBAR (CHAT) --- */
        .sidebar { width: 360px; background: white; color: #202124; display: none; flex-direction: column; border-left: 1px solid #ddd; height: 100%; }
        .sidebar.open { display: flex; }
        .sidebar-header { padding: 20px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
        .sidebar-content { flex: 1; overflow-y: auto; padding: 20px; background: #f8f9fa; }
        .sidebar-footer { padding: 20px; border-top: 1px solid #eee; background: white; }

        /* Chat Styles */
        .chat-message { margin-bottom: 16px; display: flex; flex-direction: column; }
        .chat-meta { font-size: 11px; color: #5f6368; margin-bottom: 4px; padding-left: 4px; }
        .chat-bubble { background: white; padding: 10px 14px; border-radius: 12px; font-size: 14px; display: inline-block; box-shadow: 0 1px 2px rgba(0,0,0,0.1); max-width: 85%; border: 1px solid #e0e0e0; }
        .chat-message.me { align-items: flex-end; }
        .chat-message.me .chat-bubble { background: #d2e3fc; border-color: #d2e3fc; color: #174ea6; }

        /* Lista de Participantes */
        .participant-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #f1f1f1; }
        .p-avatar { width: 36px; height: 36px; border-radius: 50%; background: #3c4043; color: white; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: bold; overflow: hidden; }
        .p-avatar img { width: 100%; height: 100%; object-fit: cover; }
    </style>
</head>
<body>

    <!-- ================== PANTALLA DE ESPERA (LOBBY) ================== -->
    <div id="lobby-screen">
        <h2 class="mb-4 fw-light text-white">¿Listo para unirte a la clase?</h2>
        
        <div class="preview-container">
            <video id="localPreview" autoplay muted playsinline class="preview-video"></video>
            
            <!-- Avatar si la cámara está apagada -->
            <div id="noCamPreview" class="no-camera-avatar" style="display:none; position:absolute;">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>

            <!-- Controles del Lobby -->
            <div class="lobby-controls">
                <button class="control-btn-circle btn-on" id="btnToggleMicPreview" onclick="toggleMic('preview')" title="Micrófono">
                    <i class="fas fa-microphone"></i>
                </button>
                <button class="control-btn-circle btn-on" id="btnToggleCamPreview" onclick="toggleCam('preview')" title="Cámara">
                    <i class="fas fa-video"></i>
                </button>
            </div>
        </div>

        <div class="mt-5 d-flex gap-4 align-items-center bg-dark p-3 rounded-4 px-5 border border-secondary">
            <div class="text-start border-end pe-4 border-secondary">
                <h5 class="m-0 fw-bold">{{ Auth::user()->name }}</h5>
                <small class="text-success">Dispositivo listo</small>
            </div>
            <button class="btn btn-primary rounded-pill px-5 py-2 fw-bold fs-5" onclick="joinClassroom()">
                Unirse ahora
            </button>
        </div>
    </div>

    <!-- ================== SALA PRINCIPAL ================== -->
    <div class="main-container">
        
        <!-- Grid de Videos -->
        <div class="video-grid" id="videoGrid">
            <!-- MI VIDEO -->
            <div class="participant-card">
                <video id="localVideo" autoplay muted playsinline class="participant-video"></video>
                <div id="localNoCam" class="no-camera-avatar" style="display:none; position:absolute;">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="participant-name">{{ Auth::user()->name }} (Tú)</div>
                <div class="participant-mic"><i class="fas fa-microphone-slash text-danger" id="localMicIcon" style="display:none;"></i></div>
            </div>

            <!-- PARTICIPANTE SIMULADO 1 -->
            <div class="participant-card">
                <img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=600" class="participant-video" style="object-fit: cover;">
                <div class="participant-name">Juan Pérez</div>
                <div class="participant-mic"><i class="fas fa-microphone text-white"></i></div>
            </div>

            <!-- PARTICIPANTE SIMULADO 2 -->
            <div class="participant-card speaking">
                <div class="no-camera-avatar" style="background: #e91e63;">M</div>
                <div class="participant-name">María González</div>
                <div class="participant-mic"><i class="fas fa-microphone text-white"></i></div>
            </div>
        </div>

        <!-- PANEL LATERAL (CHAT / PARTICIPANTES) -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <h5 class="m-0 fw-bold" id="sidebarTitle">Chat de la clase</h5>
                <button class="btn-close" onclick="toggleSidebar()"></button>
            </div>
            
            <div class="sidebar-content" id="sidebarContent">
                <!-- Aquí se inyecta el chat o la lista -->
            </div>
            
            <div class="sidebar-footer" id="sidebarFooter" style="display:none;">
                <div class="input-group">
                    <input type="text" id="chatInput" class="form-control bg-white border-end-0" placeholder="Escribe un mensaje..." style="border-radius: 20px 0 0 20px;">
                    <button class="btn btn-primary border-start-0" onclick="sendMessage()" style="border-radius: 0 20px 20px 0;">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ================== BARRA DE CONTROLES ================== -->
    <div class="bottom-bar">
        <div class="bar-left text-white">
            <div class="class-time" id="clock">00:00</div>
            <div class="class-id">ID: abc-def-ghi</div>
        </div>
        
        <div class="bar-center">
            <button class="action-btn" id="btnToggleMic" onclick="toggleMic('main')" title="Micrófono (Ctrl + D)">
                <i class="fas fa-microphone"></i>
            </button>
            <button class="action-btn" id="btnToggleCam" onclick="toggleCam('main')" title="Cámara (Ctrl + E)">
                <i class="fas fa-video"></i>
            </button>
            <button class="action-btn" onclick="shareScreen()" title="Compartir Pantalla">
                <i class="fas fa-desktop"></i>
            </button>
            <button class="action-btn btn-call-end" onclick="window.close()" title="Salir de la clase">
                <i class="fas fa-phone-slash"></i>
            </button>
        </div>

        <div class="bar-right">
            <button class="action-btn" onclick="showSidebar('participants')" title="Participantes">
                <i class="fas fa-users"></i>
            </button>
            <button class="action-btn" onclick="showSidebar('chat')" title="Chat">
                <i class="fas fa-comment-alt"></i>
            </button>
        </div>
    </div>

    <!-- ================== LÓGICA JAVASCRIPT ================== -->
    <script>
        let localStream;
        let isMicOn = true;
        let isCamOn = true;

        // 1. RELOJ
        setInterval(() => {
            const now = new Date();
            document.getElementById('clock').innerText = now.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }, 1000);

        // 2. INICIALIZAR CÁMARA
        document.addEventListener('DOMContentLoaded', async () => {
            try {
                localStream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });
                // Asignar stream a los elementos de video
                document.getElementById('localPreview').srcObject = localStream;
                document.getElementById('localVideo').srcObject = localStream;
            } catch (error) {
                console.error("Error de cámara:", error);
                alert("Por favor permite el acceso a tu cámara y micrófono para entrar a la clase.");
            }
        });

        // 3. ENTRAR A LA CLASE
        function joinClassroom() {
            const lobby = document.getElementById('lobby-screen');
            lobby.style.transition = "opacity 0.5s";
            lobby.style.opacity = "0";
            setTimeout(() => { lobby.style.display = 'none'; }, 500);
            
            // Sincronizar estado inicial
            updateMainControls();
        }

        // 4. MICRÓFONO
        function toggleMic(context) {
            isMicOn = !isMicOn;
            if(localStream) localStream.getAudioTracks()[0].enabled = isMicOn;
            updateButtonUI('Mic', isMicOn);
        }

        // 5. CÁMARA
        function toggleCam(context) {
            isCamOn = !isCamOn;
            if(localStream) localStream.getVideoTracks()[0].enabled = isCamOn;

            // Mostrar u ocultar video/avatar
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
            const icon = type === 'Mic' ? (isOn ? 'fa-microphone' : 'fa-microphone-slash') : (isOn ? 'fa-video' : 'fa-video-slash');
            
            // Botones Lobby
            const btnLobby = document.getElementById(`btnToggle${type}Preview`);
            if(btnLobby) {
                btnLobby.innerHTML = `<i class="fas ${icon}"></i>`;
                btnLobby.classList.toggle('btn-on', isOn);
                btnLobby.classList.toggle('btn-off', !isOn);
            }

            // Botones Sala Principal
            const btnMain = document.getElementById(`btnToggle${type}`);
            if(btnMain) {
                btnMain.innerHTML = `<i class="fas ${icon}"></i>`;
                btnMain.classList.toggle('btn-off', !isOn);
                btnMain.style.background = !isOn ? '#ea4335' : '#3c4043';
            }

            // Icono de mute en la tarjeta pequeña
            if(type === 'Mic') {
                document.getElementById('localMicIcon').style.display = isOn ? 'none' : 'block';
            }
        }

        function updateMainControls() {
            updateButtonUI('Mic', isMicOn);
            updateButtonUI('Cam', isCamOn);
        }

        // 6. COMPARTIR PANTALLA
        async function shareScreen() {
            try {
                const screenStream = await navigator.mediaDevices.getDisplayMedia({ cursor: true });
                const screenTrack = screenStream.getVideoTracks()[0];
                const videoSender = document.getElementById('localVideo');
                
                videoSender.srcObject = screenStream; // Cambiar video por pantalla

                // Al dejar de compartir, volver a la cámara
                screenTrack.onended = function () {
                    videoSender.srcObject = localStream;
                };
            } catch (err) {
                console.log("Compartir pantalla cancelado");
            }
        }

        // 7. SIDEBAR Y CHAT
        function showSidebar(type) {
            const sidebar = document.getElementById('sidebar');
            const title = document.getElementById('sidebarTitle');
            const content = document.getElementById('sidebarContent');
            const footer = document.getElementById('sidebarFooter');

            sidebar.classList.add('open');

            if (type === 'chat') {
                title.innerText = 'Chat de la clase';
                footer.style.display = 'block';
                // Mensajes de prueba si está vacío
                if(content.innerHTML.trim() === '') {
                    content.innerHTML = `
                        <div class="chat-message">
                            <div class="chat-meta">Juan Pérez • 10:05</div>
                            <div class="chat-bubble">¡Hola a todos!</div>
                        </div>
                    `;
                }
            } else {
                title.innerText = 'Participantes (3)';
                footer.style.display = 'none';
                content.innerHTML = `
                    <div class="participant-item">
                        <div class="p-avatar">{{ substr(Auth::user()->name, 0, 1) }}</div>
                        <div><div class="fw-bold">{{ Auth::user()->name }} (Tú)</div><small class="text-muted">Anfitrión</small></div>
                    </div>
                    <div class="participant-item">
                        <div class="p-avatar"><img src="https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?w=100"></div>
                        <div><div class="fw-bold">Juan Pérez</div><small class="text-muted">Estudiante</small></div>
                        <i class="fas fa-microphone text-muted ms-auto"></i>
                    </div>
                    <div class="participant-item">
                        <div class="p-avatar" style="background:#e91e63">M</div>
                        <div><div class="fw-bold">María González</div><small class="text-muted">Estudiante</small></div>
                        <i class="fas fa-microphone text-muted ms-auto"></i>
                    </div>
                `;
            }
        }

        function toggleSidebar() {
            document.getElementById('sidebar').classList.remove('open');
        }

        function sendMessage() {
            const input = document.getElementById('chatInput');
            const text = input.value.trim();
            if (text) {
                const content = document.getElementById('sidebarContent');
                const now = new Date();
                const time = now.getHours() + ":" + (now.getMinutes()<10?'0':'') + now.getMinutes();
                content.innerHTML += `
                    <div class="chat-message me">
                        <div class="chat-meta">Tú • ${time}</div>
                        <div class="chat-bubble">${text}</div>
                    </div>
                `;
                input.value = '';
                content.scrollTop = content.scrollHeight;
            }
        }
        
        document.getElementById('chatInput').addEventListener('keypress', function (e) {
            if (e.key === 'Enter') sendMessage();
        });
    </script>
</body>
</html>