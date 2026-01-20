<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpenseRequest extends FormRequest
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
            'category' => ['nullable', 'string', 'in:fuel,maintenance,parts,salary,transport,food,other'],
            'amount' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'currency' => ['nullable', 'string', 'in:USD,EUR,GBP,KES'],
            'expense_date' => ['nullable', 'date'],
            'vendor_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'receipt_path' => ['nullable', 'string', 'max:500'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'category.in' => 'Invalid expense category. Must be one of: fuel, maintenance, parts, salary, transport, food, other.',
            'amount.min' => 'Expense amount must be greater than or equal to 0.',
            'job_id.exists' => 'The selected job does not exist.',
            'driver_id.exists' => 'The selected driver does not exist.',
        ];
    }
}
