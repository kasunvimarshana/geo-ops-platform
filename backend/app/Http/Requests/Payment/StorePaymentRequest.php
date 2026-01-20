<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:999999.99'],
            'payment_method' => ['required', 'string', 'in:cash,check,bank_transfer,mobile_money,credit_card,other'],
            'payment_date' => ['nullable', 'date', 'before_or_equal:today'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'currency' => ['nullable', 'string', 'in:USD,EUR,GBP,KES'],
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_id.required' => 'Invoice is required.',
            'invoice_id.exists' => 'The selected invoice does not exist.',
            'amount.required' => 'Payment amount is required.',
            'amount.min' => 'Payment amount must be greater than zero.',
            'payment_method.required' => 'Payment method is required.',
            'payment_method.in' => 'Invalid payment method selected.',
            'payment_date.before_or_equal' => 'Payment date cannot be in the future.',
        ];
    }
}
