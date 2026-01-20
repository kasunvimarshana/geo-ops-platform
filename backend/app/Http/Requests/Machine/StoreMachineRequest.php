<?php

namespace App\Http\Requests\Machine;

use Illuminate\Foundation\Http\FormRequest;

class StoreMachineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'machine_type' => 'required|string|max:100',
            'registration_number' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'rate_per_acre' => 'nullable|numeric|min:0',
            'rate_per_hectare' => 'nullable|numeric|min:0',
            'is_active' => 'nullable|boolean',
        ];
    }
}
