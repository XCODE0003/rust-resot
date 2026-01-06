<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Pally
{
    protected string $apiUrl;
    protected string $token;
    protected string $shopId;

    public function __construct()
    {
        $this->apiUrl = config('pally.api_url');
        $this->token = config('pally.token');
        $this->shopId = config('pally.shop_id');
    }

    /**
     * Создать счет на оплату
     *
     * @param float $amount Сумма платежа
     * @param string|null $orderId ID заказа
     * @param string|null $description Описание платежа
     * @param array $options Дополнительные параметры
     * @return array|null
     */
    public function createBill(float $amount, ?string $orderId = null, ?string $description = null, array $options = []): ?array
    {
        $data = array_merge([
            'amount' => $amount,
            'shop_id' => $this->shopId,
            'order_id' => $orderId,
            'description' => $description,
            'type' => $options['type'] ?? 'normal',
            'currency_in' => $options['currency_in'] ?? 'RUB',
            'custom' => $options['custom'] ?? null,
            'payer_pays_commission' => $options['payer_pays_commission'] ?? 1,
            'name' => $options['name'] ?? 'Платёж',
        ], $options);

        $data = array_filter($data, function($value) {
            return $value !== null && $value !== '';
        });

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->asForm()->post($this->apiUrl . 'bill/create', $data);
            if ($response->successful()) {
                return $response->json();
            }

            Log::channel('pally')->error('Ошибка создания счета', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::channel('pally')->error('Исключение при создании счета', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Получить статус счета
     *
     * @param string $billId ID счета
     * @return array|null
     */
    public function getBillStatus(string $billId): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->get($this->apiUrl . 'bill/status', [
                'id' => $billId,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::channel('pally')->error('Исключение при получении статуса счета', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Получить баланс мерчанта
     *
     * @return array|null
     */
    public function getBalance(): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
            ])->get($this->apiUrl . 'merchant/balance');

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::channel('pally')->error('Исключение при получении баланса', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Проверить подпись вебхука для платежа
     *
     * @param string $outSum Сумма платежа
     * @param string $invId ID заказа
     * @param string $signature Подпись из запроса
     * @return bool
     */
    public function verifyPaymentSignature(string $outSum, string $invId, string $signature): bool
    {
        $expectedSignature = strtoupper(md5($outSum . ':' . $invId . ':' . $this->token));
        return strtoupper($signature) === $expectedSignature;
    }

    /**
     * Проверить подпись вебхука для выплаты
     *
     * @param string $amount Сумма выплаты
     * @param string $trsId ID транзакции
     * @param string $signature Подпись из запроса
     * @return bool
     */
    public function verifyPayoutSignature(string $amount, string $trsId, string $signature): bool
    {
        $expectedSignature = strtoupper(md5($amount . ':' . $trsId . ':' . $this->token));
        return strtoupper($signature) === $expectedSignature;
    }

    /**
     * Проверить подпись вебхука для возврата
     *
     * @param string $amount Сумма возврата
     * @param string $currency Валюта
     * @param string $billId ID счета
     * @param string $paymentId ID платежа
     * @param string $id ID возврата
     * @param string $signature Подпись из запроса
     * @return bool
     */
    public function verifyRefundSignature(string $amount, string $currency, string $billId, string $paymentId, string $id, string $signature): bool
    {
        $expectedSignature = strtoupper(md5($amount . ':' . $currency . ':' . $billId . ':' . $paymentId . ':' . $id . ':' . $this->token));
        return strtoupper($signature) === $expectedSignature;
    }

    /**
     * Проверить подпись вебхука для чарджбэка
     *
     * @param string $billId ID счета
     * @param string $paymentId ID платежа
     * @param string $id ID чарджбэка
     * @param string $signature Подпись из запроса
     * @return bool
     */
    public function verifyChargebackSignature(string $billId, string $paymentId, string $id, string $signature): bool
    {
        $expectedSignature = strtoupper(md5($billId . ':' . $paymentId . ':' . $id . ':' . $this->token));
        return strtoupper($signature) === $expectedSignature;
    }
}

