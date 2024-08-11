<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    // Create Comment
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'post_id' => 'required|exists:posts,id',
            'parent_id' => 'nullable|exists:comments,id',
            'content' => 'required|string',
        ]);

        $comment = new Comment();
        $comment->user_id = $validatedData['user_id'];
        $comment->post_id = $validatedData['post_id'];
        $comment->parent_id = $validatedData['parent_id'] ?? null;
        $comment->content = $validatedData['content'];
        $comment->save();

        return response()->json(['message' => 'Comment created successfully'], 201);
    }

    // Get Comments for a Post
    public function getCommentsForPost($postId)
    {
        $post = Post::find($postId);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $comments = Comment::where('post_id', $postId)
            ->whereNull('parent_id')
            ->with('replies')
            ->get();

        return response()->json($comments, 200);
    }
}
