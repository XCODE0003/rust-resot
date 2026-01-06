<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Server;
use App\Models\Option;

class ServerConfig
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */

    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('server_id')) {
            session()->put('server_id', '1');
        }

        $server = Server::where('id', session('server_id'))->first();
        $options = json_decode($server->options);

        //Записываем в конфиг подключения значения для текущего сервера
        config(['server_api.ip' => $options->ip]);
        config(['server_api.rcon_ip' => $options->rcon_ip]);
        config(['server_api.rsworld_db_type' => $options->rsworld_db_type]);
        config(['server_api.api_url' => (isset($options->api_url)) ? $options->api_url : '']);
        config(['server_api.api_key' => (isset($options->api_key)) ? $options->api_key : '']);
        config(['server_api.rcon_passw' => (isset($options->rcon_passw)) ? $options->rcon_passw : '']);

        return $next($request);
    }
}
