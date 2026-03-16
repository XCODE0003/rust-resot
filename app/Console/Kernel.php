<?php

namespace App\Console;

use App\Models\User;
use App\Models\RconTask;
use App\Models\PlayersOnline;
use App\Models\Player;
use App\Models\Statistic;
use App\Models\ClearStatistic;
use App\Models\Shopping;
use App\Models\ServerOnline;
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
use App\Services\RconConnectionManager;

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

            $manager = RconConnectionManager::getInstance();

            foreach (getservers() as $server) {
                Log::channel('rcon_master')->info('Processing server: ' . $server->id);
                
                $manager->connect($server->id);
                
                try {
                    $result = $manager->sendCommand($server->id, 'status');
                    
                    if ($result && isset($result->Message) && substr($result->Message, 0, 8) === 'hostname') {
                        Log::channel('rcon_master')->info("Server {$server->id} online data received");
                        
                        $count = 0;
                        $count_max = 0;
                        $queued = 0;
                        
                        $messageParts = explode("players : ", $result->Message);
                        if (isset($messageParts[1])) {
                            $queuedParts = explode(" queued", $messageParts[1]);
                            if (isset($queuedParts[0])) {
                                $maxParts = explode("max) (", $queuedParts[0]);
                                if (isset($maxParts[1])) {
                                    $queued = intval($maxParts[1]);
                                }
                            }
                            
                            $playersParts = explode(" queued", $messageParts[1]);
                            if (isset($playersParts[0])) {
                                $maxParts = explode(" max)", $playersParts[0]);
                                if (isset($maxParts[0])) {
                                    $countParts = explode(" (", $maxParts[0]);
                                    if (isset($countParts[0])) {
                                        $count = intval($countParts[0]);
                                    }
                                }
                                $countMaxParts = explode(" (", $playersParts[0]);
                                if (isset($countMaxParts[1])) {
                                    $maxNumParts = explode(" max)", $countMaxParts[1]);
                                    $count_max = intval($maxNumParts[0]);
                                }
                            }
                        }
                        
                        ServerOnline::updateOrCreate(
                            ['server_id' => $server->id],
                            [
                                'online_count' => $count,
                                'online_max' => $count_max,
                                'online_queued' => $queued,
                                'players_data' => $result->Message,
                                'updated_at' => now(),
                            ]
                        );
                        
                        Log::channel('rcon_master')->info("Server {$server->id} online: {$count}/{$count_max}, queued: {$queued}");
                    }
                    
                } catch (\Exception $ex) {
                    Log::channel('rcon_master')->error("Error getting online for server {$server->id}: {$ex->getMessage()}");
                }
                
                Log::channel('rcon_master')->info('Checking shopping tasks for server: ' . $server->id);
                $shoppings = Shopping::where('status', 0)->where('server', $server->id)->get();
                
                if ($shoppings) {
                    foreach ($shoppings as $shopping) {
                        
                        $lock_shop = Cache::lock('server' . $server->id . ':shopping_lock' . $shopping->id, 30);
                        if ($lock_shop->get()) {
                            Log::channel('rcon_master')->info("Sending shop command: {$shopping->command} for server {$shopping->server}");
                            
                            try {
                                $rcon_result = $manager->sendCommand($server->id, $shopping->command);
                                
                                Log::channel('rcon_master')->info('Shop command result: ' . json_encode($rcon_result));
                                
                                if ($rcon_result && isset($rcon_result->Message)) {
                                    $successPhrases = ['Added to group', 'time extended', 'ermission granted', 'успешно', 'granted permission'];
                                    $isSuccess = false;
                                    
                                    foreach ($successPhrases as $phrase) {
                                        if (strpos($rcon_result->Message, $phrase) !== FALSE) {
                                            $isSuccess = true;
                                            break;
                                        }
                                    }
                                    
                                    if ($isSuccess) {
                                        Log::channel('rcon_master')->info("Shop command success: {$shopping->command}");
                                        $shopping->status = 1;
                                        $shopping->save();
                                    }
                                }
                                
                                $lock_shop->release();
                            } catch (\Exception $ex) {
                                Log::channel('rcon_master')->error("Error sending shop command for server {$server->id}: {$ex->getMessage()}");
                                $lock_shop->release();
                            }
                        }
                    }
                }
            }
            
        })->everyMinute();

        //Get players online
        $schedule->call(function () {

            Log::channel('players_online')->info('Method: getPlayersOnline. Start...');

            foreach(getservers() as $server) {

                if(config('options.server_'.$server->id.'_plate', 0) > 0) continue;

                $serverOnline = ServerOnline::where('server_id', $server->id)->first();
                
                if ($serverOnline && $serverOnline->players_data) {
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

                        if ($player->online_time <= 0 || in_array($player->id, $players_online)) continue;
                        $players_online[] = $player->id;

                        if ($player_online->online_prev <= $player->online_time) {
                            $diff = $player->online_time - $player_online->online_prev;
                            $player_online->online_time += $diff;
                        } else {
                            $player_online->online_time += $player->online_time;
                        }

                        if ($player_online->online_prev <= $player->online_time) {
                            $diff = $player->online_time - $player_online->online_prev;
                            $user->online_time += $diff;
                        } else {
                            $user->online_time += $player->online_time;
                        }

                        if ($server->id == 3) {
                            if ($player_online->online_prev <= $player->online_time) {
                                $diff = $player->online_time - $player_online->online_prev;
                                $user->online_time_monday += $diff;
                            } else {
                                $user->online_time_monday += $player->online_time;
                            }
                        }

                        if (config('options.bonusth_status', '0') == '1' && ($server->id == 1 || $server->id == 2)) {
                            if ($player_online->online_prev <= $player->online_time) {
                                $diff = $player->online_time - $player_online->online_prev;
                                $user->online_time_thursday += $diff;
                            } else {
                                $user->online_time_thursday += $player->online_time;
                            }
                        }

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
        $schedule->call(function ()  {

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
        $schedule->call(function ()  {

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

        $schedule->call(function ()  {

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


        $schedule->call(function ()  {

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
