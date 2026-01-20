<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateJobRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users to create jobs
    }

    public function rules()
    {
        return [
            'land_id' => 'required|exists:lands,id',
            'driver_id' => 'required|exists:drivers,id',
            'machine_id' => 'required|exists:machines,id',
            'scheduled_time' => 'required|date',
            'description' => 'nullable|string|max:255',
            'status' => 'in:pending,in_progress,completed',
        ];
    }

    public function messages()
    {
        return [
            'land_id.required' => 'The land ID is required.',
            'land_id.exists' => 'The selected land does not exist.',
            'driver_id.required' => 'The driver ID is required.',
            'driver_id.exists' => 'The selected driver does not exist.',
            'machine_id.required' => 'The machine ID is required.',
            'machine_id.exists' => 'The selected machine does not exist.',
            'scheduled_time.required' => 'The scheduled time is required.',
            'scheduled_time.date' => 'The scheduled time must be a valid date.',
            'description.string' => 'The description must be a string.',
            'description.max' => 'The description may not be greater than 255 characters.',
            'status.in' => 'The selected status is invalid.',
        ];
    }
}