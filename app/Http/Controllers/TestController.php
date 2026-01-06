<?php

namespace App\Http\Controllers;

use App\Lib\SteamApi;
use App\Lib\RustCON;
use App\Lib\RustGameApi;
use App\Lib\SkinsbackAPI;

use App\Http\Requests\AccountRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\Account;
use App\Models\Characters;
use App\Models\Warehouse;
use App\Models\Server;
use App\Models\User;
use App\Models\PlayersOnline;
use App\Models\Option;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

use WebSocket\BadOpcodeException;
use WebSocket\Client;
use WebSocket\TimeoutException;
use Mail;
use GameServer;

class TestController extends Controller
{
    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|Response
     */
    public function rcon()
    {
        /*
        $command = 'ownerid 76561198216632595';
        $result = GameServer::transferServiceGameServer($command, 1);
        dd($result);
        */

        $result = GameServer::getPlayersOnline(2);

        foreach(getservers() as $server) {
            $server_id = $server->id;
            $result = GameServer::getPlayersOnline($server_id);

            foreach ($result as $player) {

                $user = User::where('steam_id', $player->id)->first();
                if (!$user) continue;

                $player_online = PlayersOnline::where('steam_id', $player->id)->first();
                if (!$player_online) {
                    $player_online = new PlayersOnline;
                    $player_online->steam_id = $player->id;
                    $player_online->user_id = $user->id;
                    $player_online->server = $server_id;
                }

                if ($player_online->online_prev < $player->online_time) {
                    $diff = $player->online_time - $player_online->online_prev;
                    $user->online_time += $diff;
                } else {
                    $user->online_time += $player->online_time;
                }

                $player_online->online_prev = $player->online_time;
                $player_online->save();
                $user->save();
            }
        }
    }

    public function resetonline()
    {
        dd('ok');
        DB::statement("UPDATE players_onlines SET online_time = '0' WHERE user_id = '497' AND server = '1'");
        //DB::statement("UPDATE players_onlines SET online_time = '0' WHERE online_time > 0");
    }

    public function setonline()
    {
        dd('ok');
        $users_str = '76561198338730957,2,93600
76561198134155203,2,162000
76561199436204586,2,46800
76561198930927374,2,205200
76561199087198916,2,100800
76561199054514903,2,61200
76561198976392866,1,152000
76561198856881016,3,28800
76561198841778620,3,234000
76561198019279408,3,118800
76561198852456660,2,158000
76561198254477827,3,18000
76561198424213494,3,25200
76561199106231840,2,134000
76561199274889095,2,36000
76561198344418812,2,260000
76561199435477143,2,116000
76561198829411733,3,36000
76561199015060722,2,216000
76561199224226699,2,115000
76561199231713229,3,36000
76561198393844947,3,18000
76561198128451141,2,162000
76561198956011121,2,333000
76561199365800358,2,280000
76561199006498975,2,18000
76561198259869627,2,240000
76561198146854197,2,36000
76561199465469938,2,144000
76561198869617081,3,18000
76561198066940624,2,127000
76561199140750642,3,82800
76561198950930378,2,112000
76561199303506854,2,305000
76561199414665604,2,100000
76561198257029987,2,108000
76561199157990986,3,18000
76561198164521737,3,18000
76561198971645270,3,144000
76561198232125848,2,36000
76561198306455760,2,234000
76561198217634429,2,200000
76561198276544797,2,151200
76561198401639871,2,64800
76561198202000783,2,97200
76561198378535534,3,144000
76561199155801610,3,21600
76561199059229844,3,9000
76561198046044009,3,129600
76561198062887950,3,0
76561198230254188,3,129600
76561199012010168,3,126000
76561198082231626,2,226800
76561199015857826,2,136800
76561199120236223,3,334800
76561199259806043,2,126000
76561197969780467,1,320400
76561199027850815,3,234000
76561198333381533,2,241200
76561198205907888,2,136800
76561198212520645,2,133200
76561198178768677,3,241200
76561198978887177,2,165600
76561198288555488,3,144000
76561199206930016,2,187200
76561199140473944,2,93600
76561198931928582,2,68400
76561199225069156,2,10800
76561198274715108,2,61200
76561199133785775,3,61200
76561198886634249,3,3600
76561198161841739,2,138312
76561199392270897,3,165600
76561198850287169,3,36000
76561198170043150,2,576000
76561199063071271,3,29000
76561199049713187,3,18000
76561199106686466,3,140000
76561199073478689,3,36000
76561199095238871,2,46800
76561198383421145,3,10800
76561198166196992,2,90000
76561199245668660,3,18000
76561198107292744,3,19000
76561198805483073,2,14400
76561198390901543,3,18000
76561198972762238,3,10808
76561198131197080,2,10600
76561199070877380,3,65000
76561198260337982,3,5000
76561199119346348,3,43200
76561199446356370,3,5000
76561199036347319,3,25200
76561198978116644,3,58000
76561198820692811,3,5000
76561199435081339,3,5000
76561198292790492,1,46000
76561199066737606,3,5000';

        $users = explode("\n",$users_str);
        foreach ($users as $user) {
            $params = explode(",",$user);
            echo("UPDATE players_onlines SET `online_prev` = '".$params[2]."', `online_time` = '".$params[2]."' WHERE steam_id = '".$params[0]."' AND server = '".$params[1]."'");
            DB::statement("UPDATE players_onlines SET `online_prev` = '".$params[2]."', `online_time` = '".$params[2]."' WHERE steam_id = '".$params[0]."' AND server = '".$params[1]."'");
        }


    }

