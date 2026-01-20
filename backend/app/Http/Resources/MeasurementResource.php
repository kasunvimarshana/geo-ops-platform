<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Measurement API Resource
 *
 * Transforms a Measurement model into a JSON response.
 */
class MeasurementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'land_id' => $this->land_id,
            'user_id' => $this->user_id,
            'organization_id' => $this->organization_id,
            'type' => $this->type,
            'coordinates' => $this->coordinates,
            'area' => [
                'acres' => $this->area_acres,
                'hectares' => $this->area_hectares,
                'square_meters' => $this->area_square_meters,
            ],
            'perimeter_meters' => $this->perimeter_meters,
            'center_point' => $this->center_point,
            'point_count' => $this->point_count,
            'accuracy_meters' => $this->accuracy_meters,
            'measurement_times' => [
                'started_at' => $this->measurement_started_at->toISOString(),
                'completed_at' => $this->measurement_completed_at->toISOString(),
                'duration_seconds' => $this->duration_seconds,
                'duration_minutes' => $this->duration_minutes,
            ],
            'notes' => $this->notes,
            'is_synced' => $this->is_synced,
            'device_id' => $this->device_id,
            'land' => $this->whenLoaded('land', function () {
                return [
                    'id' => $this->land->id,
                    'name' => $this->land->name,
                    'status' => $this->land->status,
                ];
            }),
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->full_name,
                    'email' => $this->user->email,
                ];
            }),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ];
    }
}
