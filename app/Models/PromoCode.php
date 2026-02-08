<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PromoCode extends Model
{
    protected $fillable = [
        'title',
        'code',
        'type',
        'type_reward',
        'user_id',
        'bonus_amount',
        'premium_period',
        'case_id',
        'bonus_case_id',
        'server_id',
        'users',
        'date_start',
        'date_end',
        'items',
        'shop_item_id',
        'variation_id',
        'max_activations',
        'is_created_bot',
        'public_uuid'
    ];

    protected $casts = [
        'is_created_bot' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Автогенерация UUID при создании
        static::creating(function ($model) {
            if (empty($model->public_uuid)) {
                $model->public_uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Получить публичную ссылку на статистику
     */
    public function getPublicUrlAttribute(): string
    {
        return route('promo.public', ['uuid' => $this->public_uuid]);
    }

    /**
     * Найти промокод по public_uuid
     */
    public static function findByPublicUuid(string $uuid): ?self
    {
        return static::where('public_uuid', $uuid)->first();
    }
}

