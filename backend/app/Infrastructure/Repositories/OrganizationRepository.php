<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Repositories\OrganizationRepositoryInterface;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;

class OrganizationRepository implements OrganizationRepositoryInterface
{
    public function findById(int $id): ?Organization
    {
        return Organization::find($id);
    }

    public function findByStatus(string $status): Collection
    {
        return Organization::status($status)->get();
    }

    public function create(array $data): Organization
    {
        return Organization::create($data);
    }

    public function update(int $id, array $data): Organization
    {
        $organization = Organization::findOrFail($id);
        $organization->update($data);
        return $organization->fresh();
    }

    public function delete(int $id): bool
    {
        $organization = Organization::findOrFail($id);
        return $organization->delete();
    }
}
