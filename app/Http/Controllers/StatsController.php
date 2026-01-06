<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Player;
use App\Models\Statistic;
use App\Models\ClearStatistic;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use GameServer;

class StatsController extends Controller
{
    public function __construct()
    {
        $this->middleware('server.status');
    }

    public function stats_old()
    {

        $statistics = [];
        foreach (getservers() as $server) {

            $cache_requests = request()->all();
            $cache_requests[] = ['server_id' => $server->id];
            $cache_request = 'old_'. json_encode($cache_requests);

            //Cache::forget($cache_request);
            if (Cache::has($cache_request)) {
                $server_statistics[$server->id] = Cache::get($cache_request);
            }
            else {

                $type = request()->query('type') ?? '';

                $statistics_arr = [];

                //Cache::forget('statistics_old:server' . $server->id . 'type' . $type);
                if (Cache::has('statistics_old:server' . $server->id . 'type' . $type) && (!request()->has('search') || request()->query('search') == '')) {

                    $statistics_arr = Cache::get('statistics_old:server' . $server->id . 'type' . $type);

                    $resourses = $statistics_arr['resourses'];
                    $raids = $statistics_arr['raids'];
                    $raids_doors = $statistics_arr['raids_doors'];
                    $statistics_src_new = $statistics_arr['statistics_src_new'];

                }
                else {

                    $statistics = Statistic::query()->where('server', $server->id);

                    if ($type !== '') {
                        switch ($type) {
                            case 'day':
                                $date_sql = date('Y-m-d');
                                break;
                            case 'week':
                                $day_week = date("N") - 1;
                                $week_start = date('d.m.Y', strtotime("-" . $day_week . " day", strtotime(date('Y-m-d'))));
                                $date_sql = date('Y-m-d', strtotime($week_start));
                                break;
                            case 'month':
                                $date_sql = date('Y-m') . '-01';
                                break;
                        }
                    }

                    if (isset($date_sql)) {
                        $statistics->where('general', 0)->where('date', '>=', $date_sql);
                    } else {
                        $statistics->where('general', 1);
                    }

                    $search = request()->query('search');
                    if (request()->has('search') && is_string($search)) {
                        $statistics->where('name', 'LIKE', "%{$search}%");
                    }

                    $statistics_src = $statistics->get();

                    $statistics_src_new = [];
                    foreach ($statistics_src as $statistic) {
                        if (!isset($statistics_src_new[$statistic->player_id])) {
                            $player_stat = (object)[];

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

                            $statistics_src_new[$statistic->player_id] = $player_stat;

                        } else {
                            $player_stat = $statistics_src_new[$statistic->player_id];


                            //Рейды
                            $raid_list_req = json_decode($statistic->raid_list);
                            //Объединяем рейды в группы
                            $raid_list_req = $this->collectListraide($raid_list_req);
                            $raid_list = $this->collectList($raid_list_req, $player_stat->raid_list);

                            //Ресурсы
                            $resourse_list_req = json_decode($statistic->resourse_list);
                            $resourse_list = $this->collectList($resourse_list_req, $player_stat->resourse_list);

                            $player_stat->deaths += $statistic->deaths;
                            $player_stat->kills += $statistic->kills;
                            $player_stat->deaths_player += $statistic->deaths_player;
                            $player_stat->head_shots += $statistic->head_shots;
                            $player_stat->hits += $statistic->hits;
                            $player_stat->shoots += $statistic->shoots;
                            $player_stat->resourse_list = $resourse_list;
                            $player_stat->raid_list = $raid_list;

                            $statistics_src_new[$statistic->player_id] = $player_stat;
                        }
                    }

                    $resourses = [];
                    $raids = [];
                    $raids_doors = [];
                    foreach ($statistics_src_new as $statistic) {
                        foreach ($statistic->resourse_list as $resourse_key => $resourse_value) {
                            if (!in_array($resourse_key, $resourses)) {
                                $resourses[] = $resourse_key;
                            }
                        }
                        foreach ($statistic->raid_list as $raid_key => $raid_value) {
                            if (!in_array($raid_key, $raids)) {
                                if (!str_contains($raid_key, 'дверь')) {
                                    $raids[] = $raid_key;
                                }
                            }
                            if (!in_array($raid_key, $raids_doors)) {
                                if (str_contains($raid_key, 'двойная деревянная дверь') || str_contains($raid_key, 'гаражная дверь') || str_contains($raid_key, 'бронированная дверь') || str_contains($raid_key, 'деревянная дверь')
                                    || str_contains($raid_key, 'двойная бронированная дверь') || str_contains($raid_key, 'металлическая дверь') || str_contains($raid_key, 'двойная металлическая дверь')) {
                                    $raids_doors[] = $raid_key;
                                }
                            }
                        }
                    }

                    $resourses_sort = [];
                    foreach (getResources() as $resource_key => $resource) {
                        foreach ($resourses as $resourse) {
                            if ($resourse == $resource_key) {
                                $resourses_sort[] = $resourse;
                            }
                        }
                    }
                    $resourses = $resourses_sort;


                    $statistics_arr = [
                        'resourses'          => $resourses,
                        'raids'              => $raids,
                        'raids_doors'        => $raids_doors,
                        'statistics_src_new' => $statistics_src_new,
                    ];

                    if (!request()->has('search') || request()->query('search') == '') {
                        Cache::put('statistics_old:server' . $server->id . 'type' . $type, $statistics_arr, 86400);
                    }
                }


                //Total Statistic
                $total_statistics_arr = [];

                //Cache::forget('total_statistics:server' . $server->id);
                if (Cache::has('total_statistics_old:server' . $server->id)) {

                    $total_statistics_arr = Cache::get('total_statistics_old:server' . $server->id);

                    $total_pvp_stat = $total_statistics_arr['total_pvp_stat'];
                    $total_pvp_kdr = $total_statistics_arr['total_pvp_kdr'];
                    $total_pvp_kills = $total_statistics_arr['total_pvp_kills'];
                    $total_pvp_deaths = $total_statistics_arr['total_pvp_deaths'];
                    $total_pvp_deaths_player = $total_statistics_arr['total_pvp_deaths_player'];

                }
                else {
                    $total_pvp_stat = 0;
                    $total_pvp_kdr = 0;
                    $total_pvp_kills = 0;
                    $total_pvp_deaths = 0;
                    $total_pvp_deaths_player = 0;
                    $total_statistics = Statistic::query()->where('server', $server->id)->where('general', 1)->get();
                    $total_pvp_stat = count($total_statistics);
                    foreach ($total_statistics as $statistic) {
                        $total_pvp_kills += $statistic->kills;
                        $total_pvp_deaths += $statistic->deaths;
                        $total_pvp_deaths_player += $statistic->deaths_player;
                    }

                    if (intval($total_pvp_kills) > 0) {
                        $total_pvp_kdr = number_format((intval($total_pvp_kills) / intval($total_pvp_deaths)), 2);
                    } else {
                        $total_pvp_kdr = number_format(0, 2);
                    }

                    $total_statistics_arr = [
                        'total_pvp_stat'          => $total_pvp_stat,
                        'total_pvp_kdr'           => $total_pvp_kdr,
                        'total_pvp_kills'         => $total_pvp_kills,
                        'total_pvp_deaths'        => $total_pvp_deaths,
                        'total_pvp_deaths_player' => $total_pvp_deaths_player,
                    ];

                    Cache::put('total_statistics_old:server' . $server->id, $total_statistics_arr, 86400);
                }


                $server_statistics[$server->id]['total_pvp_stat'] = $total_pvp_stat;
                $server_statistics[$server->id]['total_pvp_kills'] = $total_pvp_kills;
                $server_statistics[$server->id]['total_pvp_deaths'] = $total_pvp_deaths;
                $server_statistics[$server->id]['total_pvp_deaths_player'] = $total_pvp_deaths_player;
                $server_statistics[$server->id]['total_pvp_kdr'] = $total_pvp_kdr;
                $server_statistics[$server->id]['resourses'] = $resourses;
                $server_statistics[$server->id]['raids'] = $raids;
                $server_statistics[$server->id]['raids_doors'] = $raids_doors;
                $server_statistics[$server->id]['statistics'] = $statistics_src_new;


                //Сортировки
                $is_sort = FALSE;


                //Сортировка по ресурсам
                $res_sort = request()->query('res_sort');
                if (request()->has('res_sort') && is_string($res_sort)) {

                    $arr = [];
                    foreach ($server_statistics[$server->id]['statistics'] as $statistic) {
                        $arr[] = [
                            'player_id' => $statistic->player_id,
                            'res_sort'  => (isset($statistic->resourse_list->$res_sort)) ? $statistic->resourse_list->$res_sort : 0,
                        ];
                    }

                    usort($arr, "res_sort");
                    $arr_new = [];
                    foreach ($arr as $ar) {
                        foreach ($server_statistics[$server->id]['statistics'] as $statistic) {
                            if ($ar['player_id'] == $statistic->player_id) {
                                $arr_new[] = $statistic;
                            }
                        }
                    }
                    $server_statistics[$server->id]['statistics'] = $arr_new;
                    $is_sort = TRUE;

                }

                //Сортировка PVP
                if ($is_sort === FALSE) {
                    $pvp_sort = request()->query('pvp_sort');
                    if (request()->has('pvp_sort') && is_string($pvp_sort)) {

                        $arr = [];
                        if ($pvp_sort == 'kdr') {
                            foreach ($server_statistics[$server->id]['statistics'] as $statistic) {
                                $arr[] = [
                                    'player_id' => $statistic->player_id,
                                    'pvp_sort'  => (intval($statistic->deaths) > 0) ? number_format((intval($statistic->kills) / intval($statistic->deaths)), 2) : 0,
                                ];
                            }
                        } else {
                            foreach ($server_statistics[$server->id]['statistics'] as $statistic) {
                                $arr[] = [
                                    'player_id' => $statistic->player_id,
                                    'pvp_sort'  => (isset($statistic->$pvp_sort)) ? $statistic->$pvp_sort : 0,
                                ];
                            }
                        }

                        usort($arr, "pvp_sort");
                        $arr_new = [];
                        foreach ($arr as $ar) {
                            foreach ($server_statistics[$server->id]['statistics'] as $statistic) {
                                if ($ar['player_id'] == $statistic->player_id) {
                                    $arr_new[] = $statistic;
                                }
                            }
                        }
                        $server_statistics[$server->id]['statistics'] = $arr_new;
                        $is_sort = TRUE;

                    }
                }

                //Сортировка raids
                if ($is_sort === FALSE) {
                    $raids_sort = request()->query('raids_sort');
                    if (request()->has('raids_sort') && is_string($raids_sort)) {

                        $arr = [];
                        foreach ($server_statistics[$server->id]['statistics'] as $statistic) {
                            $raids_sort_value = 0;
                            if (!empty($statistic->raid_list)) {
                                foreach ($statistic->raid_list as $key => $value) {
                                    if ($key == $raids_sort) {
                                        $raids_sort_value = $value;
                                    }
                                }
                            }
                            $arr[] = [
                                'player_id'  => $statistic->player_id,
                                'raids_sort' => $raids_sort_value,
                            ];
                        }

                        usort($arr, "raids_sort");

                        $arr_new = [];
                        foreach ($arr as $ar) {
                            foreach ($server_statistics[$server->id]['statistics'] as $statistic) {
                                if ($ar['player_id'] == $statistic->player_id) {
                                    $arr_new[] = $statistic;
                                }
                            }
                        }

                        $server_statistics[$server->id]['statistics'] = $arr_new;
                        $is_sort = TRUE;

                    }
                }

                //Сортировка raids doors
                if ($is_sort === FALSE) {
                    $raids_doors_sort = request()->query('raids_doors_sort');
                    if (request()->has('raids_doors_sort') && is_string($raids_doors_sort)) {

                        $arr = [];
                        foreach ($server_statistics[$server->id]['statistics'] as $statistic) {
                            $raids_doors_sort_value = 0;
                            if (!empty($statistic->raid_list)) {
                                foreach ($statistic->raid_list as $key => $value) {
                                    if ($key == $raids_doors_sort) {
                                        $raids_doors_sort_value = $value;
                                    }
                                }
                            }
                            $arr[] = [
                                'player_id'  => $statistic->player_id,
                                'raids_sort' => $raids_doors_sort_value,
                            ];
                        }

                        usort($arr, "raids_sort");

                        $arr_new = [];
                        foreach ($arr as $ar) {
                            foreach ($server_statistics[$server->id]['statistics'] as $statistic) {
                                if ($ar['player_id'] == $statistic->player_id) {
                                    $arr_new[] = $statistic;
                                }
                            }
                        }

                        $server_statistics[$server->id]['statistics'] = $arr_new;
                        $is_sort = TRUE;

                    }
                }

                //Сортировка hits
                if ($is_sort === FALSE) {
                    $hits_sort = request()->query('hits_sort');
                    if (request()->has('hits_sort') && is_string($hits_sort)) {

                        $arr = [];
                        if ($hits_sort == 'hits_kdr') {
                            foreach ($server_statistics[$server->id]['statistics'] as $statistic) {
                                $arr[] = [
                                    'player_id' => $statistic->player_id,
                                    'hits_sort' => (intval($statistic->shoots) > 0) ? number_format((intval($statistic->hits) / intval($statistic->shoots)), 2) * 1000 : 0,
                                ];
                            }
                        } else {
                            foreach ($server_statistics[$server->id]['statistics'] as $statistic) {
                                $arr[] = [
                                    'player_id' => $statistic->player_id,
                                    'hits_sort' => (isset($statistic->$hits_sort)) ? $statistic->$hits_sort : 0,
                                ];
                            }
                        }

                        usort($arr, "hits_sort");
                        $arr_new = [];
                        foreach ($arr as $ar) {
                            foreach ($server_statistics[$server->id]['statistics'] as $statistic) {
                                if ($ar['player_id'] == $statistic->player_id) {
                                    $arr_new[] = $statistic;
                                }
                            }
                        }
                        $server_statistics[$server->id]['statistics'] = $arr_new;
                        $is_sort = TRUE;
                    }
                }

                Cache::put($cache_request, $server_statistics[$server->id], 86400);
            }

            $page = (request()->has('page')) ? request()->query('page') : NULL;
            $server_statistics[$server->id]['statistics'] = paginate($server_statistics[$server->id]['statistics'], $perPage = 20, $page, $options = []);

        }

        return view('pages.main.stats_old', compact( 'server_statistics'));
    }

