<?php

declare(strict_types=1);

namespace App\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLandPlotRequest extends FormRequest
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
            'coordinates' => 'required|array|min:3',
            'coordinates.*.latitude' => 'required|numeric|between:-90,90',
            'coordinates.*.longitude' => 'required|numeric|between:-180,180',
            'measurement_method' => 'required|in:walk_around,manual_points',
            'accuracy_meters' => 'nullable|numeric|min:0',
            'measured_at' => 'required|date',
            'notes' => 'nullable|string',
        ];
    }
}
