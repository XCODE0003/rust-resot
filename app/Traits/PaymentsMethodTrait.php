<?php

namespace App\Traits;

use App\Models\Donate;
use App\Models\User;
use App\Http\Controllers\ShopController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use GuzzleHttp\Client;
use Paymentwall\Paymentwall_Config;
use Paymentwall\Paymentwall_Widget;
use Paymentwall\Paymentwall_Product;
use Paymentwall\Paymentwall_Pingback;
use App\Services\PayPal;
use App\Services\Qrcode;
use App\Services\PixCode;
use App\Services\Pally;
use App\Services\Heleket;
use MongoDB\Driver\Query;
use YooKassa\Client as YClient;
use URL;
use Session;

trait PaymentsMethodTrait
{

    public function setPayment($request, $shopitem, $price_rub, $price_usd, $qty = 1)
    {
        $server = $request->has('server_id') ? $request->server_id : 1;
        $steam_id = ($request->has('steam_id') && $request->steam_id != '') ? $request->steam_id : auth()->user()->steam_id;
        $payment_id = $request->payment_id;
        $email = $request->has('email') ? $request->email : '';
        $currency = (app()->getLocale() == 'ru') ? 'RUB' : 'USD';
        $price_rub = abs($price_rub);
        $price_usd = abs($price_usd);
        $price = ($currency == 'USD') ? $price_usd : $price_rub;
        if ($currency === 'USD')
            $price_rub = $price_usd * config('options.exchange_rate_usd', 1);

        //Начисляем бонус
        $price_amount = 0;
        if (session()->has('deposit_bonus') && session('deposit_bonus') > 0) {
            $price_amount = round($price_rub * intval(session('deposit_bonus')) / 100, 2);
            $request->session()->forget('deposit_bonus');
        }

        //Делаем проверку, если метод с выбором платежки, то определяем исходя из суммы
        if ($payment_id == 35) {
            $payment_id = $price_rub < 500 ? 3 : 5;
        }

        //Делаем проверку, если метод 451, то это иноземные карты
        if ($payment_id == 451) {
            $payment_id = 45;
            $unitpay_method = 'cardForeign';
        }
        dd($payment_id);
        switch ($payment_id) {

            case 1: {
                //Paymentwall
                $price_rub = ($currency == 'USD') ? $price_usd * config('options.exchange_rate_usd', 70) : $price_rub;

                if ($price_rub < 500) {
                    $this->alert('danger', __('Временно минимальная сумма пополнения от 500 ₽') . "<br>" . __('Если вам нужно пополнить счет менее 500р - напишите в наш') . ' - <a href="https://discord.com/invite/rustresort">Discord</a>');
                    return back();
                }

                Paymentwall_Config::getInstance()->set(array(
                    'api_type' => Paymentwall_Config::API_GOODS,
                    'public_key' => config('options.paymentwall_public_key', ''),
                    'private_key' => config('options.paymentwall_private_key', '')
                ));

                $donate = Donate::create([
                    'payment_system' => 'paymentwall',
                    'user_id' => auth()->user()->id,
                    'amount' => $price_rub,
                    'bonus_amount' => $price_amount,
                    'item_id' => $shopitem->id,
                    'var_id' => $request->var_id,
                    'server' => $server,
                    'status' => 0,
                ]);

                session()->put('donate_id', $donate['id']);

                $widget = new Paymentwall_Widget(
                    'user' . auth()->id(),   // id of the end-user who's making the payment
                    'pw',          // widget code, e.g. pw; can be picked inside of your merchant account
                    array(         // product details for Flexible Widget Call. To let users select the product on Paymentwall's end, leave this array empty
                        new Paymentwall_Product(
                            $donate['id'],                           // id of the product in your system
                            $price,                                   // price
                            $currency,                                  // currency code
                            'Пополнение баланса для проекта ' . config('app.name'),                      // product name
                            Paymentwall_Product::TYPE_FIXED, // this is a time-based product; for one-time products, use Paymentwall_Product::TYPE_FIXED and omit the following 3 array elements
                            1,                                      // duration is 1
                            Paymentwall_Product::PERIOD_TYPE_MONTH, //               month
                            false                                    // recurring
                        )
                    ),
                    array('email' => config('options.smtp_user', ''))           // additional parameters
                );

                echo $widget->getHtmlCode();

            }

            case 2: {
                //Qiwi

                $price_rub = ($currency == 'USD') ? $price_usd * config('options.exchange_rate_usd', 70) : $price_rub;

                if ($price_rub < 500) {
                    $this->alert('danger', __('Временно минимальная сумма пополнения от 500 ₽') . "<br>" . __('Если вам нужно пополнить счет менее 500р - напишите в наш') . ' - <a href="https://discord.com/invite/rustresort">Discord</a>');
                    return back();
                }

                $donate = Donate::create([
                    'payment_system' => 'qiwi',
                    'user_id' => auth()->user()->id,
                    'amount' => $price_rub,
                    'bonus_amount' => $price_amount,
                    'item_id' => $shopitem->id,
                    'var_id' => $request->var_id,
                    'server' => $server,
                    'status' => 0,
                ]);

                $billPayments = new BillPayments(config('options.qiwi_secret_key', ''));
                $billId = $billPayments->generateId();
                $lifetime = $billPayments->getLifetimeByDay(1);

                $customFields = ['innerID' => $donate['id']];
                $fields = [
                    'amount' => $price,
                    'currency' => $currency,
                    'pay_source' => 'qw',
                    'comment' => 'Donation for the project ' . config('app.name'),
                    'expirationDateTime' => $lifetime,
                    'email' => config('options.smtp_user', ''), // e-mail
                    'account' => config('options.qiwi_account', ''),
                    'successUrl' => config('app.url', '') . 'qiwi/success',
                    'failUrl' => config('app.url', '') . 'qiwi/fail',
                    'customFields' => $customFields,
                ];

                $response = $billPayments->createBill($billId, $fields);

                if (isset($response['payUrl'])) {
                    // в переменной payUrl будет ссылка, на этот адрес вам нужно перенаправить пользователя
                    return Redirect::to($response['payUrl']);
                } else {
                    $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                    return back();
                }

            }

            case 3: {
                //Enot.io

                $price_rub = ($currency == 'USD') ? $price_usd * config('options.exchange_rate_usd', 70) : $price_rub;

                if ($price_rub < 200) {
                    $this->alert('danger', __('Минимальная сумма пополнения от 200 ₽'));
                    return back();
                }

                $params = array(
                    'account' => auth()->user()->id,
                    'currency' => $currency,
                    'desc' => 'Donation for the project ' . config('app.name'),
                    'sum' => $price,
                );

                $donate = Donate::create([
                    'payment_system' => 'enot.io',
                    'user_id' => auth()->user()->id,
                    'amount' => $price_rub,
                    'bonus_amount' => $price_amount,
                    'item_id' => $shopitem->id,
                    'var_id' => $request->var_id,
                    'steam_id' => $steam_id,
                    'server' => $server,
                    'status' => 0,
                ]);

                $sign = $this->sign($params['sum'], $donate->id, config('options.enot_merchant_id', ""), config('options.enot_secret_word', ""));
                $merchant_id = config('options.enot_merchant_id', "");
                $amount = $params['sum'];
                $payment_id = $donate->id;
                $cur = $params['currency'];
                $desc = $params['desc'];

                return view('utils.enot', compact('merchant_id', 'amount', 'payment_id', 'cur', 'desc', 'sign'));
            }

            case 4: {
                //App.cent

                $price_rub = ($currency == 'USD') ? $price_usd * config('options.exchange_rate_usd', 70) : $price_rub;

                if ($price_rub < 500) {
                    $this->alert('danger', __('Временно минимальная сумма пополнения от 500 ₽') . "<br>" . __('Если вам нужно пополнить счет менее 500р - напишите в наш') . ' - <a href="https://discord.com/invite/rustresort">Discord</a>');
                    return back();
                }

                $client = new Client();

                $donate = Donate::create([
                    'payment_system' => 'app.cent',
                    'user_id' => auth()->user()->id,
                    'amount' => $price_rub,
                    'bonus_amount' => $price_amount,
                    'item_id' => $shopitem->id,
                    'var_id' => $request->var_id,
                    'server' => $server,
                    'status' => 0,
                ]);

                $res = $client->post('https://cent.app/api/v1/bill/create', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . config('options.cent_authorization', ''),
                    ],
                    'form_params' => [
                        'amount' => intval($price),
                        'order_id' => $donate['id'],
                        'shop_id' => config('options.cent_shop_id', ''),
                        'currency_in' => $currency,
                        'name' => 'Donation for the project ' . config('app.name'),
                    ],
                ]);

                $data = json_decode($res->getBody()->getContents(), true);

                return Redirect::to($data['link_page_url']);
            }

