<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Models\Organization;
use Illuminate\Database\Eloquent\Collection;

interface OrganizationRepositoryInterface
{
    public function findById(int $id): ?Organization;

    public function findByStatus(string $status): Collection;

    public function create(array $data): Organization;

    public function update(int $id, array $data): Organization;

    public function delete(int $id): bool;
}
