<?php

namespace App\Http\Requests\Subscription;

use Illuminate\Foundation\Http\FormRequest;

class CheckLimitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resource' => ['required', 'string', 'in:lands,measurements,jobs,drivers'],
            'count' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'resource.required' => 'Resource type is required.',
            'resource.in' => 'Invalid resource type. Must be one of: lands, measurements, jobs, drivers.',
            'count.integer' => 'Count must be a valid integer.',
            'count.min' => 'Count must be at least 1.',
        ];
    }
}
