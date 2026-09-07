<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PasswordResetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'token' => __('validation.attributes.token'),
            'email' => __('validation.attributes.email'),
            'password' => __('validation.attributes.password'),
            'password_confirmation' => __('validation.attributes.password_confirmation'),
        ];
    }
}
