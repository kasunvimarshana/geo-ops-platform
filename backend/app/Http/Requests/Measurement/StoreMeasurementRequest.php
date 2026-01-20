<?php

namespace App\Http\Requests\Measurement;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMeasurementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'land_id' => ['required', 'integer', 'exists:lands,id'],
            'type' => ['required', 'string', Rule::in(['walk_around', 'point_based'])],
            'coordinates' => ['required', 'array', 'min:3'],
            'coordinates.*.lat' => ['required', 'numeric', 'between:-90,90'],
            'coordinates.*.lng' => ['required', 'numeric', 'between:-180,180'],
            'measurement_started_at' => ['required', 'date'],
            'measurement_completed_at' => ['required', 'date', 'after:measurement_started_at'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'accuracy_meters' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'device_id' => ['nullable', 'string', 'max:255'],
            'is_synced' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'land_id.required' => 'Land ID is required.',
            'land_id.exists' => 'The specified land does not exist.',
            'type.required' => 'Measurement type is required.',
            'type.in' => 'Measurement type must be one of: walk_around, point_based.',
            'coordinates.required' => 'GPS coordinates are required.',
            'coordinates.min' => 'At least 3 coordinate points are required.',
            'measurement_started_at.required' => 'Measurement start time is required.',
            'measurement_completed_at.required' => 'Measurement completion time is required.',
            'measurement_completed_at.after' => 'Completion time must be after start time.',
        ];
    }
}
