<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsageStatsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'subscription' => [
                'package_tier' => $this['package_tier'],
                'package_expires_at' => $this['package_expires_at'],
                'is_expired' => $this['is_expired'],
            ],
            'package_limits' => $this['package_limits'],
            'current_usage' => $this['current_usage'],
            'usage_percentages' => $this['usage_percentages'],
            'warnings' => $this['warnings'],
        ];
    }
}
