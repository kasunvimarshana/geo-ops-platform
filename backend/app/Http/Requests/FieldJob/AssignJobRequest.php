<?php

namespace App\Http\Requests\FieldJob;

use Illuminate\Foundation\Http\FormRequest;

class AssignJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'driver_id' => ['required', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'driver_id.required' => 'Driver is required.',
            'driver_id.exists' => 'The selected driver does not exist.',
        ];
    }
}
