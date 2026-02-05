<?php

namespace App\Telegram\Scenes;

use App\Models\Donate;
use App\Models\PromoCode;
use App\Telegram\Services\ConversationService;

class PromoStatsScene extends BaseScene
{
    public static function getName(): string
    {
        return 'promo_stats';
    }

    public function enter(): void
    {
        ConversationService::setState($this->telegramId, self::getName(), 'enter', []);
        $this->askPromoCode();
    }

    public function handle(): void
    {
        $step = $this->getCurrentStep();
        $callbackData = $this->getCallbackData();

        if ($callbackData) {
            $this->answerCallback();

            if ($callbackData === 'back_to_menu') {
                $this->leave();
                $this->goToMainMenu();
                return;
            }

            if ($callbackData === 'back_to_list') {
                ConversationService::setState($this->telegramId, PromoListScene::getName(), 'list', ['page' => 0]);
                $scene = new PromoListScene();
                $scene->setContext($this->telegram, $this->update, $this->telegramId, $this->chatId);
                $scene->showList(0, true, $this->getCallbackMessageId());
                return;
            }

            if (strpos($callbackData, 'refresh_stats_') === 0) {
                $promoId = (int)str_replace('refresh_stats_', '', $callbackData);
                $this->showPromoStats($promoId, true, $this->getCallbackMessageId());
                return;
            }

            return;
        }

        // Обработка текстового ввода кода
        if ($step === 'enter') {
            $text = $this->getMessageText();
            if ($text) {
                $this->handlePromoCodeInput($text);
            }
        }
    }

    protected function askPromoCode(): void
    {
        $text = "📊 <b>Статистика промокода</b>\n\n";
        $text .= "Введите код промокода для просмотра статистики:";

        $keyboard = [
            'inline_keyboard' => [
                [['text' => '🏠 Главное меню', 'callback_data' => 'back_to_menu']],
            ],
        ];

        $this->reply($text, ['reply_markup' => json_encode($keyboard)]);
    }

    protected function handlePromoCodeInput(string $text): void
    {
        $code = strtolower(trim($text));

        // Только промокоды созданные ботом
        $promo = PromoCode::whereRaw('LOWER(code) = ?', [$code])
            ->where('is_created_bot', true)
            ->first();

        if (!$promo) {
            $text = "❌ Промокод <code>{$code}</code> не найден.\n\n<i>Показываются только промокоды созданные ботом.</i>";

            $keyboard = [
                'inline_keyboard' => [
                    [['text' => '🏠 Главное меню', 'callback_data' => 'back_to_menu']],
                ],
            ];

            $this->reply($text, ['reply_markup' => json_encode($keyboard)]);
            return;
        }

        $this->setData(['promo_id' => $promo->id]);
        $this->goToStep('view');
        $this->showPromoStats($promo->id);
    }

    public function showPromoStats(int $promoId, bool $edit = false, ?int $messageId = null): void
    {
        $promo = PromoCode::where('id', $promoId)->where('is_created_bot', true)->first();

        if (!$promo) {
            $this->reply("❌ Промокод не найден");
            $this->goToMainMenu();
            return;
        }

        $users = json_decode($promo->users, true) ?? [];
        $totalActivations = count($users);

        // Определяем статус
        $status = '🟢 Активен';
        if ($promo->max_activations && $totalActivations >= $promo->max_activations) {
            $status = '🔴 Лимит достигнут';
        }

        // Считаем статистику по типам
        $botActivations = 0;
        $siteActivations = 0;

        foreach ($users as $user) {
            if (isset($user['is_bot_promo']) && $user['is_bot_promo']) {
                $botActivations++;
            } else {
                $siteActivations++;
            }
        }

        // Собираем steam_id для подсчёта донатов
        $steamIds = [];
        foreach ($users as $user) {
            if (isset($user['steam_id']) && !empty($user['steam_id'])) {
                $steamIds[] = $user['steam_id'];
            }
        }

        // Считаем донаты по steam_id
        $donateStats = ['count' => 0, 'total' => 0];
        if (!empty($steamIds)) {
            $donateStats = Donate::whereIn('steam_id', $steamIds)
                ->where('status', 1) // Только успешные
                ->selectRaw('COUNT(*) as count, COALESCE(SUM(amount), 0) as total')
                ->first();
        }

        $text = "📊 <b>Статистика промокода</b>\n\n";
        $text .= "📝 <b>Код:</b> <code>{$promo->code}</code>\n";
        $text .= "📌 <b>Название:</b> {$promo->title}\n";
        $text .= "📍 <b>Статус:</b> {$status}\n\n";

        $text .= "📈 <b>Активации:</b> <b>{$totalActivations}</b>\n";
        $text .= "   🎮 С сервера: {$botActivations}\n";
        $text .= "   🌐 С сайта: {$siteActivations}\n\n";

        // Статистика донатов
        if ($donateStats->count > 0) {
            $text .= "💰 <b>Донаты:</b>\n";
            $text .= "   Количество: {$donateStats->count}\n";
            $text .= "   Сумма: <b>" . number_format($donateStats->total, 2, '.', ' ') . " ₽</b>\n\n";
        } else {
            $text .= "💰 <b>Донаты:</b> нет\n\n";
        }

        // Последние 10 активаций
        if ($totalActivations > 0) {
            $text .= "🕐 <b>Последние активации:</b>\n";

            usort($users, function ($a, $b) {
                return strtotime($b['date'] ?? '1970-01-01') - strtotime($a['date'] ?? '1970-01-01');
            });

            $lastUsers = array_slice($users, 0, 10);

            foreach ($lastUsers as $user) {
                $date = isset($user['date']) ? date('d.m H:i', strtotime($user['date'])) : 'N/A';

                if (isset($user['steam_id'])) {
                    $identifier = "🎮 <code>{$user['steam_id']}</code>";
                } elseif (isset($user['user_id'])) {
                    $identifier = "🌐 ID: {$user['user_id']}";
                } else {
                    $identifier = "❓ Unknown";
                }

                $text .= "• {$identifier} — {$date}\n";
            }

            if ($totalActivations > 10) {
                $text .= "<i>... и ещё " . ($totalActivations - 10) . "</i>\n";
            }
        }

        $keyboard = [
            'inline_keyboard' => [
                [['text' => '🔄 Обновить', 'callback_data' => 'refresh_stats_' . $promoId]],
                [['text' => '📋 К списку', 'callback_data' => 'back_to_list']],
                [['text' => '🏠 Главное меню', 'callback_data' => 'back_to_menu']],
            ],
        ];

        if ($edit && $messageId) {
            $this->editMessage($messageId, $text, ['reply_markup' => json_encode($keyboard)]);
        } else {
            $this->reply($text, ['reply_markup' => json_encode($keyboard)]);
        }
    }

    protected function goToMainMenu(): void
    {
        $scene = new MainMenuScene();
        $scene->setContext($this->telegram, $this->update, $this->telegramId, $this->chatId);
        $scene->enter();
    }
}
