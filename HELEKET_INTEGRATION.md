# Интеграция платежной системы Heleket

## Настройка

Добавьте следующие переменные в файл `.env`:

```env
HELEKET_PAYMENT_KEY=uQ4LFWCBE3dT84uQnt7ycL7p9WcSwjkSPQaZbik3ChoWO0egw51f4EAaZQKmefhPP0F1cX8OpRcl2c3HexNedoR7FGEYGA1mTgMPI8lzKl7Ct2I43R6SSC3gVDS3rkGX
HELEKET_PAYOUT_KEY=qseRhcxu6wsxhygfhyidwrrgryrrgefhPP0F1cNedoR7FGEYGA1mTgMPX8OpRcl2c3HexNedoR7FGEYGA1mTgMPI8lzKl7Ct2I43R6S1f4EAaZQKmefhSC3gVDS3rkGX
HELEKET_MERCHANT_UUID=c26b80a8-9549-4a66-bb53-774f12809249
HELEKET_CALLBACK_URL=https://yourdomain.com/api/payments/notification/heleket
HELEKET_RETURN_URL=https://yourdomain.com/heleket/success
```

## Использование сервиса

### Создание платежа

```php
use App\Services\Heleket;

$heleketService = new Heleket();

// Создание платежа
$result = $heleketService->createPayment(
    amount: 16.00,
    currency: 'USD',
    network: 'ETH',
    orderId: 'order-123',
    options: [
        'url_return' => route('heleket.success'),
        'url_callback' => route('heleket.webhook'),
        'is_payment_multiple' => false,
        'lifetime' => '7200',
        'to_currency' => 'ETH',
    ]
);

if ($result && isset($result['url'])) {
    return redirect($result['url']);
}
```

### Пример интеграции в PaymentsMethodTrait

Добавьте новый case в метод `setPayment`:

```php
case 22: {
    // Heleket

    $price_usd = ($currency == 'USD') ? $price_usd : $price_rub / config('options.exchange_rate_usd', 70);

    $donate = Donate::create([
        'payment_system' => 'heleket',
        'user_id'        => auth()->user()->id,
        'amount'         => $price_rub,
        'bonus_amount'   => $price_amount,
        'item_id'        => $shopitem->id,
        'var_id'         => $request->var_id,
        'server'         => $server,
        'status'         => 0,
    ]);

    $heleketService = new Heleket();

    // Определяем сеть и валюту по умолчанию
    $network = 'TRON';
    $currency = 'USDT';

    $result = $heleketService->createPayment(
        amount: $price_usd,
        currency: $currency,
        network: $network,
        orderId: (string)$donate->id,
        options: [
            'url_return' => route('heleket.success'),
            'url_callback' => route('heleket.webhook'),
        ]
    );

    if ($result && isset($result['url'])) {
        return Redirect::to($result['url']);
    }

    $this->alert('danger', __('Ошибка создания платежа. Попробуйте позже.'));
    return back();
}
```

## Роуты

### Вебхуки (API)

- `POST /api/payments/notification/heleket` - вебхук для платежей
- `POST /api/payments/notification/heleket/payout` - вебхук для выплат

### Редиректы (Web)

- `GET /heleket/success` - страница успешной оплаты
- `GET /heleket/fail` - страница неуспешной оплаты

## Методы API

### Получить доступные платежные сервисы

```php
$services = $heleketService->getPaymentServices();
```

### Получить информацию о платеже

```php
// По UUID
$info = $heleketService->getPaymentInfo('uuid-here');

// По order_id
$info = $heleketService->getPaymentInfo(null, 'order-123');
```

### Получить историю платежей

```php
$history = $heleketService->getPaymentHistory(1); // страница 1
```

### Получить баланс

```php
$balance = $heleketService->getBalance();
```

### Повторно отправить уведомление

```php
$result = $heleketService->resendNotification('uuid-here');
// или
$result = $heleketService->resendNotification(null, 'order-123');
```

### Создать статический кошелек

```php
$wallet = $heleketService->createWallet(
    network: 'TRON',
    currency: 'USDT',
    orderId: 'order-123'
);
```

### Создать выплату

```php
$payout = $heleketService->createPayout(
    amount: 15.00,
    currency: 'USDT',
    network: 'TRON',
    address: 'TXguLRFtrAFrEDA17WuPfrxB84jVzJcNNV',
    orderId: 'payout-123'
);
```

### Получить информацию о выплате

```php
$info = $heleketService->getPayoutInfo('uuid-here');
// или
$info = $heleketService->getPayoutInfo(null, 'order-123');
```

## Статусы платежей

- `check` - проверка платежа
- `paid` - оплачен
- `expired` - истек срок
- `cancelled` - отменен

## Логирование

Все операции логируются в канал `heleket`. Логи находятся в `storage/logs/heleket.log`.

## Обработка исключений

Все методы автоматически обрабатывают исключения `RequestBuilderException` и логируют ошибки.

