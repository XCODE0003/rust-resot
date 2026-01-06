<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Characters;
use App\Models\Donate;
use App\Models\User;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\DonateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Client;
use Qiwi\Api\BillPayments;
use App\Services\PayPal;
use URL;

class PaymentsController extends Controller
{

    public function enotStatus(Request $request, string $method)
    {
        $donate_prev_url = session()->has('donate_prev_url') ? session()->get('donate_prev_url') : route('shop');
        if ($method == 'fail') {
            $this->alert('danger', __('Ошибка при пополнении баланса!'));
            return Redirect::to($donate_prev_url);
        }

        if ($method == 'success') {
            $this->alert('success', __('Баланс успешно пополнен!'));
            return Redirect::to($donate_prev_url);
        }

        $this->alert('danger', __('Ошибка при пополнении баланса!'));
        return Redirect::to($donate_prev_url);
    }

    public function appStatus(Request $request, string $method)
    {
        $donate_prev_url = session()->has('donate_prev_url') ? session()->get('donate_prev_url') : route('shop');
        if ($method == 'fail') {
            $this->alert('danger', __('Ошибка при пополнении баланса!'));
            return Redirect::to($donate_prev_url);
        }

        if ($method == 'success') {
            $this->alert('success', __('Баланс успешно пополнен!'));
            return Redirect::to($donate_prev_url);
        }

        $this->alert('danger', __('Ошибка при пополнении баланса!'));
        return Redirect::to($donate_prev_url);
    }

    public function freekassaStatus(Request $request, string $method)
    {
        $donate_prev_url = session()->has('donate_prev_url') ? session()->get('donate_prev_url') : route('shop');
        if ($method == 'fail') {
            $this->alert('danger', __('Ошибка при пополнении баланса!'));
            return Redirect::to($donate_prev_url);
        }

        if ($method == 'success') {
            $this->alert('success', __('Баланс успешно пополнен!'));
            return Redirect::to($donate_prev_url);
        }

        $this->alert('danger', __('Ошибка при пополнении баланса!'));
        return Redirect::to($donate_prev_url);
    }

    public function qiwiStatus(Request $request, string $method)
    {
        $donate_prev_url = session()->has('donate_prev_url') ? session()->get('donate_prev_url') : route('shop');
        if ($method == 'fail') {
            $this->alert('danger', __('Ошибка при пополнении баланса!'));
            return Redirect::to($donate_prev_url);
        }

        if ($method == 'success') {
            $this->alert('success', __('Баланс успешно пополнен!'));
            return Redirect::to($donate_prev_url);
        }

        $this->alert('danger', __('Ошибка при пополнении баланса!'));
        return Redirect::to($donate_prev_url);
    }

    public function paypalStatus(Request $request, DonateController $donateController)
    {

        //$donationController->transfer_store(2, 'Baqural');
        //exit('ok');

        Log::channel('paypal')->info("Данные paypal - " . print_r($request->all(), 1));
        $paypal = new PayPal();
        $payment_id = $paypal->getPaymentStatus($request);
        //$payment_id = 'PAYID-MIY6ODA7VA64058YN004104L';

        if ($payment_id) {

            $donate = Donate::where('payment_id', $payment_id)->first();
            Log::channel('paypal')->info("Данные доната - " . print_r($donate, 1));

            session()->put('server_id', $donate->server);

            if ($donate->status === 0) {
                $donate->status = 1;
                $donate->save();

                $donateController->transfer_balance($donate->coins, $donate->user_id);
                if ($donate->bonus_item_id > 0) {
                    $donateController->transfer_item($donate->bonus_item_id, $donate->user_id);
                }

            }

            $this->alert('success', __('Баланс успешно пополнен!'));
            /** Очищаем ID платежа **/
            session()->forget('paypal_payment_id');

        } else {
            $this->alert('danger', __('Ошибка при пополнении баланса!'));
        }

        return Redirect::to(session()->get('donate_prev_url'));
    }

    public function pagseguroStatus(Request $request, DonateController $donateController)
    {

        Log::channel('pagseguro')->info("Данные pagseguro- " . print_r($request->all(), 1));

        if (session()->has('donate_id')) {
            $donate = Donate::find(session()->get('donate_id'));

            Log::channel('pagseguro')->info("Данные доната - " . print_r($donate, 1));

            session()->put('server_id', $donate->server);

            if ($donate->status === 0) {
                $donate->status = 1;
                $donate->save();

                $donateController->transfer_balance($donate->coins, $donate->user_id);
                if ($donate->bonus_item_id > 0) {
                    $donateController->transfer_item($donate->bonus_item_id, $donate->user_id);
                }
            }

            $this->alert('success', "Баланс успешно пополнен!");
            /** Очищаем ID платежа **/
            Session::forget('donate_id');

        } else {
            $this->alert('danger', "Ошибка при пополнении баланса!");
        }

        return Redirect::to(session()->get('donate_prev_url'));
    }

