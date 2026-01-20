<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'assigned_to' => new UserResource($this->whenLoaded('user')),
            'land' => new LandResource($this->whenLoaded('land')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}