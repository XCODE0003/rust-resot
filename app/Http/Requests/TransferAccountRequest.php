<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class TransferAccountRequest extends FormRequest
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
            'user_id' => ['required', 'string'],
            'transfer_user_id' => ['required', 'string'],
            'user_name' => ['string'],
            'server_id' => ['required', 'string']
        ];
    }
}
