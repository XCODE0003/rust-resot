# Исправление Telegram бота (GuzzleHttp Promise unwrap)

## Проблема
При нажатии «Обновить стату промокодов» бот падает с ошибкой:
`Call to undefined function GuzzleHttp\Promise\unwrap()`

## Решение

### Вариант A: Деплой через Git + Composer (рекомендуется)

1. Закоммитьте и запушьте изменения:
   - `composer.json` (с patches и cweagans/composer-patches в require)
   - `composer.lock`
   - `patches/telegram-bot-sdk-guzzle-promises-v2.patch`

2. На production-сервере:
```bash
cd /var/www/u3377206/data/www/rustresort.com
git pull
composer install --no-dev --optimize-autoloader
```

Патч применится автоматически.

### Вариант B: Ручная замена файла (если нет доступа к composer)

Скопируйте исправленный файл на сервер, заменив:
`vendor/irazasyed/telegram-bot-sdk/src/HttpClients/GuzzleHttpClient.php`

Через SCP:
```bash
scp vendor/irazasyed/telegram-bot-sdk/src/HttpClients/GuzzleHttpClient.php \
  user@server:/var/www/u3377206/data/www/rustresort.com/vendor/irazasyed/telegram-bot-sdk/src/HttpClients/
```

Или через FTP/SFTP — загрузите файл в ту же папку.

### Проверка
После деплоя нажмите «Обновить стату промокодов» в боте — ошибка должна исчезнуть.
