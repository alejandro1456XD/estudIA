<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB; 

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

       
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'bio' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        try {
            
            $updateData = [
                'name' => $request->name,
                'email' => $request->email,
                'bio' => $request->bio,
                'updated_at' => now(), 
            ];

         
            if ($request->hasFile('photo')) {
                
                if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }
                
               
                $path = $request->file('photo')->store('profile-photos', 'public');
                $updateData['profile_photo_path'] = $path;
            }

         
            DB::table('users')
                ->where('id', $user->id)
                ->update($updateData);

            return redirect()->route('profile')->with('success', 'Perfil actualizado correctamente.');

        } catch (\Exception $e) {
           
            dd("ERROR DE BASE DE DATOS:", $e->getMessage()); 
        }
    }
}