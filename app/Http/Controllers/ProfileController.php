<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;


class ProfileController extends Controller
{
    public function show()
    {
        return view('profile', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20', 
            'bio' => 'nullable|string|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        try {
           
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone; 
            $user->bio = $request->bio;

            
            if ($request->hasFile('photo')) {
               
                if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                    Storage::disk('public')->delete($user->profile_photo_path);
                }
                
               
                $path = $request->file('photo')->store('profile-photos', 'public');
                $user->profile_photo_path = $path;
            }

            
            $user->save();

            return redirect()->route('profile')->with('success', 'Perfil actualizado correctamente.');

        } catch (\Exception $e) {
           
            return back()->with('error', 'Error al actualizar perfil: ' . $e->getMessage());
        }
    }
}