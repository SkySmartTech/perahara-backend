<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlogPostController extends Controller
{
    // Public: view all posts
    public function index()
    {
        return BlogPost::with('user:id,username')->latest()->paginate(15);
    }

    // Public: view a single post
    public function show(BlogPost $blogPost)
    {
        return $blogPost->load('user:id,username');
    }

    // Authenticated: create post
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'     => ['required','string','max:255'],
            'content'   => ['required','string'],
            'image_url' => ['nullable','url','max:2048'],
        ]);

        $post = $request->user()->blogPosts()->create($data);

        return response()->json($post, 201);
    }

    // Authenticated: update own post
    public function update(Request $request, BlogPost $blogPost)
    {
        $user = $request->user();

        if ($user->id !== $blogPost->user_id && $user->user_type !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'title'     => ['sometimes','string','max:255'],
            'content'   => ['sometimes','string'],
            'image_url' => ['sometimes','nullable','url','max:2048'],
        ]);

        $blogPost->update($data);

        return response()->json($blogPost);
    }

    // Authenticated: delete own post OR admin can delete any post
    public function destroy(Request $request, BlogPost $blogPost)
    {
        $user = $request->user();

        if ($user->id !== $blogPost->user_id && $user->user_type !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $blogPost->delete();

        return response()->json(['message' => 'Post deleted']);
    }
}
