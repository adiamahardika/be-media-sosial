<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    // Create Post
    public function create(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'content' => 'nullable|string',
            'media_type' => 'nullable|string|in:image,video',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,avi,mov|max:20480',
        ]);

        $post = new Post();
        $post->user_id = $validatedData['user_id'];
        $post->content = $validatedData['content'];

        if ($request->hasFile('media')) {
            $path = $request->file('media')->store('posts', 'public');
            $post->media_type = $validatedData['media_type'];
            $post->media_path = $path;
        }

        $post->save();

        return response()->json(['message' => 'Post created successfully'], 201);
    }

    // Get Post
    public function show($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        return response()->json($post, 200);
    }

    // Edit Post
    public function update(Request $request, $id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $validatedData = $request->validate([
            'content' => 'nullable|string',
            'media_type' => 'nullable|string|in:image,video',
            'media' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,avi,mov|max:20480',
        ]);

        $post->content = $validatedData['content'] ?? $post->content;

        if ($request->hasFile('media')) {
            if ($post->media_path) {
                Storage::disk('public')->delete($post->media_path);
            }

            $path = $request->file('media')->store('posts', 'public');
            $post->media_type = $validatedData['media_type'];
            $post->media_path = $path;
        }

        $post->save();

        return response()->json(['message' => 'Post updated successfully'], 200);
    }

    // Delete Post
    public function destroy($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        if ($post->media_path) {
            Storage::disk('public')->delete($post->media_path);
        }

        $post->delete();

        return response()->json(['message' => 'Post deleted successfully'], 200);
    }

    // Like Post
    public function like($id)
    {
        $post = Post::find($id);

        if (!$post) {
            return response()->json(['message' => 'Post not found'], 404);
        }

        $post->increment('likes');

        return response()->json(['message' => 'Post liked successfully', 'likes' => $post->likes], 200);
    }
}
