@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 h-[calc(100vh-150px)]">
        
        <!-- Sidebar: Lista de Chats y Botones -->
        <div class="md:col-span-1 bg-white dark:bg-gray-800 rounded-lg shadow-md flex flex-col overflow-hidden">
            <div class="p-4 border-b dark:border-gray-700">
                <h2 class="text-xl font-bold mb-4 text-gray-800 dark:text-white">Mensajes</h2>
                
                <!-- Botones de Acción -->
                <div class="flex space-x-2">
                    <!-- Botón Nuevo Chat Privado -->
                    <button onclick="document.getElementById('newChatModal').classList.remove('hidden')" 
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-sm py-2 px-3 rounded-md transition flex justify-center items-center" 
                            title="Nuevo Chat">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        Chat
                    </button>
                    
                    <!-- Botón Nuevo Grupo -->
                    <button onclick="document.getElementById('newGroupModal').classList.remove('hidden')" 
                            class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm py-2 px-3 rounded-md transition flex justify-center items-center" 
                            title="Nuevo Grupo">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Grupo
                    </button>
                </div>
            </div>

            <!-- Lista de Conversaciones -->
            <div class="overflow-y-auto flex-1">
                @if($conversations->isEmpty())
                    <div class="p-4 text-center text-gray-500">
                        <p>No tienes mensajes aún.</p>
                        <p class="text-sm mt-2">¡Inicia una conversación con tus amigos!</p>
                    </div>
                @else
                    <ul>
                        @foreach($conversations as $conversation)
                            <li class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                <a href="{{ route('chat.show', $conversation->id) }}" class="flex items-center p-4">
                                    <div class="relative">
                                        <img src="{{ $conversation->image }}" alt="Avatar" class="w-10 h-10 rounded-full object-cover">
                                        @if($conversation->is_group)
                                            <span class="absolute bottom-0 right-0 bg-indigo-500 text-white text-xs rounded-full px-1 border border-white">G</span>
                                        @endif
                                    </div>
                                    <div class="ml-3 flex-1 overflow-hidden">
                                        <div class="flex justify-between items-baseline">
                                            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $conversation->title }}</h3>
                                            <span class="text-xs text-gray-500">{{ $conversation->updated_at->shortAbsoluteDiffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-gray-500 truncate">
                                            @if($conversation->lastMessage)
                                                {{ $conversation->lastMessage->user_id == auth()->id() ? 'Tú: ' : '' }}
                                                {{ $conversation->lastMessage->body ?? 'Adjunto enviado' }}
                                            @else
                                                <span class="italic text-xs">Nueva conversación</span>
                                            @endif
                                        </p>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>

        <!-- Área Principal (Placeholder cuando no hay chat seleccionado) -->
        <div class="md:col-span-3 bg-white dark:bg-gray-800 rounded-lg shadow-md flex flex-col justify-center items-center text-gray-500 p-8">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            <h3 class="text-xl font-medium">Selecciona una conversación</h3>
            <p class="mt-2">O inicia un nuevo chat con tus amigos.</p>
        </div>
    </div>
</div>

<!-- Modal Nuevo Chat Privado -->
<div id="newChatModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex justify-center items-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4 dark:text-white">Nuevo Chat</h3>
        <form action="{{ route('chat.private') }}" method="POST">
            @csrf
            <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Selecciona un amigo:</label>
            <select name="recipient_id" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white mb-4" required>
                <option value="">-- Elige un amigo --</option>
                @foreach(auth()->user()->all_friends as $friend)
                    <option value="{{ $friend->id }}">{{ $friend->name }}</option>
                @endforeach
            </select>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('newChatModal').classList.add('hidden')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded">Cancelar</button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">Iniciar Chat</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Nuevo Grupo -->
<div id="newGroupModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex justify-center items-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 w-full max-w-md">
        <h3 class="text-lg font-bold mb-4 dark:text-white">Crear Nuevo Grupo</h3>
        <form action="{{ route('chat.group') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Nombre del Grupo:</label>
                <input type="text" name="name" class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required placeholder="Ej: Proyecto Final">
            </div>
            
            <div class="mb-4">
                <label class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Selecciona participantes:</label>
                <div class="max-h-40 overflow-y-auto border rounded p-2 dark:border-gray-600">
                    @foreach(auth()->user()->all_friends as $friend)
                        <div class="flex items-center mb-2">
                            <input type="checkbox" name="users[]" value="{{ $friend->id }}" id="friend_{{ $friend->id }}" class="mr-2 rounded text-indigo-600">
                            <label for="friend_{{ $friend->id }}" class="text-sm text-gray-700 dark:text-gray-300">{{ $friend->name }}</label>
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 mt-1">Selecciona al menos un amigo.</p>
            </div>

            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('newGroupModal').classList.add('hidden')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded">Cancelar</button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded">Crear Grupo</button>
            </div>
        </form>
    </div>
</div>
@endsection