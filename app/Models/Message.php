<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'user_id',
        'body',
        'attachment',
        'type',
        'is_read'
    ];

    // Relación: Chat al que pertenece
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    // Relación: Usuario que lo envió
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}