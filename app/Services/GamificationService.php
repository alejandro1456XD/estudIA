<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Session;

class GamificationService
{
    
    const XP_RATES = [
        'comment' => 10,         
        'create_post' => 20,      
        'upload_resource' => 100, 
        'feed_pet' => 15,         
        'buy_item' => 50,         
        'complete_test' => 150,   
        'daily_login' => 30
    ];

  
    const COIN_RATES = [
        'comment' => 5,
        'create_post' => 10,
        'upload_resource' => 50,
        'complete_test' => 100,
        'daily_login' => 20
    ];

   
    public function earn(User $user, string $action)
    {
       
        if (array_key_exists($action, self::COIN_RATES)) {
            $coins = self::COIN_RATES[$action];
            $user->increment('coins', $coins);
            
            
            if($coins > 0) {
                Session::flash('coin_earned', [
                    'amount' => $coins,
                    'message' => "¡Ganaste {$coins} monedas!"
                ]);
            }
        }

        
        if (array_key_exists($action, self::XP_RATES)) {
            $xp = self::XP_RATES[$action];
            $this->addXp($user, $xp);
        }
    }
    
   
    public function spend(User $user, int $amount)
    {
        if ($user->coins < $amount) {
            return false; 
        }
        
        $user->decrement('coins', $amount);
        return true;
    }

   
    private function addXp(User $user, int $amount)
    {
        
        $currentXp = $user->pet_xp ?? 0;
        $nextLevelXp = $user->pet_xp_next_level ?? 100;
        $currentLevel = $user->pet_level ?? 1;

        $user->pet_xp = $currentXp + $amount;

        
        if ($user->pet_xp >= $nextLevelXp) {
            
            
            $user->pet_xp -= $nextLevelXp;
            
          
            $user->pet_level = $currentLevel + 1;

          
            $user->pet_xp_next_level = intval($nextLevelXp * 1.2);

            
            Session::flash('level_up', [
                'level' => $user->pet_level,
                'pet_name' => $this->getPetName($user->pet_type)
            ]);
        }
        
        $user->save();
    }

    
    private function getPetName($type) {
        $names = [
            'dog' => 'Firulais', 
            'cat' => 'Michi', 
            'shark' => 'Tibby', 
            'dolphin' => 'Flipper'
        ];
        return $names[$type] ?? 'Tu mascota';
    }
}