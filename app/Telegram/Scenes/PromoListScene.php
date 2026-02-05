<?php

namespace App\Telegram\Scenes;

use App\Models\PromoCode;
use App\Telegram\Services\ConversationService;

class PromoListScene extends BaseScene
{
    protected const PER_PAGE = 10;

    public static function getName(): string
    {
        return 'promo_list';
    }

    public function enter(): void
    {
        ConversationService::setState($this->telegramId, self::getName(), 'list', ['page' => 0]);
        $this->showList(0);
    }

    public function handle(): void
    {
        $callbackData = $this->getCallbackData();

        if (!$callbackData) {
            return;
        }

        $this->answerCallback();

        if ($callbackData === 'back_to_menu') {
            $this->leave();
            $this->goToMainMenu();
            return;
        }

        if ($callbackData === 'refresh_list') {
            $data = $this->getData();
            $this->showList($data['page'] ?? 0, true, $this->getCallbackMessageId());
            return;
        }

        if (strpos($callbackData, 'page_') === 0) {
            $page = (int)str_replace('page_', '', $callbackData);
            $this->setData(['page' => $page]);
            $this->showList($page, true, $this->getCallbackMessageId());
            return;
        }

        if (strpos($callbackData, 'stats_') === 0) {
            $promoId = (int)str_replace('stats_', '', $callbackData);
            ConversationService::setState($this->telegramId, PromoStatsScene::getName(), 'view', ['promo_id' => $promoId]);
            $scene = new PromoStatsScene();
            $scene->setContext($this->telegram, $this->update, $this->telegramId, $this->chatId);
            $scene->showPromoStats($promoId, true, $this->getCallbackMessageId());
            return;
        }
    }

    public function showList(int $page = 0, bool $edit = false, ?int $messageId = null): void
    {
        // Только промокоды созданные ботом
        $total = PromoCode::where('is_created_bot', true)->count();
        $totalPages = max(1, ceil($total / self::PER_PAGE));
        $page = max(0, min($page, $totalPages - 1));

        $promoCodes = PromoCode::where('is_created_bot', true)
            ->orderBy('created_at', 'desc')
            ->skip($page * self::PER_PAGE)
            ->take(self::PER_PAGE)
            ->get();

        $text = "📋 <b>Список промокодов</b> <i>(созданные ботом)</i>\n";
        $text .= "Страница " . ($page + 1) . " из {$totalPages} (всего: {$total})\n\n";

        if ($promoCodes->isEmpty()) {
            $text .= "<i>Промокодов не найдено</i>";
        } else {
            foreach ($promoCodes as $promo) {
                $users = json_decode($promo->users, true) ?? [];
                $activations = count($users);
                $maxActivations = $promo->max_activations ?? '∞';

                // Определяем статус
                $statusEmoji = '🟢';
                if ($promo->max_activations && $activations >= $promo->max_activations) {
                    $statusEmoji = '🔴';
                }

                $text .= "{$statusEmoji} <code>{$promo->code}</code> — {$promo->title}\n";
                $text .= "   📊 Активаций: {$activations}" . ($promo->max_activations ? "/{$maxActivations}" : "") . "\n";
            }
        }

        $text .= "\n<i>🟢 Активен | 🔴 Неактивен</i>";

        // Кнопки
        $buttons = [];

        // Кнопки для каждого промокода
        $promoButtons = [];
        foreach ($promoCodes as $promo) {
            $promoButtons[] = ['text' => "📊 {$promo->code}", 'callback_data' => "stats_{$promo->id}"];
        }

        // Разбиваем на ряды по 3 кнопки
        $promoRows = array_chunk($promoButtons, 3);
        foreach ($promoRows as $row) {
            $buttons[] = $row;
        }

        // Пагинация
        $navButtons = [];
        if ($page > 0) {
            $navButtons[] = ['text' => '⬅️', 'callback_data' => 'page_' . ($page - 1)];
        }
        $navButtons[] = ['text' => '🔄', 'callback_data' => 'refresh_list'];
        if ($page < $totalPages - 1) {
            $navButtons[] = ['text' => '➡️', 'callback_data' => 'page_' . ($page + 1)];
        }
        $buttons[] = $navButtons;

        // Кнопка назад
        $buttons[] = [['text' => '🏠 Главное меню', 'callback_data' => 'back_to_menu']];

        $keyboard = ['inline_keyboard' => $buttons];

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
