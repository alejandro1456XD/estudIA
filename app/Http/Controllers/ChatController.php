<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Muestra la lista de conversaciones del usuario.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Obtenemos las conversaciones ordenadas por la última actualización (mensaje más reciente)
        $conversations = $user->conversations()
            ->with(['lastMessage', 'participants'])
            ->orderByDesc('updated_at')
            ->get();

        return view('chat.index', compact('conversations'));
    }

    /**
     * Muestra una conversación específica y sus mensajes.
     */
    public function show($id)
    {
        $conversation = Conversation::with(['messages.user', 'participants'])->findOrFail($id);

        // Seguridad: Verificar que el usuario pertenece a esta conversación
        if (!$conversation->participants->contains(Auth::id())) {
            abort(403, 'No tienes permiso para ver este chat.');
        }

        // Marcar como leído (actualizar pivot)
        $conversation->participants()->updateExistingPivot(Auth::id(), [
            'last_read_at' => now()
        ]);

        return view('chat.show', compact('conversation'));
    }

    /**
     * Inicia o recupera un chat privado con un amigo.
     */
    public function storePrivate(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id'
        ]);

        $me = Auth::user();
        $recipientId = $request->recipient_id;

        // 1. Verificar si son amigos
        // Usamos el helper que definimos en User (o verificamos las relaciones manualmente)
        $isFriend = $me->friends()->where('friend_id', $recipientId)->exists() || 
                    $me->friendsReceived()->where('user_id', $recipientId)->exists();

        if (!$isFriend) {
            return back()->with('error', 'Solo puedes iniciar chats con amigos.');
        }

        // 2. Verificar si ya existe una conversación privada entre estos dos
        // Buscamos una conversación que NO sea grupo y tenga exactamente estos 2 participantes
        $conversation = Conversation::where('is_group', false)
            ->whereHas('participants', function ($q) use ($me) {
                $q->where('user_id', $me->id);
            })
            ->whereHas('participants', function ($q) use ($recipientId) {
                $q->where('user_id', $recipientId);
            })
            ->first();

        // 3. Si no existe, la creamos
        if (!$conversation) {
            DB::transaction(function () use (&$conversation, $me, $recipientId) {
                $conversation = Conversation::create([
                    'is_group' => false,
                ]);

                $conversation->participants()->attach([$me->id, $recipientId]);
            });
        }

        return redirect()->route('chat.show', $conversation->id);
    }

    /**
     * Crea un nuevo grupo de chat con varios amigos.
     */
    public function storeGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'users' => 'required|array|min:1', // IDs de los amigos a agregar
            'users.*' => 'exists:users,id'
        ]);

        $me = Auth::user();
        $friendIds = $request->users;

        // 1. Validar que TODOS los usuarios seleccionados sean amigos
        // (Esta validación es opcional pero recomendada para consistencia estricta)
        foreach ($friendIds as $friendId) {
            $isFriend = $me->friends()->where('friend_id', $friendId)->exists() || 
                        $me->friendsReceived()->where('user_id', $friendId)->exists();
            
            if (!$isFriend) {
                return back()->with('error', 'Uno de los usuarios seleccionados no es tu amigo.');
            }
        }

        // 2. Crear el grupo
        $conversation = DB::transaction(function () use ($request, $me, $friendIds) {
            $group = Conversation::create([
                'name' => $request->name,
                'is_group' => true,
                'admin_id' => $me->id,
                'icon' => null // Podrías agregar subida de imagen aquí
            ]);

            // Agregar al creador y a los amigos seleccionados
            $participants = array_merge([$me->id], $friendIds);
            $group->participants()->attach($participants);

            return $group;
        });

        return redirect()->route('chat.show', $conversation->id)->with('success', 'Grupo creado exitosamente.');
    }

    /**
     * Envía un mensaje a una conversación.
     */
    public function sendMessage(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);

        // Seguridad: Verificar pertenencia
        if (!$conversation->participants->contains(Auth::id())) {
            abort(403, 'No perteneces a este chat.');
        }

        $request->validate([
            'body' => 'required_without:attachment|string',
            'attachment' => 'nullable|file|max:10240' // Max 10MB
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('chat_attachments', 'public');
        }

        // Crear el mensaje
        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'body' => $request->body,
            'attachment' => $attachmentPath,
            'type' => $attachmentPath ? 'file' : 'text',
        ]);

        // Actualizar el timestamp de la conversación para que suba en la lista
        $conversation->touch();

        return redirect()->route('chat.show', $conversation->id);
    }
}