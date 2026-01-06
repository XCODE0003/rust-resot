<?php
namespace App\Lib;

require base_path() . '/vendor/source-query-master/SourceQuery/bootstrap.php';
use xPaw\SourceQuery\SourceQuery;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;

class ServerQueriesApi
{
    public static function getOnlineCount($ip, $port)
    {
        $data = [
            'count' => 0,
            'count_max' => 0,
            'queued' => 0,
        ];

        define('SQ_TIMEOUT', 1);
        define('SQ_ENGINE', SourceQuery::SOURCE);

        $Query = new SourceQuery();

        try {
            $Query->Connect($ip, $port, SQ_TIMEOUT, SQ_ENGINE);

            //Получаем каол-во онлайна
            $q_info = $Query->GetInfo();

            Log::channel('server_queries')->info('Method GetInfo: ' . print_r($q_info, 1));

            $data['count_max'] = (isset($q_info['MaxPlayers'])) ? $q_info['MaxPlayers'] : 0;
            $data['count'] = (isset($q_info['Players'])) ? $q_info['Players'] : 0;
            $data['queued'] = (isset($q_info['Bots'])) ? $q_info['Bots'] : 0;

            //Получаем список игроков
            $players = [];
            $q_players = $Query->GetPlayers();

            if (!empty($q_players)) {
                foreach ($q_players as $q_player) {
                    $players[] = (object) [
                        'name' => $q_player['Name'],
                        'online_time' => $q_player['Time'],
                    ];
                }
            }

            $data['players'] = $players;

            //print_r($Query->GetRules());

        } catch(Exception $e) {
            //echo $e->getMessage();
        } finally {
            $Query->Disconnect();
        }

        return $data;
    }

    public static function getPlayersOnline($ip, $port)
    {
        define('SQ_TIMEOUT', 1);
        define('SQ_ENGINE', SourceQuery::SOURCE);

        $players = [];

        $Query = new SourceQuery();

        try {
            $Query->Connect($ip, $port, SQ_TIMEOUT, SQ_ENGINE);

            $q_players = $Query->GetPlayers();

            Log::channel('server_queries')->info('Method GetPlayers: ' . print_r($q_players, 1));

            if (!empty($q_players)) {
                foreach ($q_players as $q_player) {
                    $players[] = (object) [
                        'name' => $q_player['Name'],
                        'online_time' => $q_player['Time'],
                    ];
                }
            }
        } catch(Exception $e) {
            //echo $e->getMessage();
        } finally {
            $Query->Disconnect();
        }

        return $players;
    }
}
