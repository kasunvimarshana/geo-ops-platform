<?php

namespace App\DTOs;

class InvoiceDTO
{
    public int $id;
    public int $customerId;
    public float $amount;
    public string $status;
    public string $createdAt;
    public string $updatedAt;

    public function __construct(int $id, int $customerId, float $amount, string $status, string $createdAt, string $updatedAt)
    {
        $this->id = $id;
        $this->customerId = $customerId;
        $this->amount = $amount;
        $this->status = $status;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }
}