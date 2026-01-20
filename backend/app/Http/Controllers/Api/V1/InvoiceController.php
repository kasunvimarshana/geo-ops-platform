<?php

namespace App\Http\Controllers\Api\V1;

use App\DTOs\Invoice\CreateInvoiceDTO;
use App\DTOs\Invoice\UpdateInvoiceDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Invoice\StoreInvoiceRequest;
use App\Http\Requests\Invoice\UpdateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Invoice Controller
 *
 * Handles invoice management endpoints.
 */
class InvoiceController extends Controller
{
    public function __construct(
        protected InvoiceService $invoiceService
    ) {}

    /**
     * List invoices with filters and pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = [
                'status' => $request->input('status'),
                'customer_id' => $request->input('customer_id'),
                'job_id' => $request->input('job_id'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
                'search' => $request->input('search'),
                'sort_by' => $request->input('sort_by', 'created_at'),
                'sort_direction' => $request->input('sort_direction', 'desc'),
            ];

            $perPage = $request->input('per_page', 15);
            $invoices = $this->invoiceService->getInvoicesPaginated($filters, $perPage);

            return $this->successResponse(
                InvoiceResource::collection($invoices)->response()->getData(true),
                'Invoices retrieved successfully.'
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve invoices', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve invoices.',
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Create new invoice
     */
    public function store(StoreInvoiceRequest $request): JsonResponse
    {
        try {
            $dto = CreateInvoiceDTO::fromArray($request->validated());
            $invoice = $this->invoiceService->createInvoice($dto);

            return $this->successResponse(
                new InvoiceResource($invoice),
                'Invoice created successfully.',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            \Log::error('Failed to create invoice', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to create invoice: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Get single invoice with details
     */
    public function show(int $id): JsonResponse
    {
        try {
            $invoice = $this->invoiceService->getInvoice($id);
            $invoice->load(['job', 'customer', 'payments']);

            return $this->successResponse(
                new InvoiceResource($invoice),
                'Invoice retrieved successfully.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Invoice not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to retrieve invoice', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to retrieve invoice: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update invoice
     */
    public function update(UpdateInvoiceRequest $request, int $id): JsonResponse
    {
        try {
            $dto = UpdateInvoiceDTO::fromArray($request->validated());
            $invoice = $this->invoiceService->updateInvoice($id, $dto);

            return $this->successResponse(
                new InvoiceResource($invoice),
                'Invoice updated successfully.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Invoice not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to update invoice', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to update invoice: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Soft delete invoice
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->invoiceService->deleteInvoice($id);

            return $this->successResponse(
                null,
                'Invoice deleted successfully.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Invoice not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to delete invoice', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to delete invoice: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Generate and download PDF invoice
     */
    public function generatePDF(int $id): JsonResponse
    {
        try {
            $result = $this->invoiceService->generatePDF($id);

            return $this->successResponse(
                $result,
                'PDF generation initiated.'
            );
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->errorResponse(
                'Invoice not found.',
                Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            \Log::error('Failed to generate PDF', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to generate PDF: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Create invoice from completed job
     */
    public function createFromJob(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'job_id' => ['required', 'integer', 'exists:field_jobs,id'],
            ]);

            $invoice = $this->invoiceService->createInvoiceFromJob($request->input('job_id'));

            return $this->successResponse(
                new InvoiceResource($invoice),
                'Invoice created from job successfully.',
                Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            \Log::error('Failed to create invoice from job', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return $this->errorResponse(
                'Failed to create invoice from job: ' . $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    protected function successResponse(mixed $data, string $message, int $status = Response::HTTP_OK): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function errorResponse(string $message, int $status = Response::HTTP_BAD_REQUEST, ?array $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $status);
    }
}
