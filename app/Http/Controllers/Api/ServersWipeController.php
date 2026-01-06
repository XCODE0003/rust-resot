<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;
use App\Models\Server;
use App\Models\Shopping;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GameServer;

class ServersWipeController extends Controller
{

    protected function setLastWipeDate()
    {
        Log::channel('api')->info('Request: ' . print_r(@file_get_contents('php://input'), 1));
        $request = json_decode(@file_get_contents('php://input'));
        Log::channel('api')->info('Request: ' . print_r($request, 1));

        if (!isset($request->api_key) || $request->api_key != config('options.game_api_key', '')) {
            return response()->json([
                'status' => 'error',
                'msg'    => 'API key is invalid.',
            ], 500);
        }

        if (!isset($request->server)) {
            return response()->json([
                'status' => 'error',
                'msg'    => 'server is missed',
            ], 500);
        }
        if (!isset($request->wipe)) {
            return response()->json([
                'status' => 'error',
                'msg'    => 'wipe is missed',
            ], 500);
        }

        $server_id = 1;
        $server_find = FALSE;

        foreach (getservers() as $server) {
            if ($server->name == $request->server) {
                $server_id = $server->id;
                $server_find = TRUE;
            }
        }

        if ($server_find === FALSE) {
            return response()->json([
                'status' => 'error',
                'msg'    => 'server not find',
            ], 500);
        }

        $server = Server::where('id', $server_id)->first();
        $server->wipe = $request->wipe;
        $server->save();

        //Задаем время след вайпа
        $next_wipe_time = Option::where('key', 'server_'.$server->id.'_next_wipe_time')->first()->value;
        $next_wipe_time_add = Option::where('key', 'server_'.$server->id.'_next_wipe_time_add')->first()->value;

        $next_wipe = strtotime($request->wipe) + 60*60*$next_wipe_time;

        $date_wipe_form = date('d.m.Y', strtotime($request->wipe));
        $date_first = date('d.m.Y', strtotime('first thursday of this month'));

        if ($date_wipe_form == $date_first) {
            $next_wipe = $next_wipe + 60*60*$next_wipe_time_add;
        }

        $server->next_wipe = date('Y-m-d H:i:s', $next_wipe);
        $server->save();

        $server->options = '';

        return response()->json([
            'status' => 'success',
            'server' => $server,
        ]);
    }

    protected function forgetCacheOnline(Request $request)
    {
        //Log::channel('api')->info('Request: ' . print_r(@file_get_contents('php://input'), 1));
        $request = json_decode(@file_get_contents('php://input'));
        Log::channel('api')->info('Method: forgetCacheOnline. Request: ' . print_r($request, 1));

        if (!isset($request->api_key) || $request->api_key != config('options.game_api_key', '')) {
            return response()->json([
                'status' => 'error',
                'msg'    => 'API key is invalid.',
            ], 500);
        }

        if (!isset($request->server)) {
            return response()->json([
                'status' => 'error',
                'msg'    => 'server is missed',
            ], 500);
        }

        //Отключено, идет через schedule
        return response()->json([
            'status' => 'success',
            'server' => $request->server,
        ]);



        $server_id = 1;
        $server_find = FALSE;

        foreach (getservers() as $server) {
            if ($server->name == $request->server) {
                $server_id = $server->id;
                $server_find = TRUE;
            }
        }

        if ($server_find === FALSE) {
            return response()->json([
                'status' => 'error',
                'msg'    => 'server not find',
            ], 500);
        }

        /*
        Cache::forget('server'.$server_id.':online_count');
        Cache::forget('server'.$server_id.':online_max');
        Cache::forget('server'.$server_id.':online_queued');
        */

        //Задаем ограничение на кол-во запросов на обновление онлайна раз в секунду
        $lock = Cache::lock('server'.$server_id.':online_count_lock', 1);
        if ($lock->get()) {

            //Проверяем, есть ли не доставленные покупки и доставляем повторно
            if (1==1) {
                $result = GameServer::getPlayersOnline($server_id);
                $shoppings = Shopping::where('status', 0)->get();
                if ($result && $shoppings) {
                    foreach ($shoppings as $shopping) {
                        foreach ($result as $player) {
                            if ($shopping->user->steam_id == $player->id) {

                                //Задаем блок на отправку команды
                                $lock_shop = Cache::lock('server'.$server_id.':shopping_lock'.$shopping->id, 10);
                                if ($lock_shop->get()) {
                                    Log::channel('api')->info('Send command: ' . $shopping->command . '. Server: ' . $shopping->server);
                                    if (GameServer::transferServiceGameServer($shopping->command, $shopping->server)) {
                                        Log::channel('api')->info('Send command success: ' . $shopping->command . '. Server: ' . $shopping->server);
                                        $shopping->status = 1;
                                        $shopping->save();

                                        $lock_shop->release();
                                    }
                                }

                            }
                        }
                    }
                }
            }

            if (1==2) {
                $shoppings = Shopping::where('status', 0)->get();
                if ($shoppings) {
                    foreach ($shoppings as $shopping) {

                        //Задаем блок на отправку команды
                        $lock_shop = Cache::lock('server' . $server_id . ':shopping_lock' . $shopping->id, 10);
                        if ($lock_shop->get()) {
                            Log::channel('api')->info('Send command: ' . $shopping->command . '. Server: ' . $shopping->server);
                            if (GameServer::transferServiceGameServer($shopping->command, $shopping->server)) {
                                Log::channel('api')->info('Send command success: ' . $shopping->command . '. Server: ' . $shopping->server);
                                $shopping->status = 1;
                                $shopping->save();

                                $lock_shop->release();
                            }
                        }
                    }
                }
            }


            Log::channel('api')->info('Method: forgetCacheOnline. Is forget.');

            $data_online = GameServer::all_online_count($server_id);
            if ($data_online['count'] > 0) {
                Cache::forget('server'.$server_id.':online_count');
                Cache::forever('server'.$server_id.':online_count', $data_online['count']);
            }
            if ($data_online['count_max'] > 0) {
                Cache::forget('server'.$server_id.':online_max');
                Cache::forever('server'.$server_id.':online_max', $data_online['count_max']);
            }
            if ($data_online['queued'] >= 0) {
                Cache::forget('server'.$server_id.':online_queued');
                Cache::forever('server'.$server_id.':online_queued', $data_online['queued']);
            }
        }

        return response()->json([
            'status' => 'success',
            'server' => $request->server,
        ]);
    }

    protected function refreshStatus(Request $request)
    {
        Log::channel('api')->info('Method: RefreshServerStatus.');

        if (!isset($request->token) || $request->token != 'ZFghyxDL71z94WgY') {
            exit('error');
        }

        foreach (getservers() as $server) {
            $status = 'Offline';
            $options = json_decode($server->options);

            $ip_port = explode(':', $options->rcon_ip);
            if (!isset($ip_port[1])) return 'Offline';
            $ip = $ip_port[0];
            $port = $ip_port[1];

            $fp = @fsockopen($ip, $port, $errno, $errstr, 1);
            if ($fp) {
                fclose($fp);
                $status = 'Online';
            }

            Cache::forget('server'.$server->id.':status');
            Cache::forever('server'.$server->id.':status', $status);
        }

        exit('OK');
    }
}
