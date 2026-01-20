<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreLandMeasurementRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'coordinates' => 'required|array|min:3',
            'coordinates.*.latitude' => 'required|numeric|between:-90,90',
            'coordinates.*.longitude' => 'required|numeric|between:-180,180',
            'measured_at' => 'nullable|date',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Measurement name is required',
            'coordinates.required' => 'Coordinates are required',
            'coordinates.array' => 'Coordinates must be an array',
            'coordinates.min' => 'At least 3 coordinate points are required to form a polygon',
            'coordinates.*.latitude.required' => 'Each coordinate must have a latitude',
            'coordinates.*.latitude.numeric' => 'Latitude must be a number',
            'coordinates.*.latitude.between' => 'Latitude must be between -90 and 90',
            'coordinates.*.longitude.required' => 'Each coordinate must have a longitude',
            'coordinates.*.longitude.numeric' => 'Longitude must be a number',
            'coordinates.*.longitude.between' => 'Longitude must be between -180 and 180',
            'measured_at.date' => 'Measured at must be a valid date',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'error' => [
                'code' => 'VALIDATION_ERROR',
                'message' => 'Validation failed',
                'details' => $validator->errors()
            ]
        ], 422));
    }
}
