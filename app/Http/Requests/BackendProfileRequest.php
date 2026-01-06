<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class BackendProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //Если разрешено изменять email
        if (config('options.change_email', '0') == '1') {
            return [
                'name'  => ['required', 'string', 'min:3', 'max:32', Rule::unique('users')->ignore(auth()->user()->id)],
                'email' => ['required', 'email', 'max:35', Rule::unique('users')->ignore(auth()->user()->id)],
                'password' => ['max:14', Password::min(3)],
                'new_password' => ['confirmed', 'max:14', Password::min(3)]
            ];
        } else {
            return [
                'name'  => ['required', 'string', 'min:3', 'max:32', Rule::unique('users')->ignore(auth()->user()->id)],
                'password' => ['max:14', Password::min(3)],
                'new_password' => ['confirmed', 'max:14', Password::min(3)]
            ];
        }
    }
}