            case 5: {
                //Freekassa
                $price_rub = ($currency == 'USD') ? $price_usd * config('options.exchange_rate_usd', 70) : $price_rub;

                if ($price_rub < 500) {
                    $this->alert('danger', __('Временно минимальная сумма пополнения от 500 ₽') . "<br>" . __('Если вам нужно пополнить счет менее 500р - напишите в наш') . ' - <a href="https://discord.com/invite/rustresort">Discord</a>');
                    return back();
                }

                $merchant_id = config('options.freekassa_merchant_id', '');
                $secret_word = config('options.freekassa_secret_word', '');

                $donate = Donate::create([
                    'payment_system' => 'freekassa',
                    'user_id' => auth()->user()->id,
                    'amount' => $price_rub,
                    'bonus_amount' => $price_amount,
                    'item_id' => $shopitem->id,
                    'var_id' => $request->var_id,
                    'steam_id' => $steam_id,
                    'server' => $server,
                    'status' => 0,
                ]);

                $sign = md5("{$merchant_id}:{$price}:{$secret_word}:{$currency}:{$donate['id']}");

                $params = [
                    'm' => $merchant_id,
                    'oa' => $price,
                    'o' => $donate['id'],
                    's' => $sign,
                    'currency' => $currency,
                ];

                return Redirect::to('https://pay.freekassa.ru?' . http_build_query($params));
            }


