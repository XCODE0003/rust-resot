<?php

namespace App\Services;

use Heleket\Api\Client;
use Heleket\Api\RequestBuilderException;
use Illuminate\Support\Facades\Log;

class Heleket
{
    protected $paymentClient;
    protected $payoutClient;
    protected string $merchantUuid;

    public function __construct()
    {
        $this->merchantUuid = config('heleket.merchant_uuid');

        $paymentKey = config('heleket.payment_key');
        $payoutKey = config('heleket.payout_key');

        if ($paymentKey) {
            $this->paymentClient = Client::payment($paymentKey, $this->merchantUuid);
        }

        if ($payoutKey) {
            $this->payoutClient = Client::payout($payoutKey, $this->merchantUuid);
        }
    }

    /**
     * Получить доступные платежные сервисы
     *
     * @return array|null
     */
    public function getPaymentServices(): ?array
    {
        if (!$this->paymentClient) {
            return null;
        }

        try {
            return $this->paymentClient->services();
        } catch (RequestBuilderException $e) {
            Log::channel('heleket')->error('Ошибка получения платежных сервисов', [
                'message' => $e->getMessage(),
                'method' => $e->getMethod(),
            ]);
            return null;
        }
    }

    /**
     * Создать платеж
     *
     * @param float $amount Сумма платежа
     * @param string $currency Валюта (USD, TRX, ETH, BTC, USDT и т.д.)
     * @param string $network Сеть (TRON, ETH, BTC и т.д.)
     * @param string|null $orderId ID заказа
     * @param array $options Дополнительные параметры
     * @return array|null
     */
    public function createPayment(float $amount, string $currency, string $network, ?string $orderId = null, array $options = []): ?array
    {
        if (!$this->paymentClient) {
            Log::channel('heleket')->error('Payment client не инициализирован');
            return null;
        }

        $data = array_merge([
            'amount' => (string)$amount,
            'currency' => $currency,
            'network' => $network,
            'order_id' => $orderId,
            'url_return' => $options['url_return'] ?? config('heleket.return_url'),
            'url_callback' => $options['url_callback'] ?? config('heleket.callback_url'),
            'is_payment_multiple' => $options['is_payment_multiple'] ?? false,
            'lifetime' => $options['lifetime'] ?? '7200',
        ], $options);

        // Удаляем пустые значения
        $data = array_filter($data, function($value) {
            return $value !== null && $value !== '';
        });

        try {
            return $this->paymentClient->create($data);
        } catch (RequestBuilderException $e) {
            Log::channel('heleket')->error('Ошибка создания платежа', [
                'message' => $e->getMessage(),
                'method' => $e->getMethod(),
                'data' => $data,
            ]);
            return null;
        }
    }

    /**
     * Получить информацию о платеже
     *
     * @param string|null $uuid UUID платежа
     * @param string|null $orderId ID заказа
     * @return array|null
     */
    public function getPaymentInfo(?string $uuid = null, ?string $orderId = null): ?array
    {
        if (!$this->paymentClient) {
            return null;
        }

        $data = [];
        if ($uuid) {
            $data['uuid'] = $uuid;
        } elseif ($orderId) {
            $data['order_id'] = $orderId;
        } else {
            return null;
        }

        try {
            return $this->paymentClient->info($data);
        } catch (RequestBuilderException $e) {
            Log::channel('heleket')->error('Ошибка получения информации о платеже', [
                'message' => $e->getMessage(),
                'method' => $e->getMethod(),
            ]);
            return null;
        }
    }

    /**
     * Получить историю платежей
     *
     * @param int $page Номер страницы
     * @return array|null
     */
    public function getPaymentHistory(int $page = 1): ?array
    {
        if (!$this->paymentClient) {
            return null;
        }

        try {
            return $this->paymentClient->history($page);
        } catch (RequestBuilderException $e) {
            Log::channel('heleket')->error('Ошибка получения истории платежей', [
                'message' => $e->getMessage(),
                'method' => $e->getMethod(),
            ]);
            return null;
        }
    }

