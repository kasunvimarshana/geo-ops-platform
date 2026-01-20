<?php

declare(strict_types=1);

namespace App\Domain\Repositories;

use App\Models\Invoice;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InvoiceRepositoryInterface
{
    public function findById(int $id): ?Invoice;

    public function findByInvoiceNumber(string $invoiceNumber): ?Invoice;

    public function paginateByOrganization(int $organizationId, int $perPage = 15): LengthAwarePaginator;

    public function create(array $data): Invoice;

    public function update(int $id, array $data): Invoice;

    public function generateInvoiceNumber(): string;
}
