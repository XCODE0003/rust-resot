<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileRequest extends FormRequest
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
            ];
        } else {
            return [
                'name'  => ['required', 'string', 'min:3', 'max:32', Rule::unique('users')->ignore(auth()->user()->id)]
            ];
        }
    }
}
