<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start_time',
        'location',
        'is_virtual',
        'max_attendees',
        'type'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'is_virtual' => 'boolean',
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public function attendees()
    {
        return $this->belongsToMany(User::class, 'event_attendees')->withTimestamps();
    }

    

    
    public function getSpotsLeftAttribute()
    {
        if (is_null($this->max_attendees)) {
            return '∞'; 
        }
        
        return max(0, $this->max_attendees - $this->attendees()->count());
    }

   
    public function getIsAttendingAttribute()
    {
        if (!Auth::check()) return false;
        
        
        if ($this->user_id === Auth::id()) {
            return true; 
        }
        
        
        return $this->attendees()->where('user_id', Auth::id())->exists();
    }
}