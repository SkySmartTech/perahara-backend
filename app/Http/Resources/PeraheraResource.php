<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PeraheraResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'start_date'  => $this->start_date ? $this->start_date->format('Y-m-d') : null,
            'end_date'    => $this->end_date ? $this->end_date->format('Y-m-d') : null,
            'event_time'  => $this->event_time ? $this->event_time->format('H:i') : null,
            'image'       => $this->image,
            'location'    => $this->location,
            'status'      => $this->status,
            // only return limited user info
            'organizer' => $this->whenLoaded('user', function () {
                return [
                    'id'       => $this->user->id,
                    'username' => $this->user->username,
                ];
            }),

        ];
    }
}
