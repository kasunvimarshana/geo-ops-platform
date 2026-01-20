<?php

namespace App\Http\Requests\Land;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'location_name' => 'nullable|string|max:255',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'status' => 'sometimes|in:draft,confirmed,archived',
        ];
    }
}
