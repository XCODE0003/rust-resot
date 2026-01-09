<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DonateController;
use App\Http\Controllers\ShopController;
use Illuminate\Http\Request;
use App\Models\Donate;
use App\Models\User;
use App\Models\Server;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class PaymentsController extends Controller
{
    public function freekassa(Request $request, ShopController $shopController, DonateController $donateController)
    {
        if ($request->get('MERCHANT_ID')) {

            Log::channel('freekassa')->info("Робот: Оплата freekassa. Данные платежа - " . print_r($request->all(), 1));

            $merchant_id = config('options.freekassa_merchant_id', '');
            $merchant_secret = config('options.freekassa_secret_word_2', '');

            $sign = md5($merchant_id . ':' . $request->get('AMOUNT') . ':' . $merchant_secret . ':' . $request->get('MERCHANT_ORDER_ID'));

            if ($sign != $request->get('SIGN')) {
                Log::channel('freekassa')->info("wrong sign");
                die('wrong sign');
            }

            $donate = Donate::find($request->get('MERCHANT_ORDER_ID'));

            if (!$donate) {
                Log::channel('freekassa')->info("not donate");
                die('[ERROR] not donate');
            }

            Log::channel('freekassa')->info("Данные доната - " . print_r($donate, 1));

            session()->put('server_id', $donate->server);

            if ($donate->status === 0) {
                $donate->status = 1;
                $donate->save();

                if ($donate->item_id > 0) {
                    $shopController->send_item($donate->item_id, $donate->var_id, $donate->steam_id, $donate->server);
                } else {
                    $amount = $donate->amount + $donate->bonus_amount;
                    $donateController->addBalance($donate->user_id, $amount);
                }

            }

            die('YES');
        } else {
            Log::channel('freekassa')->info("Not MERCHANT ID");
            die('[ERROR] not MERCHANT ID');
        }
    }

    public function appCent(Request $request, ShopController $shopController, DonateController $donateController)
    {
        Log::channel('appcent')->info("Робот: Оплата freekassa. Данные платежа - " . print_r($request->all(), 1));

        if ($request->input('Status') == 'SUCCESS') {
            $donate = Donate::find($request->input('InvId'));

            Log::channel('appcent')->info("Данные доната - " . print_r($donate, 1));

            $apiToken = config('options.cent_authorization', '');

            $sing = strtoupper(md5($request->input('OutSum') . ":" . $request->input('InvId') . ":" . $apiToken));

            if ($request->input('SignatureValue') == $sing) {

                session()->put('server_id', $donate->server);

                if ($donate->status === 0) {
                    $donate->status = 1;
                    $donate->save();

                    if ($donate->item_id > 0) {
                        $shopController->send_item($donate->item_id, $donate->var_id, $donate->steam_id, $donate->server);
                    } else {
                        $amount = $donate->amount + $donate->bonus_amount;
                        $donateController->addBalance($donate->user_id, $amount);
                    }
                }


            } else {
                Log::channel('appcent')->info("error sing");
            }
        } else {
            Log::channel('appcent')->info("error payment");
        }
        die('OK');
    }

    public function qiwi(ShopController $shopController)
    {
        $data = json_decode(file_get_contents('php://input'));

        Log::channel('qiwi')->info("Робот: Оплата qiwi. Данные платежа - " . print_r($data, 1));

        if (isset($data->bill->customFields->innerID) && $data->bill->customFields->innerID !== NULL) {

            $donate = Donate::find($data->bill->customFields->innerID);

            Log::channel('qiwi')->info("Данные доната - " . print_r($donate, 1));

            session()->put('server_id', $donate->server);

            if ($donate->status === 0) {
                $donate->status = 1;
                $donate->save();

                $shopController->send_item($donate->item_id, $donate->var_id, $donate->steam_id, $donate->server);
            }
        }

        echo 'OK'; // в конце дать ответ "OK"
    }

    public function primepayments(Request $request, DonationController $donationController, DonateController $donateController)
    {

        Log::channel('primepayments')->info("Робот: Оплата primepayments. Данные платежа - " . print_r($request->all(), 1));

        /*
        $donate = Donate::find($request->get('innerID'));
        session()->put('server_id', $donate->server);
        $donationController->transfer_store($donate->coins, $donate->char_name);
        //$donateController->transfer_balance($donate->coins, $donate->user_id);
        */

        $secret2 = config('options.primepayments_secret2', 'xxxx');   // Секретное слово 2
        $hash = md5($secret2 . $request->get('orderID') . $request->get('payWay') . $request->get('innerID') . $request->get('sum') . $request->get('webmaster_profit'));
        if ($hash != $request->get('sign')) {
            Log::channel('primepayments')->info("wrong sign");
            die('wrong sign'); // проверка подписи
        }

        $donate = Donate::find($request->get('innerID'));

        Log::channel('primepayments')->info("Данные доната - " . print_r($donate, 1));

        session()->put('server_id', $donate->server);

        if ($request->get('action') == 'order_cancel') {
            $donate->status = 0;
            $donate->save();
        }

        if ($request->get('action') == 'order_payed') {
            if ($donate->status === 0) {
                $donate->status = 1;
                $donate->save();

                if ($donate->user_id !== 0) {
                    $donateController->transfer_balance($donate->coins, $donate->user_id);
                    $donateController->transfer_item($donate->bonus_item_id, $donate->user_id);
                } else {
                    $donationController->transfer_store($donate->coins, $donate->char_name);
                    $donationController->transfer_item_store($donate->bonus_item_id, $donate->char_name);
                }
            }
        }

        echo 'OK'; // в конце дать ответ "OK"
    }

    public function enot(Request $request, ShopController $shopController, DonateController $donateController)
    {
        $method = 'success';
        Log::channel('enot')->info("Робот: Оплата enot. Данные платежа - " . print_r($request->all(), 1));

        $merchant = $request->get('merchant');
        $secret_word2 = config('options.enot_secret_word_2', 'xxxx');

        $sign = md5($merchant . ':' . $request->get('amount') . ':' . $secret_word2 . ':' . $request->get('merchant_id'));
        if ($sign != $request->get('sign_2')) {
            return $this->error('wrong sign');
        }

        $donate = Donate::find(request()->get('merchant_id'));

        Log::channel('enot')->info("Данные доната - " . print_r($donate, 1));

        session()->put('server_id', $donate->server);

        if ($method == 'fail') {
            $donate->status = 0;
            $donate->save();
            return $this->success();
        }

        if ($method == 'success') {

            if ($donate->status == 0) {
                $donate->status = 1;
                $donate->save();

                if ($donate->item_id > 0) {
                    $shopController->send_item($donate->item_id, $donate->var_id, $donate->steam_id, $donate->server);
                } else {
                    $amount = $donate->amount + $donate->bonus_amount;
                    $donateController->addBalance($donate->user_id, $amount);
                }

            }

            return $this->success();
        }

        return $this->error($method . ' not supported');

    }

    public function pagseguro(DonationController $donationController, DonateController $donateController)
    {

        $data = json_decode(file_get_contents('php://input'));

        /*
        $donate = Donate::find($data->bill->innerID);
        session()->put('server_id', $donate->server);
        //$donationController->transfer_store($donate->coins, $donate->char_name);
        $donateController->transfer_balance($donate->coins, $donate->user_id);
        */


        Log::channel('pagseguro')->info("Робот: Оплата pagseguro. Данные платежа - " . print_r($data, 1));

        if ($data->bill->customFields->innerID !== NULL) {

            $donate = Donate::find($data->bill->customFields->innerID);

            Log::channel('pagseguro')->info("Данные доната - " . print_r($donate, 1));

            session()->put('server_id', $donate->server);

            if ($donate->status === 0) {
                $donate->status = 1;
                $donate->save();

                if ($donate->user_id !== 0) {
                    $donateController->transfer_balance($donate->coins, $donate->user_id);
                    $donateController->transfer_item($donate->bonus_item_id, $donate->user_id);
                } else {
                    $donationController->transfer_store($donate->coins, $donate->char_name);
                    $donationController->transfer_item_store($donate->bonus_item_id, $donate->char_name);
                }
            }
        }

        echo 'OK'; // в конце дать ответ "OK"
    }

    public function paymentwall(ShopController $shopController)
    {

        $data = json_decode(file_get_contents('php://input'));

        /*
        $donate = Donate::find($data->bill->innerID);
        session()->put('server_id', $donate->server);
        //$donationController->transfer_store($donate->coins, $donate->char_name);
        $donateController->transfer_balance($donate->coins, $donate->user_id);
        */


        Log::channel('paymentwall')->info("Робот: Оплата paymentwall. Данные платежа - " . print_r($data, 1));

        if ($data->bill->customFields->innerID !== NULL) {

            $donate = Donate::find($data->bill->customFields->innerID);

            Log::channel('paymentwall')->info("Данные доната - " . print_r($donate, 1));

            session()->put('server_id', $donate->server);

            if ($donate->status === 0) {
                $donate->status = 1;
                $donate->save();

                $shopController->send_item($donate->item_id, $donate->var_id, $donate->steam_id, $donate->server);
            }
        }

        echo 'OK'; // в конце дать ответ "OK"
    }

    public function paypal(Request $request, ShopController $shopController, DonateController $donateController)
    {
        Log::channel('paypal')->info("Робот: Оплата paypal. Данные платежа - " . print_r($request->all(), 1));

        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);

        Log::channel('paypal')->info("Робот: Оплата paypal. Данные платежа - " . print_r($raw_post_array, 1));

        $myPost = [];
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2) {
                if ($keyval[0] === 'payment_date') {
                    if (substr_count($keyval[1], '+') === 1) {
                        $keyval[1] = str_replace('+', '%2B', $keyval[1]);
                    }
                }
                $myPost[$keyval[0]] = urldecode($keyval[1]);
            }
        }
        $req = 'cmd=_notify-validate';
        foreach ($myPost as $key => $value) {
            $value = urlencode($value);
            $req .= "&$key=$value";
        }

        $ch = curl_init('https://ipnpb.paypal.com/cgi-bin/webscr?'.$req);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        $res = curl_exec($ch);
        $info = curl_getinfo($ch);
        $http_code = $info['http_code'];
        curl_close($ch);

        Log::channel('paypal')->info("http_code - " . $http_code);
        Log::channel('paypal')->info("res - " . print_r($res, 1));

        if ($http_code == 200 && $res == 'VERIFIED' && $myPost['payment_status'] == 'Completed') {

            $donate = Donate::find($myPost['custom']);
            if (!$donate) {
                Log::channel('paypal')->info("wrong donate");
                return $this->error('wrong donate');
            }

            Log::channel('paypal')->info("Данные доната - " . print_r($donate, 1));
            session()->put('server_id', $donate->server);

            if ($donate->status == 0) {
                $donate->status = 1;
                $donate->payment_id = $myPost['txn_id'];
                $donate->save();

                if ($donate->item_id > 0) {
                    $shopController->send_item($donate->item_id, $donate->var_id, $donate->steam_id, $donate->server);
                } else {
                    $amount = $donate->amount + $donate->bonus_amount;
                    $donateController->addBalance($donate->user_id, $amount);
                }

            }

            return $this->success();
        }

        Log::channel('paypal')->info("wrong status");
        return $this->error('wrong status');
    }

    public function tebex(Request $request, ShopController $shopController, DonateController $donateController)
    {
        Log::channel('tebex')->info("Робот: Оплата tebex. Данные платежа - " . print_r($request->all(), 1));

        $secret = config('options.tebex_webhook_Key', "");
        $allowed_ips = ['18.209.80.3', '54.87.231.232', '159.224.90.242'];

        // if (!in_array($request->ip(), $allowed_ips)) {
        //     Log::channel('tebex')->info("ip denied");
        //     return new JsonResponse(['error' => ['message' => 'ip denied']]);
        // }

        $json = file_get_contents('php://input');
        $data = json_decode($json);

        Log::channel('tebex')->info("Json - " . $json);

        if (isset($data->type) && $data->type === 'validation.webhook') {
            Log::channel('tebex')->info("validate ok");
            return new JsonResponse(['id' => $data->id]);
        }

        if (!isset($data->type) || $data->type != 'payment.completed') {
            Log::channel('tebex')->info("wrong type");
            return new JsonResponse(['error' => ['message' => 'wrong type']]);
        }

        if (!isset($data->subject)) {
            Log::channel('tebex')->info("wrong subject");
            return new JsonResponse(['error' => ['message' => 'wrong subject']]);
        }

        $subject = $data->subject;

        if (!isset($subject->status->id) || $subject->status->id != 1) {
            Log::channel('tebex')->info("wrong status");
            return new JsonResponse(['error' => ['message' => 'wrong status']]);
        }

        $sign = hash_hmac('sha256', hash('sha256', $json), $secret);
        $signature = $request->header('X-Signature');
        Log::channel('tebex')->info("sign: " . $sign . " | " . $signature);

        if ($sign != $signature) {
            Log::channel('tebex')->info("wrong signature");
            return new JsonResponse(['error' => ['message' => 'wrong signature']]);
        }

        //Находим пользователя
        if (!isset($subject->customer) || !isset($subject->customer->username) || !isset($subject->customer->username->id)) {
            Log::channel('tebex')->info("user not found");
            return new JsonResponse(['error' => ['message' => 'user not found']]);
        }
        $user = User::query()->where('steam_id', $subject->customer->username->id)->first();
        if (!$user) {
            Log::channel('tebex')->info("user not found");
            return new JsonResponse(['error' => ['message' => 'user not found']]);
        }

        //Проверяем, что такого платежа еще не было
        if (Donate::query()->where('payment_id', $subject->transaction_id)->exists()) {
            Log::channel('tebex')->info("payment already exist");
            return new JsonResponse(['error' => ['message' => 'payment already exist']]);
        }

        //Создаем донат
        $price_rub = ($subject->price_paid->currency === 'USD') ? $subject->price_paid->amount * config('options.exchange_rate_usd', 70) : $subject->price_paid->amount;

        if (isset($subject->custom->donate_id)) {
            $donate = Donate::query()->where('id', $subject->custom->donate_id)->where('status', 0)->first();
        }
        if (!isset($donate) || !$donate) {
            $donate = Donate::create([
                'payment_system' => 'tebex: ' . $subject->payment_method->name,
                'payment_id'     => $subject->transaction_id,
                'user_id'        => $user->id,
                'amount'         => $price_rub,
                'bonus_amount'   => 0,
                'item_id'        => 0,
                'var_id'         => 0,
                'steam_id'       => $subject->customer->username->id,
                'server'         => 1,
                'status'         => 1,
            ]);
        }

        Log::channel('tebex')->info("Данные доната - " . print_r($donate, 1));

        session()->put('server_id', $donate->server);

        // На tebex идет покупка товара или сета вместо пополнения баланса
        if (isset($subject->products) && is_array($subject->products)) {
            $amount = 0;
            foreach ($subject->products as $product) {

                $quantity = $product->quantity;
                if ($product->custom !== null && str_contains($product->custom, 'item')) {
                    $item_id = intval(str_replace('item=', '', $product->custom));
                    $shopController->send_item($donate->user_id, $item_id, $donate->var_id, $donate->steam_id, $donate->server, $quantity);
                } elseif ($product->custom !== null && str_contains($product->custom, 'set')) {
                    $set_id = intval(str_replace('set=', '', $product->custom));
                    $shopController->send_set($donate->user_id, $set_id, $donate->steam_id, $donate->server, $quantity);
                }

                //Покупка гифт карт, пополняем на баланс
                elseif ($product->custom !== null && str_contains($product->custom, 'giftcard')) {
                    $giftcard_amount = intval(str_replace('giftcard=', '', $product->custom));
                    $amount += $giftcard_amount * $quantity * config('options.exchange_rate_usd', 70);
                }
            }

            if ($amount > 0) {
                $amount = $amount + $donate->bonus_amount;
                $donate->amount = $amount;
                $donate->status = 1;
                $donate->save();
                $donateController->addBalance($donate->user_id, $amount);
            }
        }

        return new JsonResponse(['id' => $data->id]);
    }

    public function yookassa(Request $request, ShopController $shopController, DonateController $donateController)
    {
        Log::channel('yookassa')->info("Робот: Оплата yookassa. Данные платежа - " . print_r($request->all(), 1));

        if ($request->post('event') != 'payment.succeeded') {
            Log::channel('yookassa')->info("wrong event");
            die('wrong event');
        }

        if (!$request->has('object')) {
            Log::channel('yookassa')->info("wrong object");
            die('wrong object');
        }

        $object = $request->post('object');
        if (!isset($object['id'])) {
            Log::channel('yookassa')->info("wrong id");
            die('wrong id');
        }
        if (!isset($object['status']) || $object['status'] != 'succeeded') {
            Log::channel('yookassa')->info("wrong status");
            die('wrong status');
        }

        $donate = Donate::where('payment_id', $object['id'])->first();
        if (!$donate) {
            Log::channel('yookassa')->info("not donate");
            die('[ERROR] not donate');
        }

        Log::channel('yookassa')->info("Данные доната - " . print_r($donate, 1));

        if ($donate->status === 0) {
            $donate->status = 1;
            $donate->save();

            if ($donate->item_id > 0) {
                $shopController->send_item($donate->item_id, $donate->var_id, $donate->steam_id, $donate->server);
            } else {
                $amount = $donate->amount + $donate->bonus_amount;
                $donateController->addBalance($donate->user_id, $amount);
            }

        }

        die('YES');
    }

    public function paykeeper(Request $request, ShopController $shopController, DonateController $donateController)
    {
        Log::channel('paykeeper')->info("Робот: Оплата paykeeper. Данные платежа - " . print_r($request->all(), 1));

        if (!$request->has('id') || !$request->has('sum') || !$request->has('clientid') || !$request->has('orderid') || !$request->has('key')) {
            Log::channel('paykeeper')->info("wrong id");
            die('wrong id');
        }

        $id = $request->id;
        $sum = $request->sum;
        $clientid = $request->clientid;
        $orderid = $request->orderid;
        $key = $request->key;

        $secret = config('options.paykeeper_secret', '');
        $hash = md5($request->post('id') . $secret);
        $sign = md5($id . $sum . $clientid . $orderid . $secret);

        if ($sign != $key) {
            Log::channel('paykeeper')->info("wrong sign");
            die('wrong sign');
        }

        $donate = Donate::where('id', $orderid)->where('user_id', $clientid)->where('amount', $sum)->first();
        if (!$donate) {
            Log::channel('paykeeper')->info("not donate");
            die('[ERROR] not donate');
        }

        Log::channel('paykeeper')->info("Данные доната - " . print_r($donate, 1));

        session()->put('server_id', $donate->server);

        if ($donate->status === 0) {
            $donate->status = 1;
            $donate->save();

            if ($donate->item_id > 0) {
                $shopController->send_item($donate->item_id, $donate->var_id, $donate->steam_id, $donate->server);
            } else {
                $amount = $donate->amount + $donate->bonus_amount;
                $donateController->addBalance($donate->user_id, $amount);
            }
        }

        die('OK ' . $hash);
    }

    public function alfabank(Request $request, ShopController $shopController, DonateController $donateController)
    {
        Log::channel('alfabank')->info("Робот: Оплата alfabank. Данные платежа - " . print_r($request->all(), 1));

        if (!$request->has('amount') || !$request->has('orderNumber') || !$request->has('processingId') || !$request->has('currency')
            || !$request->has('operation') || !$request->has('operation') || !$request->has('status') || !$request->has('checksum')) {

            Log::channel('alfabank')->info("wrong orderNumber");
            die('[ERROR] wrong orderNumber');
        }

        if ($request->operation != 'deposited') {
            Log::channel('alfabank')->info("wrong operation type");
            die('[ERROR] wrong operation type');
        }
        if ($request->status != 1) {
            Log::channel('alfabank')->info("wrong operation status");
            die('[ERROR] wrong operation status');
        }

        $data = $request->all();
        unset($data['checksum']);
        ksort($data);

        $open_key = config('options.alfabank_open_key', '');
        $callback_token = config('options.alfabank_callback_token', '');

        $sign_str = '';
        foreach ($data as $key => $value) {
            $sign_str .= $key . ';' . $value . ';';
        }

        $sign = hash_hmac('sha256', $sign_str, $callback_token);

        if (strtoupper($sign) != strtoupper($request->checksum)) {
            Log::channel('alfabank')->info("wrong checksum");
            die('[ERROR] wrong checksum');
        }

        $amount = floatval($data['amount'] / 100);
        $donate = Donate::where('id', $data['orderNumber'])->where('amount', $amount)->first();

        if (!$donate) {
            Log::channel('alfabank')->info("donate not found");
            die('[ERROR] donate not found');
        }

        Log::channel('alfabank')->info("Данные доната - " . print_r($donate, 1));

        session()->put('server_id', $donate->server);

        if ($donate->status === 0) {
            $donate->status = 1;
            $donate->save();

            if ($donate->item_id > 0) {
                $shopController->send_item($donate->item_id, $donate->var_id, $donate->steam_id, $donate->server);
            } else {
                $amount = $donate->amount + $donate->bonus_amount;
                $donateController->addBalance($donate->user_id, $amount);
            }
        }

        die('OK');
    }

    public function unitpay(Request $request, ShopController $shopController, DonateController $donateController)
    {
        Log::channel('unitpay')->info("Робот: Оплата unitpay. Данные платежа - " . print_r($request->all(), 1));

        $allowed_ips = ['31.186.100.49', '52.29.152.23', '52.19.56.234', '191.101.157.219'];

        if (!in_array($request->ip(), $allowed_ips)) {
            Log::channel('unitpay')->info("ip denied");
            return new JsonResponse(['error' => ['message' => 'ip denied']]);
        }

        if (!$request->has('method') || !$request->has('params')) {
            Log::channel('unitpay')->info("wrong method");
            return new JsonResponse(['error' => ['message' => 'wrong method']]);
        }

        $params = $request->params;

        if (!isset($params['account'])) {
            Log::channel('unitpay')->info("account not found");
            return new JsonResponse(['error' => ['message' => 'account not found']]);
        }

        //Если метод check, то отдаем успешный ответ, что мы разрешаем оплату
        if ($request->get('method') === 'check') {
            //Если аккаунт = test (тестовый режим), то отдаем сразу успех
            if ($params['account'] === 'test') {
                return new JsonResponse(['result' => ['message' => 'Запрос успешно обработан']]);
            }

            $donate = Donate::find($params['account']);
            if (!$donate) {
                Log::channel('unitpay')->info("check not donate");
                return new JsonResponse(['error' => ['message' => 'check not donate']]);
            }

            return new JsonResponse(['result' => ['message' => 'Запрос успешно обработан']]);
        }

        //Если успешная оплата
        if ($request->get('method') === 'pay') {
            $sign = $this->signUnitpay($request);
            Log::channel('unitpay')->info("sign: " . $sign);

            if ($sign != $params['signature']) {
                Log::channel('unitpay')->info("wrong signature");
                return new JsonResponse(['error' => ['message' => 'wrong signature']]);
            }

            $donate = Donate::find($params['account']);
            if (!$donate) {
                Log::channel('unitpay')->info("not donate");
                return new JsonResponse(['error' => ['message' => 'not donate']]);
            }

            Log::channel('unitpay')->info("Данные доната - " . print_r($donate, 1));

            session()->put('server_id', $donate->server);

            if ($donate->status === 0) {
                $donate->status = 1;
                $donate->save();

                if ($donate->item_id > 0) {
                    $shopController->send_item($donate->item_id, $donate->var_id, $donate->steam_id, $donate->server);
                } else {
                    $amount = $donate->amount + $donate->bonus_amount;
                    $donateController->addBalance($donate->user_id, $amount);
                }

            }
        }

        return new JsonResponse(['result' => ['message' => 'Запрос успешно обработан']]);
    }

    public function cryptocloud(Request $request, ShopController $shopController, DonateController $donateController)
    {
        Log::channel('cryptocloud')->info("Робот: Оплата cryptocloud. Данные платежа - " . print_r($request->all(), 1));

        if ($request->post('order_id')) {

            $shop_id = config('options.cryptocloud_shop_id', '');
            $secret_key = config('options.cryptocloud_secret_key', '');

            if ($request->post('status') !== 'success') {
                Log::channel('cryptocloud')->info("fail status");
                die('[ERROR] fail status');
            }

            if (!$request->has('token') || $request->post('token') == '') {
                Log::channel('cryptocloud')->info("invalid token");
                die('[ERROR] invalid token');
            }

            // проверяем токен
            $token = $request->post('token');
            $jwtParts = explode('.', $token);
            if (!isset($jwtParts[2])) {
                Log::channel('cryptocloud')->info("invalid token parts");
                die('[ERROR] invalid token parts');
            }
            $jwtHeader = base64_decode($jwtParts[0]);
            $jwtPayload = base64_decode($jwtParts[1]);
            $signature = $jwtParts[2];

            $validSignature = hash_hmac('sha256', "$jwtParts[0].$jwtParts[1]", $secret_key, true);
            $validSignature = base64_encode($validSignature);
            $validSignature = strtr(rtrim($validSignature, '='), '+/', '-_');

            // Сравниваем подпись из токена с подписью, которую мы сгенерировали
            Log::channel('cryptocloud')->info("Signature: " . $validSignature . ' - ' . $signature);

            if ($signature !== $validSignature) {
                Log::channel('cryptocloud')->info("wrong token signature");
                die('[ERROR] wrong token signature');
            }

            $donate = Donate::find($request->post('order_id'));
            if (!$donate) {
                Log::channel('cryptocloud')->info("not donate");
                die('[ERROR] not donate');
            }

            Log::channel('cryptocloud')->info("Данные доната - " . json_encode($donate->getOriginal()));
            session()->put('server_id', $donate->server);

            if ($donate->status === 0) {
                $donate->status = 1;
                $donate->payment_id = $request->post('invoice_id');
                $donate->save();

                if ($donate->item_id > 0) {
                    $shopController->send_item($donate->item_id, $donate->var_id, $donate->steam_id, $donate->server);
                } else {
                    $amount = $donate->amount + $donate->bonus_amount;
                    $donateController->addBalance($donate->user_id, $amount);
                }
            }

            die('OK');
        } else {
            Log::channel('cryptocloud')->info("Not found order ID");
            die('[ERROR] Not found order ID');
        }
    }

    public function skinspay(Request $request, ShopController $shopController, DonateController $donateController)
    {
        Log::channel('skinspay')->info("Робот: Оплата skinspay. Данные платежа - " . print_r($request->all(), 1));

        if (!$request->has('api_key') || $request->api_key != 'SjA2o7v@Bqn$mZNCBd2EX8Vwd!5') {
            return response()->json([
                'status' => 'error',
                'result' => 'API key is invalid.',
            ]);
        }

        if (!$request->has('userID') || !$request->has('Amount') || !$request->has('PaymentID')) {
            return response()->json([
                'status' => 'error',
                'result' => 'userID|Amount|PaymentID is missed',
            ]);
        }

        $user = User::where('steam_id', $request->userID)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'result' => 'user not find',
            ]);
        }

        $donate = Donate::create([
            'payment_system' => 'skinspay',
            'payment_id'     => $request->PaymentID,
            'user_id'        => $user->id,
            'amount'         => floatval($request->Amount),
            'bonus_amount'   => 0,
            'item_id'        => 0,
            'var_id'         => 0,
            'steam_id'       => $request->userID,
            'server'         => 1,
            'status'         => 1,
        ]);

        session()->put('server_id', $donate->server);

        if ($donate->item_id > 0) {
            $shopController->send_item($donate->item_id, $donate->var_id, $donate->steam_id, $donate->server);
        } else {
            $amount = $donate->amount + $donate->bonus_amount;
            $donateController->addBalance($donate->user_id, $amount);
        }

        return response()->json([
            'status' => 'success',
            'result' => 'ok',
        ]);
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

    public function signUnitpay(Request $request): string
    {
        $secret_key = config('options.unitpay_secret_key', '');

        $method = $request->get('method');
        $params = $request->get('params');
        unset($params['signature']);

        $paramsArray = [];
        foreach ($params as $value) {
            $paramsArray[] = $value;
        }

        ksort($paramsArray);

        $param_str = '';
        foreach ($paramsArray as $param) {
            $param_str .= $param . '{up}';
        }

        return hash('sha256', $method . '{up}' . $param_str . $secret_key);
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
