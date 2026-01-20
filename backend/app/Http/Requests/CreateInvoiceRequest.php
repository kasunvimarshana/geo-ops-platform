<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateInvoiceRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users to create invoices
    }

    public function rules()
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'due_date' => 'required|date|after:today',
            'items' => 'required|array',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'customer_id.required' => 'A customer ID is required.',
            'customer_id.exists' => 'The selected customer does not exist.',
            'amount.required' => 'An amount is required.',
            'amount.numeric' => 'The amount must be a number.',
            'amount.min' => 'The amount must be at least 0.',
            'due_date.required' => 'A due date is required.',
            'due_date.date' => 'The due date must be a valid date.',
            'due_date.after' => 'The due date must be a date after today.',
            'items.required' => 'At least one item is required.',
            'items.array' => 'Items must be an array.',
            'items.*.description.required' => 'Each item must have a description.',
            'items.*.quantity.required' => 'Each item must have a quantity.',
            'items.*.quantity.integer' => 'The quantity must be an integer.',
            'items.*.quantity.min' => 'The quantity must be at least 1.',
            'items.*.price.required' => 'Each item must have a price.',
            'items.*.price.numeric' => 'The price must be a number.',
            'items.*.price.min' => 'The price must be at least 0.',
        ];
    }
}