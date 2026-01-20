<?php

namespace App\Http\Requests\Land;

use Illuminate\Foundation\Http\FormRequest;

class StoreLandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'measurement_type' => 'required|in:walk-around,point-based',
            'polygon' => 'required|array|min:3',
            'polygon.*.latitude' => 'required|numeric|between:-90,90',
            'polygon.*.longitude' => 'required|numeric|between:-180,180',
            'polygon.*.altitude' => 'nullable|numeric',
            'polygon.*.accuracy' => 'required|numeric',
            'polygon.*.recorded_at' => 'nullable|date',
            'location_name' => 'nullable|string|max:255',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'measured_at' => 'nullable|date',
            'offline_id' => 'nullable|uuid',
        ];
    }
}
