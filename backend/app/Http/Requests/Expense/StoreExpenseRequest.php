<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'job_id' => ['nullable', 'integer', 'exists:field_jobs,id'],
            'driver_id' => ['nullable', 'integer', 'exists:users,id'],
            'category' => ['required', 'string', 'in:fuel,maintenance,parts,salary,transport,food,other'],
            'amount' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'currency' => ['nullable', 'string', 'in:USD,EUR,GBP,KES'],
            'expense_date' => ['required', 'date'],
            'vendor_name' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'receipt_path' => ['nullable', 'string', 'max:500'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'category.required' => 'Expense category is required.',
            'category.in' => 'Invalid expense category. Must be one of: fuel, maintenance, parts, salary, transport, food, other.',
            'amount.required' => 'Expense amount is required.',
            'amount.min' => 'Expense amount must be greater than or equal to 0.',
            'expense_date.required' => 'Expense date is required.',
            'description.required' => 'Expense description is required.',
            'job_id.exists' => 'The selected job does not exist.',
            'driver_id.exists' => 'The selected driver does not exist.',
        ];
    }
}
