<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Player;
use App\Models\Statistic;
use App\Models\ClearStatistic;
use Illuminate\Support\Facades\Log;

class ClearStatisticsController extends Controller
{

    public function clearStatistics(Request $request)
    {
        if (!isset($request->api_key) || $request->api_key != config('options.game_api_key', '')) {
            return response()->json([
                'status' => 'error',
                'msg'    => 'API key is invalid.',
            ],500);
        }

        foreach (getservers() as $server) {
            if ($server->id != 8) continue;

            Statistic::where('server', $server->id)->chunk(50, function($statistics) use($server) {

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

                //dd('stop');

            });

        }

        return response()->json([
            'status' => 'success',
            'msg'    => 'ok',
        ]);
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