    public function index()
    {
        if(!isset(auth()->user()->role) || auth()->user()->role != 'admin') {
            abort(404);
        }
        $type = request()->query('type') ?? '';

        $server_statistics = [];
        foreach (getservers() as $server) {

            $cache_requests = request()->all();
            $cache_requests[] = ['server_id' => $server->id];
            $cache_request = json_encode($cache_requests);

            //Cache::forget($cache_request);
            if (Cache::has($cache_request)) {
                $statistics = Cache::get($cache_request);
            }
            else {

                $statistics = ClearStatistic::query()->where('server', $server->id);

                if ($type !== '') {
                    switch ($type) {
                        case 'day':
                            $date_sql = date('Y-m-d');
                            break;
                        case 'week':
                            $day_week = date("N") - 1;
                            $week_start = date('d.m.Y', strtotime("-" . $day_week . " day", strtotime(date('Y-m-d'))));
                            $date_sql = date('Y-m-d', strtotime($week_start));
                            break;
                        case 'month':
                            $date_sql = date('Y-m') . '-01';
                            break;
                    }
                }

                if (isset($date_sql)) {
                    $statistics->where('general', 0)->where('date', '>=', $date_sql);
                } else {
                    $statistics->where('general', 1);
                }

                $search = request()->query('search');
                if (request()->has('search') && is_string($search)) {
                    $statistics->where('name', 'LIKE', "%{$search}%");
                }

                //Сортировки
                $is_sort = FALSE;

                //Сортировка по ресурсам
                $res_sort = request()->query('res_sort');
                $res_sort = str_replace('.', '_', $res_sort);
                if (request()->has('res_sort') && is_string($res_sort) && in_array($res_sort,['wood','stones','metal_ore','sulfur_ore','hq_metal_ore','leather','fat_animal','bone_fragments','cloth'])) {
                    $statistics->orderByDesc($res_sort);
                    $is_sort = TRUE;
                }

                //Сортировка PVP
                if ($is_sort === FALSE) {
                    $pvp_sort = request()->query('pvp_sort');
                    if (request()->has('pvp_sort') && is_string($pvp_sort) && in_array($pvp_sort,['kdr','kills','deaths','deaths_player'])) {
                        $statistics->orderByDesc($pvp_sort);
                        $is_sort = TRUE;
                    }
                }

                //Сортировка raids
                if ($is_sort === FALSE) {
                    $raids_sort = request()->query('raids_sort');
                    if (request()->has('raids_sort') && is_string($raids_sort) && in_array($raids_sort,['bb_wooden','bb_stone','bb_metal','bb_mvk','bb_reinf_w_glass','bb_auto_turret','bb_reinf_w_grilles'])) {
                        $statistics->orderByDesc($raids_sort);
                        $is_sort = TRUE;
                    }
                }

                //Сортировка raids doors
                if ($is_sort === FALSE) {
                    $raids_doors_sort = request()->query('raids_doors_sort');
                    if (request()->has('raids_doors_sort') && is_string($raids_doors_sort) && in_array($raids_doors_sort,['d_garage','d_wooden','d_metal','d_d_metal','d_d_wooden','d_d_armored','d_armored'])) {
                        $statistics->orderByDesc($raids_doors_sort);
                        $is_sort = TRUE;
                    }
                }

                //Сортировка hits
                if ($is_sort === FALSE) {
                    $hits_sort = request()->query('hits_sort');
                    if (request()->has('hits_sort') && is_string($hits_sort)) {
                        $statistics->orderByDesc($hits_sort);
                        $is_sort = TRUE;
                    }
                }

                $statistics = $statistics->paginate();
                Cache::put($cache_request, $statistics, 3600);

            }


            //Total Statistic
            //Cache::forget('total_statistics:server' . $server->id);
            if (Cache::has('total_statistics:server' . $server->id)) {

                $total_statistics_arr = Cache::get('total_statistics:server' . $server->id);

                $total_pvp_stat = $total_statistics_arr['total_pvp_stat'];
                $total_pvp_kdr = $total_statistics_arr['total_pvp_kdr'];
                $total_pvp_kills = $total_statistics_arr['total_pvp_kills'];
                $total_pvp_deaths = $total_statistics_arr['total_pvp_deaths'];
                $total_pvp_deaths_player = $total_statistics_arr['total_pvp_deaths_player'];

            } else {

                $total_pvp_kills = ClearStatistic::query()->where('general', 1)->where('server', $server->id)->sum('kills');
                $total_pvp_deaths = ClearStatistic::query()->where('general', 1)->where('server', $server->id)->sum('deaths');
                $total_pvp_deaths_player = ClearStatistic::query()->where('general', 1)->where('server', $server->id)->sum('deaths_player');
                $total_pvp_stat = ClearStatistic::query()->where('general', 1)->where('server', $server->id)->count();

                if (intval($total_pvp_kills) > 0) {
                    $total_pvp_kdr = number_format((intval($total_pvp_kills) / intval($total_pvp_deaths)), 2);
                } else {
                    $total_pvp_kdr = number_format(0, 2);
                }

                $total_statistics_arr = [
                    'total_pvp_stat'          => $total_pvp_stat,
                    'total_pvp_kdr'           => $total_pvp_kdr,
                    'total_pvp_kills'         => $total_pvp_kills,
                    'total_pvp_deaths'        => $total_pvp_deaths,
                    'total_pvp_deaths_player' => $total_pvp_deaths_player,
                ];

                Cache::put('total_statistics:server' . $server->id, $total_statistics_arr, 3600);
            }

            $server_statistics[$server->id]['total_pvp_stat'] = $total_pvp_stat;
            $server_statistics[$server->id]['total_pvp_kills'] = $total_pvp_kills;
            $server_statistics[$server->id]['total_pvp_deaths'] = $total_pvp_deaths;
            $server_statistics[$server->id]['total_pvp_deaths_player'] = $total_pvp_deaths_player;
            $server_statistics[$server->id]['total_pvp_kdr'] = $total_pvp_kdr;

            $server_statistics[$server->id]['statistics'] = $statistics;
        }

        return view('pages.main.stats', compact( 'server_statistics'));
    }

