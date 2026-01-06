<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CasesRequest extends FormRequest
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
            'price' => ['required','max:12'],
            'price_usd' => ['required','max:12'],
            'status' => ['required','max:1'],
            'is_free' => ['nullable','max:1'],
            'kind' => ['required','max:10'],
            'online_amount' => ['max:20'],
            'prizes_max' => ['max:10'],
            'title_en' => ['max:255'],
            'title_ru' => ['max:255'],
            'title_de' => ['max:255'],
            'title_es' => ['max:255'],
            'title_fr' => ['max:255'],
            'title_it' => ['max:255'],
            'title_uk' => ['max:255'],
            'subtitle_en' => ['max:255'],
            'subtitle_ru' => ['max:255'],
            'subtitle_de' => ['max:255'],
            'subtitle_es' => ['max:255'],
            'subtitle_fr' => ['max:255'],
            'subtitle_it' => ['max:255'],
            'subtitle_uk' => ['max:255'],
            'description_en' => [''],
            'description_ru' => [''],
            'description_de' => [''],
            'description_es' => [''],
            'description_fr' => [''],
            'description_it' => [''],
            'description_uk' => [''],
            'sort' => ['max:10'],
            'server' => ['max:20'],
            'image' => ['required_without:edit', 'image'],
        ];
    }
}
