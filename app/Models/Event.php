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

    // Relación: El creador del evento
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Los asistentes (Muchos usuarios a través de la tabla pivote)
    public function attendees()
    {
        return $this->belongsToMany(User::class, 'event_attendees')->withTimestamps();
    }

    // --- ACCESSORS (Cálculos automáticos) ---

    // 1. ¿Cuántos cupos quedan?
    public function getSpotsLeftAttribute()
    {
        if (is_null($this->max_attendees)) {
            return '∞'; // Ilimitado
        }
        
        return max(0, $this->max_attendees - $this->attendees()->count());
    }

    // 2. ¿El usuario actual ya se inscribió?
    public function getIsAttendingAttribute()
    {
        if (!Auth::check()) return false;
        
        // CORRECCIÓN: Si soy el creador, cuento como asistente automáticamente
        // Opcional: Si quieres que el creador también tenga que darle click, borra esta línea.
        if ($this->user_id === Auth::id()) {
            return true; 
        }
        
        // Verificamos si el ID del usuario está en la lista de asistentes
        return $this->attendees()->where('user_id', Auth::id())->exists();
    }
}