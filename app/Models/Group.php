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
        'group_photo_path', 
        'cover_photo_path'  
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

    

    
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    
    public function members()
    {
        return $this->belongsToMany(User::class, 'group_user', 'group_id', 'user_id')
                    ->withPivot('role')
                    ->withTimestamps();
    }

    
    public function messages()
    {
        return $this->hasMany(GroupMessage::class);
    }

  
    public function getGroupPhotoUrlAttribute()
    {
        if ($this->group_photo_path) {
            return asset('storage/' . $this->group_photo_path);
        }
        return null;
    }

   
    public function getCoverPhotoUrlAttribute()
    {
        if ($this->cover_photo_path) {
            return asset('storage/' . $this->cover_photo_path);
        }
        return null;
    }

    
    public function getHasGroupPhotoAttribute()
    {
        return !is_null($this->group_photo_path);
    }

    
    public function getHasCoverPhotoAttribute()
    {
        return !is_null($this->cover_photo_path);
    }
}