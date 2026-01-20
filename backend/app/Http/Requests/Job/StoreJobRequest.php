<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'land_id' => 'nullable|exists:lands,id',
            'machine_id' => 'required|exists:machines,id',
            'driver_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'job_date' => 'required|date',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'location' => 'nullable|string',
            'location_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'offline_id' => 'nullable|uuid',
        ];
    }
}
