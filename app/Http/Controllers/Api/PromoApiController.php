<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PromoCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PromoApiController extends Controller
{
    /**
     * GET /api/promo/get
     * Возвращает список активных промокодов для плагина Rust
     * Формат: строго JSON массив строк ["promo1", "promo2"]
     */
    public function getPromos()
    {
        $now = now();

        $promoCodes = PromoCode::query()
            // Проверка по датам
            ->where(function ($query) use ($now) {
                $query->whereNull('date_start')
                    ->orWhere('date_start', '<=', $now);
            })
            ->where(function ($query) use ($now) {
                $query->whereNull('date_end')
                    ->orWhere('date_end', '>=', $now);
            })
            ->where('is_created_bot', true)
            ->get();

        // Фильтруем по лимиту активаций
        $activeCodes = [];

        foreach ($promoCodes as $promo) {
            // Проверяем лимит активаций
            if ($promo->max_activations !== null && $promo->max_activations > 0) {
                $users = json_decode($promo->users, true) ?? [];
                if (count($users) >= $promo->max_activations) {
                    continue; // Лимит достигнут, пропускаем
                }
            }

            // Приводим к lower-case как требует плагин
            $activeCodes[] = strtolower($promo->code);
        }

        // Строго возвращаем JSON массив строк без обёрток
        return response()->json($activeCodes);
    }

    /**
     * POST /api/activate
     * Активация промокода с сервера Rust
     *
     * Body: {"promo": "code", "steam_id": "76561...", "server_ip": "1.2.3.4"}
     *
     * Responses:
     * - 200: Успешно активировано
     * - 400: Промокод не найден, неактивен или лимит достигнут
     * - 409: Этот steam_id уже активировал данный промокод
     */
    public function activate(Request $request)
    {
        $promoCode = strtolower($request->input('promo', ''));
        $steamId = $request->input('steam_id', '');
        $serverIp = $request->input('server_ip', '');
        Log::channel('adminlog')->info("Promo API: Activation attempt - promo: {$promoCode}, steam_id: {$steamId}, server_ip: {$serverIp}");

        if (empty($promoCode) || empty($steamId)) {
            Log::channel('adminlog')->warning("Promo API: Missing required fields - promo: {$promoCode}, steam_id: {$steamId}");
            return response()->json(['error' => 'Missing required fields'], 400);
        }

        // Используем транзакцию и блокировку для защиты от гонок
        return DB::transaction(function () use ($promoCode, $steamId, $serverIp) {
            // Ищем промокод с блокировкой для обновления (case-insensitive)
            $promo = PromoCode::whereRaw('LOWER(code) = ?', [$promoCode])
                ->lockForUpdate()
                ->first();

            if (!$promo || $promo->is_created_bot === false) {
                Log::channel('adminlog')->warning("Promo API: Promo not found - {$promoCode}");
                return response()->json(['error' => 'Promo code not found'], 400);
            }

            $now = now();

            // Проверка по датам
            if ($promo->date_start && $promo->date_start > $now) {
                Log::channel('adminlog')->warning("Promo API: Promo not yet active - {$promoCode}");
                return response()->json(['error' => 'Promo code not yet active'], 400);
            }

            if ($promo->date_end && $promo->date_end < $now) {
                Log::channel('adminlog')->warning("Promo API: Promo expired - {$promoCode}");
                return response()->json(['error' => 'Promo code expired'], 400);
            }

            // Парсим текущих пользователей
            $users = json_decode($promo->users, true) ?? [];

            // Проверка на повторную активацию по steam_id
            foreach ($users as $user) {
                // Проверяем как steam_id (новый формат), так и user_id (старый формат, на всякий случай)
                if (isset($user['steam_id']) && $user['steam_id'] === $steamId) {
                    Log::channel('adminlog')->warning("Promo API: Already activated - promo: {$promoCode}, steam_id: {$steamId}");
                    return response()->json(['error' => 'Already activated'], 409);
                }
            }

            // Проверка лимита активаций
            if ($promo->max_activations !== null && $promo->max_activations > 0) {
                if (count($users) >= $promo->max_activations) {
                    Log::channel('adminlog')->warning("Promo API: Max activations reached - {$promoCode}");
                    return response()->json(['error' => 'Max activations reached'], 400);
                }
            }

            // Добавляем новую запись активации
            $users[] = [
                'steam_id' => $steamId,
                'date' => $now->format('Y-m-d H:i:s'),
                'is_bot_promo' => true,
                'server_ip' => $serverIp,
            ];

            $promo->users = json_encode($users);
            $promo->save();

            Log::channel('adminlog')->info("Promo API: Successfully activated - promo: {$promoCode}, steam_id: {$steamId}, server_ip: {$serverIp}");

            return response()->json(['success' => true, 'message' => 'Promo code activated'], 200);
        });
    }
}
