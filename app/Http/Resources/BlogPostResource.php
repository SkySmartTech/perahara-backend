<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BlogPostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'               => $this->id,
            'user_id'          => $this->user_id,
            'title'            => $this->title,
            'short_description'=> $this->short_description,
            'content'          => $this->content,
            'image_url'        => $this->image_url,
            'status'           => $this->status,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
            // only return limited user info
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id'       => $this->user->id,
                    'username' => $this->user->username,
                ];
            }),
        ];
    }
}
