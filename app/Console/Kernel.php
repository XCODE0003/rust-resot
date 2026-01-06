<?php

namespace App\Console;

use App\Models\User;
use App\Models\RconTask;
use App\Models\PlayersOnline;
use App\Models\Player;
use App\Models\Statistic;
use App\Models\ClearStatistic;
use App\Models\Shopping;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use WebSocket\BadOpcodeException;
use WebSocket\Client;
use WebSocket\TimeoutException;
use GameServer;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {

            $client = [];
            //Подключаемся к ркон и держим подключение

            foreach (getservers() as $server) {

                Log::channel('rcon_master')->info('Servers: ' . json_encode($server));

                $options = json_decode($server->options);
                $rcon_ip = $options->rcon_ip ?? '';
                $rcon_passw = $options->rcon_passw ?? '';

                try {
                    $client[$server->id] = new Client("ws://" . $rcon_ip . "/" . $rcon_passw);
                    Log::channel('rcon_master')->info('Connect success! Server ID: ' . $server->id);
                } catch (\Exception $ex) {
                    Log::channel('rcon_master')->info('Connect error! Server ID: ' . $server->id);
                }
            }


            for ($t = 0; $t < 7; $t++) {

                //Get rcon tasks
                /*
                foreach (getservers() as $server) {

                    $rcon_tasks = RconTask::where('status', 0)->where('server', $server->id)->get();

                    foreach ($rcon_tasks as $task) {
                        Log::channel('rcon_master')->info('Rcon Task: ' . json_encode($task));

                        //Отправляем команду на ркон
                        if (isset($client[$server->id]) && $client[$server->id]) {

                            try {
                                $data = json_encode([
                                    'Identifier' => 0,
                                    'Message'    => $task->command,
                                    'Stacktrace' => '',
                                    'Type'       => 3,
                                ]);

                                $client[$server->id]->send($data);
                                $result = json_decode($client[$server->id]->receive());

                                Log::channel('rcon_master')->info('Rcon command Result:' . json_encode($result));

                                if (isset($result->Message) && (strpos($result->Message, 'Added to group') !== FALSE || strpos($result->Message, 'time extended') !== FALSE || strpos($result->Message, 'ermission granted') !== FALSE)) {
                                    $task->status = 1;
                                } else {
                                    $task->status = 2;
                                    $task->comment = 'Some error';
                                }
                                $task->save();
                            } catch (\Exception $ex) {
                                Log::channel('rcon_master')->info('Rcon error set command!');
                            }

                        }

                    }

                }
                */

                //Get the server online date

                foreach (getservers() as $server) {

                    try {

                        if (isset($client[$server->id]) && $client[$server->id]) {

                            $data = json_encode([
                                'Identifier' => 0,
                                'Message'    => 'status',
                                'Stacktrace' => '',
                                'Type'       => 3,
                            ]);

                            $client[$server->id]->send($data);
                            $result = json_decode($client[$server->id]->receive());

                            Log::channel('rcon_master')->info('Rcon Result: ' . json_encode($result));

                            if (isset($result->Message) && substr($result->Message, 0, 8) === 'hostname') {
                                Log::channel('rcon_master')->info('Rcon online_data: ' . json_encode($result));
                                Cache::forever('server' . $server->id . ':online_data', $result);
                                break;
                            }
                        }

                    } catch (\Exception $ex) {
                        Log::channel('rcon_master')->info('Rcon error get online data!');
                    }

                }


                Log::channel('schedule')->info('Seconds - ' . $t);

                sleep(9);
            }
        })->everyMinute();


        //Get Servers online count
        $getOnline = function () {

            foreach (getservers() as $server) {

                //Get online count
                if (Cache::has('server' . $server->id . ':online_data')) {
                    $result = Cache::get('server' . $server->id . ':online_data');

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

                    //Refresh cache online count
                    if ($count > 0) {
                        Cache::forget('server' . $server->id . ':online_count');
                        Cache::forever('server' . $server->id . ':online_count', $count);
                    }
                    if ($count_max > 0) {
                        Cache::forget('server' . $server->id . ':online_max');
                        Cache::forever('server' . $server->id . ':online_max', $count_max);
                    }
                    if ($queued >= 0) {
                        Cache::forget('server' . $server->id . ':online_queued');
                        Cache::forever('server' . $server->id . ':online_queued', $queued);
                    }

                }

                //Check and send shop purchase without check online
                Log::channel('rcon_master')->info('Check Shopping...');
                $shoppings = Shopping::where('status', 0)->where('server', $server->id)->get();

                if ($shoppings) {
                    foreach ($shoppings as $shopping) {

                        //Если есть блок на отправку команды от ркон, то пропускаем
                        $lock_rcon = Cache::get('transferServiceGameServer'.json_encode($shopping->command).'_lock', false);
                        if ($lock_rcon) continue;

                        //Задаем блок на отправку команды
                        $lock_shop = Cache::lock('server' . $server->id . ':shopping_lock' . $shopping->id, 30);
                        if ($lock_shop->get()) {
                            Log::channel('rcon_master')->info('Send command: ' . $shopping->command . '. Server: ' . $shopping->server);

                            //Отправляем на ркон команду

                            $options = json_decode($server->options);
                            $rcon_ip = $options->rcon_ip ?? '';
                            $rcon_passw = $options->rcon_passw ?? '';

                            try {
                                $client[$server->id] = new Client("ws://" . $rcon_ip . "/" . $rcon_passw);
                                Log::channel('rcon_master')->info('Command Connect success! Server ID: ' . $server->id);
                            } catch (\Exception $ex) {
                                Log::channel('rcon_master')->info('Command Connect error! Server ID: ' . $server->id);
                            }

                            if (isset($client[$server->id]) && $client[$server->id]) {

                                $data = json_encode([
                                    'Identifier' => 0,
                                    'Message'    => $shopping->command,
                                    'Stacktrace' => '',
                                    'Type'       => 3,
                                ]);

                                try {
                                    $client[$server->id]->send($data);
                                    $rcon_result = json_decode($client[$server->id]->receive());

                                    //$shopping->status = 1;
                                    //$shopping->save();

                                    Log::channel('rcon_master')->info('Rcon Send command Result: ' . json_encode($rcon_result));

                                    if (isset($rcon_result->Message) && (strpos($rcon_result->Message, 'Added to group') !== FALSE || strpos($rcon_result->Message, 'time extended') !== FALSE || strpos($rcon_result->Message, 'ermission granted') !== FALSE || strpos($rcon_result->Message, 'успешно') !== FALSE || strpos($rcon_result->Message, 'granted permission') !== FALSE)) {
                                        Log::channel('rcon_master')->info('Send command success: ' . $shopping->command . '. Server: ' . $shopping->server);
                                        $shopping->status = 1;
                                        $shopping->save();

                                        $lock_shop->release();
                                    }
                                } catch (\Exception $ex) {
                                    Log::channel('rcon_master')->info('Rcon Send command error! Server ID: ' . $server->id);
                                }
                            }

                        }
                    }

                }

                //Check and send shop purchase
                /*
                if (Cache::has('server' . $server->id . ':online_data')) {
                    $result = Cache::get('server' . $server->id . ':online_data');

                    if ($result) {

                        $players = [];
                        $result = explode("kicks ", $result->Message);
                        if (isset($result[1])) {
                            $result1 = explode("\r\n", $result[1]);
                            if (isset($result1[0])) {
                                foreach ($result1 as $r1) {
                                    if ($r1 == '') continue;
                                    $result2 = explode(" ", $r1);
                                    if (isset($result2[1])) {
                                        $player_id = $result2[0];
                                        foreach ($result2 as $r2) {
                                            if (mb_substr($r2, -1) != 's') continue;
                                            $players[] = (object)[
                                                'id'          => $player_id,
                                                'online_time' => intval(str_replace('s', '', $r2)),
                                            ];
                                        }
                                    }
                                }

                            }
                        }

                        $shoppings = Shopping::where('status', 0)->where('server', $server->id)->get();

                        if ($players && $shoppings) {
                            foreach ($shoppings as $shopping) {
                                foreach ($players as $player) {
                                    if ($shopping->user->steam_id == $player->id) {

                                        //Задаем блок на отправку команды
                                        $lock_shop = Cache::lock('server' . $server->id . ':shopping_lock' . $shopping->id, 5);
                                        if ($lock_shop->get()) {
                                            Log::channel('rcon_master')->info('Send command: ' . $shopping->command . '. Server: ' . $shopping->server);

                                            //Отправляем на ркон команду

                                            $options = json_decode($server->options);
                                            $rcon_ip = $options->rcon_ip ?? '';
                                            $rcon_passw = $options->rcon_passw ?? '';

                                            try {
                                                $client[$server->id] = new Client("ws://" . $rcon_ip . "/" . $rcon_passw);
                                                Log::channel('rcon_master')->info('Command Connect success! Server ID: ' . $server->id);
                                            } catch (\Exception $ex) {
                                                Log::channel('rcon_master')->info('Command Connect error! Server ID: ' . $server->id);
                                            }

                                            if (isset($client[$server->id]) && $client[$server->id]) {

                                                $data = json_encode([
                                                    'Identifier' => 0,
                                                    'Message'    => $shopping->command,
                                                    'Stacktrace' => '',
                                                    'Type'       => 3,
                                                ]);

                                                $client[$server->id]->send($data);
                                                $rcon_result = json_decode($client[$server->id]->receive());

                                                Log::channel('rcon_master')->info('Rcon Send command Result: ' . json_encode($rcon_result));

                                                if (isset($rcon_result->Message) && (strpos($rcon_result->Message, 'Added to group') !== FALSE || strpos($rcon_result->Message, 'time extended') !== FALSE || strpos($rcon_result->Message, 'ermission granted') !== FALSE)) {
                                                    Log::channel('rcon_master')->info('Send command success: ' . $shopping->command . '. Server: ' . $shopping->server);
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
                    }
                }
                */
            }
        };

        $schedule->call(function () use ($getOnline) {

            Log::channel('rcon_master')->info("getOnline:" . "\n");

            for ($t = 0; $t < 6; $t++) {
                $getOnline();
                sleep(10);
            }

        })->everyMinute();


        //Get servers status
        $schedule->call(function () {

            /*
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
            */

        })->everyMinute();

        //Get players online
        $schedule->call(function () {

            Log::channel('players_online')->info('Method: getPlayersOnline. Start...');


            foreach(getservers() as $server) {

                if(config('options.server_'.$server->id.'_plate', 0) > 0) continue;


                if (Cache::has('server'.$server->id.':online_data')) {
                    $result = Cache::get('server' . $server->id . ':online_data');

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

                    $players_online = [];
                    foreach ($players as $player) {

                        $user = User::where('steam_id', $player->id)->first();
                        if (!$user) continue;

                        $player_online = PlayersOnline::where('steam_id', $player->id)->where('server', $server->id)->latest('updated_at')->first();
                        if (!$player_online) {
                            $player_online = new PlayersOnline;
                            $player_online->steam_id = $player->id;
                            $player_online->user_id = $user->id;
                            $player_online->server = $server->id;
                            $player_online->online_prev = $player->online_time;
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

                        //Записываем отдельно время онлайна для сервера EU Main
                        if ($server->id == 1) {
                            if ($player_online->online_prev <= $player->online_time) {
                                $diff = $player->online_time - $player_online->online_prev;
                                $user->online_time_eumain += $diff;
                            } else {
                                $user->online_time_eumain += $player->online_time;
                            }
                        }



                        $d = (isset($diff)) ? $diff : $player->online_time;
                        Log::channel('players_online')->info('Method: getPlayersOnline. Server: '.$server->id.'. Player: '.$player->id.'('. $user->name .'). Prev online: ' . $player_online->online_prev . ', Server Online: '. $player_online->online_time . ', Online: '. $player->online_time .'. Diff online: '. $d .', All online: ' . $user->online_time . ', Monday online: ' . $user->online_time_monday . ', Thursday online: ' . $user->online_time_thursday);

                        $player_online->online_prev = $player->online_time;
                        $player_online->save();

                        $user->save();

                    }

                }

            }


        })->everyMinute();

        //Cache statistics
        $schedule->call(function () use ($getOnline) {

            Log::channel('schedule')->info('Start statistics cache...');
            for ($p = 0; $p <= 2; $p++) {
                foreach (getservers() as $server) {
                    $urls = [
                        '0'  => 'https://rustresort.com/stats',
                        '1'  => 'https://rustresort.com/stats?type=&search=&pvp_sort=kdr&server_id=' . $server->id,
                        '2'  => 'https://rustresort.com/stats?type=&search=&pvp_sort=kills&server_id=' . $server->id,
                        '3'  => 'https://rustresort.com/stats?type=&search=&pvp_sort=deaths&server_id=' . $server->id,
                        '4'  => 'https://rustresort.com/stats?type=&search=&pvp_sort=deaths_player&server_id=' . $server->id,
                        '5'  => 'https://rustresort.com/stats?type=&search=&res_sort=wood&server_id=' . $server->id,
                        '6'  => 'https://rustresort.com/stats?type=&search=&res_sort=stones&server_id=' . $server->id,
                        '7'  => 'https://rustresort.com/stats?type=&search=&res_sort=metal.ore&server_id=' . $server->id,
                        '8'  => 'https://rustresort.com/stats?type=&search=&res_sort=sulfur.ore&server_id=' . $server->id,
                        '9'  => 'https://rustresort.com/stats?type=&search=&res_sort=hq.metal.ore&server_id=' . $server->id,
                        '10' => 'https://rustresort.com/stats?type=&search=&res_sort=leather&server_id=' . $server->id,
                        '11' => 'https://rustresort.com/stats?type=&search=&res_sort=fat.animal&server_id=' . $server->id,
                        '12' => 'https://rustresort.com/stats?type=&search=&res_sort=bone.fragments&server_id=' . $server->id,
                        '13' => 'https://rustresort.com/stats?type=&search=&res_sort=cloth&server_id=' . $server->id,
                        '14' => 'https://rustresort.com/stats?type=&search=&res_sort=leather&server_id=' . $server->id,
                        '15' => 'https://rustresort.com/stats?type=&search=&res_sort=leather&server_id=' . $server->id,
                        '16' => 'https://rustresort.com/stats?type=&search=&res_sort=fat.animal&server_id=' . $server->id,
                        '17' => 'https://rustresort.com/stats?type=&search=&res_sort=bone.fragments&server_id=' . $server->id,
                        '18' => 'https://rustresort.com/stats?type=&search=&res_sort=cloth&server_id=' . $server->id,
                        '19' => 'https://rustresort.com/stats?type=&search=&raids_doors_sort=d_garage&server_id=' . $server->id,
                        '20' => 'https://rustresort.com/stats?type=&search=&raids_doors_sort=d_wooden&server_id=' . $server->id,
                        '21' => 'https://rustresort.com/stats?type=&search=&raids_doors_sort=d_metal&server_id=' . $server->id,
                        '22' => 'https://rustresort.com/stats?type=&search=&raids_doors_sort=d_d_metal&server_id=' . $server->id,
                        '23' => 'https://rustresort.com/stats?type=&search=&raids_doors_sort=d_d_wooden&server_id=' . $server->id,
                        '24' => 'https://rustresort.com/stats?type=&search=&raids_doors_sort=d_d_armored&server_id=' . $server->id,
                        '25' => 'https://rustresort.com/stats?type=&search=&raids_doors_sort=d_armored&server_id=' . $server->id,
                        '26' => 'https://rustresort.com/stats?type=&search=&raids_sort=bb_wooden&server_id=' . $server->id,
                        '27' => 'https://rustresort.com/stats?type=&search=&raids_sort=bb_stone&server_id=' . $server->id,
                        '28' => 'https://rustresort.com/stats?type=&search=&raids_sort=bb_metal&server_id=' . $server->id,
                        '29' => 'https://rustresort.com/stats?type=&search=&raids_sort=bb_mvk&server_id=' . $server->id,
                        '30' => 'https://rustresort.com/stats?type=&search=&raids_sort=bb_reinf_w_glass&server_id=' . $server->id,
                        '31' => 'https://rustresort.com/stats?type=&search=&raids_sort=bb_auto_turret&server_id=' . $server->id,
                        '32' => 'https://rustresort.com/stats?type=&search=&raids_sort=bb_reinf_w_grilles&server_id=' . $server->id,
                        '33' => 'https://rustresort.com/stats?type=&search=&hits_sort=hits_kdr&server_id=' . $server->id,
                        '34' => 'https://rustresort.com/stats?type=&search=&hits_sort=shoots&server_id=' . $server->id,
                        '35' => 'https://rustresort.com/stats?type=&search=&hits_sort=hits&server_id=' . $server->id,
                        '36' => 'https://rustresort.com/stats?type=&search=&hits_sort=head_shots&server_id=' . $server->id,
                        '37' => 'https://rustresort.com/stats?page=1&type=&search=&server_id=' . $server->id,
                        '38' => 'https://rustresort.com/stats?page=2&type=&search=&server_id=' . $server->id,
                    ];

                    foreach ($urls as $url) {
                        if ($p > 0) {
                            $url = str_replace('?type=', '?page=' . $p . '&type=', $url);
                        }
                        $f = @file_get_contents($url);
                    }
                }
            }

            Log::channel('schedule')->info('Finish statistics cache.');

        })->dailyAt('03:00');
        //})->everyTenMinutes();

        //Clear statistics
        $schedule->call(function () use ($getOnline) {

            Log::channel('schedule')->info('Start statistics clear 2 ...');

            foreach (getservers() as $server) {
                if($server->id != 2) continue;

                Statistic::where('server', $server->id)->chunk(100, function($statistics) use($server) {

                    $players_ids = [];
                    foreach ($statistics as $statistic) {

                        //Собираем player_id для очистки старых записей
                        if (!in_array($statistic->player_id, $players_ids)) {
                            $players_ids[] = $statistic->player_id;
                        }

                        $player_stat = (object)[];

                        $player_stat->general = $statistic->general;
                        $player_stat->server = $statistic->server;
                        $player_stat->date = $statistic->date;

                        $player_stat->player_id = $statistic->player_id;
                        $player_stat->name = $statistic->name;
                        $player_stat->user_id = $statistic->user_id;
                        $player_stat->is_npc = $statistic->is_npc;

                        $player_stat->deaths = $statistic->deaths;
                        $player_stat->kills = $statistic->kills;
                        $player_stat->deaths_player = $statistic->deaths_player;
                        $player_stat->head_shots = $statistic->head_shots;
                        $player_stat->hits = $statistic->hits;
                        $player_stat->shoots = $statistic->shoots;
                        $player_stat->resourse_list = json_decode($statistic->resourse_list);
                        $player_stat->raid_list = json_decode($statistic->raid_list);

                        //Объединяем рейды в группы
                        $player_stat->raid_list = $this->collectListraide($player_stat->raid_list);

                        $player_stat->kdr = (intval($statistic->deaths) > 0) ? number_format((intval($statistic->kills) / intval($statistic->deaths)), 2) : 0;
                        $player_stat->hits_kdr = (intval($statistic->shoots) > 0) ? number_format((intval($statistic->hits) / intval($statistic->shoots)), 2) : 0;


                        //dd($player_stat);
                        ClearStatistic::updateOrInsert(
                            [
                                'general' => $player_stat->general,
                                'server' => $player_stat->server,
                                'player_id' => $player_stat->player_id,
                                'date' => $player_stat->date,
                            ],
                            [
                                'name' => $player_stat->name,
                                'user_id' => $player_stat->user_id,
                                'is_npc' => $player_stat->is_npc,
                                'deaths' => $player_stat->deaths,
                                'kills' => $player_stat->kills,
                                'deaths_player' => $player_stat->deaths_player,
                                'head_shots' => $player_stat->head_shots,
                                'hits' => $player_stat->hits,
                                'shoots' => $player_stat->shoots,
                                'kdr' => $player_stat->kdr,
                                'hits_kdr' => $player_stat->hits_kdr,

                                'wood' => (isset($player_stat->resourse_list->wood)) ? $player_stat->resourse_list->wood : 0,
                                'stones' => (isset($player_stat->resourse_list->stones)) ? $player_stat->resourse_list->stones : 0,
                                'metal_ore' => (isset($player_stat->resourse_list->{'metal.ore'})) ? $player_stat->resourse_list->{'metal.ore'} : 0,
                                'sulfur_ore' => (isset($player_stat->resourse_list->{'sulfur.ore'})) ? $player_stat->resourse_list->{'sulfur.ore'} : 0,
                                'hq_metal_ore' => (isset($player_stat->resourse_list->{'hq.metal.ore'})) ? $player_stat->resourse_list->{'hq.metal.ore'} : 0,
                                'leather' => (isset($player_stat->resourse_list->leather)) ? $player_stat->resourse_list->leather : 0,
                                'fat_animal' => (isset($player_stat->resourse_list->{'fat.animal'})) ? $player_stat->resourse_list->{'fat.animal'} : 0,
                                'bone_fragments' => (isset($player_stat->resourse_list->{'bone.fragments'})) ? $player_stat->resourse_list->{'bone.fragments'} : 0,
                                'cloth' => (isset($player_stat->resourse_list->cloth)) ? $player_stat->resourse_list->cloth : 0,

                                'd_garage' => (isset($player_stat->raid_list->{'гаражная дверь'})) ? $player_stat->raid_list->{'гаражная дверь'} : 0,
                                'd_wooden' => (isset($player_stat->raid_list->{'деревянная дверь'})) ? $player_stat->raid_list->{'деревянная дверь'} : 0,
                                'd_metal' => (isset($player_stat->raid_list->{'металлическая дверь'})) ? $player_stat->raid_list->{'металлическая дверь'} : 0,
                                'd_d_metal' => (isset($player_stat->raid_list->{'двойная металлическая дверь'})) ? $player_stat->raid_list->{'двойная металлическая дверь'} : 0,
                                'd_d_wooden' => (isset($player_stat->raid_list->{'двойная деревянная дверь'})) ? $player_stat->raid_list->{'двойная деревянная дверь'} : 0,
                                'd_d_armored' => (isset($player_stat->raid_list->{'двойная бронированная дверь'})) ? $player_stat->raid_list->{'двойная бронированная дверь'} : 0,
                                'd_armored' => (isset($player_stat->raid_list->{'бронированная дверь'})) ? $player_stat->raid_list->{'бронированная дверь'} : 0,

                                'bb_wooden' => (isset($player_stat->raid_list->{'деревянные'})) ? $player_stat->raid_list->{'деревянные'} : 0,
                                'bb_stone' => (isset($player_stat->raid_list->{'каменные'})) ? $player_stat->raid_list->{'каменные'} : 0,
                                'bb_metal' => (isset($player_stat->raid_list->{'металлические'})) ? $player_stat->raid_list->{'металлические'} : 0,
                                'bb_mvk' => (isset($player_stat->raid_list->{'мвк'})) ? $player_stat->raid_list->{'мвк'} : 0,
                                'bb_reinf_w_glass' => (isset($player_stat->raid_list->{'окно из укреплённого стекла'})) ? $player_stat->raid_list->{'окно из укреплённого стекла'} : 0,
                                'bb_auto_turret' => (isset($player_stat->raid_list->{'автоматическая турель'})) ? $player_stat->raid_list->{'автоматическая турель'} : 0,
                                'bb_reinf_w_grilles' => (isset($player_stat->raid_list->{'укреплённые оконные решётки'})) ? $player_stat->raid_list->{'укреплённые оконные решётки'} : 0,
                            ]);

                    }

                    //Удаляем записи позже 30 дней
                    foreach ($players_ids as $player_id) {
                        $date_old = date('Y-m-d', strtotime(date('Y-m-d')) - 60 * 60 * 24 * 30);
                        $player_stats = ClearStatistic::where('player_id', $player_id)->where('server', $server->id)->where('general', 0)->where('date', '<', $date_old)->get();
                        foreach ($player_stats as $stat) {
                            $stat->delete();
                        }
                    }
                });
            }

            Log::channel('schedule')->info('Finish statistics clear.');

        })->dailyAt('02:30');

        $schedule->call(function () use ($getOnline) {

            Log::channel('schedule')->info('Start statistics clear 3 ...');

            foreach (getservers() as $server) {
                if($server->id != 3) continue;

                Statistic::where('server', $server->id)->chunk(100, function($statistics) use($server) {

                    $players_ids = [];
                    foreach ($statistics as $statistic) {

                        //Собираем player_id для очистки старых записей
                        if (!in_array($statistic->player_id, $players_ids)) {
                            $players_ids[] = $statistic->player_id;
                        }

                        $player_stat = (object)[];

                        $player_stat->general = $statistic->general;
                        $player_stat->server = $statistic->server;
                        $player_stat->date = $statistic->date;

                        $player_stat->player_id = $statistic->player_id;
                        $player_stat->name = $statistic->name;
                        $player_stat->user_id = $statistic->user_id;
                        $player_stat->is_npc = $statistic->is_npc;

                        $player_stat->deaths = $statistic->deaths;
                        $player_stat->kills = $statistic->kills;
                        $player_stat->deaths_player = $statistic->deaths_player;
                        $player_stat->head_shots = $statistic->head_shots;
                        $player_stat->hits = $statistic->hits;
                        $player_stat->shoots = $statistic->shoots;
                        $player_stat->resourse_list = json_decode($statistic->resourse_list);
                        $player_stat->raid_list = json_decode($statistic->raid_list);

                        //Объединяем рейды в группы
                        $player_stat->raid_list = $this->collectListraide($player_stat->raid_list);

                        $player_stat->kdr = (intval($statistic->deaths) > 0) ? number_format((intval($statistic->kills) / intval($statistic->deaths)), 2) : 0;
                        $player_stat->hits_kdr = (intval($statistic->shoots) > 0) ? number_format((intval($statistic->hits) / intval($statistic->shoots)), 2) : 0;


                        //dd($player_stat);
                        ClearStatistic::updateOrInsert(
                            [
                                'general' => $player_stat->general,
                                'server' => $player_stat->server,
                                'player_id' => $player_stat->player_id,
                                'date' => $player_stat->date,
                            ],
                            [
                                'name' => $player_stat->name,
                                'user_id' => $player_stat->user_id,
                                'is_npc' => $player_stat->is_npc,
                                'deaths' => $player_stat->deaths,
                                'kills' => $player_stat->kills,
                                'deaths_player' => $player_stat->deaths_player,
                                'head_shots' => $player_stat->head_shots,
                                'hits' => $player_stat->hits,
                                'shoots' => $player_stat->shoots,
                                'kdr' => $player_stat->kdr,
                                'hits_kdr' => $player_stat->hits_kdr,

                                'wood' => (isset($player_stat->resourse_list->wood)) ? $player_stat->resourse_list->wood : 0,
                                'stones' => (isset($player_stat->resourse_list->stones)) ? $player_stat->resourse_list->stones : 0,
                                'metal_ore' => (isset($player_stat->resourse_list->{'metal.ore'})) ? $player_stat->resourse_list->{'metal.ore'} : 0,
                                'sulfur_ore' => (isset($player_stat->resourse_list->{'sulfur.ore'})) ? $player_stat->resourse_list->{'sulfur.ore'} : 0,
                                'hq_metal_ore' => (isset($player_stat->resourse_list->{'hq.metal.ore'})) ? $player_stat->resourse_list->{'hq.metal.ore'} : 0,
                                'leather' => (isset($player_stat->resourse_list->leather)) ? $player_stat->resourse_list->leather : 0,
                                'fat_animal' => (isset($player_stat->resourse_list->{'fat.animal'})) ? $player_stat->resourse_list->{'fat.animal'} : 0,
                                'bone_fragments' => (isset($player_stat->resourse_list->{'bone.fragments'})) ? $player_stat->resourse_list->{'bone.fragments'} : 0,
                                'cloth' => (isset($player_stat->resourse_list->cloth)) ? $player_stat->resourse_list->cloth : 0,

                                'd_garage' => (isset($player_stat->raid_list->{'гаражная дверь'})) ? $player_stat->raid_list->{'гаражная дверь'} : 0,
                                'd_wooden' => (isset($player_stat->raid_list->{'деревянная дверь'})) ? $player_stat->raid_list->{'деревянная дверь'} : 0,
                                'd_metal' => (isset($player_stat->raid_list->{'металлическая дверь'})) ? $player_stat->raid_list->{'металлическая дверь'} : 0,
                                'd_d_metal' => (isset($player_stat->raid_list->{'двойная металлическая дверь'})) ? $player_stat->raid_list->{'двойная металлическая дверь'} : 0,
                                'd_d_wooden' => (isset($player_stat->raid_list->{'двойная деревянная дверь'})) ? $player_stat->raid_list->{'двойная деревянная дверь'} : 0,
                                'd_d_armored' => (isset($player_stat->raid_list->{'двойная бронированная дверь'})) ? $player_stat->raid_list->{'двойная бронированная дверь'} : 0,
                                'd_armored' => (isset($player_stat->raid_list->{'бронированная дверь'})) ? $player_stat->raid_list->{'бронированная дверь'} : 0,

                                'bb_wooden' => (isset($player_stat->raid_list->{'деревянные'})) ? $player_stat->raid_list->{'деревянные'} : 0,
                                'bb_stone' => (isset($player_stat->raid_list->{'каменные'})) ? $player_stat->raid_list->{'каменные'} : 0,
                                'bb_metal' => (isset($player_stat->raid_list->{'металлические'})) ? $player_stat->raid_list->{'металлические'} : 0,
                                'bb_mvk' => (isset($player_stat->raid_list->{'мвк'})) ? $player_stat->raid_list->{'мвк'} : 0,
                                'bb_reinf_w_glass' => (isset($player_stat->raid_list->{'окно из укреплённого стекла'})) ? $player_stat->raid_list->{'окно из укреплённого стекла'} : 0,
                                'bb_auto_turret' => (isset($player_stat->raid_list->{'автоматическая турель'})) ? $player_stat->raid_list->{'автоматическая турель'} : 0,
                                'bb_reinf_w_grilles' => (isset($player_stat->raid_list->{'укреплённые оконные решётки'})) ? $player_stat->raid_list->{'укреплённые оконные решётки'} : 0,
                            ]);

                    }

                    //Удаляем записи позже 30 дней
                    foreach ($players_ids as $player_id) {
                        $date_old = date('Y-m-d', strtotime(date('Y-m-d')) - 60 * 60 * 24 * 30);
                        $player_stats = ClearStatistic::where('player_id', $player_id)->where('server', $server->id)->where('general', 0)->where('date', '<', $date_old)->get();
                        foreach ($player_stats as $stat) {
                            $stat->delete();
                        }
                    }
                });
            }

            Log::channel('schedule')->info('Finish statistics clear.');

        })->dailyAt('03:30');


        $schedule->call(function () use ($getOnline) {

            Log::channel('schedule')->info('Start statistics clear 8 ...');

            foreach (getservers() as $server) {
                if($server->id != 8) continue;

                Statistic::where('server', $server->id)->chunk(100, function($statistics) use($server) {

                    $players_ids = [];
                    foreach ($statistics as $statistic) {

                        //Собираем player_id для очистки старых записей
                        if (!in_array($statistic->player_id, $players_ids)) {
                            $players_ids[] = $statistic->player_id;
                        }

                        $player_stat = (object)[];

                        $player_stat->general = $statistic->general;
                        $player_stat->server = $statistic->server;
                        $player_stat->date = $statistic->date;

                        $player_stat->player_id = $statistic->player_id;
                        $player_stat->name = $statistic->name;
                        $player_stat->user_id = $statistic->user_id;
                        $player_stat->is_npc = $statistic->is_npc;

                        $player_stat->deaths = $statistic->deaths;
                        $player_stat->kills = $statistic->kills;
                        $player_stat->deaths_player = $statistic->deaths_player;
                        $player_stat->head_shots = $statistic->head_shots;
                        $player_stat->hits = $statistic->hits;
                        $player_stat->shoots = $statistic->shoots;
                        $player_stat->resourse_list = json_decode($statistic->resourse_list);
                        $player_stat->raid_list = json_decode($statistic->raid_list);

                        //Объединяем рейды в группы
                        $player_stat->raid_list = $this->collectListraide($player_stat->raid_list);

                        $player_stat->kdr = (intval($statistic->deaths) > 0) ? number_format((intval($statistic->kills) / intval($statistic->deaths)), 2) : 0;
                        $player_stat->hits_kdr = (intval($statistic->shoots) > 0) ? number_format((intval($statistic->hits) / intval($statistic->shoots)), 2) : 0;


                        //dd($player_stat);
                        ClearStatistic::updateOrInsert(
                            [
                                'general' => $player_stat->general,
                                'server' => $player_stat->server,
                                'player_id' => $player_stat->player_id,
                                'date' => $player_stat->date,
                            ],
                            [
                                'name' => $player_stat->name,
                                'user_id' => $player_stat->user_id,
                                'is_npc' => $player_stat->is_npc,
                                'deaths' => $player_stat->deaths,
                                'kills' => $player_stat->kills,
                                'deaths_player' => $player_stat->deaths_player,
                                'head_shots' => $player_stat->head_shots,
                                'hits' => $player_stat->hits,
                                'shoots' => $player_stat->shoots,
                                'kdr' => $player_stat->kdr,
                                'hits_kdr' => $player_stat->hits_kdr,

                                'wood' => (isset($player_stat->resourse_list->wood)) ? $player_stat->resourse_list->wood : 0,
                                'stones' => (isset($player_stat->resourse_list->stones)) ? $player_stat->resourse_list->stones : 0,
                                'metal_ore' => (isset($player_stat->resourse_list->{'metal.ore'})) ? $player_stat->resourse_list->{'metal.ore'} : 0,
                                'sulfur_ore' => (isset($player_stat->resourse_list->{'sulfur.ore'})) ? $player_stat->resourse_list->{'sulfur.ore'} : 0,
                                'hq_metal_ore' => (isset($player_stat->resourse_list->{'hq.metal.ore'})) ? $player_stat->resourse_list->{'hq.metal.ore'} : 0,
                                'leather' => (isset($player_stat->resourse_list->leather)) ? $player_stat->resourse_list->leather : 0,
                                'fat_animal' => (isset($player_stat->resourse_list->{'fat.animal'})) ? $player_stat->resourse_list->{'fat.animal'} : 0,
                                'bone_fragments' => (isset($player_stat->resourse_list->{'bone.fragments'})) ? $player_stat->resourse_list->{'bone.fragments'} : 0,
                                'cloth' => (isset($player_stat->resourse_list->cloth)) ? $player_stat->resourse_list->cloth : 0,

                                'd_garage' => (isset($player_stat->raid_list->{'гаражная дверь'})) ? $player_stat->raid_list->{'гаражная дверь'} : 0,
                                'd_wooden' => (isset($player_stat->raid_list->{'деревянная дверь'})) ? $player_stat->raid_list->{'деревянная дверь'} : 0,
                                'd_metal' => (isset($player_stat->raid_list->{'металлическая дверь'})) ? $player_stat->raid_list->{'металлическая дверь'} : 0,
                                'd_d_metal' => (isset($player_stat->raid_list->{'двойная металлическая дверь'})) ? $player_stat->raid_list->{'двойная металлическая дверь'} : 0,
                                'd_d_wooden' => (isset($player_stat->raid_list->{'двойная деревянная дверь'})) ? $player_stat->raid_list->{'двойная деревянная дверь'} : 0,
                                'd_d_armored' => (isset($player_stat->raid_list->{'двойная бронированная дверь'})) ? $player_stat->raid_list->{'двойная бронированная дверь'} : 0,
                                'd_armored' => (isset($player_stat->raid_list->{'бронированная дверь'})) ? $player_stat->raid_list->{'бронированная дверь'} : 0,

                                'bb_wooden' => (isset($player_stat->raid_list->{'деревянные'})) ? $player_stat->raid_list->{'деревянные'} : 0,
                                'bb_stone' => (isset($player_stat->raid_list->{'каменные'})) ? $player_stat->raid_list->{'каменные'} : 0,
                                'bb_metal' => (isset($player_stat->raid_list->{'металлические'})) ? $player_stat->raid_list->{'металлические'} : 0,
                                'bb_mvk' => (isset($player_stat->raid_list->{'мвк'})) ? $player_stat->raid_list->{'мвк'} : 0,
                                'bb_reinf_w_glass' => (isset($player_stat->raid_list->{'окно из укреплённого стекла'})) ? $player_stat->raid_list->{'окно из укреплённого стекла'} : 0,
                                'bb_auto_turret' => (isset($player_stat->raid_list->{'автоматическая турель'})) ? $player_stat->raid_list->{'автоматическая турель'} : 0,
                                'bb_reinf_w_grilles' => (isset($player_stat->raid_list->{'укреплённые оконные решётки'})) ? $player_stat->raid_list->{'укреплённые оконные решётки'} : 0,
                            ]);

                    }

                    //Удаляем записи позже 30 дней
                    foreach ($players_ids as $player_id) {
                        $date_old = date('Y-m-d', strtotime(date('Y-m-d')) - 60 * 60 * 24 * 30);
                        $player_stats = ClearStatistic::where('player_id', $player_id)->where('server', $server->id)->where('general', 0)->where('date', '<', $date_old)->get();
                        foreach ($player_stats as $stat) {
                            $stat->delete();
                        }
                    }
                });
            }

            Log::channel('schedule')->info('Finish statistics clear.');

        })->dailyAt('03:30');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    private function collectList($list_req, $list)
    {
        foreach ($list_req as $name_req => $value_req) {
            $list_find = FALSE;
            foreach ($list as $name_db => $value_db) {
                if ($name_req == $name_db) {
                    $list->$name_db += intval($value_req);
                    $list_find = TRUE;
                }
            }
            if ($list_find === FALSE) {
                $list->$name_req = intval($value_req);
            }
        }

        return $list;
    }

    private function collectListraide($list_req)
    {
        $list_req = (array)$list_req;
        $list = [];
        $list['деревянные'] = 0;
        $list['каменные'] = 0;
        $list['металлические'] = 0;
        $list['мвк'] = 0;
        foreach ($list_req as $name_req => $value_req) {
            $list_find = FALSE;
            $name_req = mb_strtolower($name_req);
            if (str_contains($name_req, 'метал') && !str_contains($name_req, 'двер')) {
                $list['металлические'] += $value_req;
            } else if (str_contains($name_req, 'камен')) {
                $list['каменные'] += $value_req;
            } else if (str_contains($name_req, 'деревян') && !str_contains($name_req, 'двер')) {
                $list['деревянные'] += $value_req;
            } else if (str_contains($name_req, 'мвк') && !str_contains($name_req, 'двер')) {
                $list['мвк'] += $value_req;
            } else if (str_contains($name_req, 'Автоматическая башня')) {
                $list['Автоматическая башня'] = $value_req;
            } else if (str_contains($name_req, 'укреплённые оконные решётки')) {
                $list['укреплённые оконные решётки'] = $value_req;
            } else if (str_contains($name_req, 'автоматическая турель')) {
                $list['автоматическая турель'] = $value_req;
            } else if (str_contains($name_req, 'окно из укреплённого стекла')) {
                $list['окно из укреплённого стекла'] = $value_req;
            } else if (str_contains($name_req, 'двер')) {
                $list[$name_req] = $value_req;
            }
        }

        return (object)$list;
    }
}
