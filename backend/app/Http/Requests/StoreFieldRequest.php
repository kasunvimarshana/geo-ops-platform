<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|min:2',
            'location' => 'nullable|string|max:500',
            'area' => 'nullable|numeric|min:0',
            'perimeter' => 'nullable|numeric|min:0',
            'crop_type' => 'nullable|string|max:100',
            'measurement_type' => 'required|in:walk_around,polygon,manual',
            'boundary' => 'required|json',
            'notes' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Field name is required',
            'measurement_type.required' => 'Measurement type is required',
            'boundary.required' => 'Boundary data is required',
            'boundary.json' => 'Boundary must be valid GeoJSON',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}
