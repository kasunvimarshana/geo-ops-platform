<?php

namespace App\Http\Requests\FieldJob;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'land_id' => ['nullable', 'integer', 'exists:lands,id'],
            'customer_id' => ['nullable', 'integer', 'exists:users,id'],
            'service_type' => ['required', 'string', 'in:plowing,seeding,harvesting,spraying,other'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
            'customer_address' => ['nullable', 'string', 'max:500'],
            'location_coordinates' => ['nullable', 'array'],
            'location_coordinates.*.lat' => ['required_with:location_coordinates', 'numeric', 'between:-90,90'],
            'location_coordinates.*.lng' => ['required_with:location_coordinates', 'numeric', 'between:-180,180'],
            'area_acres' => ['nullable', 'numeric', 'min:0', 'max:99999.9999'],
            'area_hectares' => ['nullable', 'numeric', 'min:0', 'max:99999.9999'],
            'rate_per_unit' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'rate_unit' => ['nullable', 'string', 'in:acre,hectare,hour,fixed'],
            'estimated_amount' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'scheduled_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'service_type.required' => 'Service type is required.',
            'service_type.in' => 'Invalid service type. Must be one of: plowing, seeding, harvesting, spraying, other.',
            'customer_name.required' => 'Customer name is required.',
            'land_id.exists' => 'The selected land does not exist.',
            'customer_id.exists' => 'The selected customer does not exist.',
            'location_coordinates.*.lat.between' => 'Latitude must be between -90 and 90 degrees.',
            'location_coordinates.*.lng.between' => 'Longitude must be between -180 and 180 degrees.',
        ];
    }
}
