<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SubscriptionPackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'display_name' => $this->display_name,
            'description' => $this->description,
            'limits' => [
                'measurements' => $this->max_measurements,
                'drivers' => $this->max_drivers,
                'jobs' => $this->max_jobs,
                'lands' => $this->max_lands,
                'storage_mb' => $this->max_storage_mb,
            ],
            'pricing' => [
                'monthly' => (float) $this->price_monthly,
                'yearly' => $this->price_yearly ? (float) $this->price_yearly : null,
                'yearly_savings' => $this->yearly_savings,
                'yearly_savings_percentage' => $this->yearly_savings_percentage,
            ],
            'features' => $this->features ?? [],
            'is_active' => $this->is_active,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }
}
