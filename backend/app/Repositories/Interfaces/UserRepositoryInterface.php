<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    public function create(array $data): object;
    public function findById(int $id): ?object;
    public function findByIdAndOrganization(int $id, int $organizationId): ?object;
    public function findByOrganization(int $organizationId, array $filters = []): object;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findByEmail(string $email): ?object;
    public function findByPhone(string $phone): ?object;
    public function findDrivers(int $organizationId): array;
}