    public function account_stats($player)
    {

        $server_default = getservers()[0];

        $server_id = request()->has('server_id') ? request()->query('server_id') : $server_default->id;
        $search = request()->query('search');
        if (request()->has('search') && is_string($search) && $search != '') {
            return redirect()->route('account.stats', $search);
        }

        $statistics = [];
        $users_stat = [];
        foreach (getservers() as $server) {

            $user = Statistic::where('player_id', $player)->first();
            $statistics = Statistic::query()->where('server', $server->id)->where('player_id', $player);

            //Получаю список игроков из статистики
            $users_stat[$server->id] = Statistic::query()->where('server', $server->id)->where('general', 1)->limit(10)->get();

            if (request()->has('type') && is_string(request()->query('type'))) {
                $type = request()->query('type');

                switch ($type) {
                    case 'day':
                        $date_sql = date('Y-m-d');
                        break;
                    case 'week':
                        $day_week = date("N") - 1;
                        $week_start = date('d.m.Y', strtotime("-" . $day_week . " day", strtotime(date('Y-m-d'))));
                        $date_sql = date('Y-m-d', strtotime($week_start));
                        break;
                    case 'month':
                        $date_sql = date('Y-m') . '-01';
                        break;
                }
            }

            if (isset($date_sql)) {
                $statistics->where('general', 0)->where('date', '>=', $date_sql);
            } else {
                $statistics->where('general', 1);
            }

            $statistics_src = $statistics->get();

            $statistics_src_new = [];
            foreach ($statistics_src as $statistic) {
                if (!isset($statistics_src_new[$statistic->player_id])) {
                    $player_stat = (object)[];

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

                    $statistics_src_new[$statistic->player_id] = $player_stat;

                } else {
                    $player_stat = $statistics_src_new[$statistic->player_id];

                    //Рейды
                    $raid_list_req = json_decode($statistic->raid_list);
                    $raid_list = $this->collectList($raid_list_req, $player_stat->raid_list);

                    //Ресурсы
                    $resourse_list_req = json_decode($statistic->resourse_list);
                    $resourse_list = $this->collectList($resourse_list_req, $player_stat->resourse_list);


                    $player_stat->deaths += $statistic->deaths;
                    $player_stat->kills += $statistic->kills;
                    $player_stat->deaths_player += $statistic->deaths_player;
                    $player_stat->head_shots += $statistic->head_shots;
                    $player_stat->hits += $statistic->hits;
                    $player_stat->shoots += $statistic->shoots;
                    $player_stat->resourse_list = $resourse_list;
                    $player_stat->raid_list = $raid_list;

                    $statistics_src_new[$statistic->player_id] = $player_stat;
                }
            }

            $resourses = [];
            $raids = [];
            foreach ($statistics_src_new as $statistic) {
                foreach ($statistic->resourse_list as $resourse_key => $resourse_value) {
                    if (!in_array($resourse_key, $resourses)) {
                        $resourses[] = $resourse_key;
                    }
                }
                foreach ($statistic->raid_list as $raid_key => $raid_value) {
                    if (!in_array($raid_key, $raids)) {
                        $raids[] = $raid_key;
                    }
                }
            }

            //Total Statistic
            $total_pvp_stat = 0;
            $total_pvp_kdr = 0;
            $total_pvp_kills = 0;
            $total_pvp_deaths = 0;
            $total_pvp_deaths_player = 0;
            $total_statistics = Statistic::query()->where('server', $server->id)->where('general', 1)->get();
            $total_pvp_stat = count($total_statistics);
            foreach ($total_statistics as $statistic) {
                $total_pvp_kills += $statistic->kills;
                $total_pvp_deaths += $statistic->deaths;
                $total_pvp_deaths_player += $statistic->deaths_player;
            }

            if (intval($total_pvp_kills) > 0) {
                $total_pvp_kdr = number_format((intval($total_pvp_kills) / intval($total_pvp_deaths)), 2);
            } else {
                $total_pvp_kdr = number_format(0, 2);
            }

            $resourses_sort = [];
            foreach (getResources() as $resource_key => $resource) {
                foreach ($resourses as $resourse) {
                    if($resourse == $resource_key) {
                        $resourses_sort[] = $resourse;
                    }
                }
            }
            $resourses = $resourses_sort;

            $server_statistics[$server->id]['total_pvp_stat'] = $total_pvp_stat;
            $server_statistics[$server->id]['total_pvp_kills'] = $total_pvp_kills;
            $server_statistics[$server->id]['total_pvp_deaths'] = $total_pvp_deaths;
            $server_statistics[$server->id]['total_pvp_deaths_player'] = $total_pvp_deaths_player;
            $server_statistics[$server->id]['total_pvp_kdr'] = $total_pvp_kdr;
            $server_statistics[$server->id]['resourses'] = $resourses;
            $server_statistics[$server->id]['raids'] = $raids;
            $server_statistics[$server->id]['statistics'] = $statistics_src_new;
        }

        //$user = current($server_statistics[$server_id]['statistics']);

        return view('pages.cabinet.stats', compact('server_statistics', 'user', 'users_stat'));
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