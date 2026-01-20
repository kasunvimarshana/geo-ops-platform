<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Models\FieldJob;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface FieldJobRepositoryInterface
{
    public function findById(int $id): ?FieldJob;

    public function findByOrganization(int $organizationId): Collection;

    public function findByStatus(int $organizationId, string $status): Collection;

    public function paginateByOrganization(int $organizationId, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): FieldJob;

    public function update(int $id, array $data): FieldJob;

    public function delete(int $id): bool;

    public function updateStatus(int $id, string $status): FieldJob;
}
