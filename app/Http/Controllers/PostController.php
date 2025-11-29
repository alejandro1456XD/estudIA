<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function store(Request $request)
    {
        // 1. VALIDACIÓN
        $validator = Validator::make($request->all(), [
            'content' => 'nullable|string|max:5000|required_without:file',
            'type' => 'required|in:photo,document,question', // Sin video
            'file' => 'nullable|file|max:10240', // 10MB limit
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            return redirect()->back()->with('error', 'No se pudo publicar: ' . $error);
        }

        $post = new Post();
        $post->user_id = Auth::id();
        
        // --- AQUÍ ESTÁ EL ARREGLO ---
        // Usamos el operador '??' para decir: "Si content es null, usa una cadena vacía ''"
        $post->content = $request->content ?? ''; 
        
        // Inicialmente usamos el tipo que envió el formulario
        $post->type = $request->type;

        // 2. MANEJO DE ARCHIVOS
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            
            if (!$file->isValid()) {
                return redirect()->back()->with('error', 'Error al subir el archivo.');
            }

            $mime = $file->getMimeType();
            
            // Bloqueamos videos por ahora
            if (str_starts_with($mime, 'video/')) {
                return redirect()->back()->with('error', 'La subida de videos está deshabilitada temporalmente.');
            }
            
            // Auto-detectar tipo
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