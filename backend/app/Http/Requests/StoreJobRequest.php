<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Models\Job;

class StoreJobRequest extends FormRequest
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
            'customer_id' => 'required|integer|exists:customers,id',
            'land_measurement_id' => 'required|integer|exists:land_measurements,id',
            'driver_id' => 'nullable|integer|exists:drivers,id',
            'machine_id' => 'nullable|integer|exists:machines,id',
            'service_type' => 'required|string|in:ploughing,harrowing,seeding,harvesting,leveling',
            'scheduled_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string|max:1000',
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
            'customer_id.required' => 'Customer is required',
            'customer_id.exists' => 'Selected customer does not exist',
            'land_measurement_id.required' => 'Land measurement is required',
            'land_measurement_id.exists' => 'Selected land measurement does not exist',
            'driver_id.exists' => 'Selected driver does not exist',
            'machine_id.exists' => 'Selected machine does not exist',
            'service_type.required' => 'Service type is required',
            'service_type.in' => 'Invalid service type selected',
            'scheduled_at.date' => 'Scheduled date must be a valid date',
            'scheduled_at.after' => 'Scheduled date must be in the future',
            'notes.max' => 'Notes cannot exceed 1000 characters',
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
