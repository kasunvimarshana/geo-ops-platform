<?php

namespace App\Http\Requests\Tracking;

use Illuminate\Foundation\Http\FormRequest;

class StoreTrackingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'job_id' => ['nullable', 'integer', 'exists:field_jobs,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'accuracy_meters' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'altitude_meters' => ['nullable', 'numeric', 'between:-500,10000'],
            'speed_mps' => ['nullable', 'numeric', 'min:0', 'max:200'],
            'heading_degrees' => ['nullable', 'numeric', 'between:0,360'],
            'recorded_at' => ['nullable', 'date'],
            'device_id' => ['nullable', 'string', 'max:255'],
            'platform' => ['nullable', 'string', 'in:android,ios,web'],
            'metadata' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'user_id.required' => 'User ID is required.',
            'user_id.exists' => 'The selected user does not exist.',
            'latitude.required' => 'Latitude is required.',
            'latitude.between' => 'Latitude must be between -90 and 90.',
            'longitude.required' => 'Longitude is required.',
            'longitude.between' => 'Longitude must be between -180 and 180.',
            'job_id.exists' => 'The selected job does not exist.',
        ];
    }
}
