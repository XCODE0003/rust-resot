<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopSetRequest extends FormRequest
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
            'category_id' => ['required', 'max:20'],
            'price'       => ['required', 'max:12'],
            'price_usd'   => ['required', 'max:12'],
            'status'      => ['required', 'max:1'],
            'server'      => ['required', 'max:10'],
            'servers'     => [''],
            'can_gift'    => ['max:1'],
            'name_ru'     => ['max:255'],
            'name_en'     => ['max:255'],
            'name_de'     => ['max:255'],
            'name_fr'     => ['max:255'],
            'name_it'     => ['max:255'],
            'name_es'     => ['max:255'],
            'name_uk'     => ['max:255'],
            'sort'        => ['max:10'],
            'amount'      => ['max:20'],
            'image'       => ['required_without:edit', 'image'],
        ];
    }
}
