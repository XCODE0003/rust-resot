<?php

namespace App\Http\Controllers;

use App\Models\Donate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use GameServer;

class DonateController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index(Request $request)
    {
        //
    }

    public function create(Request $request)
    {
	    session()->put('donate_prev_url', route('account.profile'));

        $request->var_id = 0;
        $request->server_id = 0;
        $price_rub = $request->has('amount') ? $request->amount : 1;
        $price_usd = $price_rub;
        $shopitem = (object)[
            'id' => 0
        ];

        return $this->setPayment($request, $shopitem, $price_rub, $price_usd);

    }

    public function addBalance($user_id, $amount)
    {
        $amount = abs(intval($amount));

        $user = User::find($user_id);
        $user->balance += $amount;
        $user->save();

        Log::channel('paymentslog')->info('Robot: Player ' . $user->name . ' (' . $user->email . ') ' . 'successfully top up the balance + ' . $amount);

        $this->alert('success', __('Баланс успешно пополнен!'));
        return back();
    }

    public function tebex(Request $request)
    {
        if ($request->has('donate_id') && $request->has('success') && $request->get('success') === 'true') {
            $donate_id = $request->get('donate_id');
            $public_token = config('options.tebex_public_token', '');
            $base_url = 'https://headless.tebex.io';

            // Получаем массив товаров на сумму $amount
            $donate = Donate::where('id', $donate_id)->where('status', 0)->first();
            dd($donate);
            if ($donate) {
                $basket_ident = $donate->payment_id;
                $price_usd = round($donate->amount / config('options.exchange_rate_usd', 70));
                $items = $this->createGiftCardArray($price_usd);

                // Добавляем товары в корзину
                $result = $this->addItemToBasket($base_url, $basket_ident, $items);

                if (isset($result['data']['links']['checkout'])) {
                    return Redirect::to($result['data']['links']['checkout']);
                }
            }
        }

        $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
        return Redirect::route('account.profile');
    }


    /* PaymentsMethodTrait */
    //В трейте Методы оплаты с вызовом редиректа на оплату setPayment();
    // Везовы колбеков в \App\Http\Controllers\Api\PaymentsController
    //Редиректы после оплаты в \App\Http\Controllers\PaymentsController

    use \App\Traits\PaymentsMethodTrait;


    public function createGiftCardArray($amount)
    {
        // Доступные гифткарты с их значениями и package_id
        $giftCards = [
            6498758 => 10, // $10
            6498756 => 3,  // $3
            6498753 => 1   // $1
        ];

        $items = [];
        foreach ($giftCards as $packageId => $value) {
            while ($amount >= $value) {
                if (isset($items[$packageId])) {
                    $items[$packageId]['quantity']++;
                } else {
                    $items[$packageId] = [
                        'package_id' => $packageId,
                        'quantity' => 1
                    ];
                }
                $amount -= $value;
            }
        }

        return array_values($items);
    }

    private function addItemToBasket($base_url, $basket_ident, $items)
    {
        foreach ($items as $item) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "{$base_url}/api/baskets/{$basket_ident}/packages",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    'package_id' => $item['package_id'],
                    'quantity' => $item['quantity'] ?? 1
                ]),
                CURLOPT_HTTPHEADER => [
                    "Accept: application/json",
                    "Content-Type: application/json"
                ],
            ]);

            $response = curl_exec($curl);
            curl_close($curl);
        }

        if (isset($response)) {
            return json_decode($response, true);
        } else {
            return false;
        }
    }


    protected function validator(array $data): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'amount' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);
    }

    protected function sign($out_amount, $order_id, $merchant_id = 13251, $secret = '')
    {
        return md5($merchant_id.':'.$out_amount.':'.$secret.':'.$order_id);
    }

    protected function error($message)
    {
        return response($message)->header('Content-Type', 'text/plain');
    }

    protected function success()
    {
        return response('YES')->header('Content-Type', 'text/plain');
    }
}
