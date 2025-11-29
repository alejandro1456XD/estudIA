<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'file_path',
        'file_type',
        'file_size',
        'category',
        'downloads'
    ];

    // Relación 1: El dueño del recurso
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación 2: Las calificaciones que ha recibido
    public function ratings()
    {
        return $this->hasMany(ResourceRating::class);
    }

    // --- FUNCIONES MÁGICAS (ACCESSORS) ---

    // 1. Calcular el promedio de estrellas (Ej: 4.5) automáticamente
    public function getAverageRatingAttribute()
    {
        // Si no tiene votos, retorna 0. Si tiene, saca el promedio.
        return round($this->ratings()->avg('rating'), 1) ?? 0;
    }

    // 2. Saber si el usuario actual ya votó este recurso (para bloquear el botón)
    public function getCurrentUserRatingAttribute()
    {
        if (!Auth::check()) return 0;
        
        $rating = $this->ratings()->where('user_id', Auth::id())->first();
        return $rating ? $rating->rating : 0;
    }
}