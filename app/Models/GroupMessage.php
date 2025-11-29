<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'user_id',
        'content',
        'file_path',
        'type'
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // --- NUEVOS MÉTODOS PARA MODERACIÓN ---
    
    // Verificar si el mensaje puede ser eliminado por un usuario
    public function canBeDeletedBy($user)
    {
        return $user->id === $this->user_id || 
               $this->group->admin_id === $user->id ||
               $user->isAdminOfGroup($this->group_id);
    }

    // Verificar si el mensaje tiene archivo adjunto
    public function hasAttachment()
    {
        return !is_null($this->file_path);
    }

    // Obtener el tipo de archivo en formato legible
    public function getAttachmentType()
    {
        if (!$this->hasAttachment()) return null;
        
        return match($this->type) {
            'image' => 'Imagen',
            'video' => 'Video', 
            'file' => 'Archivo',
            default => 'Archivo'
        };
    }
}