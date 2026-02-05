<?php

namespace App\Telegram\Scenes;

use App\Telegram\Services\ConversationService;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

abstract class BaseScene
{
    /**
     * @var Api
     */
    protected Api $telegram;

    /**
     * @var Update
     */
    protected Update $update;

    /**
     * @var int
     */
    protected int $telegramId;

    /**
     * @var int
     */
    protected int $chatId;

    /**
     * Имя сцены
     */
    abstract public static function getName(): string;

    /**
     * Точка входа в сцену
     */
    abstract public function enter(): void;

    /**
     * Обработка входящего сообщения/callback
     */
    abstract public function handle(): void;

    /**
     * Установить зависимости
     */
    public function setContext(Api $telegram, Update $update, int $telegramId, int $chatId): void
    {
        $this->telegram = $telegram;
        $this->update = $update;
        $this->telegramId = $telegramId;
        $this->chatId = $chatId;
    }

    /**
     * Получить текущий шаг
     */
    protected function getCurrentStep(): ?string
    {
        $state = ConversationService::getState($this->telegramId);
        return $state['step'] ?? null;
    }

    /**
     * Получить данные сцены
     */
    protected function getData(): array
    {
        $state = ConversationService::getState($this->telegramId);
        return $state['data'] ?? [];
    }

    /**
     * Обновить данные сцены
     */
    protected function setData(array $data): void
    {
        ConversationService::updateData($this->telegramId, $data);
    }

    /**
     * Перейти к шагу
     */
    protected function goToStep(string $step): void
    {
        ConversationService::nextStep($this->telegramId, $step);
    }

    /**
     * Завершить сцену
     */
    protected function leave(): void
    {
        ConversationService::clearState($this->telegramId);
    }

    /**
     * Отправить сообщение
     */
    protected function reply(string $text, array $options = []): void
    {
        $this->telegram->sendMessage(array_merge([
            'chat_id' => $this->chatId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ], $options));
    }

    /**
     * Ответить на callback query
     */
    protected function answerCallback(?string $text = null): void
    {
        if ($this->update->getCallbackQuery()) {
            $this->telegram->answerCallbackQuery([
                'callback_query_id' => $this->update->getCallbackQuery()->getId(),
                'text' => $text,
            ]);
        }
    }

    /**
     * Редактировать сообщение
     */
    protected function editMessage(int $messageId, string $text, array $options = []): void
    {
        $this->telegram->editMessageText(array_merge([
            'chat_id' => $this->chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => 'HTML',
        ], $options));
    }

    /**
     * Получить текст сообщения
     */
    protected function getMessageText(): ?string
    {
        if ($this->update->getMessage()) {
            return $this->update->getMessage()->getText();
        }
        return null;
    }

    /**
     * Получить данные callback
     */
    protected function getCallbackData(): ?string
    {
        if ($this->update->getCallbackQuery()) {
            return $this->update->getCallbackQuery()->getData();
        }
        return null;
    }

    /**
     * Получить ID сообщения из callback
     */
    protected function getCallbackMessageId(): ?int
    {
        if ($this->update->getCallbackQuery() && $this->update->getCallbackQuery()->getMessage()) {
            return $this->update->getCallbackQuery()->getMessage()->getMessageId();
        }
        return null;
    }
}
