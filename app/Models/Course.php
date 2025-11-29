<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    // Campos que permitimos llenar masivamente
    protected $fillable = [
        'user_id',
        'name',
        'category',
        'language',
        'level',
        'description',
        'type',        // recorded, live, hybrid
        'schedule',    // JSON con los horarios
        'is_published',
        'is_live_now'  // <--- NUEVO CAMPO AGREGADO
    ];

    // Conversión automática de tipos
    protected $casts = [
        'schedule' => 'array',
        'is_published' => 'boolean',
        'is_live_now' => 'boolean', // <--- IMPORTANTE: Para que Laravel lo trate como true/false
    ];

    // --- RELACIONES ---

    // 1. El Instructor (Dueño del curso)
    public function instructor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // 2. Los Estudiantes inscritos
    public function students()
    {
        return $this->belongsToMany(User::class, 'course_user', 'course_id', 'user_id')
                    ->withPivot('progress')
                    ->withTimestamps();
    }
}