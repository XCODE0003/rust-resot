<?php

namespace App\Console\Commands;

use App\Telegram\Scenes\MainMenuScene;
use App\Telegram\Scenes\PromoCreateScene;
use App\Telegram\Scenes\PromoListScene;
use App\Telegram\Scenes\PromoStatsScene;
use App\Telegram\Services\ConversationService;
use Illuminate\Console\Command;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramBotPoll extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'telegram:poll {--timeout=30 : Timeout для long polling}';

    /**
     * The console command description.
     */
    protected $description = 'Запустить Telegram бота через long polling';

    /**
     * @var Api
     */
    protected Api $telegram;

    /**
     * Маппинг сцен
     */
    protected array $scenes = [
        'main_menu' => MainMenuScene::class,
        'promo_create' => PromoCreateScene::class,
        'promo_list' => PromoListScene::class,
        'promo_stats' => PromoStatsScene::class,
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $token = env('TELEGRAM_BOT_TOKEN');

        if (empty($token) || $token === 'YOUR-BOT-TOKEN') {
            $this->error('TELEGRAM_BOT_TOKEN не настроен в .env');
            return 1;
        }

        $this->telegram = new Api($token);

        // Удаляем webhook если был установлен
        try {
            $this->telegram->removeWebhook();
            $this->info('Webhook удалён (если был)');
        } catch (TelegramSDKException $e) {
            $this->warn('Не удалось удалить webhook: ' . $e->getMessage());
        }

        $this->info('🤖 Бот запущен в режиме long polling...');
        $this->info('Нажмите Ctrl+C для остановки');
        $this->newLine();

        $offset = 0;
        $timeout = (int)$this->option('timeout');

        while (true) {
            try {
                $updates = $this->telegram->getUpdates([
                    'offset' => $offset,
                    'timeout' => $timeout,
                ]);

                foreach ($updates as $update) {
                    $offset = $update->getUpdateId() + 1;

                    // Отладка - показываем тип апдейта
                    $hasMessage = $update->getMessage() ? 'yes' : 'no';
                    $hasCallback = $update->getCallbackQuery() ? 'yes' : 'no';
                    $this->line("<fg=gray>[DEBUG] Update #{$update->getUpdateId()}: message={$hasMessage}, callback={$hasCallback}</>");

                    $this->processUpdate($update);
                }

            } catch (TelegramSDKException $e) {
                $this->error('Ошибка Telegram API: ' . $e->getMessage());
                sleep(5); // Пауза перед повтором
            } catch (\Exception $e) {
                $this->error('Ошибка: ' . $e->getMessage());
                sleep(5);
            }
        }

        return 0;
    }

    /**
     * Обработка update
     */
    protected function processUpdate($update): void
    {
        // Определяем telegram_id и chat_id
        $telegramId = null;
        $chatId = null;
        $username = null;
        $isBot = false;

        // ВАЖНО: сначала проверяем callback_query, потом message
        if ($update->getCallbackQuery()) {
            $from = $update->getCallbackQuery()->getFrom();
            $telegramId = $from->getId();
            $chatId = $update->getCallbackQuery()->getMessage()->getChat()->getId();
            $username = $from->getUsername() ?? $from->getFirstName();
            $isBot = $from->getIsBot() ?? false;
        } elseif ($update->getMessage()) {
            $from = $update->getMessage()->getFrom();
            $telegramId = $from->getId();
            $chatId = $update->getMessage()->getChat()->getId();
            $username = $from->getUsername() ?? $from->getFirstName();
            $isBot = $from->getIsBot() ?? false;
        }

        if (!$telegramId || !$chatId) {
            return;
        }

        // Игнорируем сообщения от ботов (но не callback от пользователей!)
        if ($isBot) {
            return;
        }

        // Логируем входящее сообщение
        $callback = $update->getCallbackQuery() ? $update->getCallbackQuery()->getData() : null;
        $text = (!$callback && $update->getMessage()) ? $update->getMessage()->getText() : null;

        // Получаем текущее состояние для логирования
        $currentState = ConversationService::getState($telegramId);
        $currentScene = $currentState['scene'] ?? 'none';
        $currentStep = $currentState['step'] ?? 'none';

        $logMessage = "[@{$username}] ";
        if ($callback) {
            $logMessage .= "Callback: {$callback}";
        } elseif ($text) {
            $logMessage .= "Сообщение: {$text}";
        }
        $logMessage .= " [сцена: {$currentScene}/{$currentStep}]";
        $this->line($logMessage);

        // Проверка whitelist
        if (!$this->isAllowed($telegramId)) {
            $this->warn("  ⛔ Нет доступа (ID: {$telegramId})");

            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => '⛔ Нет доступа. Ваш Telegram ID не в списке разрешённых.',
            ]);

            return;
        }

        try {
            // Проверяем команду /start
            if ($text && (strtolower($text) === '/start' || strtolower($text) === '/menu')) {
                ConversationService::clearState($telegramId);
                $scene = new MainMenuScene();
                $scene->setContext($this->telegram, $update, $telegramId, $chatId);
                $scene->enter();
                $this->info("  ✓ Показано главное меню");
                return;
            }

            // Получаем текущее состояние
            $state = ConversationService::getState($telegramId);

            if ($state && isset($state['scene'])) {
                $sceneName = $state['scene'];

                if (isset($this->scenes[$sceneName])) {
                    $sceneClass = $this->scenes[$sceneName];
                    $scene = new $sceneClass();
                    $scene->setContext($this->telegram, $update, $telegramId, $chatId);
                    $scene->handle();
                    $this->info("  ✓ Обработано сценой: {$sceneName}");
                }
            } else {
                $scene = new MainMenuScene();
                $scene->setContext($this->telegram, $update, $telegramId, $chatId);
                $scene->enter();
                $this->info("  ✓ Показано главное меню (новый пользователь)");
            }

        } catch (\Exception $e) {
            $this->error("  ✗ Ошибка: " . $e->getMessage());
        }
    }

    /**
     * Проверка whitelist
     */
    protected function isAllowed(int $telegramId): bool
    {
        $allowedIds = explode(',', env('TELEGRAM_ALLOWED_IDS', ''));
        $allowedIds = array_map('trim', $allowedIds);
        $allowedIds = array_filter($allowedIds);

        return in_array((string)$telegramId, $allowedIds);
    }
}
