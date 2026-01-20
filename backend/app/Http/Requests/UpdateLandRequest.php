<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLandRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Authorization logic can be added here
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'area' => 'required|numeric|min:0',
            'coordinates' => 'required|array',
            'coordinates.*' => 'required|array',
            'coordinates.*.lat' => 'required|numeric',
            'coordinates.*.lng' => 'required|numeric',
            'description' => 'nullable|string|max:500',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The land name is required.',
            'area.required' => 'The area is required.',
            'coordinates.required' => 'The coordinates are required.',
            'coordinates.array' => 'The coordinates must be an array.',
            'coordinates.*.lat.required' => 'The latitude is required for each coordinate.',
            'coordinates.*.lng.required' => 'The longitude is required for each coordinate.',
        ];
    }
}