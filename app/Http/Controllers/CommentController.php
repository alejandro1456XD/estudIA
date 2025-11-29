<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $post = Post::findOrFail($postId);

        $comment = new Comment();
        $comment->post_id = $post->id;
        $comment->user_id = Auth::id();
        $comment->content = $request->content;
        $comment->save();

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user_name' => Auth::user()->name,
                'user_avatar' => Auth::user()->avatar ?? 'https://via.placeholder.com/45',
                'created_at' => $comment->created_at->diffForHumans(),
                'is_owner' => true
            ],
            'comments_count' => $post->comments()->count()
        ]);
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        
       
        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permiso para eliminar este comentario'
            ], 403);
        }

        $postId = $comment->post_id;
        $comment->delete();

        $post = Post::find($postId);

        return response()->json([
            'success' => true,
            'comments_count' => $post->comments()->count()
        ]);
    }
}