<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use App\Http\Resources\BlogPostResource;
use Illuminate\Support\Facades\Auth;

class BlogPostController extends Controller
{
    /**
     * Display a list of published posts (public).
     */
    public function latest()
    {
        $posts = BlogPost::with('user:id,username')
            ->where('status', 'published')
            ->latest()
            ->take(6)
            ->get();

        return response()->json([
            'data' => BlogPostResource::collection($posts)->resolve()
        ]);
    }

    /**
     * Display all posts with pagination (public).
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);

        $posts = BlogPost::with('user:id,username')
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'data' => BlogPostResource::collection($posts)->resolve(),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page'    => $posts->lastPage(),
                'per_page'     => $posts->perPage(),
                'total'        => $posts->total(),
            ],
        ]);
    }

    /**
     * Display a single post (public).
     */
    public function show(BlogPost $blogPost)
    {
        // Only show published posts to public
        if ($blogPost->status !== 'published' && 
            Auth::id() !== $blogPost->user_id && 
            Auth::user()?->user_type !== 'admin') 
        {
            return response()->json(['message' => 'Not available'], 403);
        }

        return new BlogPostResource($blogPost->load('user:id,username'));
    }

    /**
     * Store a newly created post (auth required).
     */
    public function store(Request $request)
    {
        $user = $request->user();

        // Only normal users or admins can create posts
        if (!in_array($user->user_type, ['user', 'admin'])) {
            return response()->json(['message' => 'Only registered users can add blog posts'], 403);
        }

        $data = $request->validate([
            'title'             => ['required', 'string', 'max:255'],
            'short_description' => ['required', 'string', 'max:500'],
            'content'           => ['required', 'string'],
            'image'             => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'status'            => ['nullable', 'string', 'in:pending,published,unpublished'],
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('blog_posts', 'public');
        }

        $post = BlogPost::create([
            'user_id'            => $user->id,
            'title'              => $data['title'],
            'short_description'  => $data['short_description'],
            'content'            => $data['content'],
            'image_url'          => $imagePath,
            'status'             => $data['status'] ?? 'pending',
        ]);

        // Load user relation so frontend can display username
        return response()->json($post->load('user'), 201);
    }

    /**
     * Update a post (owner or admin only).
     */
    public function update(Request $request, BlogPost $blogPost)
    {
        $user = $request->user();

        if ($user->id !== $blogPost->user_id && $user->user_type !== 'admin') {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validate([
            'title'             => ['sometimes','string','max:255'],
            'short_description' => ['sometimes','string','max:500'],
            'content'           => ['sometimes','string'],
            'status'            => ['sometimes','in:pending,published,unpublished'],
            'image'             => ['sometimes','nullable','image','mimes:jpeg,png,jpg,gif','max:2048'],
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('blog_posts', 'public');
            $data['image_url'] = $imagePath;
        }

        // Only admin can change status
        if (isset($data['status']) && $user->user_type !== 'admin') {
            unset($data['status']);
        }

        $blogPost->update($data);

        return response()->json($blogPost->load('user'));
    }

    /**
     * Delete a post (owner or admin only).
     */
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