    /**
     * Получить баланс
     *
     * @return array|null
     */
    public function getBalance(): ?array
    {
        if (!$this->paymentClient) {
            return null;
        }

        try {
            return $this->paymentClient->balance();
        } catch (RequestBuilderException $e) {
            Log::channel('heleket')->error('Ошибка получения баланса', [
                'message' => $e->getMessage(),
                'method' => $e->getMethod(),
            ]);
            return null;
        }
    }

    /**
     * Повторно отправить уведомление
     *
     * @param string|null $uuid UUID платежа
     * @param string|null $orderId ID заказа
     * @return bool
     */
    public function resendNotification(?string $uuid = null, ?string $orderId = null): bool
    {
        if (!$this->paymentClient) {
            return false;
        }

        $data = [];
        if ($uuid) {
            $data['uuid'] = $uuid;
        } elseif ($orderId) {
            $data['order_id'] = $orderId;
        } else {
            return false;
        }

        try {
            return $this->paymentClient->reSendNotifications($data);
        } catch (RequestBuilderException $e) {
            Log::channel('heleket')->error('Ошибка повторной отправки уведомления', [
                'message' => $e->getMessage(),
                'method' => $e->getMethod(),
            ]);
            return false;
        }
    }

    /**
     * Создать статический кошелек
     *
     * @param string $network Сеть
     * @param string $currency Валюта
     * @param string|null $orderId ID заказа
     * @param array $options Дополнительные параметры
     * @return array|null
     */
    public function createWallet(string $network, string $currency, ?string $orderId = null, array $options = []): ?array
    {
        if (!$this->paymentClient) {
            return null;
        }

        $data = array_merge([
            'network' => $network,
            'currency' => $currency,
            'order_id' => $orderId,
            'url_callback' => $options['url_callback'] ?? config('heleket.callback_url'),
        ], $options);

        try {
            return $this->paymentClient->createWallet($data);
        } catch (RequestBuilderException $e) {
            Log::channel('heleket')->error('Ошибка создания кошелька', [
                'message' => $e->getMessage(),
                'method' => $e->getMethod(),
            ]);
            return null;
        }
    }

    /**
     * Создать выплату
     *
     * @param float $amount Сумма выплаты
     * @param string $currency Валюта
     * @param string $network Сеть
     * @param string $address Адрес для выплаты
     * @param string|null $orderId ID заказа
     * @param array $options Дополнительные параметры
     * @return array|null
     */
    public function createPayout(float $amount, string $currency, string $network, string $address, ?string $orderId = null, array $options = []): ?array
    {
        if (!$this->payoutClient) {
            Log::channel('heleket')->error('Payout client не инициализирован');
            return null;
        }

        $data = array_merge([
            'amount' => (string)$amount,
            'currency' => $currency,
            'network' => $network,
            'address' => $address,
            'order_id' => $orderId,
            'is_subtract' => $options['is_subtract'] ?? '1',
            'url_callback' => $options['url_callback'] ?? config('heleket.callback_url'),
        ], $options);

        try {
            return $this->payoutClient->create($data);
        } catch (RequestBuilderException $e) {
            Log::channel('heleket')->error('Ошибка создания выплаты', [
                'message' => $e->getMessage(),
                'method' => $e->getMethod(),
                'data' => $data,
            ]);
            return null;
        }
    }

    /**
     * Получить информацию о выплате
     *
     * @param string|null $uuid UUID выплаты
     * @param string|null $orderId ID заказа
     * @return array|null
     */
    public function getPayoutInfo(?string $uuid = null, ?string $orderId = null): ?array
    {
        if (!$this->payoutClient) {
            return null;
        }

        $data = [];
        if ($uuid) {
            $data['uuid'] = $uuid;
        } elseif ($orderId) {
            $data['order_id'] = $orderId;
        } else {
            return null;
        }

        try {
            return $this->payoutClient->info($data);
        } catch (RequestBuilderException $e) {
            Log::channel('heleket')->error('Ошибка получения информации о выплате', [
                'message' => $e->getMessage(),
                'method' => $e->getMethod(),
            ]);
            return null;
        }
    }
}

