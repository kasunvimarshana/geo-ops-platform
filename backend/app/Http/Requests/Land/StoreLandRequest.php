<?php

namespace App\Http\Requests\Land;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'owner_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'coordinates' => ['required', 'array', 'min:3'],
            'coordinates.*.lat' => ['required', 'numeric', 'between:-90,90'],
            'coordinates.*.lng' => ['required', 'numeric', 'between:-180,180'],
            'location_address' => ['nullable', 'string', 'max:500'],
            'location_district' => ['nullable', 'string', 'max:100'],
            'location_province' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Land name is required.',
            'coordinates.required' => 'GPS coordinates are required.',
            'coordinates.min' => 'At least 3 coordinate points are required to form a valid polygon.',
            'coordinates.*.lat.between' => 'Latitude must be between -90 and 90 degrees.',
            'coordinates.*.lng.between' => 'Longitude must be between -180 and 180 degrees.',
        ];
    }
}
