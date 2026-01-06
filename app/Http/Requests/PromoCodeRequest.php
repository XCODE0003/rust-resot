<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromoCodeRequest extends FormRequest
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
                'title'          => ['required', 'max:255'],
                'code'           => ['required', 'max:30', 'unique:promo_codes,code,' . $requests['id']],
                'type'           => ['required', 'max:1'],
                'type_reward'    => ['max:10'],
                'user_id'        => ['max:20'],
                'case_id'        => ['max:20'],
                'bonus_case_id'  => ['max:20'],
                'server_id'      => ['max:20'],
                'bonus_amount'   => ['max:15'],
                'premium_period' => ['max:10'],
                'shop_item_id'   => ['max:20'],
                'variation_id'   => ['max:20'],
                'users'          => [''],
                'date_start'     => ['date'],
                'date_end'       => ['date'],
                'items'          => [''],
            ];
        } else {
            return [
                'title'            => ['required', 'max:255'],
                'code'             => ['required', 'max:30', 'unique:promo_codes'],
                'type'             => ['required', 'max:1'],
                'type_reward'      => ['max:10'],
                'user_id'          => ['max:20'],
                'case_id'          => ['max:20'],
                'server_id'        => ['max:20'],
                'bonus_amount'     => ['max:15'],
                'premium_period'   => ['max:10'],
                'shop_item_id'     => ['max:20'],
                'variation_id'     => ['max:20'],
                'users'            => [''],
                'date_start'       => ['date'],
                'date_end'         => ['date'],
                'items'            => [''],
            ];
        }
    }
    public function messages()
    {
        return [
            'code.unique' => __('Промо Код должен быть уникальным!'),
        ];
    }
}
