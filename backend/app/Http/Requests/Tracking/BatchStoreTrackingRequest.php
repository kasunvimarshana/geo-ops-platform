<?php

namespace App\Http\Requests\Tracking;

use Illuminate\Foundation\Http\FormRequest;

class BatchStoreTrackingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tracking_logs' => ['required', 'array', 'min:1', 'max:100'],
            'tracking_logs.*.user_id' => ['required', 'integer', 'exists:users,id'],
            'tracking_logs.*.job_id' => ['nullable', 'integer', 'exists:field_jobs,id'],
            'tracking_logs.*.latitude' => ['required', 'numeric', 'between:-90,90'],
            'tracking_logs.*.longitude' => ['required', 'numeric', 'between:-180,180'],
            'tracking_logs.*.accuracy_meters' => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'tracking_logs.*.altitude_meters' => ['nullable', 'numeric', 'between:-500,10000'],
            'tracking_logs.*.speed_mps' => ['nullable', 'numeric', 'min:0', 'max:200'],
            'tracking_logs.*.heading_degrees' => ['nullable', 'numeric', 'between:0,360'],
            'tracking_logs.*.recorded_at' => ['nullable', 'date'],
            'tracking_logs.*.device_id' => ['nullable', 'string', 'max:255'],
            'tracking_logs.*.platform' => ['nullable', 'string', 'in:android,ios,web'],
            'tracking_logs.*.metadata' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'tracking_logs.required' => 'Tracking logs array is required.',
            'tracking_logs.min' => 'At least one tracking log is required.',
            'tracking_logs.max' => 'Cannot submit more than 100 tracking logs at once.',
            'tracking_logs.*.user_id.required' => 'User ID is required for each tracking log.',
            'tracking_logs.*.latitude.required' => 'Latitude is required for each tracking log.',
            'tracking_logs.*.longitude.required' => 'Longitude is required for each tracking log.',
        ];
    }
}
