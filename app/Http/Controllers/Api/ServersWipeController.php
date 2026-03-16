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

        return response()->json([
            'status' => 'success',
            'server' => $request->server,
            'msg' => 'Online data now updates automatically every minute via schedule',
        ]);
    }

    protected function refreshStatus(Request $request)
    {
        Log::channel('api')->info('Method: RefreshServerStatus.');

        if (!isset($request->token) || $request->token != 'ZFghyxDL71z94WgY') {
            exit('error');
        }

        exit('OK - Server status now determined by database update time');
    }
}
