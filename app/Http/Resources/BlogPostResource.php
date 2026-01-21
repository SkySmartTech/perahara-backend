<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'user_id'           => $this->user_id,
            'title'             => $this->title,
            'short_description' => $this->short_description,
            'content'           => $this->content,
            'status'            => $this->status,
            'created_at'        => $this->created_at->toDateTimeString(),
            'updated_at'        => $this->updated_at->toDateTimeString(),

            // ✅ All related images
            'images' => $this->whenLoaded('images', function () {
                return $this->images->map(function ($image) {
                    return [
                        'id'        => $image->id,
                        'image_url' => asset('storage/' . $image->image_url),
                    ];
                });
            }),

            // ✅ Limited user info
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id'       => $this->user->id,
                    'username' => $this->user->username,
                ];
            }),
        ];
    }
}
