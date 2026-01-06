<?php

namespace App\Services;

use App\Models\UserData;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

use App\Http\Controllers\Controller;
use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Api\Agreement;
use PayPal\Api\Details;
use PayPal\Api\Payer;
use PayPal\Api\Plan;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\PayerInfo;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Amount;
use PayPal\Api\Transaction;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;
use URL;


class PayPal
{

    public function __construct()
    {
        /** PayPal api context **/
        $paypal_conf = \Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
                $paypal_conf['client_id'],
                $paypal_conf['secret'])
        );

        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    public function payWithpaypal($amount, $donate)
    {

        //Временная заглушка

        //    Session::put('alert.danger', ["An error occurred, sorry for the inconvenience. Try again."]);
        //    return Redirect::route('donation');

        $amountToBePaid = $amount;
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $item_1 = new Item();
        $item_1->setName('Donate to ' . config('app.name')) /** название элемента **/
        ->setCurrency('BRL')
            ->setQuantity(1)
            ->setPrice($amountToBePaid); /** цена **/

        $item_list = new ItemList();
        $item_list->setItems(array($item_1));

	$details = new Details();
	$details->setShipping(0)
	    ->setTax(0)
	    ->setSubtotal($amountToBePaid);

        $amount = new Amount();
        $amount->setCurrency('BRL')
            ->setTotal($amountToBePaid)
	    ->setDetails($details);

        $redirect_urls = new RedirectUrls();
        /** Укажите обратный URL **/
        $redirect_urls->setReturnUrl(URL::route('paypal.status'))
            ->setCancelUrl(URL::route('paypal.status'));

        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription('Donate to ' . config('app.name'));

        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));

        try {
            $payment->create($this->_api_context);
        } catch (\Exception $ex) {
	    echo $ex->getCode();
            echo $ex->getData(); // Prints the detailed error message 
            die($ex);

            if (\Config::get('app.debug')) {
                Session::put('alert.danger', ["Connection timeout"]);
                return false;
            } else {
                Session::put('alert.danger', ["An error occurred, sorry for the inconvenience. Try again."]);
                return false;
            }

        }

        foreach ($payment->getLinks() as $link) {
            if ($link->getRel() == 'approval_url') {
                $redirect_url = $link->getHref();
                break;
            }
        }

        /** добавляем ID платежа в сессию **/
        Session::put('paypal_payment_id', $payment->getId());

        //Сохраняем payment_id в БД
        $donate->payment_id = $payment->getId();
        $donate->save();

        if (isset($redirect_url)) {

            /** редиректим в paypal **/
            return $redirect_url;
        }

        Session::put('alert.danger', ["An error occurred, sorry for the inconvenience. Try again."]);
        return false;
    }

    public function getPaymentStatus($request)
    {
        /** Получаем ID платежа до очистки сессии **/
        $payment_id = $request->paymentId;

        if (empty($request->PayerID) || empty($request->token)) {
            //Session::put('alert.danger', ["Payment failed. Try again."]);
            return false;
        }

        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId($request->PayerID);

        /** Выполняем платёж **/
        $result = $payment->execute($execution, $this->_api_context);

	Log::channel('paypal')->info("Результат paypal - " . print_r($result, 1));

        if ($result->getState() == 'approved') {
            //Session::put('alert.success', ["Payment was successful"]);
            return $payment_id;
        }

        //Session::put('alert.danger', ["Payment failed. Try again."]);

        return false;
    }

}