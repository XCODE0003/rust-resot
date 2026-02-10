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

        // Собираем steam_id + дату активации для статистики донатов
        $steamIdActivations = [];
        $userIds = [];
        
        foreach ($users as $user) {
            $activationDate = $user['date'] ?? null;
            
            if (isset($user['steam_id']) && !empty($user['steam_id']) && $activationDate) {
                $steamIdActivations[$user['steam_id']] = $activationDate;
            }
            if (isset($user['user_id']) && !empty($user['user_id'])) {
                $userIds[] = [
                    'user_id' => $user['user_id'],
                    'activation_date' => $activationDate,
                ];
            }
        }

        // Получаем steam_id пользователей по user_id
        if (!empty($userIds)) {
            $usersData = User::whereIn('id', array_column($userIds, 'user_id'))
                ->whereNotNull('steam_id')
                ->get(['id', 'steam_id']);
            
            foreach ($usersData as $userData) {
                $activation = collect($userIds)->firstWhere('user_id', $userData->id);
                if ($activation && isset($activation['activation_date'])) {
                    // Берём самую раннюю дату активации для этого steam_id
                    if (!isset($steamIdActivations[$userData->steam_id]) || 
                        strtotime($activation['activation_date']) < strtotime($steamIdActivations[$userData->steam_id])) {
                        $steamIdActivations[$userData->steam_id] = $activation['activation_date'];
                    }
                }
            }
        }

        $steamIds = array_keys($steamIdActivations);

        // Кэшируем агрегаты на 5 минут
        $cacheKey = "promo_stats_{$promo->id}_v3";
        $donateStats = Cache::remember($cacheKey, 300, function () use ($steamIds, $steamIdActivations) {
            if (empty($steamIds)) {
                return [
                    'total_count' => 0,
                    'total_amount' => 0,
                    'first_donation_at' => null,
                    'last_donation_at' => null,
                ];
            }

            // Подзапрос для фильтрации донатов только после активации
            $query = Donate::whereIn('steam_id', $steamIds)
                ->where('status', 1)
                ->where(function ($q) use ($steamIdActivations) {
                    foreach ($steamIdActivations as $steamId => $activationDate) {
                        $q->orWhere(function ($subQ) use ($steamId, $activationDate) {
                            $subQ->where('steam_id', $steamId)
                                ->where('created_at', '>=', $activationDate);
                        });
                    }
                });

            $stats = $query->selectRaw('
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
                ->where('status', 1)
                ->where(function ($q) use ($steamIdActivations) {
                    foreach ($steamIdActivations as $steamId => $activationDate) {
                        $q->orWhere(function ($subQ) use ($steamId, $activationDate) {
                            $subQ->where('steam_id', $steamId)
                                ->where('created_at', '>=', $activationDate);
                        });
                    }
                })
                ->orderBy('created_at', 'desc')
                ->paginate(50);
        }

        // Группировка по дням (последние 30 дней)
        $dailyStats = collect();
        if (!empty($steamIds)) {
            $dailyStats = Donate::whereIn('steam_id', $steamIds)
                ->where('status', 1)
                ->where('created_at', '>=', now()->subDays(30))
                ->where(function ($q) use ($steamIdActivations) {
                    foreach ($steamIdActivations as $steamId => $activationDate) {
                        $q->orWhere(function ($subQ) use ($steamId, $activationDate) {
                            $subQ->where('steam_id', $steamId)
                                ->where('created_at', '>=', $activationDate);
                        });
                    }
                })
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
