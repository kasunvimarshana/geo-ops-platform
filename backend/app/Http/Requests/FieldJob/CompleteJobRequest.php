<?php

namespace App\Http\Requests\FieldJob;

use Illuminate\Foundation\Http\FormRequest;

class CompleteJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'actual_amount' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'distance_km' => ['nullable', 'numeric', 'min:0', 'max:9999.99'],
            'completion_notes' => ['nullable', 'string', 'max:1000'],
            'attachments' => ['nullable', 'array'],
            'attachments.*' => ['string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'actual_amount.numeric' => 'Actual amount must be a valid number.',
            'distance_km.numeric' => 'Distance must be a valid number.',
        ];
    }
}
