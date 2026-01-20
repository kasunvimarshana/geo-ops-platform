<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    /**
     * Record a payment
     */
    public function recordPayment(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $payment = Payment::create([
                'organization_id' => $data['organization_id'],
                'customer_id' => $data['customer_id'],
                'invoice_id' => $data['invoice_id'] ?? null,
                'amount' => $data['amount'],
                'method' => $data['method'],
                'reference' => $data['reference'] ?? null,
                'notes' => $data['notes'] ?? null,
                'paid_at' => $data['paid_at'] ?? now(),
                'recorded_by' => $data['recorded_by'] ?? null,
            ]);
            
            // Update invoice status if provided
            if (isset($data['invoice_id'])) {
                $invoice = Invoice::find($data['invoice_id']);
                if ($invoice) {
                    $this->updateInvoiceStatus($invoice);
                }
            }
            
            // Update customer balance
            $this->updateCustomerBalance($payment->customer_id);
            
            return $payment;
        });
    }
    
    /**
     * Update payment
     */
    public function update(Payment $payment, array $data): Payment
    {
        return DB::transaction(function () use ($payment, $data) {
            $oldInvoiceId = $payment->invoice_id;
            
            $payment->update([
                'amount' => $data['amount'] ?? $payment->amount,
                'method' => $data['method'] ?? $payment->method,
                'reference' => $data['reference'] ?? $payment->reference,
                'notes' => $data['notes'] ?? $payment->notes,
                'paid_at' => $data['paid_at'] ?? $payment->paid_at,
            ]);
            
            // Update invoice statuses
            if ($oldInvoiceId && isset($data['invoice_id']) && $oldInvoiceId != $data['invoice_id']) {
                $oldInvoice = Invoice::find($oldInvoiceId);
                if ($oldInvoice) {
                    $this->updateInvoiceStatus($oldInvoice);
                }
            }
            
            if (isset($data['invoice_id'])) {
                $invoice = Invoice::find($data['invoice_id']);
                if ($invoice) {
                    $this->updateInvoiceStatus($invoice);
                }
            }
            
            // Update customer balance
            $this->updateCustomerBalance($payment->customer_id);
            
            return $payment->fresh();
        });
    }
    
    /**
     * Delete payment
     */
    public function delete(Payment $payment): bool
    {
        return DB::transaction(function () use ($payment) {
            $invoiceId = $payment->invoice_id;
            $customerId = $payment->customer_id;
            
            $payment->delete();
            
            // Update invoice status
            if ($invoiceId) {
                $invoice = Invoice::find($invoiceId);
                if ($invoice) {
                    $this->updateInvoiceStatus($invoice);
                }
            }
            
            // Update customer balance
            $this->updateCustomerBalance($customerId);
            
            return true;
        });
    }
    
    /**
     * Update invoice status based on payments
     */
    private function updateInvoiceStatus(Invoice $invoice): void
    {
        $totalPaid = $invoice->payments()->sum('amount');
        
        if ($totalPaid >= $invoice->total) {
            $invoice->update([
                'status' => Invoice::STATUS_PAID,
                'paid_at' => now(),
            ]);
        } elseif ($totalPaid > 0) {
            $invoice->update([
                'status' => Invoice::STATUS_SENT,
            ]);
        } else {
            // Check if overdue
            if ($invoice->due_at && $invoice->due_at->isPast()) {
                $invoice->update([
                    'status' => Invoice::STATUS_OVERDUE,
                ]);
            }
        }
    }
    
    /**
     * Update customer balance
     */
    private function updateCustomerBalance(int $customerId): void
    {
        $customer = Customer::find($customerId);
        if (!$customer) {
            return;
        }
        
        // Calculate total invoiced (exclude only cancelled invoices)
        $totalInvoiced = Invoice::where('customer_id', $customerId)
            ->where('status', '!=', Invoice::STATUS_CANCELLED)
            ->sum('total');
        
        // Calculate total paid
        $totalPaid = Payment::where('customer_id', $customerId)
            ->sum('amount');
        
        $balance = $totalInvoiced - $totalPaid;
        
        $customer->update(['balance' => $balance]);
    }
    
    /**
     * Get payment summary for organization
     */
    public function getSummary(int $organizationId, ?string $period = 'all'): array
    {
        $query = Payment::where('organization_id', $organizationId);
        
        // Apply period filter
        switch ($period) {
            case 'today':
                $query->whereDate('paid_at', today());
                break;
            case 'this_week':
                $query->whereBetween('paid_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth('paid_at', now()->month)
                      ->whereYear('paid_at', now()->year);
                break;
            case 'this_year':
                $query->whereYear('paid_at', now()->year);
                break;
        }
        
        $payments = $query->get();
        
        return [
            'total_count' => $payments->count(),
            'total_amount' => $payments->sum('amount'),
            'by_method' => [
                'cash' => $payments->where('method', Payment::METHOD_CASH)->sum('amount'),
                'bank' => $payments->where('method', Payment::METHOD_BANK)->sum('amount'),
                'mobile' => $payments->where('method', Payment::METHOD_MOBILE)->sum('amount'),
                'credit' => $payments->where('method', Payment::METHOD_CREDIT)->sum('amount'),
            ],
            'period' => $period,
        ];
    }
    
    /**
     * Get customer payment history
     */
    public function getCustomerHistory(int $customerId, int $organizationId): array
    {
        $payments = Payment::where('customer_id', $customerId)
            ->where('organization_id', $organizationId)
            ->with('invoice')
            ->orderBy('paid_at', 'desc')
            ->get();
        
        $customer = Customer::find($customerId);
        
        return [
            'customer' => $customer,
            'payments' => $payments,
            'total_paid' => $payments->sum('amount'),
            'payment_count' => $payments->count(),
        ];
    }
}
