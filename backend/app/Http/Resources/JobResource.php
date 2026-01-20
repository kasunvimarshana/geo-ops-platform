<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * FieldJob API Resource
 *
 * Transforms a FieldJob model into a JSON response.
 */
class JobResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'land_id' => $this->land_id,
            'customer_id' => $this->customer_id,
            'driver_id' => $this->driver_id,
            'job_number' => $this->job_number,
            'status' => $this->status,
            'service_type' => $this->service_type,
            'customer' => [
                'name' => $this->customer_name,
                'phone' => $this->customer_phone,
                'address' => $this->customer_address,
            ],
            'location_coordinates' => $this->location_coordinates,
            'area' => [
                'acres' => $this->area_acres,
                'hectares' => $this->area_hectares,
            ],
            'rate' => [
                'per_unit' => $this->rate_per_unit,
                'unit' => $this->rate_unit,
            ],
            'amount' => [
                'estimated' => $this->estimated_amount,
                'actual' => $this->actual_amount,
            ],
            'schedule' => [
                'scheduled_date' => $this->scheduled_date?->toISOString(),
                'started_at' => $this->started_at?->toISOString(),
                'completed_at' => $this->completed_at?->toISOString(),
                'duration_minutes' => $this->duration_minutes,
            ],
            'distance_km' => $this->distance_km,
            'notes' => $this->notes,
            'completion_notes' => $this->completion_notes,
            'attachments' => $this->attachments,
            'is_synced' => $this->is_synced,
            'land' => $this->whenLoaded('land', function () {
                return [
                    'id' => $this->land->id,
                    'name' => $this->land->name,
                    'area_acres' => $this->land->area_acres,
                ];
            }),
            'customer_user' => $this->whenLoaded('customer', function () {
                return [
                    'id' => $this->customer->id,
                    'name' => $this->customer->full_name,
                    'email' => $this->customer->email,
                    'phone' => $this->customer->phone,
                ];
            }),
            'driver' => $this->whenLoaded('driver', function () {
                return [
                    'id' => $this->driver->id,
                    'name' => $this->driver->full_name,
                    'email' => $this->driver->email,
                    'phone' => $this->driver->phone,
                ];
            }),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ];
    }
}
