<?php

namespace App\Telegram\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Сервис для управления состояниями диалогов Telegram
 */
class ConversationService
{
    /**
     * Префикс для ключей кэша
     */
    protected const CACHE_PREFIX = 'telegram_conversation_';

    /**
     * Время жизни состояния (в секундах) - 1 час
     */
    protected const TTL = 3600;

    /**
     * Получить текущее состояние пользователя
     */
    public static function getState(int $telegramId): ?array
    {
        return Cache::get(self::CACHE_PREFIX . $telegramId);
    }

    /**
     * Установить состояние пользователя
     */
    public static function setState(int $telegramId, string $scene, string $step, array $data = []): void
    {
        Cache::put(self::CACHE_PREFIX . $telegramId, [
            'scene' => $scene,
            'step' => $step,
            'data' => $data,
            'updated_at' => now()->toDateTimeString(),
        ], self::TTL);
    }

    /**
     * Обновить данные в текущем состоянии
     */
    public static function updateData(int $telegramId, array $newData): void
    {
        $state = self::getState($telegramId);
        if ($state) {
            $state['data'] = array_merge($state['data'] ?? [], $newData);
            $state['updated_at'] = now()->toDateTimeString();
            Cache::put(self::CACHE_PREFIX . $telegramId, $state, self::TTL);
        }
    }

    /**
     * Перейти к следующему шагу
     */
    public static function nextStep(int $telegramId, string $step): void
    {
        $state = self::getState($telegramId);
        if ($state) {
            $state['step'] = $step;
            $state['updated_at'] = now()->toDateTimeString();
            Cache::put(self::CACHE_PREFIX . $telegramId, $state, self::TTL);
        }
    }

    /**
     * Очистить состояние (завершить сцену)
     */
    public static function clearState(int $telegramId): void
    {
        Cache::forget(self::CACHE_PREFIX . $telegramId);
    }

    /**
     * Проверить, находится ли пользователь в сцене
     */
    public static function inScene(int $telegramId, ?string $scene = null): bool
    {
        $state = self::getState($telegramId);
        if (!$state) {
            return false;
        }
        if ($scene !== null) {
            return ($state['scene'] ?? null) === $scene;
        }
        return true;
    }
}
