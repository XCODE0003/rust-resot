<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class GameServerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('GameServer',function(){
            $class = "\\App\\Lib\\GameServer\\GameServerType" . config('server_api.rsworld_db_type');
            return new $class();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
