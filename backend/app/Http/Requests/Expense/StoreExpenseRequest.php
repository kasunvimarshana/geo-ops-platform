<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'machine_id' => 'nullable|exists:machines,id',
            'driver_id' => 'nullable|exists:users,id',
            'job_id' => 'nullable|exists:jobs,id',
            'expense_type' => 'required|in:fuel,maintenance,repair,driver-payment,other',
            'category' => 'required|string|max:100',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'receipt_path' => 'nullable|string',
            'offline_id' => 'nullable|uuid',
        ];
    }
}
