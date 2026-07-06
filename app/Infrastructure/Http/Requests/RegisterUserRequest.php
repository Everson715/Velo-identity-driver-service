<?php

namespace App\Infrastructure\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array (
          'name' => 'required|string',
          'email' => 'required|email|unique:users',
          'password' => 'required|min:8|confirmed',
          'role' => 'sometimes|string',
          'phone' => 'sometimes|string|nullable',
        );
    }
}
