<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopCategoryRequest extends FormRequest
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
            'path' => ['required','max:255'],
            'title_ru' => ['max:255'],
            'title_en' => ['max:255'],
            'title_de' => ['max:255'],
            'title_fr' => ['max:255'],
            'title_it' => ['max:255'],
            'title_es' => ['max:255'],
            'title_uk' => ['max:255'],
            'description_ru' => ['max:255'],
            'description_en' => ['max:255'],
            'description_de' => ['max:255'],
            'description_fr' => ['max:255'],
            'description_it' => ['max:255'],
            'description_es' => ['max:255'],
            'description_uk' => ['max:255'],
            'sort' => ['max:10'],
        ];
    }
}
