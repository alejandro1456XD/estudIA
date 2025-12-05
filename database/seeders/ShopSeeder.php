<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShopSeeder extends Seeder
{
    public function run()
    {
        $items = [
            
            ['name' => 'Galleta Energética', 'category' => 'food', 'pet_type_restriction' => null, 'price' => 10, 'xp_reward' => 15, 'image_path' => 'cookie.png'],
            ['name' => 'Bebida Inteligente', 'category' => 'food', 'pet_type_restriction' => null, 'price' => 20, 'xp_reward' => 30, 'image_path' => 'potion.png'],

          
            ['name' => 'Gorra de Detective', 'category' => 'hat', 'pet_type_restriction' => 'dog', 'price' => 100, 'xp_reward' => 50, 'image_path' => 'dog_detective.png'],
            ['name' => 'Collar de Picos', 'category' => 'accessory', 'pet_type_restriction' => 'dog', 'price' => 50, 'xp_reward' => 25, 'image_path' => 'dog_collar.png'],
            ['name' => 'Traje de Astronauta', 'category' => 'body', 'pet_type_restriction' => 'dog', 'price' => 500, 'xp_reward' => 200, 'image_path' => 'dog_astro.png'],

            
            ['name' => 'Sombrero de Mago', 'category' => 'hat', 'pet_type_restriction' => 'cat', 'price' => 150, 'xp_reward' => 60, 'image_path' => 'cat_wizard.png'],
            ['name' => 'Lentes Hipster', 'category' => 'glasses', 'pet_type_restriction' => 'cat', 'price' => 80, 'xp_reward' => 40, 'image_path' => 'cat_glasses.png'],
            ['name' => 'Capa de Superhéroe', 'category' => 'body', 'pet_type_restriction' => 'cat', 'price' => 400, 'xp_reward' => 150, 'image_path' => 'cat_hero.png'],

            
            ['name' => 'Parche Pirata', 'category' => 'glasses', 'pet_type_restriction' => 'shark', 'price' => 120, 'xp_reward' => 55, 'image_path' => 'shark_pirate.png'],
            ['name' => 'Corbata de Negocios', 'category' => 'accessory', 'pet_type_restriction' => 'shark', 'price' => 200, 'xp_reward' => 100, 'image_path' => 'shark_tie.png'],
            ['name' => 'Láser en la Cabeza', 'category' => 'hat', 'pet_type_restriction' => 'shark', 'price' => 1000, 'xp_reward' => 500, 'image_path' => 'shark_laser.png'],

            
            ['name' => 'Corona Real', 'category' => 'hat', 'pet_type_restriction' => 'dolphin', 'price' => 300, 'xp_reward' => 120, 'image_path' => 'dolphin_crown.png'],
            ['name' => 'Auriculares Gamer', 'category' => 'accessory', 'pet_type_restriction' => 'dolphin', 'price' => 150, 'xp_reward' => 70, 'image_path' => 'dolphin_headphones.png'],
            ['name' => 'Mochila Propulsora', 'category' => 'body', 'pet_type_restriction' => 'dolphin', 'price' => 600, 'xp_reward' => 250, 'image_path' => 'dolphin_jetpack.png'],
        ];

        DB::table('shop_items')->insert($items);
    }
}