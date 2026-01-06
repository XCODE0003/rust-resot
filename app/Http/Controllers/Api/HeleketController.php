<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\DonateController;
use App\Http\Controllers\ShopController;
use App\Services\Heleket;
use App\Models\Donate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class HeleketController extends Controller
{
    /**
     * Обработка вебхука платежа
     *
     * @param Request $request
     * @param Heleket $heleketService
     * @param ShopController $shopController
     * @param DonateController $donateController
     * @return JsonResponse|string
     */
    public function paymentWebhook(
        Request $request,
        Heleket $heleketService,
        ShopController $shopController,
        DonateController $donateController
    ) {
        Log::channel('heleket')->info('Payment webhook received', $request->all());

        // Проверяем обязательные параметры
        if (!$request->has(['uuid', 'order_id', 'status', 'payment_status'])) {
            Log::channel('heleket')->error('Missing required parameters', $request->all());
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        $uuid = $request->input('uuid');
        $orderId = $request->input('order_id');
        $status = $request->input('status');
        $paymentStatus = $request->input('payment_status');

        // Находим заказ по order_id
        $donate = Donate::where('id', $orderId)->orWhere('payment_id', $orderId)->orWhere('payment_id', $uuid)->first();

        if (!$donate) {
            Log::channel('heleket')->error('Donate not found', [
                'order_id' => $orderId,
                'uuid' => $uuid,
            ]);
            return response()->json(['error' => 'Donate not found'], 404);
        }

        Log::channel('heleket')->info('Donate found', ['donate' => $donate->toArray()]);

        // Устанавливаем server_id в сессию
        session()->put('server_id', $donate->server);

        // Обрабатываем платеж в зависимости от статуса
        // Статусы: check, paid, expired, cancelled
        if (($status === 'paid' || $paymentStatus === 'paid') && $donate->status === 0) {
            $donate->status = 1;
            $donate->payment_id = $uuid;
            $donate->save();

            Log::channel('heleket')->info('Payment successful, processing donate', [
                'donate_id' => $donate->id,
                'uuid' => $uuid,
            ]);

            // Отправляем предмет или добавляем баланс
            if ($donate->item_id > 0) {
                $shopController->send_item($donate->item_id, $donate->var_id, $donate->steam_id, $donate->server);
            } else {
                $amount = $donate->amount + $donate->bonus_amount;
                $donateController->addBalance($donate->user_id, $amount);
            }

            return response()->json(['success' => true, 'status' => 'processed'], 200);
        } elseif (($status === 'expired' || $status === 'cancelled' || $paymentStatus === 'expired' || $paymentStatus === 'cancelled') && $donate->status === 0) {
            Log::channel('heleket')->info('Payment expired or cancelled', [
                'donate_id' => $donate->id,
                'status' => $status,
                'payment_status' => $paymentStatus,
            ]);
            // Можно обновить статус на "не оплачено" если нужно
            return response()->json(['success' => true, 'status' => 'expired_or_cancelled'], 200);
        }

        // Если статус уже обработан или другой статус
        return response()->json(['success' => true, 'status' => 'already_processed'], 200);
    }

    /**
     * Обработка вебхука выплаты
     *
     * @param Request $request
     * @param Heleket $heleketService
     * @return JsonResponse
     */
    public function payoutWebhook(Request $request, Heleket $heleketService)
    {
        Log::channel('heleket')->info('Payout webhook received', $request->all());

        // Проверяем обязательные параметры
        if (!$request->has(['uuid', 'order_id', 'status'])) {
            Log::channel('heleket')->error('Missing required parameters', $request->all());
            return response()->json(['error' => 'Missing required parameters'], 400);
        }

        $uuid = $request->input('uuid');
        $orderId = $request->input('order_id');
        $status = $request->input('status');

        // Обработка выплаты (логика зависит от вашей бизнес-логики)
        Log::channel('heleket')->info('Payout processed', [
            'uuid' => $uuid,
            'order_id' => $orderId,
            'status' => $status,
        ]);

        return response()->json(['success' => true], 200);
    }

    /**
     * Обработка успешного редиректа
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function success(Request $request)
    {
        Log::channel('heleket')->info('Success redirect', $request->all());

        $orderId = $request->input('order_id');
        $uuid = $request->input('uuid');
        $amount = $request->input('amount');

        // Получаем информацию о платеже для проверки статуса
        $heleketService = new Heleket();
        $paymentInfo = null;

        if ($uuid) {
            $paymentInfo = $heleketService->getPaymentInfo($uuid);
        } elseif ($orderId) {
            $paymentInfo = $heleketService->getPaymentInfo(null, $orderId);
        }

        $isPaid = false;
        if ($paymentInfo && isset($paymentInfo['status']) && $paymentInfo['status'] === 'paid') {
            $isPaid = true;
        }

        return view('heleket.success', [
            'orderId' => $orderId,
            'uuid' => $uuid,
            'amount' => $amount,
            'isPaid' => $isPaid,
            'paymentInfo' => $paymentInfo,
        ]);
    }

    /**
     * Обработка неуспешного редиректа
     *
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function fail(Request $request)
    {
        Log::channel('heleket')->info('Fail redirect', $request->all());

        $orderId = $request->input('order_id');
        $uuid = $request->input('uuid');
        $amount = $request->input('amount');
        $status = $request->input('status');

        return view('heleket.fail', [
            'orderId' => $orderId,
            'uuid' => $uuid,
            'amount' => $amount,
            'status' => $status,
        ]);
    }
}

