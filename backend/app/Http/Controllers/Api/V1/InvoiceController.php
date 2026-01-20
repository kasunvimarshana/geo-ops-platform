<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateInvoiceRequest;
use App\Http\Resources\InvoiceResource;
use App\Services\InvoiceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function index(Request $request): JsonResponse
    {
        $invoices = $this->invoiceService->getAllInvoices($request->user());
        return response()->json(InvoiceResource::collection($invoices));
    }

    public function store(CreateInvoiceRequest $request): JsonResponse
    {
        $invoice = $this->invoiceService->createInvoice($request->validated());
        return response()->json(new InvoiceResource($invoice), 201);
    }

    public function show($id): JsonResponse
    {
        $invoice = $this->invoiceService->getInvoiceById($id);
        return response()->json(new InvoiceResource($invoice));
    }

    public function update(CreateInvoiceRequest $request, $id): JsonResponse
    {
        $invoice = $this->invoiceService->updateInvoice($id, $request->validated());
        return response()->json(new InvoiceResource($invoice));
    }

    public function destroy($id): JsonResponse
    {
        $this->invoiceService->deleteInvoice($id);
        return response()->json(null, 204);
    }
}