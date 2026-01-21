<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPostImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'blog_post_id',
        'image_url',
    ];

    /**
     * Each image belongs to one blog post.
     */
    public function post()
    {
        return $this->belongsTo(BlogPost::class, 'blog_post_id');
    }
}
