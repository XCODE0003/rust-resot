<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Telegram\Scenes\MainMenuScene;
use App\Telegram\Scenes\PromoCreateScene;
use App\Telegram\Scenes\PromoListScene;
use App\Telegram\Scenes\PromoStatsScene;
use App\Telegram\Services\ConversationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Exceptions\TelegramSDKException;

class TelegramBotController extends Controller
{
    /**
     * @var Api
     */
    protected $telegram;

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
     * TelegramBotController constructor.
     */
    public function __construct()
    {
        $this->telegram = new Api(config('telegram.bot_token'));
    }

    /**
     * Обработка входящих webhook от Telegram
     */
    public function webhook(Request $request)
    {
        try {
            $update = $this->telegram->getWebhookUpdate();

            // Определяем telegram_id и chat_id
            // ВАЖНО: сначала проверяем callback_query, потом message
            $telegramId = null;
            $chatId = null;
            $isBot = false;

            if ($update->getCallbackQuery()) {
                $from = $update->getCallbackQuery()->getFrom();
                $telegramId = $from->getId();
                $chatId = $update->getCallbackQuery()->getMessage()->getChat()->getId();
                $isBot = $from->getIsBot() ?? false;
            } elseif ($update->getMessage()) {
                $from = $update->getMessage()->getFrom();
                $telegramId = $from->getId();
                $chatId = $update->getMessage()->getChat()->getId();
                $isBot = $from->getIsBot() ?? false;
            }

            if (!$telegramId || !$chatId) {
                return response()->json(['status' => 'ok']);
            }

            // Игнорируем сообщения от ботов
            if ($isBot) {
                return response()->json(['status' => 'ok']);
            }

            // Проверка whitelist
            if (!$this->isAllowed($telegramId)) {
                Log::channel('adminlog')->warning("Telegram: Unauthorized access attempt from ID {$telegramId}");

                $this->telegram->sendMessage([
                    'chat_id' => $chatId,
                    'text' => '⛔ Нет доступа2. Ваш Telegram ID не в списке разрешённых.',
                ]);

                return response()->json(['status' => 'unauthorized']);
            }

            // Получаем текст сообщения или callback data
            // ВАЖНО: если есть callback, не обрабатываем message как текст
            $callbackData = $update->getCallbackQuery() ? $update->getCallbackQuery()->getData() : null;
            $text = (!$callbackData && $update->getMessage()) ? $update->getMessage()->getText() : null;

            // Проверяем команду /start - всегда переводит в главное меню
            if ($text && (strtolower($text) === '/start' || strtolower($text) === '/menu')) {
                ConversationService::clearState($telegramId);
                $scene = new MainMenuScene();
                $scene->setContext($this->telegram, $update, $telegramId, $chatId);
                $scene->enter();
                return response()->json(['status' => 'ok']);
            }

            // Получаем текущее состояние пользователя
            $state = ConversationService::getState($telegramId);

            if ($state && isset($state['scene'])) {
                // Пользователь в сцене - передаём управление сцене
                $sceneName = $state['scene'];

                if (isset($this->scenes[$sceneName])) {
                    $sceneClass = $this->scenes[$sceneName];
                    $scene = new $sceneClass();
                    $scene->setContext($this->telegram, $update, $telegramId, $chatId);
                    $scene->handle();
                }
            } else {
                // Пользователь не в сцене - показываем главное меню
                $scene = new MainMenuScene();
                $scene->setContext($this->telegram, $update, $telegramId, $chatId);
                $scene->enter();
            }

            return response()->json(['status' => 'ok']);

        } catch (TelegramSDKException $e) {
            Log::channel('adminlog')->error('Telegram webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            Log::channel('adminlog')->error('Telegram webhook error: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Проверка, находится ли Telegram ID в whitelist
     */
    protected function isAllowed(int $telegramId): bool
    {
        $allowedIds = explode(',', config('telegram.allowed_ids', ''));
        $allowedIds = array_map('trim', $allowedIds);
        $allowedIds = array_filter($allowedIds);

        return in_array((string)$telegramId, $allowedIds);
    }

    /**
     * Установка webhook (вызывается вручную для настройки)
     */
    public function setWebhook(Request $request)
    {
        try {
            $webhookUrl = config('telegram.webhook_url');

            if (empty($webhookUrl)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'TELEGRAM_WEBHOOK_URL not set in .env'
                ], 400);
            }

            $response = $this->telegram->setWebhook([
                'url' => $webhookUrl,
            ]);

            Log::channel('adminlog')->info("Telegram webhook set to: {$webhookUrl}");

            return response()->json([
                'status' => 'ok',
                'message' => 'Webhook set successfully',
                'webhook_url' => $webhookUrl,
                'response' => $response,
            ]);

        } catch (TelegramSDKException $e) {
            Log::channel('adminlog')->error('Failed to set Telegram webhook: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Удаление webhook
     */
    public function removeWebhook()
    {
        try {
            $response = $this->telegram->removeWebhook();

            Log::channel('adminlog')->info("Telegram webhook removed");

            return response()->json([
                'status' => 'ok',
                'message' => 'Webhook removed successfully',
                'response' => $response,
            ]);

        } catch (TelegramSDKException $e) {
            Log::channel('adminlog')->error('Failed to remove Telegram webhook: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Получение информации о webhook
     */
    public function getWebhookInfo()
    {
        try {
            $response = $this->telegram->getWebhookInfo();

            return response()->json([
                'status' => 'ok',
                'webhook_info' => $response,
            ]);

        } catch (TelegramSDKException $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
