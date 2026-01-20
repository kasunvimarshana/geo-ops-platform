<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateLandRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Allow all users to make this request
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
            'owner_id' => 'required|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The land name is required.',
            'area.required' => 'The area is required.',
            'coordinates.required' => 'The coordinates are required.',
            'owner_id.required' => 'The owner ID is required.',
            'owner_id.exists' => 'The selected owner does not exist.',
        ];
    }
}