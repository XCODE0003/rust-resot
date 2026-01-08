<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DonateController;
use App\Http\Controllers\ShopController;
use App\Services\Pally;
use App\Models\Donate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class PallyController extends Controller
{
    /**
     * Обработка вебхука платежа (Payment postback)
     *
     * @param Request $request
     * @param Pally $pallyService
     * @param ShopController $shopController
     * @param DonateController $donateController
     * @return JsonResponse|string
     */
    public function paymentWebhook(
        Request $request,
        Pally $pallyService,
        ShopController $shopController,
        DonateController $donateController
    ) {
        Log::channel('pally')->info('Payment webhook received', $request->all());

        // Проверяем обязательные параметры
        if (!$request->has(['InvId', 'OutSum', 'Status', 'TrsId', 'CurrencyIn', 'SignatureValue'])) {
            Log::channel('pally')->error('Missing required parameters', $request->all());
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        $invId = $request->input('InvId');
        $outSum = $request->input('OutSum');
        $status = $request->input('Status');
        $signature = $request->input('SignatureValue');

        // Проверяем подпись
        if (!$pallyService->verifyPaymentSignature($outSum, $invId, $signature)) {
            Log::channel('pally')->error('Invalid signature', [
                'invId' => $invId,
                'outSum' => $outSum,
                'signature' => $signature,
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Находим заказ по InvId (order_id)
        $donate = Donate::where('id', $invId)->orWhere('payment_id', $invId)->first();

        if (!$donate) {
            Log::channel('pally')->error('Donate not found', ['invId' => $invId]);
            return response()->json(['error' => 'Donate not found'], 404);
        }

        Log::channel('pally')->info('Donate found', ['donate' => $donate->toArray()]);

        // Устанавливаем server_id в сессию
        session()->put('server_id', $donate->server);

        // Обрабатываем платеж в зависимости от статуса
        if ($status === 'SUCCESS' && $donate->status === 0) {
            $donate->status = 1;
            $donate->payment_id = $request->input('TrsId');
            $donate->save();

            Log::channel('pally')->info('Payment successful, processing donate', ['donate_id' => $donate->id]);

            // Отправляем предмет или добавляем баланс
            if ($donate->item_id > 0) {
                $shopController->send_item($donate->item_id, $donate->var_id, $donate->steam_id, $donate->server);
            } else {
                $amount = $donate->amount + $donate->bonus_amount;
                $donateController->addBalance($donate->user_id, $amount);
            }

            return response()->json(['success' => true], 200);
        } elseif ($status === 'FAIL' && $donate->status === 0) {
            Log::channel('pally')->info('Payment failed', ['donate_id' => $donate->id]);
            // Можно обновить статус на "не оплачено" если нужно
            return response()->json(['success' => true, 'status' => 'failed'], 200);
        }

        // Если статус уже обработан или другой статус
        return response()->json(['success' => true, 'status' => 'already_processed'], 200);
    }

    /**
     * Обработка вебхука выплаты (Payout postback)
     *
     * @param Request $request
     * @param Pally $pallyService
     * @return JsonResponse
     */
    public function payoutWebhook(Request $request, Pally $pallyService)
    {
        Log::channel('pally')->info('Payout webhook received', $request->all());

        // Проверяем обязательные параметры
        if (!$request->has(['TrsId', 'Amount', 'Status', 'Currency', 'Commission', 'SignatureValue'])) {
            Log::channel('pally')->error('Missing required parameters', $request->all());
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        $trsId = $request->input('TrsId');
        $amount = $request->input('Amount');
        $signature = $request->input('SignatureValue');

        // Проверяем подпись
        if (!$pallyService->verifyPayoutSignature($amount, $trsId, $signature)) {
            Log::channel('pally')->error('Invalid payout signature', [
                'trsId' => $trsId,
                'amount' => $amount,
                'signature' => $signature,
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Обработка выплаты (логика зависит от вашей бизнес-логики)
        Log::channel('pally')->info('Payout processed', [
            'trsId' => $trsId,
            'status' => $request->input('Status'),
        ]);

        return response()->json(['success' => true], 200);
    }

    /**
     * Обработка вебхука возврата (Refund postback)
     *
     * @param Request $request
     * @param Pally $pallyService
     * @return JsonResponse
     */
    public function refundWebhook(Request $request, Pally $pallyService)
    {
        Log::channel('pally')->info('Refund webhook received', $request->all());

        // Проверяем обязательные параметры
        if (!$request->has(['Id', 'Amount', 'Currency', 'Status', 'InvId', 'BillId', 'PaymentId', 'SignatureValue'])) {
            Log::channel('pally')->error('Missing required parameters', $request->all());
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        $id = $request->input('Id');
        $amount = $request->input('Amount');
        $currency = $request->input('Currency');
        $billId = $request->input('BillId');
        $paymentId = $request->input('PaymentId');
        $signature = $request->input('SignatureValue');

        // Проверяем подпись
        if (!$pallyService->verifyRefundSignature($amount, $currency, $billId, $paymentId, $id, $signature)) {
            Log::channel('pally')->error('Invalid refund signature', [
                'id' => $id,
                'signature' => $signature,
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Обработка возврата (логика зависит от вашей бизнес-логики)
        Log::channel('pally')->info('Refund processed', [
            'id' => $id,
            'status' => $request->input('Status'),
            'invId' => $request->input('InvId'),
        ]);

        return response()->json(['success' => true], 200);
    }

    /**
     * Обработка вебхука чарджбэка (Chargeback postback)
     *
     * @param Request $request
     * @param Pally $pallyService
     * @return JsonResponse
     */
    public function chargebackWebhook(Request $request, Pally $pallyService)
    {
        Log::channel('pally')->info('Chargeback webhook received', $request->all());

        // Проверяем обязательные параметры
        if (!$request->has(['Id', 'Status', 'InvId', 'BillId', 'PaymentId', 'SignatureValue'])) {
            Log::channel('pally')->error('Missing required parameters', $request->all());
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        $id = $request->input('Id');
        $billId = $request->input('BillId');
        $paymentId = $request->input('PaymentId');
        $signature = $request->input('SignatureValue');

        // Проверяем подпись
        if (!$pallyService->verifyChargebackSignature($billId, $paymentId, $id, $signature)) {
            Log::channel('pally')->error('Invalid chargeback signature', [
                'id' => $id,
                'signature' => $signature,
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Обработка чарджбэка (логика зависит от вашей бизнес-логики)
        Log::channel('pally')->info('Chargeback processed', [
            'id' => $id,
            'status' => $request->input('Status'),
            'invId' => $request->input('InvId'),
        ]);

        return response()->json(['success' => true], 200);
    }

    /**
     * Обработка успешного редиректа (Success POST)
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function success(Request $request)
    {
        Log::channel('pally')->info('Success redirect', $request->all());

        $invId = $request->input('InvId');
        $outSum = $request->input('OutSum');

        return view('pally.success', [
            'orderId' => $invId,
            'amount' => $outSum,
        ]);
    }

    /**
     * Обработка неуспешного редиректа (Fail POST)
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function fail(Request $request)
    {
        Log::channel('pally')->info('Fail redirect', $request->all());

        $invId = $request->input('InvId');
        $outSum = $request->input('OutSum');

        return view('pally.fail', [
            'orderId' => $invId,
            'amount' => $outSum,
        ]);
    }
}


