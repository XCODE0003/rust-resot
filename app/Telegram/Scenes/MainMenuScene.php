<?php

namespace App\Telegram\Scenes;

use App\Telegram\Services\ConversationService;

class MainMenuScene extends BaseScene
{
    public static function getName(): string
    {
        return 'main_menu';
    }

    public function enter(): void
    {
        ConversationService::setState($this->telegramId, self::getName(), 'menu');
        $this->showMainMenu();
    }

    public function handle(): void
    {
        $callbackData = $this->getCallbackData();

        if ($callbackData) {
            $this->answerCallback();

            switch ($callbackData) {
                case 'promo_create':
                    ConversationService::setState($this->telegramId, PromoCreateScene::getName(), 'enter');
                    $scene = new PromoCreateScene();
                    $scene->setContext($this->telegram, $this->update, $this->telegramId, $this->chatId);
                    $scene->enter();
                    break;

                case 'promo_list':
                    ConversationService::setState($this->telegramId, PromoListScene::getName(), 'list');
                    $scene = new PromoListScene();
                    $scene->setContext($this->telegram, $this->update, $this->telegramId, $this->chatId);
                    $scene->enter();
                    break;

                case 'promo_stats':
                    ConversationService::setState($this->telegramId, PromoStatsScene::getName(), 'enter');
                    $scene = new PromoStatsScene();
                    $scene->setContext($this->telegram, $this->update, $this->telegramId, $this->chatId);
                    $scene->enter();
                    break;

                case 'refresh_menu':
                    $this->showMainMenu(true, $this->getCallbackMessageId());
                    break;
            }
        }
    }

    protected function showMainMenu(bool $edit = false, ?int $messageId = null): void
    {
        $text = "🏠 <b>Главное меню</b>\n\n";
        $text .= "Выберите действие:";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '➕ Создать промокод', 'callback_data' => 'promo_create'],
                ],
                [
                    ['text' => '📋 Список промокодов', 'callback_data' => 'promo_list'],
                ],
                [
                    ['text' => '📊 Статистика промокода', 'callback_data' => 'promo_stats'],
                ],
            ],
        ];

        if ($edit && $messageId) {
            $this->editMessage($messageId, $text, ['reply_markup' => json_encode($keyboard)]);
        } else {
            $this->reply($text, ['reply_markup' => json_encode($keyboard)]);
        }
    }
}
