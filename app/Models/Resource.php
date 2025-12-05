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

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
    public function ratings()
    {
        return $this->hasMany(ResourceRating::class);
    }

    

   
    public function getAverageRatingAttribute()
    {
        
        return round($this->ratings()->avg('rating'), 1) ?? 0;
    }

    
    public function getCurrentUserRatingAttribute()
    {
        if (!Auth::check()) return 0;
        
        $rating = $this->ratings()->where('user_id', Auth::id())->first();
        return $rating ? $rating->rating : 0;
    }
}