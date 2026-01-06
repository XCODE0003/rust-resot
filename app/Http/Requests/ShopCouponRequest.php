<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopCouponRequest extends FormRequest
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
                'title'   => ['required', 'max:255'],
                'code'    => ['required', 'max:20', 'unique:shop_coupons,code,' . $requests['id']],
                'type'    => ['required', 'max:1'],
                'percent' => ['required', 'max:255'],
                'user_id' => ['max:20'],
                'users'   => [''],
                'date_start'   => ['date'],
                'date_end'   => ['date'],
                'item_1_id'   => [''],
                'item_1_name'   => [''],
                'item_1_amount'   => [''],
            ];
        } else {
            return [
                'title'   => ['required', 'max:255'],
                'code'    => ['required', 'max:20', 'unique:shop_coupons'],
                'type'    => ['required', 'max:1'],
                'percent' => ['required', 'max:255'],
                'user_id' => ['max:20'],
                'users'   => [''],
                'date_start'   => ['date'],
                'date_end'   => ['date'],
                'item_1_id'   => [''],
                'item_1_name'   => [''],
                'item_1_amount'   => [''],
            ];
        }
    }
    public function messages()
    {
        return [
            'code.unique' => __('Код купона должен быть уникальным!'),
        ];
    }
}
