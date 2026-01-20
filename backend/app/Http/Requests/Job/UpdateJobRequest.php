<?php

namespace App\Http\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;

class UpdateJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'land_id' => 'sometimes|exists:lands,id',
            'machine_id' => 'sometimes|exists:machines,id',
            'driver_id' => 'sometimes|exists:users,id',
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'job_date' => 'sometimes|date',
            'status' => 'sometimes|in:scheduled,in-progress,completed,cancelled',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'location_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ];
    }
}
