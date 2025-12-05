<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResourceRating extends Model
{
    use HasFactory;

    protected $fillable = ['resource_id', 'user_id', 'rating'];

    
    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }
}