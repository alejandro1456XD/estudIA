<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'user_id',
        'name',
        'source_content',
        'prompt_input',
        'quiz_structure',
        'status',
    ];

    
    protected $casts = [
        'quiz_structure' => 'array',
    ];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}