    public function paymentwallStatus(Request $request)
    {
        $donate_prev_url = session()->has('donate_prev_url') ? session()->get('donate_prev_url') : route('shop');
        Paymentwall_Config::getInstance()->set(array(
            'api_type'    => Paymentwall_Config::API_GOODS,
            'public_key'  => config('options.paymentwall_public_key', ''),
            'private_key' => config('options.paymentwall_private_key', ''),
        ));

        $pingback = new Paymentwall_Pingback($_GET, $_SERVER['REMOTE_ADDR']);
        if ($pingback->validate()) {
            $productId = $pingback->getProduct()->getId();
            if ($pingback->isDeliverable()) {
                $status = 1;
            } elseif ($pingback->isCancelable()) {
                $status = 0;
            } elseif ($pingback->isUnderReview()) {
                $status = 0;
            }
            echo 'OK'; // Paymentwall expects response to be OK, otherwise the pingback will be resent
        } else {
            echo $pingback->getErrorSummary();
        }


        if (session()->has('donate_id') && $status = 1) {
            $donate = Donate::find(session()->get('donate_id'));

            if ($donate->status === 0) {
                $donate->status = $status;
                $donate->save();

                $this->transfer_store($donate->coins, $donate->char_name);

            }
            $this->alert('success', "Баланс успешно пополнен!");
            /** Очищаем ID платежа **/
            Session::forget('donate_id');

        } else {
            $this->alert('danger', "Ошибка при пополнении баланса!");
        }

        return Redirect::to($donate_prev_url);
    }

    public function yookassaStatus(Request $request, string $method)
    {
        $donate_prev_url = session()->has('donate_prev_url') ? session()->get('donate_prev_url') : route('shop');
        if ($method == 'fail') {
            $this->alert('danger', __('Ошибка при пополнении баланса!'));
            return Redirect::to($donate_prev_url);
        }

        if ($method == 'success') {
            $this->alert('success', __('Баланс успешно пополнен!'));
            return Redirect::to($donate_prev_url);
        }

        $this->alert('danger', __('Ошибка при пополнении баланса!'));
        return Redirect::to($donate_prev_url);
    }

    public function unitpayStatus(Request $request, string $method)
    {
        $donate_prev_url = session()->has('donate_prev_url') ? session()->get('donate_prev_url') : route('shop');
        if ($method == 'fail') {
            $this->alert('danger', __('Ошибка при пополнении баланса!'));
            return Redirect::to($donate_prev_url);
        }

        if ($method == 'success') {
            $this->alert('success', __('Баланс успешно пополнен!'));
            return Redirect::to($donate_prev_url);
        }

        $this->alert('danger', __('Ошибка при пополнении баланса!'));
        return Redirect::to($donate_prev_url);
    }

    public function cryptocloudStatus(Request $request, string $method)
    {
        $donate_prev_url = session()->has('donate_prev_url') ? session()->get('donate_prev_url') : route('shop');
        if ($method == 'fail') {
            $this->alert('danger', __('Ошибка при пополнении баланса!'));
            return Redirect::to($donate_prev_url);
        }

        if ($method == 'success') {
            $this->alert('success', __('Баланс успешно пополнен!'));
            return Redirect::to($donate_prev_url);
        }

        $this->alert('danger', __('Ошибка при пополнении баланса!'));
        return Redirect::to($donate_prev_url);
    }

    public function paykeeperStatus(Request $request, string $method)
    {
        $donate_prev_url = session()->has('donate_prev_url') ? session()->get('donate_prev_url') : route('shop');
        if ($method == 'fail') {
            $this->alert('danger', __('Ошибка при пополнении баланса!'));
            return Redirect::to($donate_prev_url);
        }

        if ($method == 'success') {
            $this->alert('success', __('Баланс успешно пополнен!'));
            return Redirect::to($donate_prev_url);
        }

        $this->alert('danger', __('Ошибка при пополнении баланса!'));
        return Redirect::to($donate_prev_url);
    }

    public function alfabankStatus(Request $request, string $method)
    {
        $donate_prev_url = session()->has('donate_prev_url') ? session()->get('donate_prev_url') : route('shop');
        if ($method == 'fail') {
            $this->alert('danger', __('Ошибка при пополнении баланса!'));
            return Redirect::to($donate_prev_url);
        }

        if ($method == 'success') {
            $this->alert('success', __('Баланс успешно пополнен!'));
            return Redirect::to($donate_prev_url);
        }

        $this->alert('danger', __('Ошибка при пополнении баланса!'));
        return Redirect::to($donate_prev_url);
    }

    public function tebexStatus(Request $request, string $method)
    {
        $donate_prev_url = session()->has('donate_prev_url') ? session()->get('donate_prev_url') : route('shop');
        if ($method == 'fail') {
            $this->alert('danger', __('Ошибка при пополнении баланса!'));
            return Redirect::to($donate_prev_url);
        }

        if ($method == 'success') {
            $this->alert('success', __('Баланс успешно пополнен!'));
            return Redirect::to($donate_prev_url);
        }

        $this->alert('danger', __('Ошибка при пополнении баланса!'));
        return Redirect::to($donate_prev_url);
    }

    protected function validator(array $data): \Illuminate\Contracts\Validation\Validator
    {
        return Validator::make($data, [
            'amount' => ['required', 'integer', 'min:1', 'max:10000'],
        ]);
    }

    protected function sign($out_amount, $order_id, $merchant_id = 13251, $secret = '')
    {
        return md5($merchant_id . ':' . $out_amount . ':' . $secret . ':' . $order_id);
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
