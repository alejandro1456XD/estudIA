<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_group',
        'icon',
        'admin_id'
    ];

    // Relación: Participantes del chat
    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_user')
                    ->withPivot('last_read_at')
                    ->withTimestamps();
    }

    // Relación: Mensajes del chat
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Último mensaje (para mostrar en la lista de chats)
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    // --- ACCESSORS ---

    // Obtener el nombre del chat (Si es privado, usa el nombre del otro usuario)
    public function getTitleAttribute()
    {
        if ($this->is_group) {
            return $this->name ?? 'Grupo sin nombre';
        }

        // Si es chat privado, buscamos al "otro" usuario
        $otherUser = $this->participants->where('id', '!=', Auth::id())->first();
        return $otherUser ? $otherUser->name : 'Usuario Desconocido';
    }

    // Obtener la foto del chat (Si es privado, usa la foto del otro usuario)
    public function getImageAttribute()
    {
        if ($this->is_group) {
            return $this->icon ? asset('storage/' . $this->icon) : asset('images/group-default.png'); // Asegúrate de tener una imagen default
        }

        $otherUser = $this->participants->where('id', '!=', Auth::id())->first();
        return $otherUser ? $otherUser->profile_picture : asset('images/default-avatar.png');
    }
}