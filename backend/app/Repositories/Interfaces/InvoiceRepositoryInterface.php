<?php

namespace App\Repositories\Interfaces;

interface InvoiceRepositoryInterface
{
    public function create(array $data): object;
    public function findById(int $id): ?object;
    public function findByIdAndOrganization(int $id, int $organizationId): ?object;
    public function findByOrganization(int $organizationId, array $filters = []): object;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findByOfflineId(string $offlineId, int $organizationId): ?object;
    public function getPendingSync(int $organizationId): array;
    public function getNextInvoiceNumber(int $organizationId): string;
    public function findUnpaid(int $organizationId): array;
}
