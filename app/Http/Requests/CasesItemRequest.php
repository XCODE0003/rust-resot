<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CasesItemRequest extends FormRequest
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
        $params = [
            'price' => ['required','max:12'],
            'price_usd' => ['required','max:12'],
            'status'       => ['required', 'max:1'],
            'quality_type' => ['required', 'max:1'],
            'source'       => ['max:255'],
            'title'        => ['max:255'],
            'subtitle'     => ['max:255'],
            'description'  => [],
            'sort'         => ['max:10'],
            'image'        => ['required_without:edit', 'image'],
        ];

        if (!request()->has('id')) {
            $params['item_id'] = ['required', 'string', 'max:20', 'unique:cases_items'];
        } else {
            $params['item_id'] = ['required', 'string', 'max:20', Rule::unique('cases_items')->ignore(request()->input('id'))];
        }

        return $params;
    }
}
