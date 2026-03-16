<?php
namespace App\Lib;

use App\Models\Account;
use App\Models\Characters;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\Auction;
use App\Models\UserDelivery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use GuzzleHttp\Client;
use App\Lib\RustCON;


class RustGameApi
{

    public static function createGameAccount($account, $password)
    {
        return TRUE;
    }
    public static function getOnlineCount()
    {
        $server_id = session('server_id', 1);
        
        $serverOnline = \App\Models\ServerOnline::where('server_id', $server_id)->first();
        
        if (!$serverOnline) {
            return [
                'count' => 0,
                'count_max' => 0,
                'queued' => 0,
            ];
        }

        $data = [
            'count'     => $serverOnline->online_count,
            'count_max' => $serverOnline->online_max,
            'queued'    => $serverOnline->online_queued,
        ];

        Log::channel('rcon')->info('Method: getOnlineCount. Result count: ' . print_r($data, 1));

        return $data;
    }

    public static function getPlayersOnline()
    {
        $server_id = session('server_id', 1);
        
        $serverOnline = \App\Models\ServerOnline::where('server_id', $server_id)->first();

        if (!$serverOnline || !$serverOnline->players_data) return [];

        $players = [];
        $result = explode("kicks ", $serverOnline->players_data);
        if (isset($result[1])) {
            $result1 = explode("\r\n", $result[1]);
            if (isset($result1[0])) {
                foreach ($result1 as $r1) {
                    if($r1 == '') continue;
                    $result2 = explode(" ", $r1);
                    if (isset($result2[1])) {
                        $player_id = $result2[0];
                        foreach ($result2 as $r2) {
                            if (mb_substr($r2, -1) != 's') continue;
                            $players[] = (object) [
                                'id' => $player_id,
                                'online_time' => intval(str_replace('s', '', $r2)),
                            ];
                        }
                    }
                }

            }
        }

        return $players;
    }

    public static function getAllOnlineCount()
    {
        return 300;
    }

    public static function sendItemToGame($item)
    {
        return TRUE;
    }

    public static function sendServiceToGame($command, $server_id = null)
    {
        $server_id = $server_id ?? session('server_id', 1);
        $result = RustCON::sendCommand($command, $server_id);

        Log::channel('rcon')->info('Method: sendServiceToGame. Result: ' . print_r($result, 1));

        if (isset($result->Message) && strpos($result->Message, 'not find player') !== FALSE) {
            return FALSE;
        }

        if (isset($result->Message) && (strpos($result->Message, 'Added to group') !== FALSE || strpos($result->Message, 'time extended') !== FALSE || strpos($result->Message, 'ermission granted') !== FALSE || strpos($result->Message, 'успешно') !== FALSE || strpos($result->Message, 'granted permission') !== FALSE)) {
            return TRUE;
        }

        return FALSE;
    }
}
