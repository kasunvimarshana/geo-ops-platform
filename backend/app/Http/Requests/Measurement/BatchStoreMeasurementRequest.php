<?php

namespace App\Http\Requests\Measurement;

use Illuminate\Foundation\Http\FormRequest;

class BatchStoreMeasurementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'measurements' => ['required', 'array', 'min:1', 'max:100'],
            'measurements.*.land_id' => ['required', 'integer', 'exists:lands,id'],
            'measurements.*.type' => ['required', 'string', 'in:walk_around,point_based'],
            'measurements.*.coordinates' => ['required', 'array', 'min:3'],
            'measurements.*.coordinates.*.lat' => ['required', 'numeric', 'between:-90,90'],
            'measurements.*.coordinates.*.lng' => ['required', 'numeric', 'between:-180,180'],
            'measurements.*.measurement_started_at' => ['required', 'date'],
            'measurements.*.measurement_completed_at' => ['required', 'date'],
            'measurements.*.notes' => ['nullable', 'string', 'max:1000'],
            'measurements.*.accuracy_meters' => ['nullable', 'numeric', 'min:0'],
            'measurements.*.device_id' => ['nullable', 'string', 'max:255'],
            'measurements.*.is_synced' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'measurements.required' => 'Measurements array is required.',
            'measurements.min' => 'At least one measurement is required.',
            'measurements.max' => 'Maximum 100 measurements can be created at once.',
        ];
    }
}
