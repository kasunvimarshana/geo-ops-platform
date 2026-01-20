<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Services\InvoiceService;
use App\Repositories\Interfaces\InvoiceRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceService $invoiceService,
        private InvoiceRepositoryInterface $invoiceRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        try {
            $organizationId = $request->user()->organization_id;
            
            $invoices = $this->invoiceRepository->findByOrganization($organizationId, [
                'status' => $request->status,
                'search' => $request->search,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'per_page' => $request->per_page ?? 15,
            ]);

            return response()->json([
                'success' => true,
                'data' => $invoices->items(),
                'meta' => [
                    'pagination' => [
                        'total' => $invoices->total(),
                        'per_page' => $invoices->perPage(),
                        'current_page' => $invoices->currentPage(),
                        'last_page' => $invoices->lastPage(),
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch invoices',
            ], 500);
        }
    }

    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->createInvoice(
                $request->validated(),
                $request->user()->organization_id
            );

            return response()->json([
                'success' => true,
                'data' => $invoice,
                'message' => 'Invoice created successfully',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show(int $id, Request $request): JsonResponse
    {
        try {
            $invoice = $this->invoiceRepository->findByIdAndOrganization(
                $id,
                $request->user()->organization_id
            );

            if (!$invoice) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $invoice,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch invoice',
            ], 500);
        }
    }

    public function update(int $id, UpdateInvoiceRequest $request): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->updateInvoice(
                $id,
                $request->validated(),
                $request->user()->organization_id
            );

            return response()->json([
                'success' => true,
                'data' => $invoice,
                'message' => 'Invoice updated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $id, Request $request): JsonResponse
    {
        try {
            $this->invoiceService->deleteInvoice($id, $request->user()->organization_id);

            return response()->json([
                'success' => true,
                'message' => 'Invoice deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function generatePdf(int $id, Request $request): JsonResponse
    {
        try {
            $pdfUrl = $this->invoiceService->generatePDF($id, $request->user()->organization_id);

            return response()->json([
                'success' => true,
                'data' => ['pdf_url' => $pdfUrl],
                'message' => 'PDF generated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function markAsPrinted(int $id, Request $request): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->markAsPrinted($id, $request->user()->organization_id);

            return response()->json([
                'success' => true,
                'data' => $invoice,
                'message' => 'Invoice marked as printed',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function recordPayment(int $id, StorePaymentRequest $request): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->recordPayment(
                $id,
                $request->validated(),
                $request->user()->organization_id,
                $request->user()->id
            );

            return response()->json([
                'success' => true,
                'data' => $invoice,
                'message' => 'Payment recorded successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
