<?php

namespace App\Repositories;

use App\Models\SubscriptionPackage;
use App\Models\Organization;
use App\Repositories\Contracts\SubscriptionRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SubscriptionRepository implements SubscriptionRepositoryInterface
{
    public function getAllActive(): Collection
    {
        return SubscriptionPackage::active()
            ->orderByPrice('asc')
            ->get();
    }

    public function findByName(string $name): ?SubscriptionPackage
    {
        return SubscriptionPackage::byName($name)->first();
    }

    public function getOrganizationUsage(int $organizationId): array
    {
        $organization = Organization::findOrFail($organizationId);

        return [
            'lands' => $organization->lands()->count(),
            'measurements' => $organization->measurements()->count(),
            'jobs' => $organization->fieldJobs()->count(),
            'drivers' => $organization->users()->where('role', 'driver')->count(),
        ];
    }
}
