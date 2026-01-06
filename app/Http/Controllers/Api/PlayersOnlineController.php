<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\User;
use App\Models\PlayersOnline;
use Illuminate\Http\Request;
use App\Models\Server;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use GameServer;
use App\Lib\ServerQueriesApi;

class PlayersOnlineController extends Controller
{

    protected function getPlayersOnline(Request $request)
    {

        Log::channel('players_online')->info('Method: getPlayersOnline. Start...');

        if (!$request->has('token') || $request->token != 'ZFghyxDL71z94WgY_OFF') die('error');

        foreach(getservers() as $server) {

            if(config('options.server_'.$server->id.'_plate', 0) > 0) continue;

            $server_id = $server->id;
            $result = GameServer::getPlayersOnline($server_id);
            if (!$result) continue;

            $players_online = [];
            foreach ($result as $player) {

                $user = User::where('steam_id', $player->id)->first();
                if (!$user) continue;

                $player_online = PlayersOnline::where('steam_id', $player->id)->where('server', $server_id)->latest('updated_at')->first();
                if (!$player_online) {
                    $player_online = new PlayersOnline;
                    $player_online->steam_id = $player->id;
                    $player_online->user_id = $user->id;
                    $player_online->server = $server_id;
                }

                //Проверяю, что онлайн больше 0 и что игрока уже не считали
                if ($player->online_time <= 0 || in_array($player->id, $players_online)) continue;
                $players_online[] = $player->id;

                //Считаем онлайн для каждого сервера
                if ($player_online->online_prev <= $player->online_time) {
                    $diff = $player->online_time - $player_online->online_prev;
                    $player_online->online_time += $diff;
                } else {
                    $player_online->online_time += $player->online_time;
                }


                //Запись общего онлайна
                if ($player_online->online_prev <= $player->online_time) {
                    $diff = $player->online_time - $player_online->online_prev;
                    $user->online_time += $diff;
                } else {
                    $user->online_time += $player->online_time;
                }

                //Записываем отдельно время онлайна для сервера EU Monday
                if ($server->id == 3) {
                    if ($player_online->online_prev <= $player->online_time) {
                        $diff = $player->online_time - $player_online->online_prev;
                        $user->online_time_monday += $diff;
                    } else {
                        $user->online_time_monday += $player->online_time;
                    }
                }

                //Записываем отдельно время онлайна для кейса Thursday
                if (config('options.bonusth_status', '0') == '1' && ($server->id == 1 || $server->id == 2)) {
                    if ($player_online->online_prev <= $player->online_time) {
                        $diff = $player->online_time - $player_online->online_prev;
                        $user->online_time_thursday += $diff;
                    } else {
                        $user->online_time_thursday += $player->online_time;
                    }
                }

                $d = (isset($diff)) ? $diff : $player->online_time;
                Log::channel('players_online')->info('Method: getPlayersOnline. Server: '.$server_id.'. Player: '.$player->id.'('. $user->name .'). Prev online: ' . $player_online->online_prev . ', Server Online: '. $player_online->online_time . ', Online: '. $player->online_time .'. Diff online: '. $d .', All online: ' . $user->online_time . ', Monday online: ' . $user->online_time_monday . ', Thursday online: ' . $user->online_time_thursday);

                $player_online->online_prev = $player->online_time;
                $player_online->save();

                $user->save();

            }
        }

        die('finish');
    }

    protected function getPlayersOnlineTest(Request $request)
    {

        if (!$request->has('token') || $request->token != '397zwpHVzc!SEsoE') die('error');

        foreach(getservers() as $server) {

            if(config('options.server_'.$server->id.'_plate', 0) > 0) continue;

            $server_id = $server->id;
            $result = GameServer::getPlayersOnline($server_id);
            if (!$result) continue;

            dd($result);

            echo "\n" . '==========';
            echo($server_id) . "\n";

            $players_online = [];
            foreach ($result as $player) {

                $user = User::where('steam_id', $player->id)->first();
                if (!$user) continue;

                $player_online = PlayersOnline::where('steam_id', $player->id)->where('server', $server_id)->latest('updated_at')->first();
                if (!$player_online) {
                    $player_online = new PlayersOnline;
                    $player_online->steam_id = $player->id;
                    $player_online->user_id = $user->id;
                    $player_online->server = $server_id;
                }

                //Проверяю, что онлайн больше 0 и что игрока уже не считали
                if ($player->online_time <= 0 || in_array($player->id, $players_online)) continue;
                $players_online[] = $player->id;

                echo($user->name) . "\n";
                echo($player->id) . "\n";
                echo(json_encode($player_online)) . "\n";
                echo($player->online_time) . "\n";

                if ($player_online->online_prev < $player->online_time) {
                    $diff = $player->online_time - $player_online->online_prev;
                    $user->online_time += $diff;
                    echo($diff) . "\n";
                } else {
                    echo($player->online_time) . "\n";
                    $user->online_time += $player->online_time;
                }

                //Записываем отдельно время онлайна для сервера EU Monday
                if ($server->id == 3) {
                    if ($player_online->online_prev < $player->online_time) {
                        $diff = $player->online_time - $player_online->online_prev;
                        $user->online_time_monday += $diff;
                        echo($diff) . "\n";
                    } else {
                        $user->online_time_monday += $player->online_time;
                        echo($user->online_time_monday) . "\n";
                    }
                }

                //Записываем отдельно время онлайна для кейса Thursday
                if (config('options.bonusth_status', '0') == '1' && ($server->id == 1 || $server->id == 2)) {
                    if ($player_online->online_prev < $player->online_time) {
                        $diff = $player->online_time - $player_online->online_prev;
                        $user->online_time_thursday += $diff;
                        echo($diff) . "\n";
                    } else {
                        $user->online_time_thursday += $player->online_time;
                        echo($user->online_time_thursday) . "\n";
                    }
                }

                echo '----------------';
                $player_online->online_prev = $player->online_time;
                //$player_online->save();

                //$user->save();
            }
        }

        die('finish');
    }

    protected function getPlayersOnlineTest2(Request $request)
    {
        if (!$request->has('token') || $request->token != '397zwpHVzc!SEsoE') die('error');

        foreach(getservers() as $server) {

            if(config('options.server_'.$server->id.'_plate', 0) > 0) continue;

            $data = ServerQueriesApi::getOnlineCount();
            dd($data);
        }

        die('finish');
    }
}
