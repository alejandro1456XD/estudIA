<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    /**
     * Muestra la lista de conversaciones.
     */
    public function index()
    {
        $user = Auth::user();
        $conversations = $user->conversations()
            ->with(['lastMessage', 'participants'])
            ->orderByDesc('updated_at')
            ->get();

        return view('chat.index', compact('conversations'));
    }

    /**
     * Muestra una conversación específica.
     */
    public function show($id)
    {
        $conversation = Conversation::with(['messages.user', 'participants'])->findOrFail($id);

        if (!$conversation->participants->contains(Auth::id())) {
            abort(403, 'No tienes permiso para ver este chat.');
        }

        // Marcar como leído
        $conversation->participants()->updateExistingPivot(Auth::id(), ['last_read_at' => now()]);

        return view('chat.show', compact('conversation'));
    }

    /**
     * Crea o recupera un chat privado.
     */
    public function storePrivate(Request $request)
    {
        $request->validate(['recipient_id' => 'required|exists:users,id']);
        $me = Auth::user();
        $recipientId = $request->recipient_id;

        // Buscar si ya existe chat privado
        $conversation = Conversation::where('is_group', false)
            ->whereHas('participants', function ($q) use ($me) { $q->where('user_id', $me->id); })
            ->whereHas('participants', function ($q) use ($recipientId) { $q->where('user_id', $recipientId); })
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create(['is_group' => false]);
            $conversation->participants()->attach([$me->id, $recipientId]);
        }

        return redirect()->route('chat.show', $conversation->id);
    }

    /**
     * Crea un nuevo grupo.
     */
    public function storeGroup(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'users' => 'required|array|min:1',
            'users.*' => 'exists:users,id'
        ]);

        $me = Auth::user();

        $conversation = DB::transaction(function () use ($request, $me) {
            $group = Conversation::create([
                'name' => $request->name,
                'is_group' => true,
                'admin_id' => $me->id, // El creador original
            ]);

            // El creador se agrega como admin (is_admin = true)
            $group->participants()->attach($me->id, ['is_admin' => true]);
            
            // Los demás se agregan como miembros normales (is_admin = false)
            $group->participants()->attach($request->users, ['is_admin' => false]);

            return $group;
        });

        return redirect()->route('chat.show', $conversation->id);
    }

    /**
     * Envía un mensaje.
     */
    public function sendMessage(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);
        if (!$conversation->participants->contains(Auth::id())) abort(403);

        $request->validate([
            'body' => 'required_without:attachment|string',
            'attachment' => 'nullable|file|max:10240'
        ]);

        $path = $request->hasFile('attachment') ? $request->file('attachment')->store('chat_attachments', 'public') : null;

        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'body' => $request->body,
            'attachment' => $path,
            'type' => $path ? 'file' : 'text',
        ]);

        $conversation->touch(); // Actualiza 'updated_at' para subirlo en la lista
        return back();
    }

    // ==========================================
    // NUEVAS FUNCIONES DE GESTIÓN DE GRUPO
    // ==========================================

    /**
     * Salir del grupo.
     */
    public function leaveGroup($id)
    {
        $conversation = Conversation::findOrFail($id);
        
        // REGLA: El creador original NO puede abandonar el grupo (debe eliminarlo o transferirlo)
        if ($conversation->admin_id == Auth::id()) {
            return back()->with('error', 'Como creador, no puedes salir. Debes eliminar el grupo si ya no lo quieres.');
        }

        $conversation->participants()->detach(Auth::id());
        
        // CORRECCIÓN: Redirigir al HOME en lugar de chat.index
        return redirect()->route('home')->with('success', 'Has salido del grupo.');
    }

    /**
     * Eliminar grupo (Solo Creador).
     */
    public function deleteGroup($id)
    {
        $conversation = Conversation::findOrFail($id);
        
        // REGLA: SOLO el creador original puede eliminar
        if ($conversation->admin_id != Auth::id()) {
            abort(403, 'Solo el creador puede eliminar el grupo.');
        }

        $conversation->delete(); 
        
        // CORRECCIÓN: Redirigir al HOME en lugar de chat.index
        return redirect()->route('home')->with('success', 'Grupo eliminado permanentemente.');
    }

    /**
     * Cambiar foto del grupo (Admins).
     */
    public function updatePhoto(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);
        $this->authorizeAdminAction($conversation); // Verifica permisos

        $request->validate(['icon' => 'required|image|max:2048']);
        
        $path = $request->file('icon')->store('group_icons', 'public');
        $conversation->update(['icon' => $path]);

        return back()->with('success', 'Foto de grupo actualizada.');
    }

    /**
     * Añadir nuevos miembros (Admins).
     */
    public function addMembers(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);
        $this->authorizeAdminAction($conversation);

        $request->validate(['users' => 'required|array', 'users.*' => 'exists:users,id']);

        // Filtrar usuarios que ya están para no duplicar
        $currentParticipantIds = $conversation->participants->pluck('id')->toArray();
        $newUsers = array_diff($request->users, $currentParticipantIds);
        
        if (!empty($newUsers)) {
            $conversation->participants()->attach($newUsers, ['is_admin' => false]);
        }

        return back()->with('success', 'Nuevos miembros añadidos.');
    }

    /**
     * Designar a un miembro como administrador (Admins).
     */
    public function makeAdmin(Request $request, $id, $userId)
    {
        $conversation = Conversation::findOrFail($id);
        $this->authorizeAdminAction($conversation);

        // Actualizar la tabla pivot para hacer admin a este usuario
        $conversation->participants()->updateExistingPivot($userId, ['is_admin' => true]);

        return back()->with('success', 'Usuario ascendido a administrador.');
    }

    /**
     * Expulsar a un miembro (Admins).
     */
    public function removeMember(Request $request, $id, $userId)
    {
        $conversation = Conversation::findOrFail($id);
        $this->authorizeAdminAction($conversation);

        // REGLA: No puedes expulsar al creador original
        if ($userId == $conversation->admin_id) {
            return back()->with('error', 'No puedes expulsar al creador del grupo.');
        }

        $conversation->participants()->detach($userId);
        return back()->with('success', 'Usuario eliminado del grupo.');
    }

    /**
     * Helper privado para verificar si el usuario actual tiene poder de admin en este grupo.
     * Es admin si: Es el Creador (admin_id) O tiene el flag is_admin en la tabla pivot.
     */
    private function authorizeAdminAction($conversation)
    {
        $me = Auth::user();
        $participant = $conversation->participants()->where('user_id', $me->id)->first();
        
        $isCreator = ($conversation->admin_id == $me->id);
        $isDesignatedAdmin = ($participant && $participant->pivot->is_admin);

        if (!$isCreator && !$isDesignatedAdmin) {
            abort(403, 'No tienes permisos de administrador en este grupo.');
        }
    }
}