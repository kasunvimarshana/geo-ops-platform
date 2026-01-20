<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)/',
            'phone' => 'nullable|string|max:20|regex:/^[+]?[0-9\s\-()]+$/',
            'organization_id' => 'nullable|exists:organizations,id',
            'role' => 'nullable|in:admin,manager,field_worker,driver',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required',
            'email.unique' => 'This email address is already registered',
            'password.min' => 'Password must be at least 8 characters',
            'password.regex' => 'Password must contain uppercase, lowercase, and number',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()
        ], 422));
    }
}
