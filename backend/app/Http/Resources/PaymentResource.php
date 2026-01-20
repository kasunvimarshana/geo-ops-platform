<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Payment API Resource
 *
 * Transforms a Payment model into a JSON response.
 */
class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'invoice_id' => $this->invoice_id,
            'customer_id' => $this->customer_id,
            'payment_number' => $this->payment_number,
            'payment_method' => $this->payment_method,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'payment_date' => $this->payment_date?->toISOString(),
            'reference_number' => $this->reference_number,
            'notes' => $this->notes,
            'receipt_path' => $this->receipt_path,
            'is_synced' => $this->is_synced,
            'invoice' => $this->whenLoaded('invoice', function () {
                return [
                    'id' => $this->invoice->id,
                    'invoice_number' => $this->invoice->invoice_number,
                    'status' => $this->invoice->status,
                    'total_amount' => $this->invoice->total_amount,
                    'balance_amount' => $this->invoice->balance_amount,
                ];
            }),
            'customer' => $this->whenLoaded('customer', function () {
                return [
                    'id' => $this->customer->id,
                    'name' => $this->customer->full_name,
                    'email' => $this->customer->email,
                    'phone' => $this->customer->phone,
                ];
            }),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ];
    }
}
