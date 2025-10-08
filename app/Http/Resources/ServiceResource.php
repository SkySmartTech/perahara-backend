<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $baseUrl = config('app.url') . '/storage';

        return [
            'id' => $this->id,
            'name' => $this->name,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'location' => $this->location,
            'phone' => $this->phone,
            'price' => $this->price,
            'status' => $this->status,
            'image' => $this->image ? $baseUrl . '/' . $this->image : null,
            'service_type' => $this->whenLoaded('serviceType', function () {
                return [
                    'id' => $this->serviceType->id,
                    'name' => $this->serviceType->name,
                ];
            }),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                    'user_type' => $this->user->user_type,
                ];
            }),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
