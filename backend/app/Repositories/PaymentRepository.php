<?php

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository
{
    protected $model;

    public function __construct(Payment $payment)
    {
        $this->model = $payment;
    }

    public function all(): Collection
    {
        return $this->model::all();
    }

    public function find($id): ?Payment
    {
        return $this->model::find($id);
    }

    public function create(array $data): Payment
    {
        return $this->model::create($data);
    }

    public function update($id, array $data): bool
    {
        $payment = $this->find($id);
        return $payment ? $payment->update($data) : false;
    }

    public function delete($id): bool
    {
        $payment = $this->find($id);
        return $payment ? $payment->delete() : false;
    }
}