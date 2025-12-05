<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

   
    protected $fillable = [
        'user_id',
        'name',
        'category',
        'language',
        'level',
        'description',
        'type',        
        'schedule',    
        'is_published',
        'is_live_now'  
    ];

    
    protected $casts = [
        'schedule' => 'array',
        'is_published' => 'boolean',
        'is_live_now' => 'boolean', 
    ];

    // --- RELACIONES ---

    
    public function instructor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    
    public function students()
    {
        return $this->belongsToMany(User::class, 'course_user', 'course_id', 'user_id')
                    ->withPivot('progress')
                    ->withTimestamps();
    }
}