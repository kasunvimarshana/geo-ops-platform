<?php

namespace App\Repositories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;

class InvoiceRepository
{
    protected $invoiceModel;

    public function __construct(Invoice $invoice)
    {
        $this->invoiceModel = $invoice;
    }

    public function create(array $data): Invoice
    {
        return $this->invoiceModel->create($data);
    }

    public function findById(int $id): ?Invoice
    {
        return $this->invoiceModel->find($id);
    }

    public function all(): Collection
    {
        return $this->invoiceModel->all();
    }

    public function update(int $id, array $data): bool
    {
        $invoice = $this->findById($id);
        return $invoice ? $invoice->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $invoice = $this->findById($id);
        return $invoice ? $invoice->delete() : false;
    }
}