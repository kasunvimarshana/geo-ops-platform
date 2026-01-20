<?php

namespace App\DTOs;

class PaymentDTO
{
    public float $amount;
    public string $paymentMethod;
    public string $transactionId;
    public string $status;
    public string $createdAt;

    public function __construct(float $amount, string $paymentMethod, string $transactionId, string $status, string $createdAt)
    {
        $this->amount = $amount;
        $this->paymentMethod = $paymentMethod;
        $this->transactionId = $transactionId;
        $this->status = $status;
        $this->createdAt = $createdAt;
    }
}