<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Tracking Log API Resource
 *
 * Transforms a TrackingLog model into a JSON response.
 */
class TrackingLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'user_id' => $this->user_id,
            'job_id' => $this->job_id,
            'location' => [
                'latitude' => $this->latitude,
                'longitude' => $this->longitude,
                'accuracy_meters' => $this->accuracy_meters,
                'altitude_meters' => $this->altitude_meters,
            ],
            'movement' => [
                'speed_mps' => $this->speed_mps,
                'speed_kmh' => $this->speed_kmh,
                'heading_degrees' => $this->heading_degrees,
            ],
            'recorded_at' => $this->recorded_at->toISOString(),
            'device_id' => $this->device_id,
            'platform' => $this->platform,
            'metadata' => $this->metadata,
            'is_synced' => $this->is_synced,
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->full_name,
                    'email' => $this->user->email,
                ];
            }),
            'job' => $this->whenLoaded('job', function () {
                return [
                    'id' => $this->job->id,
                    'job_number' => $this->job->job_number,
                    'service_type' => $this->job->service_type,
                ];
            }),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
