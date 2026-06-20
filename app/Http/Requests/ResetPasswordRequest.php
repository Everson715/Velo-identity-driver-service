<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return array (
  'email' => 'required|email',
  'token' => 'required|string',
  'password' => 'required|min:8|confirmed',
);
    }
}
