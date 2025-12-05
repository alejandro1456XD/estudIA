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
        'phone', 
        'password',
        'bio',
        'profile_photo_path',
        
        
        'coins',
        'pet_type',
        'pet_level',
        'pet_xp',
        'pet_xp_next_level',
        'pet_name'
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

    

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user')
                    ->withPivot('last_read_at')
                    ->withTimestamps();
    }

    

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    
    
    public function tests()
    {
        return $this->hasMany(Test::class);
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

  

   
    public function friends()
    {
        return $this->belongsToMany(User::class, 'friendships', 'user_id', 'friend_id')
                    ->wherePivot('accepted', true)
                    ->withTimestamps();
    }

   
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

    
    public function getAllFriendsAttribute()
    {
        return $this->friends->merge($this->friendsReceived);
    }

    

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

   

    
    public function administeredGroups()
    {
        return $this->hasMany(Group::class, 'admin_id');
    }

    
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_user', 'user_id', 'group_id')
                    ->withPivot('role') 
                    ->withTimestamps();
    }

   
    public function groupMessages()
    {
        return $this->hasMany(GroupMessage::class);
    }

    
    
   
    public function attendingEvents()
    {
        return $this->belongsToMany(Event::class, 'event_attendees')
                    ->withTimestamps();
    }

   

    
    public function isMemberOfGroup($groupId)
    {
        return $this->groups()->where('group_id', $groupId)->exists();
    }

    
    public function isAdminOfGroup($groupId)
    {
        return $this->administeredGroups()->where('id', $groupId)->exists() || 
               $this->groups()->where('group_id', $groupId)->where('role', 'admin')->exists();
    }

    
    public function getRoleInGroup($groupId)
    {
        $group = $this->groups()->where('group_id', $groupId)->first();
        
        if ($this->administeredGroups()->where('id', $groupId)->exists()) {
            return 'admin';
        }
        
        return $group ? $group->pivot->role : null;
    }

    
    public function getDiscoverableGroupsAttribute()
    {
        return Group::whereDoesntHave('users', function($query) {
            $query->where('user_id', $this->id);
        })->public()->get();
    }

    
    public function getGroupsCountAttribute()
    {
        return $this->groups()->count();
    }

    
    public function getGroupMessagesCountAttribute()
    {
        return $this->groupMessages()->count();
    }

   
    public function getRecentGroupsAttribute()
    {
        return $this->groups()->latest('group_user.created_at')->take(5)->get();
    }

    

    
    public function getProfilePictureAttribute()
    {
        return $this->profile_photo_path ? asset('storage/' . $this->profile_photo_path) : '/default-avatar.png';
    }

    
    public function hasProfilePicture()
    {
        return !is_null($this->profile_photo_path);
    }

 

    public function items()
    {
        return $this->hasMany(\App\Models\UserItem::class);
    }

   
    public function equippedItem($category)
    {
        return $this->items()
            ->where('is_equipped', true)
            ->whereHas('item', function($query) use ($category) {
                $query->where('category', $category);
            })
            ->with('item')
            ->first()
            ?->item;
    }

    

   
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
    }

    
    public function scopeNotFriendsWith($query, $userId)
    {
        return $query->whereDoesntHave('friends', function($q) use ($userId) {
            $q->where('friend_id', $userId);
        })->whereDoesntHave('friendsReceived', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('id', '!=', $userId);
    }
}