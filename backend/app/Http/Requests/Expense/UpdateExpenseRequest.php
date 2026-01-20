<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'machine_id' => 'sometimes|exists:machines,id',
            'driver_id' => 'sometimes|exists:users,id',
            'job_id' => 'sometimes|exists:jobs,id',
            'expense_type' => 'sometimes|in:fuel,maintenance,repair,driver-payment,other',
            'category' => 'sometimes|string|max:100',
            'description' => 'sometimes|string',
            'amount' => 'sometimes|numeric|min:0',
            'expense_date' => 'sometimes|date',
            'receipt_path' => 'nullable|string',
        ];
    }
}
