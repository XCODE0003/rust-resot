<?php

namespace App\Providers;

use App\Models\Option;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

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
        $options = Cache::rememberForever('options', function () {
            return Option::all();
        });

        foreach ($options as $option) {
            config()->set("options.{$option->key}", $option->value);
        }
    }
}
