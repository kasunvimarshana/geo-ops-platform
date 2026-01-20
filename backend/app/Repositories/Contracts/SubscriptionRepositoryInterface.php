<?php

namespace App\Repositories\Contracts;

use App\Models\SubscriptionPackage;
use Illuminate\Support\Collection;

interface SubscriptionRepositoryInterface
{
    public function getAllActive(): Collection;
    
    public function findByName(string $name): ?SubscriptionPackage;
    
    public function getOrganizationUsage(int $organizationId): array;
}
