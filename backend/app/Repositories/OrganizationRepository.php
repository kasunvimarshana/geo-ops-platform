<?php

namespace App\Repositories;

use App\Models\Organization;
use App\Repositories\Interfaces\OrganizationRepositoryInterface;

class OrganizationRepository implements OrganizationRepositoryInterface
{
    public function create(array $data): object
    {
        return Organization::create($data);
    }

    public function findById(int $id): ?object
    {
        return Organization::with(['subscriptionLimit'])->find($id);
    }

    public function findBySlug(string $slug): ?object
    {
        return Organization::with(['subscriptionLimit'])->where('slug', $slug)->first();
    }

    public function update(int $id, array $data): bool
    {
        $organization = Organization::find($id);
        return $organization ? $organization->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $organization = Organization::find($id);
        return $organization ? $organization->delete() : false;
    }

    public function findActive(): array
    {
        return Organization::where('status', 'active')
            ->where('subscription_expires_at', '>', now())
            ->get()
            ->toArray();
    }
}
