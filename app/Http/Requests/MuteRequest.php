<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MuteRequest extends FormRequest
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
            'mute_date' => ['required', 'numeric'],
            'mute_reason' => ['required', 'string', 'max:255'],
            'user_id' => ['required', 'numeric']
        ];
    }
}
