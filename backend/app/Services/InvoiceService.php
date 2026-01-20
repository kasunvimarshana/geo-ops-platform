<?php

namespace App\Services;

use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use App\Repositories\Interfaces\JobRepositoryInterface;
use App\Repositories\Interfaces\PaymentRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    public function __construct(
        private InvoiceRepositoryInterface $invoiceRepository,
        private JobRepositoryInterface $jobRepository,
        private PaymentRepositoryInterface $paymentRepository
    ) {}

    public function createInvoice(array $data, int $organizationId): object
    {
        DB::beginTransaction();
        
        try {
            if (isset($data['job_id'])) {
                $job = $this->jobRepository->findByIdAndOrganization($data['job_id'], $organizationId);
                
                if ($job && $job->land) {
                    $data['land_id'] = $job->land_id;
                    $data['area_acres'] = $job->land->area_acres;
                    $data['area_hectares'] = $job->land->area_hectares;
                }
            }

            $invoiceData = array_merge($data, [
                'organization_id' => $organizationId,
                'invoice_number' => $this->invoiceRepository->getNextInvoiceNumber($organizationId),
                'status' => 'draft',
                'paid_amount' => 0,
                'balance' => $data['total_amount'],
                'sync_status' => 'synced',
            ]);

            $invoice = $this->invoiceRepository->create($invoiceData);

            DB::commit();
            return $invoice;
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateInvoice(int $id, array $data, int $organizationId): object
    {
        $invoice = $this->invoiceRepository->findByIdAndOrganization($id, $organizationId);
        
        if (!$invoice) {
            throw new \Exception('Invoice not found');
        }

        if (isset($data['total_amount'])) {
            $data['balance'] = $data['total_amount'] - $invoice->paid_amount;
        }

        $this->invoiceRepository->update($id, $data);
        
        return $this->invoiceRepository->findById($id);
    }

    public function generatePDF(int $id, int $organizationId): string
    {
        $invoice = $this->invoiceRepository->findByIdAndOrganization($id, $organizationId);
        
        if (!$invoice) {
            throw new \Exception('Invoice not found');
        }

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'organization' => $invoice->organization,
        ]);

        $filename = 'invoices/' . $invoice->invoice_number . '.pdf';
        Storage::put($filename, $pdf->output());

        $this->invoiceRepository->update($id, ['pdf_path' => $filename]);

        return Storage::url($filename);
    }

    public function markAsPrinted(int $id, int $organizationId): object
    {
        $invoice = $this->invoiceRepository->findByIdAndOrganization($id, $organizationId);
        
        if (!$invoice) {
            throw new \Exception('Invoice not found');
        }

        $this->invoiceRepository->update($id, [
            'printed_at' => now(),
            'status' => 'sent',
        ]);

        return $this->invoiceRepository->findById($id);
    }

    public function recordPayment(int $invoiceId, array $paymentData, int $organizationId, int $userId): object
    {
        $invoice = $this->invoiceRepository->findByIdAndOrganization($invoiceId, $organizationId);
        
        if (!$invoice) {
            throw new \Exception('Invoice not found');
        }

        DB::beginTransaction();
        
        try {
            $payment = $this->paymentRepository->create(array_merge($paymentData, [
                'organization_id' => $organizationId,
                'invoice_id' => $invoiceId,
                'received_by' => $userId,
                'sync_status' => 'synced',
            ]));

            $newPaidAmount = $invoice->paid_amount + $paymentData['amount'];
            $newBalance = $invoice->total_amount - $newPaidAmount;
            
            $status = $newBalance <= 0 ? 'paid' : 'partial';

            $this->invoiceRepository->update($invoiceId, [
                'paid_amount' => $newPaidAmount,
                'balance' => $newBalance,
                'status' => $status,
            ]);

            DB::commit();
            
            return $this->invoiceRepository->findById($invoiceId);
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function deleteInvoice(int $id, int $organizationId): bool
    {
        $invoice = $this->invoiceRepository->findByIdAndOrganization($id, $organizationId);
        
        if (!$invoice) {
            throw new \Exception('Invoice not found');
        }

        if ($invoice->paid_amount > 0) {
            throw new \Exception('Cannot delete invoice with payments');
        }

        return $this->invoiceRepository->delete($id);
    }
}
