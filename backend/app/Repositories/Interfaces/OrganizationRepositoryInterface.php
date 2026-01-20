<?php

namespace App\Repositories\Interfaces;

interface OrganizationRepositoryInterface
{
    public function create(array $data): object;
    public function findById(int $id): ?object;
    public function findBySlug(string $slug): ?object;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findActive(): array;
}
