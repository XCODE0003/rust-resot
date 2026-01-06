<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'login' => ['required', 'string', 'exists:accounts', 'max:14'],
            'password' => ['sometimes', 'required', 'string'],
            'pin' => ['sometimes', 'required', 'string'],
            'new_password' => ['required', 'confirmed', 'max:20', Password::min(6)]
        ];
    }
}
