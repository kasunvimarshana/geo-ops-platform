<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Api;

use App\Application\Services\InvoiceService;
use App\Http\Controllers\Controller;
use App\Presentation\Resources\InvoiceResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    public function __construct(
        private readonly InvoiceService $invoiceService
    ) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $organizationId = auth()->user()->organization_id;
        $invoices = $this->invoiceService->getAllByOrganization($organizationId, $request->get('per_page', 15));
        return InvoiceResource::collection($invoices);
    }

    public function show(int $id): JsonResponse
    {
        $invoice = $this->invoiceService->getById($id);
        
        if (!$invoice || $invoice->organization_id !== auth()->user()->organization_id) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        return response()->json(['data' => new InvoiceResource($invoice)]);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'field_job_id' => 'required|exists:field_jobs,id',
            'customer_name' => 'required|string|max:255',
            'subtotal' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'issued_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();
        $data['organization_id'] = auth()->user()->organization_id;

        $invoice = $this->invoiceService->create($data);
        return response()->json(['data' => new InvoiceResource($invoice)], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $invoice = $this->invoiceService->getById($id);
        
        if (!$invoice || $invoice->organization_id !== auth()->user()->organization_id) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        $updated = $this->invoiceService->update($id, $request->all());
        return response()->json(['data' => new InvoiceResource($updated)]);
    }

    public function generatePdf(int $id): JsonResponse
    {
        $invoice = $this->invoiceService->getById($id);
        
        if (!$invoice || $invoice->organization_id !== auth()->user()->organization_id) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        $pdfUrl = $this->invoiceService->generatePdf($id);
        return response()->json(['pdf_url' => $pdfUrl]);
    }

    public function downloadPdf(int $id)
    {
        $invoice = $this->invoiceService->getById($id);
        
        if (!$invoice || $invoice->organization_id !== auth()->user()->organization_id) {
            return response()->json(['error' => 'Invoice not found'], 404);
        }

        return $this->invoiceService->downloadPdf($id);
    }
}
