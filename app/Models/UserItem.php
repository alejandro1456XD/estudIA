<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'shop_item_id', 
        'is_equipped'
    ];

    // Relación para saber qué objeto es este
    public function item()
    {
        return $this->belongsTo(ShopItem::class, 'shop_item_id');
    }
}