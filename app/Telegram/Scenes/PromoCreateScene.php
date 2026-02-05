<?php

namespace App\Telegram\Scenes;

use App\Models\PromoCode;
use App\Telegram\Services\ConversationService;
use Illuminate\Support\Facades\Log;

class PromoCreateScene extends BaseScene
{
    public static function getName(): string
    {
        return 'promo_create';
    }

    public function enter(): void
    {
        ConversationService::setState($this->telegramId, self::getName(), 'ask_code', []);
        $this->askCode();
    }

    public function handle(): void
    {
        $step = $this->getCurrentStep();
        $callbackData = $this->getCallbackData();

        // Обработка кнопки "Отмена"
        if ($callbackData === 'cancel') {
            $this->answerCallback('Отменено');
            $this->leave();
            $this->goToMainMenu();
            return;
        }

        // Обработка кнопки "Пропустить" название
        if ($callbackData === 'skip_title') {
            $this->answerCallback();
            $data = $this->getData();
            $this->setData(['title' => $data['code']]);
            $this->goToStep('confirm');
            $this->showConfirmation();
            return;
        }

        // Обработка кнопки "Подтвердить"
        if ($callbackData === 'confirm') {
            $this->answerCallback();
            $this->createPromoCode();
            return;
        }

        // Обработка кнопки "Редактировать"
        if ($callbackData === 'edit') {
            $this->answerCallback();
            $this->enter();
            return;
        }

        // Обработка текстовых сообщений
        $text = $this->getMessageText();
        if ($text) {
            switch ($step) {
                case 'ask_code':
                    $this->handleCode($text);
                    break;
                case 'ask_title':
                    $this->handleTitle($text);
                    break;
            }
        }
    }

    protected function askCode(): void
    {
        $text = "➕ <b>Создание промокода</b>\n\n";
        $text .= "📝 <b>Шаг 1/2:</b> Введите код промокода\n\n";
        $text .= "<i>Код будет приведён к нижнему регистру.\nИспользуйте латинские буквы и цифры.</i>";

        $keyboard = [
            'inline_keyboard' => [
                [['text' => '❌ Отмена', 'callback_data' => 'cancel']],
            ],
        ];

        $this->reply($text, ['reply_markup' => json_encode($keyboard)]);
    }

    protected function handleCode(string $text): void
    {
        $code = strtolower(trim($text));

        // Валидация
        if (!preg_match('/^[a-z0-9_-]+$/i', $code)) {
            $this->reply("❌ Код может содержать только латинские буквы, цифры, _ и -\n\nПопробуйте ещё раз:");
            return;
        }

        if (strlen($code) < 3 || strlen($code) > 50) {
            $this->reply("❌ Длина кода должна быть от 3 до 50 символов\n\nПопробуйте ещё раз:");
            return;
        }

        // Проверка уникальности
        if (PromoCode::whereRaw('LOWER(code) = ?', [$code])->exists()) {
            $this->reply("❌ Промокод <code>{$code}</code> уже существует\n\nВведите другой код:");
            return;
        }

        // Сохраняем код и переходим к следующему шагу
        ConversationService::setState($this->telegramId, self::getName(), 'ask_title', ['code' => $code]);
        $this->askTitle();
    }

    protected function askTitle(): void
    {
        $data = $this->getData();

        $text = "➕ <b>Создание промокода</b>\n\n";
        $text .= "📝 Код: <code>{$data['code']}</code>\n\n";
        $text .= "📝 <b>Шаг 2/2:</b> Введите название промокода\n\n";
        $text .= "<i>Например: \"Новогодняя акция\" или \"VIP бонус\"</i>";

        $keyboard = [
            'inline_keyboard' => [
                [['text' => '⏭ Пропустить (использовать код)', 'callback_data' => 'skip_title']],
                [['text' => '❌ Отмена', 'callback_data' => 'cancel']],
            ],
        ];

        $this->reply($text, ['reply_markup' => json_encode($keyboard)]);
    }

    protected function handleTitle(string $text): void
    {
        $title = trim($text);

        if (strlen($title) > 255) {
            $this->reply("❌ Название слишком длинное (максимум 255 символов)\n\nПопробуйте ещё раз:");
            return;
        }

        $data = $this->getData();
        ConversationService::setState($this->telegramId, self::getName(), 'confirm', [
            'code' => $data['code'],
            'title' => $title,
        ]);
        $this->showConfirmation();
    }

    protected function showConfirmation(): void
    {
        $data = $this->getData();

        $text = "✅ <b>Подтверждение создания</b>\n\n";
        $text .= "📝 <b>Код:</b> <code>{$data['code']}</code>\n";
        $text .= "📌 <b>Название:</b> {$data['title']}\n\n";
        $text .= "Всё верно?";

        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => '✅ Создать', 'callback_data' => 'confirm'],
                    ['text' => '✏️ Изменить', 'callback_data' => 'edit'],
                ],
                [['text' => '❌ Отмена', 'callback_data' => 'cancel']],
            ],
        ];

        $this->reply($text, ['reply_markup' => json_encode($keyboard)]);
    }

    protected function createPromoCode(): void
    {
        $data = $this->getData();

        try {
            $promo = PromoCode::create([
                'title' => $data['title'],
                'code' => $data['code'],
                'type' => 1,
                'type_reward' => 2,
                'bonus_amount' => 0,
                'user_id' => null,
                'users' => json_encode([]),
                'max_activations' => null,
                'date_start' => date('Y-m-d H:i:s'),
                'date_end' => date('Y-m-d H:i:s', strtotime('+10 years')),
                'is_created_bot' => true,
            ]);

            Log::channel('adminlog')->info("Telegram Bot: Created promo code '{$data['code']}' by Telegram ID {$this->telegramId}");

            $text = "🎉 <b>Промокод успешно создан!</b>\n\n";
            $text .= "📝 <b>Код:</b> <code>{$promo->code}</code>\n";
            $text .= "📌 <b>Название:</b> {$promo->title}\n\n";

            $this->reply($text);

        } catch (\Exception $e) {
            Log::channel('adminlog')->error("Telegram Bot: Failed to create promo - " . $e->getMessage());
            $this->reply("❌ Ошибка при создании промокода:\n" . $e->getMessage());
        }

        $this->leave();
        $this->goToMainMenu();
    }

    protected function goToMainMenu(): void
    {
        $scene = new MainMenuScene();
        $scene->setContext($this->telegram, $this->update, $this->telegramId, $this->chatId);
        $scene->enter();
    }
}
