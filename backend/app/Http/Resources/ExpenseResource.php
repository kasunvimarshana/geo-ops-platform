<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Expense API Resource
 *
 * Transforms an Expense model into a JSON response.
 */
class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'organization_id' => $this->organization_id,
            'job_id' => $this->job_id,
            'driver_id' => $this->driver_id,
            'expense_number' => $this->expense_number,
            'category' => $this->category,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'expense_date' => $this->expense_date->toISOString(),
            'vendor_name' => $this->vendor_name,
            'description' => $this->description,
            'receipt_path' => $this->receipt_path,
            'attachments' => $this->attachments,
            'is_synced' => $this->is_synced,
            'job' => $this->whenLoaded('job', function () {
                return [
                    'id' => $this->job->id,
                    'job_number' => $this->job->job_number,
                    'service_type' => $this->job->service_type,
                    'status' => $this->job->status,
                ];
            }),
            'driver' => $this->whenLoaded('driver', function () {
                return [
                    'id' => $this->driver->id,
                    'name' => $this->driver->full_name,
                    'email' => $this->driver->email,
                    'phone' => $this->driver->phone,
                ];
            }),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ];
    }
}
