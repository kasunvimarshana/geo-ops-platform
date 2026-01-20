<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('id');

        return [
            'role_id' => 'sometimes|exists:roles,id',
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $userId,
            'phone' => 'nullable|string|max:20|unique:users,phone,' . $userId,
            'password' => 'sometimes|string|min:8',
            'language' => 'nullable|string|in:en,si',
        ];
    }
}
