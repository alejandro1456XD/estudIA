<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; 

class PetController extends Controller
{
   
    public function select(Request $request)
    {
        $request->validate([
            'pet_type' => 'required|in:dog,cat,dolphin,shark'
        ]);

        
        /** @var \App\Models\User $user */
        $user = Auth::user();

        
        $user->pet_type = $request->pet_type;

       
        if (!$user->pet_name) {
            $defaults = [
                'dog' => 'Firulais',
                'cat' => 'Michi',
                'dolphin' => 'Flipper',
                'shark' => 'Tibby'
            ];
            $user->pet_name = $defaults[$request->pet_type] ?? 'Tu Mascota';
        }

        $user->save(); 

        return back()->with('success', '¡Has elegido a tu compañero!');
    }

   
    public function rename(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:15|min:2' 
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        $user->pet_name = $request->name;
        $user->save();

        return back()->with('success', '¡Tu mascota ahora se llama ' . $request->name . '!');
    }
}