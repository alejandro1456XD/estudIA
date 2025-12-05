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
    public function index()
    {
        $user = Auth::user();
        $conversations = $user->conversations()
            ->with(['lastMessage', 'participants'])
            ->orderByDesc('updated_at')
            ->get();

        return view('chat.index', compact('conversations'));
    }

    public function show($id)
    {
        $conversation = Conversation::with(['messages.user', 'participants'])->findOrFail($id);

        if (!$conversation->participants->contains(Auth::id())) {
            abort(403);
        }

        $conversation->participants()->updateExistingPivot(Auth::id(), ['last_read_at' => now()]);

        return view('chat.show', compact('conversation'));
    }

    public function storePrivate(Request $request)
    {
        $request->validate(['recipient_id' => 'required|exists:users,id']);

        $me = Auth::user();
        $recipientId = $request->recipient_id;

        $conversation = Conversation::where('is_group', false)
            ->whereHas('participants', fn($q) => $q->where('user_id', $me->id))
            ->whereHas('participants', fn($q) => $q->where('user_id', $recipientId))
            ->first();

        if (!$conversation) {
            $conversation = Conversation::create(['is_group' => false]);
            $conversation->participants()->attach([$me->id, $recipientId]);
        }

        return redirect()->route('chat.show', $conversation->id);
    }

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
                'admin_id' => $me->id,
            ]);

            $group->participants()->attach($me->id, ['is_admin' => true]);
            $group->participants()->attach($request->users, ['is_admin' => false]);

            return $group;
        });

        return redirect()->route('chat.show', $conversation->id);
    }

    public function sendMessage(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);

        if (!$conversation->participants->contains(Auth::id())) {
            abort(403);
        }

        $request->validate([
            'body' => 'required_without:attachment|string',
            'attachment' => 'nullable|file|max:10240'
        ]);

        $path = $request->hasFile('attachment')
            ? $request->file('attachment')->store('chat_attachments', 'public')
            : null;

        Message::create([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'body' => $request->body,
            'attachment' => $path,
            'type' => $path ? 'file' : 'text',
        ]);

        $conversation->touch();
        return back();
    }

    public function leaveGroup($id)
    {
        $conversation = Conversation::findOrFail($id);

        if ($conversation->admin_id == Auth::id()) {
            return back()->with('error', 'Como creador, no puedes salir. Debes eliminar el grupo.');
        }

        $conversation->participants()->detach(Auth::id());

        return redirect()->route('home')->with('success', 'Has salido del grupo.');
    }

    public function deleteGroup($id)
    {
        $conversation = Conversation::findOrFail($id);

        if ($conversation->admin_id != Auth::id()) {
            abort(403);
        }

        $conversation->delete();

        return redirect()->route('home')->with('success', 'Grupo eliminado.');
    }

    public function updatePhoto(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);
        $this->authorizeAdminAction($conversation);

        $request->validate(['icon' => 'required|image|max:2048']);

        $path = $request->file('icon')->store('group_icons', 'public');
        $conversation->update(['icon' => $path]);

        return back()->with('success', 'Foto actualizada.');
    }

    public function addMembers(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);
        $this->authorizeAdminAction($conversation);

        $request->validate([
            'users' => 'required|array',
            'users.*' => 'exists:users,id'
        ]);

        $current = $conversation->participants->pluck('id')->toArray();
        $newUsers = array_diff($request->users, $current);

        if (!empty($newUsers)) {
            $conversation->participants()->attach($newUsers, ['is_admin' => false]);
        }

        return back()->with('success', 'Miembros añadidos.');
    }

    public function makeAdmin(Request $request, $id, $userId)
    {
        $conversation = Conversation::findOrFail($id);
        $this->authorizeAdminAction($conversation);

        $conversation->participants()->updateExistingPivot($userId, ['is_admin' => true]);

        return back()->with('success', 'Usuario ascendido.');
    }

    public function removeMember(Request $request, $id, $userId)
    {
        $conversation = Conversation::findOrFail($id);
        $this->authorizeAdminAction($conversation);

        if ($userId == $conversation->admin_id) {
            return back()->with('error', 'No puedes expulsar al creador.');
        }

        $conversation->participants()->detach($userId);

        return back()->with('success', 'Usuario eliminado.');
    }

    private function authorizeAdminAction($conversation)
    {
        $me = Auth::user();
        $participant = $conversation->participants()->where('user_id', $me->id)->first();

        $isCreator = ($conversation->admin_id == $me->id);
        $isAdmin = ($participant && $participant->pivot->is_admin);

        if (!$isCreator && !$isAdmin) {
            abort(403);
        }
    }
}
