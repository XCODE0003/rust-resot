<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServerRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'ip' => ['required', 'string'],
            'rcon_ip' => ['required', 'string'],
            'status' => ['required', 'string'],
            'sort' => ['required', 'string', 'max:20'],
            'category_id' => ['required', 'string', 'max:20'],
            'image' => ['required_without:edit', 'image'],
            'rsworld_db_type' => ['required', 'string'],
            'next_wipe' => [''],
            'rcon_passw' => [''],
        ];
    }
}