<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class SendOtpRequest extends FormRequest
{
        public function authorize(): bool
    {
        return true;
    }

   
   public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255', 'exists:users,email'],
            'type' => ['sometimes', 'string', Rule::in(['email_verify', 'password_reset'])],
        ];
    }
}