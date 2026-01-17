<?php

namespace App\Providers;

use App\Models\Option;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Throwable;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // This app stores dynamic config in the `options` table.
        // During composer scripts (e.g. `artisan package:discover`) / first install,
        // the DB may not exist yet, so boot must not hard-fail here.
        try {
            DB::connection()->getPdo();

            if (! Schema::hasTable('options')) {
                return;
            }
        } catch (Throwable $e) {
            return;
        }

        $options = Cache::rememberForever('options', function () {
            return Option::all();
        });

        foreach ($options as $option) {
            config()->set("options.{$option->key}", $option->value);
        }
    }
}
