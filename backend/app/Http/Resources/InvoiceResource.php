<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Invoice API Resource
 *
 * Transforms an Invoice model into a JSON response.
 */
class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'job_id' => $this->job_id,
            'customer_id' => $this->customer_id,
            'invoice_number' => $this->invoice_number,
            'status' => $this->status,
            'customer' => [
                'name' => $this->customer_name,
                'phone' => $this->customer_phone,
                'address' => $this->customer_address,
            ],
            'dates' => [
                'invoice_date' => $this->invoice_date?->toISOString(),
                'due_date' => $this->due_date?->toISOString(),
            ],
            'line_items' => $this->line_items,
            'amounts' => [
                'subtotal' => $this->subtotal,
                'tax_amount' => $this->tax_amount,
                'discount_amount' => $this->discount_amount,
                'total_amount' => $this->total_amount,
                'paid_amount' => $this->paid_amount,
                'balance_amount' => $this->balance_amount,
            ],
            'currency' => $this->currency,
            'notes' => $this->notes,
            'terms' => $this->terms,
            'pdf' => [
                'path' => $this->pdf_path,
                'generated_at' => $this->pdf_generated_at?->toISOString(),
            ],
            'is_synced' => $this->is_synced,
            'job' => $this->whenLoaded('job', function () {
                return [
                    'id' => $this->job->id,
                    'job_number' => $this->job->job_number,
                    'service_type' => $this->job->service_type,
                    'status' => $this->job->status,
                ];
            }),
            'customer_user' => $this->whenLoaded('customer', function () {
                return [
                    'id' => $this->customer->id,
                    'name' => $this->customer->full_name,
                    'email' => $this->customer->email,
                    'phone' => $this->customer->phone,
                ];
            }),
            'payments' => $this->whenLoaded('payments', function () {
                return PaymentResource::collection($this->payments);
            }),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ];
    }
}
