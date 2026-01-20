<?php

namespace App\Services;

use App\Models\Payment;
use App\Repositories\PaymentRepository;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected $paymentRepository;

    public function __construct(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function createPayment(array $data): Payment
    {
        DB::beginTransaction();
        try {
            $payment = $this->paymentRepository->create($data);
            DB::commit();
            return $payment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getPaymentById(int $id): ?Payment
    {
        return $this->paymentRepository->find($id);
    }

    public function updatePayment(int $id, array $data): bool
    {
        return $this->paymentRepository->update($id, $data);
    }

    public function deletePayment(int $id): bool
    {
        return $this->paymentRepository->delete($id);
    }

    public function getAllPayments(): array
    {
        return $this->paymentRepository->all();
    }
}