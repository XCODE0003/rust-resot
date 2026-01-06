# Интеграция платежной системы Pally.info

## Настройка

Добавьте следующие переменные в файл `.env`:

```env
PALLY_SHOP_ID=EMvYBxlm1X
PALLY_TOKEN=25361|GMrIOBaRnATZ8r3iyQtwFDjVzFscWaRtD6MTq18w
PALLY_API_URL=https://pal24.pro/api/v1/
PALLY_RESULT_URL=https://yourdomain.com/api/payments/notification/pally
PALLY_SUCCESS_URL=https://yourdomain.com/pally/success
PALLY_FAIL_URL=https://yourdomain.com/pally/fail
```

## Использование сервиса

### Создание счета на оплату

```php
use App\Services\Pally;

$pallyService = new Pally();

// Создание простого счета
$result = $pallyService->createBill(
    amount: 1000.00,
    orderId: 'order-123',
    description: 'Пополнение баланса',
    options: [
        'currency_in' => 'RUB',
        'name' => 'Пополнение баланса',
        'custom' => 'user_id:123',
    ]
);

if ($result && isset($result['link_page_url'])) {
    return redirect($result['link_page_url']);
}
```

### Пример интеграции в PaymentsMethodTrait

Добавьте новый case в метод `setPayment`:

```php
case 21: {
    // Pally.info

    $price_rub = ($currency == 'USD') ? $price_usd * config('options.exchange_rate_usd', 70) : $price_rub;

    $donate = Donate::create([
        'payment_system' => 'pally',
        'user_id'        => auth()->user()->id,
        'amount'         => $price_rub,
        'bonus_amount'   => $price_amount,
        'item_id'        => $shopitem->id,
        'var_id'         => $request->var_id,
        'server'         => $server,
        'status'         => 0,
    ]);

    $pallyService = new Pally();

    $result = $pallyService->createBill(
        amount: $price_rub,
        orderId: (string)$donate->id,
        description: 'Пополнение баланса',
        options: [
            'currency_in' => 'RUB',
            'name' => 'Пополнение баланса',
            'custom' => 'donate_id:' . $donate->id,
            'success_url' => config('pally.success_url'),
            'fail_url' => config('pally.fail_url'),
        ]
    );

    if ($result && isset($result['link_page_url'])) {
        return Redirect::to($result['link_page_url']);
    }

    $this->alert('danger', __('Ошибка создания платежа. Попробуйте позже.'));
    return back();
}
```

## Роуты

### Вебхуки (API)

- `POST /api/payments/notification/pally` - вебхук для платежей
- `POST /api/payments/notification/pally/payout` - вебхук для выплат
- `POST /api/payments/notification/pally/refund` - вебхук для возвратов
- `POST /api/payments/notification/pally/chargeback` - вебхук для чарджбэков

### Редиректы (Web)

- `POST /pally/success` - страница успешной оплаты
- `POST /pally/fail` - страница неуспешной оплаты

## Проверка подписи

Все вебхуки автоматически проверяют подпись запроса. Подпись формируется следующим образом:

- **Платеж**: `md5($OutSum . ":" . $InvId . ":" . $apiToken)`
- **Выплата**: `md5($Amount . ":" . $TrsId . ":" . $apiToken)`
- **Возврат**: `md5($Amount . ":" . $Currency . ":" . $BillId . ":" . $PaymentId . ":" . $Id . ":" . $apiToken)`
- **Чарджбэк**: `md5($BillId . ":" . $PaymentId . ":" . $Id . ":" . $apiToken)`

## Логирование

Все операции логируются в канал `pally`. Логи находятся в `storage/logs/laravel.log`.

## Дополнительные методы API

### Получить статус счета

```php
$status = $pallyService->getBillStatus('bill_id_here');
```

### Получить баланс

```php
$balance = $pallyService->getBalance();
```

