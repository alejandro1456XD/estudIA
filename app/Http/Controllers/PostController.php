<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Services\GamificationService; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class PostController extends Controller
{
    
    public function store(Request $request, GamificationService $gamification) 
    {
        // 1. VALIDACIÓN
        $validator = Validator::make($request->all(), [
            'content' => 'nullable|string|max:5000|required_without:file',
            'type' => 'required|in:photo,document,question', 
            'file' => 'nullable|file|max:10240', // 10MB limit
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return redirect()->back()->with('error', 'No se pudo publicar: ' . $error);
        }

        $post = new Post();
        $post->user_id = Auth::id();
        
        
        $post->content = $request->content ?? ''; 
        $post->type = $request->type;

        
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            if (!$file->isValid()) {
                return redirect()->back()->with('error', 'Error al subir el archivo.');
            }

            $mime = $file->getMimeType();
            
            // Bloqueo de videos
            if (str_starts_with($mime, 'video/')) {
                return redirect()->back()->with('error', 'La subida de videos está deshabilitada temporalmente.');
            }
            
            // Auto-detectar tipo y carpeta
            if (str_starts_with($mime, 'image/')) {
                $post->type = 'photo';
                $folder = 'posts/images';
            } 
            else {
                $post->type = 'document';
                $folder = 'posts/documents';
            }

            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs($folder, $filename, 'public');
            
            $post->file_path = $path;
        }

        $post->save();

        
        try {
            $gamification->earn(Auth::user(), 'create_post');
        } catch (\Exception $e) {
            Log::error("Error entregando monedas al crear post: " . $e->getMessage());
        }

        return redirect()->route('home')->with('success', 'Publicación creada exitosamente');
    }

   
    public function like($id)
    {
        $post = Post::findOrFail($id);
        $user = Auth::user();

        if ($post->likes()->where('user_id', $user->id)->exists()) {
            $post->likes()->detach($user->id);
            $liked = false;
        } else {
            $post->likes()->attach($user->id);
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $post->likes()->count()
        ]);
    }

    
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        
        if ($post->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'No tienes permiso para eliminar esta publicación');
        }

        if ($post->file_path) {
            Storage::disk('public')->delete($post->file_path);
        }

        $post->delete();

        return redirect()->back()->with('success', 'Publicación eliminada');
    }
}