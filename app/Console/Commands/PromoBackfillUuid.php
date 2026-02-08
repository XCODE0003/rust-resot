<?php

namespace App\Console\Commands;

use App\Models\PromoCode;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class PromoBackfillUuid extends Command
{
    protected $signature = 'promo:backfill-uuid {--force : Принудительно обновить все UUID}';

    protected $description = 'Заполнить public_uuid для существующих промокодов';

    public function handle()
    {
        $force = $this->option('force');

        if ($force) {
            if (!$this->confirm('Вы уверены что хотите перегенерировать ВСЕ UUID? Это сломает существующие ссылки!')) {
                $this->info('Отменено.');
                return 0;
            }

            $query = PromoCode::query();
        } else {
            $query = PromoCode::whereNull('public_uuid')->orWhere('public_uuid', '');
        }

        $count = $query->count();

        if ($count === 0) {
            $this->info('Нет промокодов для обновления.');
            return 0;
        }

        $this->info("Найдено {$count} промокодов для обновления.");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $updated = 0;

        $query->cursor()->each(function ($promo) use (&$updated, $bar) {
            $promo->public_uuid = (string) Str::uuid();
            $promo->save();
            $updated++;
            $bar->advance();
        });

        $bar->finish();
        $this->newLine();

        $this->info("✅ Обновлено {$updated} промокодов.");

        return 0;
    }
}
