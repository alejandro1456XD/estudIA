<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'is_private',
        'admin_id',
        'group_photo_path', // NUEVO
        'cover_photo_path'  // NUEVO
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

    // --- RELACIONES ---

    // 1. El Administrador (Creador del grupo)
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // 2. Los Miembros del grupo (Relación muchos a muchos)
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_user', 'group_id', 'user_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    // 3. Los Mensajes del grupo
    public function messages()
    {
        return $this->hasMany(GroupMessage::class);
    }

    // --- ACCESORS (OPCIONALES) PARA MÁS FACILIDAD ---

    /**
     * Obtener la URL completa de la foto del grupo
     */
    public function getGroupPhotoUrlAttribute()
    {
        if ($this->group_photo_path) {
            return asset('storage/' . $this->group_photo_path);
        }
        return null;
    }

    /**
     * Obtener la URL completa de la foto de portada
     */
    public function getCoverPhotoUrlAttribute()
    {
        if ($this->cover_photo_path) {
            return asset('storage/' . $this->cover_photo_path);
        }
        return null;
    }

    /**
     * Verificar si el grupo tiene foto
     */
    public function getHasGroupPhotoAttribute()
    {
        return !is_null($this->group_photo_path);
    }

    /**
     * Verificar si el grupo tiene foto de portada
     */
    public function getHasCoverPhotoAttribute()
    {
        return !is_null($this->cover_photo_path);
    }
}