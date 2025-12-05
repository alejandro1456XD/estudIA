<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Services\GamificationService; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function store(Request $request, $postId, GamificationService $gamification)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        DB::beginTransaction();

        try {
            $post = Post::findOrFail($postId);
            $user = Auth::user();

            $comment = new Comment();
            $comment->post_id = $post->id;
            $comment->user_id = $user->id;
            $comment->content = $request->content;
            $comment->save();

            try {
                $gamification->earn($user, 'comment');
            } catch (\Exception $e) {
                Log::error("Error al dar monedas: " . $e->getMessage());
            }

            DB::commit();

            $avatar = $user->profile_photo_path 
                ? asset('storage/' . $user->profile_photo_path) 
                : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&color=7F9CF5&background=EBF4FF';

            return response()->json([
                'success' => true,
                'comment' => [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user_name' => $user->name,
                    'user_avatar' => $avatar,
                    'created_at' => $comment->created_at->diffForHumans(),
                    'is_owner' => true
                ],
                'comments_count' => $post->comments()->count(),
                'message' => 'Comentario publicado correctamente.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error publicando comentario: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al publicar el comentario.'
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $comment = Comment::findOrFail($id);

            if ($comment->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permiso para eliminar este comentario'
                ], 403);
            }

            $postId = $comment->post_id;
            $comment->delete();

            $count = Post::find($postId)->comments()->count();

            return response()->json([
                'success' => true,
                'comments_count' => $count,
                'message' => 'Comentario eliminado.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al intentar eliminar el comentario.'
            ], 500);
        }
    }
}
