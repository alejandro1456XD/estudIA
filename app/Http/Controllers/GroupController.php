<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class GroupController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $myGroups = $user->groups()->with('members')->get();
        $otherGroups = Group::whereDoesntHave('members', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('members')->get();

        return view('groups', compact('myGroups', 'otherGroups'));
    }

    public function show(Group $group)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$group->members->contains($user->id)) {
            if ($group->is_private) {
                return redirect()->route('groups')->with('error', 'Este es un grupo privado. Debes unirte primero.');
            }
        }

        // Verificar si el usuario actual es admin del grupo
        $isAdmin = $group->admin_id === $user->id;

        // Cargar mensajes
        $messages = $group->messages()
                          ->with('user')
                          ->orderBy('created_at', 'desc')
                          ->take(50)
                          ->get();

        return view('groups.show', compact('group', 'messages', 'isAdmin'));
    }

    public function storeMessage(Request $request, Group $group)
    {
        
        $request->validate([
            'content' => 'nullable|string',
            'file'    => 'nullable|file|max:20480', 
        ]);

        if (empty($request->content) && !$request->hasFile('file')) {
             return back()->with('error', 'Debes escribir un mensaje o adjuntar un archivo.');
        }

        $message = new GroupMessage();
        $message->group_id = $group->id;
        $message->user_id = Auth::id();
        $message->content = $request->content;

        
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $mime = $file->getMimeType();

            
            if (str_starts_with($mime, 'image/')) {
                $folder = 'group_media/images';
                $message->type = 'image';
            } else if (str_starts_with($mime, 'video/')) {
                $folder = 'group_media/videos';
                $message->type = 'video';
            } else {
                $folder = 'group_media/files';
                $message->type = 'file';
            }

            $message->file_path = $file->store($folder, 'public');
        } else {
            $message->type = 'text';
        }

        $message->save();

        return back()->with('success', 'Mensaje publicado correctamente.');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_private' => 'boolean'
        ]);

        $group = Group::create([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'is_private' => $request->boolean('is_private'),
            'admin_id' => Auth::id()
        ]);
        
        $group->members()->attach(Auth::id(), ['role' => 'admin']);
        return back()->with('success', 'Grupo "' . $group->name . '" creado exitosamente.');
    }

    public function join(Group $group)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        if (!$group->members()->where('users.id', $user->id)->exists()) {
            $group->members()->attach($user->id, ['role' => 'member']);
        }
        return redirect()->route('groups.show', $group->id)->with('success', '¡Bienvenido al grupo!');
    }

    public function leave(Group $group)
    {
        $user = Auth::user();
        if ($group->admin_id === $user->id) return back()->with('error', 'No puedes salir de tu propio grupo.');
        $group->members()->detach($user->id);
        return redirect()->route('groups')->with('success', 'Has salido del grupo.');
    }

    // --- MÉTODO UPDATE PARA EDITAR GRUPO ---
    public function update(Request $request, Group $group)
    {
        
        if ($group->admin_id !== Auth::id()) {
            return back()->with('error', 'No tienes permisos para editar este grupo.');
        }

        // Validación
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_private' => 'boolean',
            'group_photo' => 'nullable|image|max:5120', 
            'cover_photo' => 'nullable|image|max:10240', 
        ]);

        // Actualizar datos básicos
        $group->update([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'is_private' => $request->boolean('is_private'),
        ]);

        // Manejar foto del grupo
        if ($request->hasFile('group_photo')) {
            // Eliminar foto anterior si existe
            if ($group->group_photo_path && Storage::disk('public')->exists($group->group_photo_path)) {
                Storage::disk('public')->delete($group->group_photo_path);
            }
            
            $group->group_photo_path = $request->file('group_photo')->store('group_photos', 'public');
        }

        // Manejar foto de portada
        if ($request->hasFile('cover_photo')) {
            // Eliminar foto anterior si existe
            if ($group->cover_photo_path && Storage::disk('public')->exists($group->cover_photo_path)) {
                Storage::disk('public')->delete($group->cover_photo_path);
            }
            
            $group->cover_photo_path = $request->file('cover_photo')->store('group_covers', 'public');
        }

        // Guardar solo si hay cambios en las fotos
        if ($request->hasFile('group_photo') || $request->hasFile('cover_photo')) {
            $group->save();
        }

        return back()->with('success', 'Grupo actualizado correctamente.');
    }

    // --- MÉTODOS PARA ELIMINAR MENSAJES Y EXPULSAR MIEMBROS ---

    public function deleteMessage(Group $group, GroupMessage $message)
    {
        $user = Auth::user();
        
        // Verificar permisos
        if ($group->admin_id !== $user->id && $message->user_id !== $user->id) {
            return back()->with('error', 'No tienes permisos para eliminar este mensaje.');
        }

        // Eliminar archivo si existe
        if ($message->file_path && Storage::disk('public')->exists($message->file_path)) {
            Storage::disk('public')->delete($message->file_path);
        }

        $message->delete();

        return back()->with('success', 'Mensaje eliminado correctamente.');
    }

    public function expelMember(Group $group, User $user)
    {
        $currentUser = Auth::user();

        
        if ($group->admin_id !== $currentUser->id) {
            return back()->with('error', 'No tienes permisos para expulsar miembros.');
        }

        
        if ($user->id === $currentUser->id) {
            return back()->with('error', 'No puedes expulsarte a ti mismo.');
        }

        
        $group->members()->detach($user->id);

        return back()->with('success', "Miembro {$user->name} expulsado correctamente.");
    }
}