<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'short_description',
        'content',
        'image_url',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
