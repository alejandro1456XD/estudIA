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

    
    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_user')
                    ->withPivot('last_read_at')
                    ->withTimestamps();
    }

    
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    
    public function lastMessage()
    {
        return $this->hasOne(Message::class)->latest();
    }

    // --- ACCESSORS ---

    
    public function getTitleAttribute()
    {
        if ($this->is_group) {
            return $this->name ?? 'Grupo sin nombre';
        }

       
        $otherUser = $this->participants->where('id', '!=', Auth::id())->first();
        return $otherUser ? $otherUser->name : 'Usuario Desconocido';
    }

    
    public function getImageAttribute()
    {
        if ($this->is_group) {
            return $this->icon ? asset('storage/' . $this->icon) : asset('images/group-default.png'); 
        }

        $otherUser = $this->participants->where('id', '!=', Auth::id())->first();
        return $otherUser ? $otherUser->profile_picture : asset('images/default-avatar.png');
    }
}