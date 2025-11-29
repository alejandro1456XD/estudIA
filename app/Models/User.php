<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 
        'email', 
        'password',
        'bio',
        'profile_photo_path'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // --- RELACIONES ---

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likedPosts()
    {
        return $this->belongsToMany(Post::class, 'post_likes', 'user_id', 'post_id')
                    ->withTimestamps();
    }

    // 1. Amigos que YO agregué
    public function friends()
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
                    ->wherePivot('accepted', true)
                    ->withTimestamps();
    }

    // 2. Amigos que ME agregaron
    public function friendsReceived()
    {
        return $this->belongsToMany(User::class, 'friendships', 'friend_id', 'user_id')
                    ->wherePivot('accepted', true)
                    ->withTimestamps();
    }

    public function friendRequests()
    {
        return $this->hasMany(Friendship::class, 'friend_id')
                    ->where('accepted', false)
                    ->with('user');
    }

    // ATAJO: Todos mis amigos
    public function getAllFriendsAttribute()
    {
        return $this->friends->merge($this->friendsReceived);
    }

    // --- RELACIONES DE CURSOS ---

    public function coursesCreated()
    {
        return $this->hasMany(Course::class, 'user_id');
    }

    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_user', 'user_id', 'course_id')
                    ->withPivot('progress')
                    ->withTimestamps();
    }

    // --- RELACIONES DE GRUPOS ---

    // Grupos que he CREADO (soy el admin)
    public function administeredGroups()
    {
        return $this->hasMany(Group::class, 'admin_id');
    }

    // Grupos a los que pertenezco (soy miembro)
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_user', 'user_id', 'group_id')
                    ->withPivot('role') // Acceder al rol (admin, member)
                    ->withTimestamps();
    }

    // Mensajes que he enviado en grupos
    public function groupMessages()
    {
        return $this->hasMany(GroupMessage::class);
    }

    // --- RELACIÓN NUEVA: EVENTOS ---
    // Eventos a los que el usuario asistirá
    public function attendingEvents()
    {
        return $this->belongsToMany(Event::class, 'event_attendees')
                    ->withTimestamps();
    }

    // --- MÉTODOS ÚTILES PARA GRUPOS ---

    // Verificar si el usuario es miembro de un grupo específico
    public function isMemberOfGroup($groupId)
    {
        return $this->groups()->where('group_id', $groupId)->exists();
    }

    // Verificar si el usuario es administrador de un grupo específico
    public function isAdminOfGroup($groupId)
    {
        return $this->administeredGroups()->where('id', $groupId)->exists() || 
               $this->groups()->where('group_id', $groupId)->where('role', 'admin')->exists();
    }

    // Obtener el rol del usuario en un grupo específico
    public function getRoleInGroup($groupId)
    {
        $group = $this->groups()->where('group_id', $groupId)->first();
        
        if ($this->administeredGroups()->where('id', $groupId)->exists()) {
            return 'admin';
        }
        
        return $group ? $group->pivot->role : null;
    }

    // Grupos públicos a los que no pertenezco (para descubrir)
    public function getDiscoverableGroupsAttribute()
    {
        return Group::whereDoesntHave('users', function($query) {
            $query->where('user_id', $this->id);
        })->public()->get();
    }

    // Contador de grupos a los que pertenezco
    public function getGroupsCountAttribute()
    {
        return $this->groups()->count();
    }

    // Contador de mensajes en grupos
    public function getGroupMessagesCountAttribute()
    {
        return $this->groupMessages()->count();
    }

    // Grupos recientes (últimos 5 grupos unidos)
    public function getRecentGroupsAttribute()
    {
        return $this->groups()->latest('group_user.created_at')->take(5)->get();
    }

    // --- MÉTODOS DE PERFIL ---

    // Obtener la URL de la foto de perfil
    public function getProfilePictureAttribute()
    {
        return $this->profile_photo_path ? asset('storage/' . $this->profile_photo_path) : '/default-avatar.png';
    }

    // Verificar si el usuario tiene foto de perfil
    public function hasProfilePicture()
    {
        return !is_null($this->profile_photo_path);
    }

    // --- SCOPES ÚTILES ---

    // Scope para buscar usuarios por nombre o email
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                     ->orWhere('email', 'like', "%{$search}%");
    }

    // Scope para usuarios que no son amigos
    public function scopeNotFriendsWith($query, $userId)
    {
        return $query->whereDoesntHave('friends', function($q) use ($userId) {
            $q->where('friend_id', $userId);
        })->whereDoesntHave('friendsReceived', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('id', '!=', $userId);
    }
}