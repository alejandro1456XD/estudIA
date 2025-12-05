<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShopItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'category', 
        'pet_type_restriction', 
        'price', 
        'xp_reward', 
        'image_path'
    ];
}