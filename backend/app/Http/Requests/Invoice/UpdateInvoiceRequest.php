<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_name' => ['sometimes', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'customer_address' => ['nullable', 'string', 'max:500'],
            'invoice_date' => ['sometimes', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:invoice_date'],
            'line_items' => ['sometimes', 'array', 'min:1'],
            'line_items.*.description' => ['required_with:line_items', 'string', 'max:500'],
            'line_items.*.quantity' => ['required_with:line_items', 'numeric', 'min:0'],
            'line_items.*.unit' => ['required_with:line_items', 'string', 'max:50'],
            'line_items.*.rate' => ['required_with:line_items', 'numeric', 'min:0'],
            'line_items.*.amount' => ['required_with:line_items', 'numeric', 'min:0'],
            'tax_amount' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'discount_amount' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'currency' => ['sometimes', 'string', 'in:USD,EUR,GBP,KES'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'terms' => ['nullable', 'string', 'max:1000'],
            'status' => ['sometimes', 'string', 'in:draft,pending,paid,overdue,cancelled'],
        ];
    }

    public function messages(): array
    {
        return [
            'due_date.after_or_equal' => 'Due date must be on or after invoice date.',
            'line_items.*.description.required_with' => 'Line item description is required.',
            'line_items.*.quantity.required_with' => 'Line item quantity is required.',
            'line_items.*.rate.required_with' => 'Line item rate is required.',
            'line_items.*.amount.required_with' => 'Line item amount is required.',
        ];
    }
}
