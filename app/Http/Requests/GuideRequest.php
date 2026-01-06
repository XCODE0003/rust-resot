<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuideRequest extends FormRequest
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
        $requests = $this->request->all();

        if (isset($requests['edit'])) {
            return [
                'path' => ['required', 'max:255', 'unique:guides,path,' . $requests['id']],
                'status' => ['max:1'],
                'category_id' => ['max:20'],
                'sort' => ['max:10'],
                'title_en' => ['max:255'],
                'description_en' => [],
                'meta_title_en' => ['max:255'],
                'meta_description_en' => [],
                'meta_keywords_en' => [],
                'meta_h1_en' => ['max:255'],
                'meta_h2_en' => ['max:255'],
                'meta_h3_en' => ['max:255'],
                'title_ru' => ['max:255'],
                'description_ru' => [],
                'meta_title_ru' => ['max:255'],
                'meta_description_ru' => [],
                'meta_keywords_ru' => [],
                'meta_h1_ru' => ['max:255'],
                'meta_h2_ru' => ['max:255'],
                'meta_h3_ru' => ['max:255'],
                'title_de' => ['max:255'],
                'description_de' => [],
                'meta_title_de' => ['max:255'],
                'meta_description_de' => [],
                'meta_keywords_de' => [],
                'meta_h1_de' => ['max:255'],
                'meta_h2_de' => ['max:255'],
                'meta_h3_de' => ['max:255'],
                'title_fr' => ['max:255'],
                'description_fr' => [],
                'meta_title_fr' => ['max:255'],
                'meta_description_fr' => [],
                'meta_keywords_fr' => [],
                'meta_h1_fr' => ['max:255'],
                'meta_h2_fr' => ['max:255'],
                'meta_h3_fr' => ['max:255'],
                'title_it' => ['max:255'],
                'description_it' => [],
                'meta_title_it' => ['max:255'],
                'meta_description_it' => [],
                'meta_keywords_it' => [],
                'meta_h1_it' => ['max:255'],
                'meta_h2_it' => ['max:255'],
                'meta_h3_it' => ['max:255'],
                'title_es' => ['max:255'],
                'description_es' => [],
                'meta_title_es' => ['max:255'],
                'meta_description_es' => [],
                'meta_keywords_es' => [],
                'meta_h1_es' => ['max:255'],
                'meta_h2_es' => ['max:255'],
                'meta_h3_es' => ['max:255'],
                'title_uk' => ['max:255'],
                'description_uk' => [],
                'meta_title_uk' => ['max:255'],
                'meta_description_uk' => [],
                'meta_keywords_uk' => [],
                'meta_h1_uk' => ['max:255'],
                'meta_h2_uk' => ['max:255'],
                'meta_h3_uk' => ['max:255'],
                'image' => ['required_without:edit', 'image'],
            ];
        } else {
            return [
                'path' => ['required', 'max:255', 'unique:guides'],
                'status' => ['max:1'],
                'category_id' => ['max:20'],
                'sort' => ['max:10'],
                'title_en' => ['max:255'],
                'description_en' => [],
                'meta_title_en' => ['max:255'],
                'meta_description_en' => [],
                'meta_keywords_en' => [],
                'meta_h1_en' => ['max:255'],
                'meta_h2_en' => ['max:255'],
                'meta_h3_en' => ['max:255'],
                'title_ru' => ['max:255'],
                'description_ru' => [],
                'meta_title_ru' => ['max:255'],
                'meta_description_ru' => [],
                'meta_keywords_ru' => [],
                'meta_h1_ru' => ['max:255'],
                'meta_h2_ru' => ['max:255'],
                'meta_h3_ru' => ['max:255'],
                'title_de' => ['max:255'],
                'description_de' => [],
                'meta_title_de' => ['max:255'],
                'meta_description_de' => [],
                'meta_keywords_de' => [],
                'meta_h1_de' => ['max:255'],
                'meta_h2_de' => ['max:255'],
                'meta_h3_de' => ['max:255'],
                'title_fr' => ['max:255'],
                'description_fr' => [],
                'meta_title_fr' => ['max:255'],
                'meta_description_fr' => [],
                'meta_keywords_fr' => [],
                'meta_h1_fr' => ['max:255'],
                'meta_h2_fr' => ['max:255'],
                'meta_h3_fr' => ['max:255'],
                'title_it' => ['max:255'],
                'description_it' => [],
                'meta_title_it' => ['max:255'],
                'meta_description_it' => [],
                'meta_keywords_it' => [],
                'meta_h1_it' => ['max:255'],
                'meta_h2_it' => ['max:255'],
                'meta_h3_it' => ['max:255'],
                'title_es' => ['max:255'],
                'description_es' => [],
                'meta_title_es' => ['max:255'],
                'meta_description_es' => [],
                'meta_keywords_es' => [],
                'meta_h1_es' => ['max:255'],
                'meta_h2_es' => ['max:255'],
                'meta_h3_es' => ['max:255'],
                'title_uk' => ['max:255'],
                'description_uk' => [],
                'meta_title_uk' => ['max:255'],
                'meta_description_uk' => [],
                'meta_keywords_uk' => [],
                'meta_h1_uk' => ['max:255'],
                'meta_h2_uk' => ['max:255'],
                'meta_h3_uk' => ['max:255'],
                'image' => ['required_without:edit', 'image'],
            ];
        }
    }
}
