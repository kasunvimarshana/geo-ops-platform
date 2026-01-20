<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Land API Resource
 *
 * Transforms a Land model into a JSON response.
 */
class LandResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'owner_user_id' => $this->owner_user_id,
            'name' => $this->name,
            'description' => $this->description,
            'coordinates' => $this->coordinates,
            'area' => [
                'acres' => $this->area_acres,
                'hectares' => $this->area_hectares,
                'square_meters' => $this->area_square_meters,
            ],
            'center' => [
                'latitude' => $this->center_latitude,
                'longitude' => $this->center_longitude,
            ],
            'location' => [
                'address' => $this->location_address,
                'district' => $this->location_district,
                'province' => $this->location_province,
            ],
            'status' => $this->status,
            'metadata' => $this->metadata,
            'owner' => $this->whenLoaded('owner', function () {
                return [
                    'id' => $this->owner->id,
                    'name' => $this->owner->full_name,
                    'email' => $this->owner->email,
                ];
            }),
            'measurements_count' => $this->when(isset($this->measurements_count), $this->measurements_count),
            'measurements' => MeasurementResource::collection($this->whenLoaded('measurements')),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ];
    }
}
