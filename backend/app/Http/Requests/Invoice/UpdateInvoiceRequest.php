<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => 'sometimes|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'invoice_date' => 'sometimes|date',
            'due_date' => 'nullable|date|after_or_equal:invoice_date',
            'rate_per_unit' => 'sometimes|numeric|min:0',
            'subtotal' => 'sometimes|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'tax_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'sometimes|numeric|min:0',
            'status' => 'sometimes|in:draft,sent,paid,partial,cancelled',
            'notes' => 'nullable|string',
        ];
    }
}
