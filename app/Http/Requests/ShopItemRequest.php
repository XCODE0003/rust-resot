<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopItemRequest extends FormRequest
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
            'rs_id'                => ['max:20'],
            'category_id'          => ['required', 'max:20'],
            'price'                => ['required', 'max:12'],
            'price_usd'            => ['required', 'max:12'],
            'item_id'              => ['max:20'],
            'status'               => ['required', 'max:1'],
            'server'               => ['required', 'max:10'],
            'servers'              => [''],
            'is_blueprint'         => ['required', 'max:1'],
            'is_command'           => ['required', 'max:1'],
            'is_item'              => ['max:1'],
            'can_gift'             => ['max:1'],
            'wipe_block'           => ['max:255'],
            'short_name'           => ['max:255'],
            'name_ru'              => ['max:255'],
            'name_en'              => ['max:255'],
            'name_de'              => ['max:255'],
            'name_fr'              => ['max:255'],
            'name_it'              => ['max:255'],
            'name_es'              => ['max:255'],
            'name_uk'              => ['max:255'],
            'short_description_ru' => ['max:255'],
            'short_description_en' => ['max:255'],
            'short_description_de' => ['max:255'],
            'short_description_fr' => ['max:255'],
            'short_description_it' => ['max:255'],
            'short_description_es' => ['max:255'],
            'short_description_uk' => ['max:255'],
            'description_ru'       => [],
            'description_en'       => [],
            'description_de'       => [],
            'description_fr'       => [],
            'description_it'       => [],
            'description_es'       => [],
            'description_uk'       => [],
            'sort'                 => ['max:10'],
            'amount'               => ['max:20'],
            'command'              => ['max:255'],
            'variations'           => [],
            'image'                => ['required_without:edit', 'image'],
        ];
    }
}