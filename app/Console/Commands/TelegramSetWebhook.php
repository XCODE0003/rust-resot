<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramSetWebhook extends Command
{
    protected $signature = 'telegram:webhook
                            {action=status : Действие: set, remove, status}';

    protected $description = 'Управление Telegram webhook';

    public function handle()
    {
        $token = env('TELEGRAM_BOT_TOKEN');

        if (empty($token) || $token === 'YOUR-BOT-TOKEN') {
            $this->error('TELEGRAM_BOT_TOKEN не настроен в .env');
            return 1;
        }

        $telegram = new Api($token);
        $action = $this->argument('action');

        try {
            switch ($action) {
                case 'set':
                    $this->setWebhook($telegram);
                    break;
                case 'remove':
                    $this->removeWebhook($telegram);
                    break;
                case 'status':
                default:
                    $this->showStatus($telegram);
                    break;
            }
        } catch (TelegramSDKException $e) {
            $this->error('Ошибка Telegram API: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    protected function setWebhook(Api $telegram): void
    {
        $webhookUrl = env('TELEGRAM_WEBHOOK_URL');

        if (empty($webhookUrl)) {
            $this->error('TELEGRAM_WEBHOOK_URL не настроен в .env');
            return;
        }

        $response = $telegram->setWebhook(['url' => $webhookUrl]);

        if ($response) {
            $this->info('✅ Webhook установлен: ' . $webhookUrl);
        } else {
            $this->error('❌ Не удалось установить webhook');
        }
    }

    protected function removeWebhook(Api $telegram): void
    {
        $response = $telegram->removeWebhook();

        if ($response) {
            $this->info('✅ Webhook удалён');
        } else {
            $this->error('❌ Не удалось удалить webhook');
        }
    }

    protected function showStatus(Api $telegram): void
    {
        $info = $telegram->getWebhookInfo();

        $this->info('📡 Статус Webhook:');
        $this->newLine();

        $url = $info->getUrl();
        if ($url) {
            $this->line("   URL: <fg=green>{$url}</>");
        } else {
            $this->line("   URL: <fg=yellow>не установлен</>");
        }

        $this->line("   Pending updates: " . ($info->getPendingUpdateCount() ?? 0));

        $lastError = $info->getLastErrorMessage();
        if ($lastError) {
            $this->line("   <fg=red>Last error: {$lastError}</>");
        }

        $this->newLine();
        $this->line('Команды:');
        $this->line('   php artisan telegram:webhook set    - установить webhook');
        $this->line('   php artisan telegram:webhook remove - удалить webhook');
        $this->line('   php artisan telegram:webhook status - статус (по умолчанию)');
    }
}
