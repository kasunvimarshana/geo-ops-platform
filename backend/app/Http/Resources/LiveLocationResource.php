<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Live Location Resource
 *
 * Transforms live location data into a JSON response.
 */
class LiveLocationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->full_name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
            ],
            'job_id' => $this->job_id,
            'job' => $this->when($this->job_id, function () {
                return [
                    'id' => $this->job->id,
                    'job_number' => $this->job->job_number,
                    'service_type' => $this->job->service_type,
                    'status' => $this->job->status,
                ];
            }),
            'location' => [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'accuracy_meters' => $this->accuracy_meters,
            ],
            'movement' => [
                'speed_mps' => $this->speed_mps,
                'speed_kmh' => $this->speed_kmh,
                'heading_degrees' => $this->heading_degrees,
            ],
            'last_update' => $this->recorded_at->toISOString(),
            'platform' => $this->platform,
            'is_active' => true,
        ];
    }
}
