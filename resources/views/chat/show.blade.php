@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- TARJETA PRINCIPAL DEL CHAT -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="height: 85vh;">
                
                <!-- 1. HEADER (ENCABEZADO) -->
                <div class="card-header bg-white p-3 border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <!-- Botón Volver -->
                        <a href="{{ route('home') }}" class="btn btn-light btn-sm rounded-circle me-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;" title="Volver al inicio">
                            <i class="fas fa-arrow-left text-muted"></i>
                        </a>

                        <!-- Foto y Nombre -->
                        <div class="position-relative">
                            <img src="{{ $conversation->image }}" class="rounded-circle object-fit-cover" width="45" height="45" alt="Avatar">
                            @if($conversation->is_group)
                                <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-primary border border-white p-1" style="font-size: 0.6rem;">G</span>
                            @endif
                        </div>
                        <div class="ms-3">
                            <h5 class="mb-0 fw-bold text-dark">{{ $conversation->title }}</h5>
                            @if($conversation->is_group)
                                <small class="text-muted">{{ $conversation->participants->count() }} participantes</small>
                            @else
                                <small class="text-success fw-bold" style="font-size: 0.8rem;"><i class="fas fa-circle fa-xs me-1"></i>En línea</small>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Botón Opciones -->
                    <button class="btn btn-light rounded-circle text-muted border-0" type="button">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>

                <!-- 2. ÁREA DE MENSAJES (CUERPO) -->
                <div class="card-body bg-light overflow-auto p-4" id="messagesContainer" style="background-color: #f8f9fa;">
                    @foreach($conversation->messages as $message)
                        @php
                            $isMe = $message->user_id == auth()->id();
                        @endphp
                        
                        <!-- Fila del mensaje -->
                        <div class="d-flex mb-3 {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
                            
                            <!-- Avatar del otro (si es grupo y no soy yo) -->
                            @if(!$isMe && $conversation->is_group)
                                <img src="{{ $message->user->profile_picture }}" class="rounded-circle me-2 align-self-end mb-1" width="30" height="30" alt="">
                            @endif
                            
                            <!-- Burbuja del mensaje -->
                            <div class="d-flex flex-column {{ $isMe ? 'align-items-end' : 'align-items-start' }}" style="max-width: 75%;">
                                
                                <!-- Nombre en grupo -->
                                @if(!$isMe && $conversation->is_group)
                                    <small class="text-muted mb-1 ms-1" style="font-size: 0.75rem;">{{ $message->user->name }}</small>
                                @endif

                                <div class="p-3 shadow-sm position-relative text-break 
                                            {{ $isMe ? 'bg-primary text-white rounded-4 rounded-bottom-end-0' : 'bg-white text-dark rounded-4 rounded-bottom-start-0' }}">
                                    
                                    <!-- Si hay archivo adjunto -->
                                    @if($message->type == 'file')
                                        <div class="mb-2 bg-white bg-opacity-25 p-2 rounded">
                                            <a href="{{ asset('storage/' . $message->attachment) }}" target="_blank" class="text-decoration-none {{ $isMe ? 'text-white' : 'text-primary' }} d-flex align-items-center">
                                                <i class="fas fa-file-download me-2"></i>
                                                <span class="text-truncate" style="max-width: 150px;">Ver adjunto</span>
                                            </a>
                                        </div>
                                    @endif

                                    <!-- Texto del mensaje -->
                                    <p class="mb-0">{{ $message->body }}</p>
                                </div>
                                
                                <!-- Hora -->
                                <small class="text-muted mt-1 mx-1 opacity-75" style="font-size: 0.7rem;">
                                    {{ $message->created_at->format('H:i') }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- 3. FOOTER (INPUT PARA ESCRIBIR) -->
                <div class="card-footer bg-white p-3 border-top">
                    <form action="{{ route('chat.send', $conversation->id) }}" method="POST" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                        @csrf
                        
                        <!-- Botón Adjuntar (Clip) -->
                        <div class="position-relative">
                            <input type="file" name="attachment" id="fileInput" class="d-none" onchange="this.nextElementSibling.classList.add('text-primary')">
                            <label for="fileInput" class="btn btn-light text-secondary rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" title="Adjuntar archivo">
                                <i class="fas fa-paperclip"></i>
                            </label>
                        </div>

                        <!-- Input Texto -->
                        <input type="text" name="body" class="form-control rounded-pill bg-light border-0 py-2 px-4" placeholder="Escribe un mensaje..." autocomplete="off">

                        <!-- Botón Enviar (Avión) -->
                        <button type="submit" class="btn btn-primary rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    // Hacer scroll automático al final del chat al cargar
    document.addEventListener('DOMContentLoaded', function() {
        var container = document.getElementById('messagesContainer');
        if(container) {
            container.scrollTop = container.scrollHeight;
        }
    });
</script>

<style>
    /* Estilo barra de desplazamiento más bonita */
    #messagesContainer::-webkit-scrollbar {
        width: 8px;
    }
    #messagesContainer::-webkit-scrollbar-track {
        background: #f1f1f1; 
    }
    #messagesContainer::-webkit-scrollbar-thumb {
        background: #ced4da; 
        border-radius: 10px;
    }
    #messagesContainer::-webkit-scrollbar-thumb:hover {
        background: #adb5bd; 
    }
</style>
@endsection