    public function searchItemsByName()
    {
        //dd('ok');
        //$items = SkinsbackAPI::getItems('rust');
        //dd($items);
        //$items = SkinsbackAPI::getItemMinPriceByName('rust', 'Ammo Wooden Box');
        //dd($items);
        $id = SkinsbackAPI::buyOneP2PName('Ammo Wooden Box', 'rust', 0.5, 11, 'https://steamcommunity.com/tradeoffer/new/?partner=256366867&token=PFfRl5ZA');
        dd($id);
    }

    public function testServerSocket()
    {
        //dd('work');

        $ip = '46.174.52.218';
        $port = 28036;

        $socket = fsockopen("udp://" . $ip, $port, $errno, $errstr);
        if (!$socket) {
            echo "Ошибка при соединении с сервером: $errstr ($errno)";
            return;
        }
        $request = "\xFF\xFF\xFF\xFF\x54Source Engine Query\x00";

        try {
            fwrite($socket, $request);
            stream_set_timeout($socket, 2);
            $response = fread($socket, 9);

            echo "Ответ от сервера:\n";
            echo $response;

            // Получение последних 5 байтов
            $last_five_bytes = substr($response, -4);

            // Добавление последних 5 байтов к запросу
            $request .= $last_five_bytes;

            // Отправка измененного запроса на сервер Steam
            fwrite($socket, $request);

            // Получение окончательного ответа от сервера
            $final_response = fread($socket, 4096);

            // Обработка и интерпретация полученных данных
            echo "Ответ от сервера:\n";
            dd($final_response);

        } catch (Exception $e) {
            echo "Ошибка при соединении с сервером: " . $e->getMessage();

        } finally {
            // Закрытие сокета
            fclose($socket);
        }

        exit();

        $socket = fsockopen("udp://" . $ip, $port, $errno, $errstr);
        if (!$socket) {
            echo "Ошибка при соединении с сервером: $errstr ($errno)";
            return;
        }
        $request = "\xFF\xFF\xFF\xFF\x55\xFF\xFF\xFF\xFF";

        try {
            fwrite($socket, $request);
            stream_set_timeout($socket, 2);
            $response = fread($socket, 9);

            echo "Ответ от сервера:\n";
            echo $response;

            // Получение последних 5 байтов
            $last_five_bytes = substr($response, -4);

            // Добавление последних 5 байтов к запросу
            $request = "\xFF\xFF\xFF\xFF\x55" . $last_five_bytes;

            // Отправка измененного запроса на сервер Steam
            fwrite($socket, $request);

            // Получение окончательного ответа от сервера
            $final_response = fread($socket, 4096);

            // Обработка и интерпретация полученных данных
            echo "Ответ от сервера:\n";
            $utf8Text = mb_convert_encoding($final_response, 'UTF-8', 'ISO-8859-1');

            echo $utf8Text;
            dd($utf8Text);

        } catch (Exception $e) {
            echo "Ошибка при соединении с сервером: " . $e->getMessage();

        } finally {
            // Закрытие сокета
            fclose($socket);
        }

        // Создание UDP-сокета
        $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);

        // Формирование запроса на получение информации о сервере
        $request = "\xFF\xFF\xFF\xFF\x54Source Engine Query\x00";

        try {
            // Отправка запроса на сервер Steam
            socket_sendto($sock, $request, strlen($request), 0, $ip, $port);

            // Получение ответа от сервера
            socket_recvfrom($sock, $response, 2, 0, $addr);

            // Обработка и интерпретация полученных данных
            // В этом примере просто выводим полученные данные на экран
            echo "Ответ от сервера:\n";
            echo $response;

        } catch (Exception $e) {
            echo "Ошибка при соединении с сервером: " . $e->getMessage();

        } finally {
            // Закрытие сокета
            socket_close($sock);
        }
    }

}
