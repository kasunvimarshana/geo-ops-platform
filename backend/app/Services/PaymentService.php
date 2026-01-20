<?php

namespace App\Services;

use App\Repositories\Contracts\PaymentRepositoryInterface;
use App\Repositories\Contracts\InvoiceRepositoryInterface;
use App\DTOs\Payment\CreatePaymentDTO;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

/**
 * Payment Service
 * 
 * Handles all business logic related to payment management.
 */
class PaymentService
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private InvoiceRepositoryInterface $invoiceRepository
    ) {}

    /**
     * Record a new payment
     */
    public function recordPayment(CreatePaymentDTO $dto): Payment
    {
        return DB::transaction(function () use ($dto) {
            $user = Auth::user();
            
            // Validate invoice exists and belongs to organization
            $invoice = $this->invoiceRepository->findById($dto->invoiceId);
            if ($invoice->organization_id !== $user->organization_id) {
                throw new \Exception('Invoice does not belong to your organization');
            }

            // Validate payment amount
            if ($dto->amount <= 0) {
                throw new \Exception('Payment amount must be greater than zero');
            }

            // Validate: can't pay more than remaining balance
            if ($dto->amount > $invoice->balance_amount) {
                throw new \Exception('Payment amount cannot exceed invoice balance');
            }

            // Validate invoice is not cancelled
            if ($invoice->status === 'cancelled') {
                throw new \Exception('Cannot add payment to cancelled invoice');
            }

            // Generate unique payment number
            $paymentNumber = $this->paymentRepository->generatePaymentNumber($user->organization_id);
            
            // Prepare payment data
            $paymentData = [
                'organization_id' => $user->organization_id,
                'invoice_id' => $dto->invoiceId,
                'customer_id' => $invoice->customer_id,
                'payment_number' => $paymentNumber,
                'payment_method' => $dto->paymentMethod,
                'amount' => $dto->amount,
                'currency' => $dto->currency,
                'payment_date' => $dto->paymentDate ?? Carbon::now()->toDateString(),
                'reference_number' => $dto->referenceNumber,
                'notes' => $dto->notes,
                'is_synced' => false,
                'created_by' => $user->id,
            ];
            
            $payment = $this->paymentRepository->create($paymentData);

            // Update invoice balance
            $this->invoiceRepository->updateBalance($invoice->id, $dto->amount);
            
            return $payment;
        });
    }

    /**
     * Get all payments for the current organization
     */
    public function getPayments(array $filters = [])
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;
        
        return $this->paymentRepository->findByOrganization($organizationId, $filters);
    }

    /**
     * Get a specific payment by ID
     */
    public function getPayment(int $paymentId): Payment
    {
        $user = Auth::user();
        $payment = $this->paymentRepository->findById($paymentId);
        
        if ($payment->organization_id !== $user->organization_id) {
            throw new \Exception('Unauthorized access to payment');
        }
        
        return $payment;
    }

    /**
     * Delete a payment (soft delete and revert invoice balance)
     */
    public function deletePayment(int $paymentId): bool
    {
        return DB::transaction(function () use ($paymentId) {
            $user = Auth::user();
            $payment = $this->paymentRepository->findById($paymentId);
            
            if ($payment->organization_id !== $user->organization_id) {
                throw new \Exception('Unauthorized access to payment');
            }

            // Revert invoice balance
            $invoice = $this->invoiceRepository->findById($payment->invoice_id);
            $newPaidAmount = $invoice->paid_amount - $payment->amount;
            $newBalance = $invoice->total_amount - $newPaidAmount;
            
            $this->invoiceRepository->update($invoice->id, [
                'paid_amount' => $newPaidAmount,
                'balance_amount' => $newBalance,
                'status' => $newBalance > 0 ? 'pending' : 'paid',
            ]);

            // Delete payment
            $result = $this->paymentRepository->delete($payment->id);
            
            return $result;
        });
    }

    /**
     * Get payments with pagination
     */
    public function getPaymentsPaginated(array $filters = [], int $perPage = 15)
    {
        $user = Auth::user();
        $organizationId = $user->organization_id;
        
        return $this->paymentRepository->paginateByOrganization($organizationId, $filters, $perPage);
    }
}