            case 20: {
                //Pay from Internal Balance
                if (app()->getLocale() == 'en') {
                    $price = $price_usd * config('options.exchange_rate_usd', 70);
                }

                //Записываем блок в кеш
                $lock = Cache::lock('pay_balance' . auth()->id() . '_lock', 60);
                if ($lock->get()) {

                    if (auth()->user()->balance < $price) {
                        $lock->release();
                        $this->alert('danger', __('Недостаточно средств на балансе'));
                        return back();
                    }

                    //Начисляем купленый товар
                    $shopController = new ShopController;
                    if ($shopController->send_item(auth()->id(), $shopitem->id, $request->var_id, $steam_id, $server, $qty)) {

                        //Уменьшаем баланс пользователя
                        auth()->user()->decrement('balance', $price);

                        $lock->release();
                        return back();
                    }

                } else {
                    Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' could not pay Balance: due to blocking.');
                    $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                    return back();
                }

            }


            case 21: {
                //PayPal (Personal account)

                if ($price_rub < 100) {
                    $this->alert('danger', __('Минимальная сумма пополнения от 100 ₽'));
                    return back();
                }

                $donate = Donate::create([
                    'payment_system' => 'paypal',
                    'user_id' => auth()->user()->id,
                    'amount' => $price_rub,
                    'bonus_amount' => $price_amount,
                    'item_id' => $shopitem->id,
                    'var_id' => $request->var_id,
                    'steam_id' => $steam_id,
                    'server' => $server,
                    'status' => 0,
                ]);

                $paypal_email = config('options.paypal_email', '');
                $description = 'Пополнение баланса для проекта ' . config('app.name');

                if ($currency == 'RUB') {
                    $price = $price_rub / config('options.exchange_rate_usd', 1);
                } else {
                    $price = $price_usd;
                }

                $dataSet = [
                    'email' => $paypal_email,
                    'description' => $description,
                    'amount' => $price,
                    'currency' => 'USD',
                    'order_id' => $donate->id,
                    'return_url' => route('account.profile'),
                ];

                return view('utils.paypal', compact('dataSet'));
            }

            case 43: {
                //Yookassa

                if ($price_rub < 100) {
                    $this->alert('danger', __('Минимальная сумма пополнения от 100 ₽'));
                    return back();
                }

                require_once base_path('vendor') . '/yoomoney/yookassa-sdk-php/lib/autoload.php';

                $donate = Donate::create([
                    'payment_system' => 'yookassa',
                    'user_id' => auth()->user()->id,
                    'amount' => $price_rub,
                    'bonus_amount' => $price_amount,
                    'item_id' => $shopitem->id,
                    'var_id' => $request->var_id,
                    'steam_id' => $steam_id,
                    'server' => $server,
                    'status' => 0,
                ]);

                $merchant_id = config('options.yookassa_merchant_id', "");
                $key = config('options.yookassa_key', "");

                $yclient = new YClient();
                $yclient->setAuth($merchant_id, $key);
                $payment = $yclient->createPayment(
                    array(
                        'amount' => array(
                            'value' => $price,
                            'currency' => $currency,
                        ),
                        'confirmation' => array(
                            'type' => 'redirect',
                            'return_url' => asset('/yookassa/success'),
                        ),
                        'capture' => true,
                        'description' => __('Пополнение баланса для проекта') . ' ' . config('app.name'),
                    ),
                    uniqid('', true)
                );

                if ($payment && isset($payment->id) && isset($payment->confirmation) && isset($payment->confirmation->confirmation_url)) {
                    $donate->payment_id = $payment->id;
                    $donate->save();

                    return Redirect::to($payment->confirmation->confirmation_url);
                }
            }

            case 44: {
                //PayKeeper
                $price_rub = ($currency == 'USD') ? $price_usd * config('options.exchange_rate_usd', 70) : $price_rub;

                $donate = Donate::create([
                    'payment_system' => 'paykeeper',
                    'user_id' => auth()->user()->id,
                    'amount' => $price_rub,
                    'bonus_amount' => $price_amount,
                    'item_id' => $shopitem->id,
                    'var_id' => $request->var_id,
                    'steam_id' => $steam_id,
                    'server' => $server,
                    'status' => 0,
                ]);

                # Логин и пароль от личного кабинета PayKeeper
                $user = config('options.paykeeper_user', "");
                $password = config('options.paykeeper_password', "");
                # Укажите адрес ВАШЕГО сервера PayKeeper, адрес demo.paykeeper.ru - пример!
                $server_paykeeper = config('options.paykeeper_server_url', '');

                # Basic-авторизация передаётся как base64
                $base64 = base64_encode("$user:$password");
                $headers = array();
                array_push($headers, 'Content-Type: application/x-www-form-urlencoded');

                # Подготавливаем заголовок для авторизации
                array_push($headers, 'Authorization: Basic ' . $base64);

                # Параметры платежа, сумма - обязательный параметр
                # Остальные параметры можно не задавать
                $payment_data = array(
                    "pay_amount" => floatval($price),
                    "clientid" => auth()->id(),
                    "orderid" => $donate->id,
                    "client_email" => auth()->user()->email,
                    "service_name" => 'Replenishment of Balance',
                );

                # Готовим первый запрос на получение токена безопасности
                $uri = "/info/settings/token/";

                $curl = curl_init();
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_URL, $server_paykeeper . $uri);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_HEADER, false);
                $response = curl_exec($curl);
                $php_array = json_decode($response, true);

                # В ответе должно быть заполнено поле token, иначе - ошибка
                if (isset($php_array['token']))
                    $token = $php_array['token'];
                else
                    die();

                # Готовим запрос 3.4 JSON API на получение счёта
                $uri = "/change/invoice/preview/";

                # Формируем список POST параметров
                $request = http_build_query(array_merge($payment_data, array('token' => $token)));
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_URL, $server_paykeeper . $uri);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_HEADER, false);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
                $response = json_decode(curl_exec($curl), true);
                # В ответе должно быть поле invoice_id, иначе - ошибка
                if (isset($response['invoice_id']))
                    $invoice_id = $response['invoice_id'];
                else
                    die();

                # В этой переменной прямая ссылка на оплату с заданными параметрами
                $link = "$server_paykeeper/bill/$invoice_id/";

                return Redirect::to($link);
            }

            case 45: {
                //UnitPay
                $price_rub = ($currency == 'USD') ? $price_usd * config('options.exchange_rate_usd', 70) : $price_rub;

                $project_id = config('options.unitpay_project_id', '');
                $public_key = config('options.unitpay_public_key', '');
                $secret_key = config('options.unitpay_secret_key', '');

                $desc = 'Replenishment of Balance';

                $donate = Donate::create([
                    'payment_system' => 'unitpay',
                    'user_id' => auth()->user()->id,
                    'amount' => $price_rub,
                    'bonus_amount' => $price_amount,
                    'item_id' => $shopitem->id,
                    'var_id' => $request->var_id,
                    'steam_id' => $steam_id,
                    'server' => $server,
                    'status' => 0,
                ]);

                $hashStr = $donate->id . '{up}' . $currency . '{up}' . $desc . '{up}' . $price . '{up}' . $secret_key;
                $sign = hash('sha256', $hashStr);

                $cashItems = base64_encode(json_encode([
                    [
                        "name" => $desc,
                        "count" => 1,
                        "price" => floatval($price),
                        "currency" => $currency,
                        "nds" => 'none',
                        "paymentMethod" => 'full_payment',
                        "type" => "commodity"
                    ]
                ]));

                $params = [
                    'sum' => $price,
                    'account' => $donate->id,
                    'desc' => $desc,
                    'signature' => $sign,
                    'currency' => $currency,
                    'customerEmail' => $email,
                    'cashItems' => $cashItems,
                    'backUrl' => asset('/unitpay/fail'),
                    'resultUrl' => asset('/unitpay/success'),
                ];

                if (isset($unitpay_method) && $unitpay_method === 'cardForeign') {
                    $params['paymentType'] = 'cardForeign';
                    $url = "https://unitpay.ru/pay/{$public_key}/cardForeign?" . http_build_query($params);
                } else {
                    $url = "https://unitpay.ru/pay/{$public_key}?" . http_build_query($params);
                }

                return Redirect::to($url);
            }

            case 46: {
                // Heleket - общий (автоматический выбор валюты)
                $price_usd = ($currency == 'USD') ? $price_usd : $price_rub / config('options.exchange_rate_usd', 70);

                $donate = Donate::create([
                    'payment_system' => 'heleket',
                    'user_id' => auth()->user()->id,
                    'amount' => $price_rub,
                    'bonus_amount' => $price_amount,
                    'item_id' => $shopitem->id,
                    'var_id' => $request->var_id,
                    'steam_id' => $steam_id,
                    'server' => $server,
                    'status' => 0,
                ]);

                $heleketService = new Heleket();

                // Получаем доступные сервисы и выбираем первый доступный
                $services = $heleketService->getPaymentServices();
                if (!$services || empty($services)) {
                    $this->alert('danger', __('Нет доступных способов оплаты. Попробуйте позже.'));
                    return back();
                }

                // Используем первый доступный сервис
                $service = $services[0];
                $network = $service['network'] ?? 'TRON';
                $currency_heleket = $service['currency'] ?? 'USDT';

                $result = $heleketService->createPayment(
                    amount: $price_usd,
                    currency: $currency_heleket,
                    orderId: (string) $donate->id,
                    options: [
                        'url_return' => config('heleket.return_url') ?: url('/heleket/success'),
                        'url_callback' => config('heleket.callback_url') ?: url('/api/payments/notification/heleket'),
                    ]
                );

                if ($result && isset($result['url'])) {
                    return Redirect::to($result['url']);
                }

                $this->alert('danger', __('Ошибка создания платежа. Попробуйте позже.'));
                return back();
            }

            case 47: {
                // Pally.info - Банковская карта (Visa/MasterCard RUB)
                $price_rub = ($currency == 'USD') ? $price_usd * config('options.exchange_rate_usd', 70) : $price_rub;

                $donate = Donate::create([
                    'payment_system' => 'pally',
                    'user_id' => auth()->user()->id,
                    'amount' => $price_rub,
                    'bonus_amount' => $price_amount,
                    'item_id' => $shopitem->id,
                    'var_id' => $request->var_id,
                    'steam_id' => $steam_id,
                    'server' => $server,
                    'status' => 0,
                ]);

                $pallyService = new Pally();

                $result = $pallyService->createBill(
                    amount: $price_rub,
                    orderId: (string) $donate->id,
                    description: __('Пополнение баланса'),
                    options: [
                        'currency_in' => 'RUB',
                        'name' => __('Пополнение баланса'),
                        'custom' => 'donate_id:' . $donate->id,
                        'payment_method' => 'BANK_CARD',
                        'success_url' => config('pally.success_url') ?: url('/pally/success'),
                        'fail_url' => config('pally.fail_url') ?: url('/pally/fail'),
                    ]
                );

                if ($result && isset($result['link_page_url'])) {
                    return Redirect::to($result['link_page_url']);
                }

                $this->alert('danger', __('Ошибка создания платежа. Попробуйте позже.'));
                return back();
            }

            case 49: {
                // Pally.info - СБП
                $price_rub = ($currency == 'USD') ? $price_usd * config('options.exchange_rate_usd', 70) : $price_rub;

                $donate = Donate::create([
                    'payment_system' => 'pally',
                    'user_id' => auth()->user()->id,
                    'amount' => $price_rub,
                    'bonus_amount' => $price_amount,
                    'item_id' => $shopitem->id,
                    'var_id' => $request->var_id,
                    'steam_id' => $steam_id,
                    'server' => $server,
                    'status' => 0,
                ]);

                $pallyService = new Pally();

                $result = $pallyService->createBill(
                    amount: $price_rub,
                    orderId: (string) $donate->id,
                    description: __('Пополнение баланса'),
                    options: [
                        'currency_in' => 'RUB',
                        'name' => __('Пополнение баланса'),
                        'custom' => 'donate_id:' . $donate->id,
                        'payment_method' => 'SBP',
                        'success_url' => config('pally.success_url') ?: url('/pally/success'),
                        'fail_url' => config('pally.fail_url') ?: url('/pally/fail'),
                    ]
                );

                if ($result && isset($result['link_page_url'])) {
                    return Redirect::to($result['link_page_url']);
                }

                $this->alert('danger', __('Ошибка создания платежа. Попробуйте позже.'));
                return back();
            }

            case 51: {
                // Heleket - Bitcoin
                $price = ($currency == 'USD') ? $price_usd : $price_rub;
                $donate = Donate::create([
                    'payment_system' => 'heleket',
                    'user_id' => auth()->user()->id,
                    'amount' => $price_rub,
                    'bonus_amount' => $price_amount,
                    'item_id' => $shopitem->id,
                    'var_id' => $request->var_id,
                    'steam_id' => $steam_id,
                    'server' => $server,
                    'status' => 0,
                ]);

                $heleketService = new Heleket();

                $result = $heleketService->createPayment(
                    amount: $price,
                    currency: $currency,
                    orderId: (string) $donate->id,
                    options: [
                        'url_return' => config('heleket.return_url') ?: url('/heleket/success'),
                        'url_callback' => config('heleket.callback_url') ?: url('/api/payments/notification/heleket'),
                        'currencies' => [
                            [
                                'currency' => 'BTC',
                                'network' => 'BTC',
                            ],
                        ],
                        'to_currency' => 'BTC',
                    ]
                );

                if ($result && isset($result['url'])) {
                    return Redirect::to($result['url']);
                }

                $this->alert('danger', __('Ошибка создания платежа. Попробуйте позже.'));
                return back();
            }

            case 52: {
                // Heleket - USDT (все сети)
                $price = ($currency == 'USD') ? $price_usd : $price_rub;
                $donate = Donate::create([
                    'payment_system' => 'heleket',
                    'user_id' => auth()->user()->id,
                    'amount' => $price_rub,
                    'bonus_amount' => $price_amount,
                    'item_id' => $shopitem->id,
                    'var_id' => $request->var_id,
                    'steam_id' => $steam_id,
                    'server' => $server,
                    'status' => 0,
                ]);

                $heleketService = new Heleket();

                // Получаем все доступные сети для USDT
                $services = $heleketService->getPaymentServices();

                $usdtNetworks = [];
                $except_currencies = [];
                if ($services) {
                    foreach ($services as $service) {
                        if (isset($service['currency']) && strtoupper($service['currency']) === 'USDT' && isset($service['is_available']) && $service['is_available']) {
                            $usdtNetworks[] = $service['network'];
                        } else {
                            $except_currencies[] = [
                                'currency' => $service['currency'],
                                'network' => $service['network'],
                            ];
                        }
                    }
                }
                $currencies = [];

                foreach ($usdtNetworks as $network) {
                    $currencies[] = [
                        'currency' => 'USDT',
                        'network' => $network,
                    ];
                }

                $result = $heleketService->createPayment(
                    amount: $price,
                    currency: $currency,
                    orderId: (string) $donate->id,
                    options: [
                        'url_return' => config('heleket.return_url') ?: url('/heleket/success'),
                        'url_callback' => config('heleket.callback_url') ?: url('/api/payments/notification/heleket'),
                        'currencies' => $currencies,
                        'except_currencies' => $except_currencies,
                        'to_currency' => 'USDT',
                    ]
                );

                if ($result && isset($result['url'])) {
                    return Redirect::to($result['url']);
                }

                $this->alert('danger', __('Ошибка создания платежа. Попробуйте позже.'));
                return back();
            }
            case 53: {
                $price = ($currency == 'USD') ? $price_usd : $price_rub;
                $donate = Donate::create([
                    'payment_system' => 'heleket',
                    'user_id' => auth()->user()->id,
                    'amount' => $price_rub,
                    'bonus_amount' => $price_amount,
                    'item_id' => $shopitem->id,
                    'var_id' => $request->var_id,
                    'steam_id' => $steam_id,
                    'server' => $server,
                    'status' => 0,
                ]);

                $heleketService = new Heleket();

                $services = $heleketService->getPaymentServices();
                $currencies = [];
                foreach ($services as $service) {
                    if (isset($service['is_available']) && $service['is_available']) {
                        $currencies[] = [
                            'currency' => $service['currency'],
                            'network' => $service['network'],
                        ];
                    }
                }

                $result = $heleketService->createPayment(
                    amount: $price,
                    currency: $currency,
                    orderId: (string) $donate->id,
                    options: [
                        'url_return' => config('heleket.return_url') ?: url('/heleket/success'),
                        'url_callback' => config('heleket.callback_url') ?: url('/api/payments/notification/heleket'),
                        'currencies' => $currencies,

                    ]
                );

                if ($result && isset($result['url'])) {
                    return Redirect::to($result['url']);
                }

                $this->alert('danger', __('Ошибка создания платежа. Попробуйте позже.'));
                return back();
            }

            case 48: {
                // Tebex
                $price_rub = ($currency == 'USD') ? $price_usd * config('options.exchange_rate_usd', 70) : $price_rub;

                $donate = Donate::create([
                    'payment_system' => 'tebex',
                    'user_id' => auth()->user()->id,
                    'amount' => $price_rub,
                    'bonus_amount' => $price_amount,
                    'item_id' => $shopitem->id,
                    'var_id' => $request->var_id,
                    'steam_id' => $steam_id,
                    'server' => $server,
                    'status' => 0,
                ]);

                Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->steam_id . ') ' . 'create tebex payment.');

                $public_token = config('options.tebex_public_token', '');
                $base_url = 'https://headless.tebex.io';
                $return_url = route('account.balance.tebex');
                $complete_url = asset('/tebex/success');
                $cancel_url = asset('/tebex/fail');

                // Создаем корзину
                if (1 === 1) {
                    $basket_response = @file_get_contents('http://195.133.21.219/tebex_create_basket.php?donate_id=' . $donate->id . '&public_token=' . $public_token . '&access_token=IsgHJbH7iViniKL6eyGWnNgGNDw7vcqf');
                    if ($basket_response && $basket_response != '') {
                        $basket_response = json_decode($basket_response, true);
                    }
                } else {
                    $basket_response = $this->createBasket($base_url, $public_token, $donate->id, $complete_url, $cancel_url);
                }
                if (isset($basket_response['data']['ident'])) {
                    $basket_ident = $basket_response['data']['ident'];

                    //Сохраняем ident в донат
                    $donate->payment_id = $basket_ident;
                    $donate->save();

                    // Получаем ссылку на авторизацию
                    $auth_url_response = $this->getBasketAuthUrl($base_url, $public_token, $donate->id, $basket_ident, $return_url);
                    dd($auth_url_response);
                    if (isset($auth_url_response[0]['url'])) {
                        return Redirect::to($auth_url_response[0]['url']);
                    }
                }
            }

            case 22: {
                // Pally.info

                $price_rub = ($currency == 'USD') ? $price_usd * config('options.exchange_rate_usd', 70) : $price_rub;

                $donate = Donate::create([
                    'payment_system' => 'pally',
                    'user_id' => auth()->user()->id,
                    'amount' => $price_rub,
                    'bonus_amount' => $price_amount,
                    'item_id' => $shopitem->id,
                    'var_id' => $request->var_id,
                    'server' => $server,
                    'status' => 0,
                ]);

                $pallyService = new Pally();

                $result = $pallyService->createBill(
                    amount: $price_rub,
                    orderId: (string) $donate->id,
                    description: __('Пополнение баланса'),
                    options: [
                        'currency_in' => 'RUB',
                        'name' => __('Пополнение баланса'),
                        'custom' => 'donate_id:' . $donate->id,
                        'success_url' => config('pally.success_url') ?: url('/pally/success'),
                        'fail_url' => config('pally.fail_url') ?: url('/pally/fail'),
                    ]
                );

                if ($result && isset($result['link_page_url'])) {
                    return Redirect::to($result['link_page_url']);
                }

                $this->alert('danger', __('Ошибка создания платежа. Попробуйте позже.'));
                return back();
            }

            case 23: {
                // Heleket

                $price_usd = ($currency == 'USD') ? $price_usd : $price_rub / config('options.exchange_rate_usd', 70);

                $donate = Donate::create([
                    'payment_system' => 'heleket',
                    'user_id' => auth()->user()->id,
                    'amount' => $price_rub,
                    'bonus_amount' => $price_amount,
                    'item_id' => $shopitem->id,
                    'var_id' => $request->var_id,
                    'server' => $server,
                    'status' => 0,
                ]);

                $heleketService = new Heleket();

                // Определяем сеть и валюту по умолчанию
                $network = 'TRON';
                $currency_heleket = 'USDT';

                $result = $heleketService->createPayment(
                    amount: $price_usd,
                    currency: $currency_heleket,
                    orderId: (string) $donate->id,
                    options: [
                        'url_return' => config('heleket.return_url') ?: url('/heleket/success'),
                        'url_callback' => config('heleket.callback_url') ?: url('/api/payments/notification/heleket'),
                    ]
                );

                if ($result && isset($result['url'])) {
                    return Redirect::to($result['url']);
                }

                $this->alert('danger', __('Ошибка создания платежа. Попробуйте позже.'));
                return back();
            }

            case 50: {
                //Tebex OLD
                /*
                Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->steam_id . ') ' . 'create tebex payment.');

                $public_token = config('options.tebex_public_token', "");

                $base_url = 'https://headless.tebex.io';

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => "{$base_url}/api/accounts/{$public_token}",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_HTTPHEADER => array(
                        "Accept: application/json"
                    ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);

                $answer = json_decode($response, true);

                if (isset($answer['data']['webstore_url'])) {
                    return Redirect::to($answer['data']['webstore_url'] . '/category/Subscriptions');
                }
                */
            }
        }

        $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
        return back();
    }

    private function createBasket($base_url, $public_token, $donate_id, $complete_url, $cancel_url)
    {
        $json = json_encode([
            'complete_url' => $complete_url,
            'cancel_url' => $cancel_url,
            'custom' => [
                'donate_id' => strval($donate_id),
            ],
        ], JSON_UNESCAPED_SLASHES);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => "{$base_url}/api/accounts/{$public_token}/baskets",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "Content-Type: application/json",
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
    }

    private function getBasketAuthUrl($base_url, $public_token, $donate_id, $basket_ident, $return_url)
    {
        $return_url = $return_url . '?donate_id=' . $donate_id;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "{$base_url}/api/accounts/{$public_token}/baskets/{$basket_ident}/auth?returnUrl={$return_url}",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Accept: application/json"
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        return json_decode($response, true);
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
