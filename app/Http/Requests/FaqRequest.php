<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FaqRequest extends FormRequest
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
            'question_ru' => ['max:255'],
            'question_en' => ['max:255'],
            'question_de' => ['max:255'],
            'question_fr' => ['max:255'],
            'question_it' => ['max:255'],
            'question_es' => ['max:255'],
            'question_uk' => ['max:255'],
            'answer_ru' => [''],
            'answer_en' => [''],
            'answer_de' => [''],
            'answer_fr' => [''],
            'answer_it' => [''],
            'answer_es' => [''],
            'answer_uk' => [''],
            'sort' => ['max:10', 'numeric'],
        ];
    }
}
