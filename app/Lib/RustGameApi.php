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
        $data = [
            'count' => 0,
            'count_max' => 0,
            'queued' => 0,
        ];
        $result = RustCON::sendCommand('status');

        Log::channel('rcon')->info('Method: getOnlineCount. Result RCON: ' . print_r($result, 1));

        if (!$result) return $data;

        $count = 0;
        $count_max = 0;
        $queued = 0;
        $result = explode("players : ", $result->Message);
        if (isset($result[1])) {
            $result5 = explode(" queued", $result[1]);
            if (isset($result5[0])) {
                $result5 = explode("max) (", $result5[0]);
                if (isset($result5[1])) {
                    $queued = $result5[1];
                }
            }
            $result = explode(" queued", $result[1]);
            if (isset($result[0])) {
                $result1 = explode(" max)", $result[0]);
                if (isset($result1[0])) {
                    $result4 = explode(" (", $result1[0]);
                    if (isset($result4[0])) {
                        $count = $result4[0];
                    }
                }
                $result2 = explode(" (", $result[0]);
                if (isset($result2[1])) {
                    $result3 = explode(" max)", $result2[1]);
                    $count_max = $result3[0];
                }
            }
        }

        $data = [
            'count'     => $count,
            'count_max' => $count_max,
            'queued'    => $queued,
        ];

        Log::channel('rcon')->info('Method: getOnlineCount. Result count: ' . print_r($data, 1));

        return $data;
    }

    public static function getPlayersOnline()
    {
        $result = RustCON::sendCommand('status');

        if (!$result) return '';

        $players = [];
        $result = explode("kicks ", $result->Message);
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

    public static function sendServiceToGame($command)
    {
        $result = RustCON::sendCommand($command);

        Log::channel('rcon')->info('Method: sendServiceToGame. Result: ' . print_r($result, 1));

        if (isset($result->Message) && strpos($result->Message, 'not find player') !== FALSE) {
            return FALSE;
        }

        if (isset($result->Message) && (strpos($result->Message, 'Added to group') !== FALSE || strpos($result->Message, 'time extended') !== FALSE || strpos($result->Message, 'ermission granted') !== FALSE || strpos($result->Message, 'успешно') !== FALSE || strpos($result->Message, 'granted permission') !== FALSE)) {
            return TRUE;
        }

        //Ставим блок, чтобы не выдало пока не придет ответ с игрового сервера, доставлена услуга или нет
        Cache::put('transferServiceGameServer'.json_encode($command).'_lock', true, 30);

        return FALSE;
    }
}
