<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'short_description',
        'content',
        'status',
    ];

    protected $with = ['images']; // Auto-load images when fetching posts

    /**
     * A blog post belongs to a user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * A blog post can have many images.
     */
    public function images()
    {
        return $this->hasMany(BlogPostImage::class);
    }
}
