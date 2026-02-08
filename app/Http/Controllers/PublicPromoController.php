<?php

namespace App\Http\Controllers;

use App\Models\Donate;
use App\Models\PromoCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PublicPromoController extends Controller
{
    /**
     * Показать публичную статистику промокода
     */
    public function show(string $uuid)
    {
        $promo = PromoCode::where('public_uuid', $uuid)->first();

        if (!$promo) {
            abort(404);
        }

        // Получаем активации
        $users = json_decode($promo->users, true) ?? [];
        $totalActivations = count($users);

        // Собираем steam_id для статистики донатов
        $steamIds = [];
        $userIds = [];
        
        foreach ($users as $user) {
            if (isset($user['steam_id']) && !empty($user['steam_id'])) {
                $steamIds[] = $user['steam_id'];
            }
            if (isset($user['user_id']) && !empty($user['user_id'])) {
                $userIds[] = $user['user_id'];
            }
        }

        // Получаем steam_id пользователей по user_id
        if (!empty($userIds)) {
            $userSteamIds = User::whereIn('id', $userIds)
                ->whereNotNull('steam_id')
                ->pluck('steam_id')
                ->toArray();
            $steamIds = array_unique(array_merge($steamIds, $userSteamIds));
        }

        // Кэшируем агрегаты на 5 минут
        $cacheKey = "promo_stats_{$promo->id}_v2";
        $donateStats = Cache::remember($cacheKey, 300, function () use ($steamIds) {
            if (empty($steamIds)) {
                return [
                    'total_count' => 0,
                    'total_amount' => 0,
                    'first_donation_at' => null,
                    'last_donation_at' => null,
                ];
            }

            $stats = Donate::whereIn('steam_id', $steamIds)
                ->selectRaw('
                    COUNT(*) as total_count,
                    COALESCE(SUM(amount), 0) as total_amount,
                    MIN(created_at) as first_donation_at,
                    MAX(created_at) as last_donation_at
                ')
                ->first();

            return [
                'total_count' => $stats->total_count ?? 0,
                'total_amount' => $stats->total_amount ?? 0,
                'first_donation_at' => $stats->first_donation_at,
                'last_donation_at' => $stats->last_donation_at,
            ];
        });

        // Пагинация донатов (последние 50)
        $donations = collect();
        if (!empty($steamIds)) {
            $donations = Donate::whereIn('steam_id', $steamIds)
                ->orderBy('created_at', 'desc')
                ->paginate(50);
        }

        // Группировка по дням (последние 30 дней)
        $dailyStats = collect();
        if (!empty($steamIds)) {
            $dailyStats = Donate::whereIn('steam_id', $steamIds)
                ->where('created_at', '>=', now()->subDays(30))
                ->selectRaw('DATE(created_at) as date, SUM(amount) as total, COUNT(*) as count')
                ->groupBy('date')
                ->orderBy('date', 'desc')
                ->get();
        }

        // Счётчик активаций по типу
        $serverActivations = 0;
        $siteActivations = 0;
        foreach ($users as $user) {
            if (isset($user['is_bot_promo']) && $user['is_bot_promo']) {
                $serverActivations++;
            } else {
                $siteActivations++;
            }
        }

        return response()
            ->view('pages.promo.public', [
                'promo' => $promo,
                'totalActivations' => $totalActivations,
                'serverActivations' => $serverActivations,
                'siteActivations' => $siteActivations,
                'donateStats' => $donateStats,
                'donations' => $donations,
                'dailyStats' => $dailyStats,
                'steamIds' => $steamIds,
            ])
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
