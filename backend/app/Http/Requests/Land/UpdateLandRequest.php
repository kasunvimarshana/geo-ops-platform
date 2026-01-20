<?php

namespace App\Http\Requests\Land;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'coordinates' => ['sometimes', 'array', 'min:3'],
            'coordinates.*.lat' => ['required_with:coordinates', 'numeric', 'between:-90,90'],
            'coordinates.*.lng' => ['required_with:coordinates', 'numeric', 'between:-180,180'],
            'location_address' => ['nullable', 'string', 'max:500'],
            'location_district' => ['nullable', 'string', 'max:100'],
            'location_province' => ['nullable', 'string', 'max:100'],
            'status' => ['sometimes', Rule::in(['active', 'inactive'])],
        ];
    }

    public function messages(): array
    {
        return [
            'coordinates.min' => 'At least 3 coordinate points are required to form a valid polygon.',
            'coordinates.*.lat.between' => 'Latitude must be between -90 and 90 degrees.',
            'coordinates.*.lng.between' => 'Longitude must be between -180 and 180 degrees.',
            'status.in' => 'Status must be either active or inactive.',
        ];
    }
}
