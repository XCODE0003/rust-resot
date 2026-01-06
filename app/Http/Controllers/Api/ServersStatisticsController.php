<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Player;
use App\Models\Statistic;
use Illuminate\Support\Facades\Log;

class ServersStatisticsController extends Controller
{

    public function setStatistics()
    {
        Log::channel('api')->info('Request: ' . print_r(@file_get_contents('php://input'), 1));
        $request = json_decode(@file_get_contents('php://input'));
        Log::channel('api')->info('Request: ' . print_r($request, 1));

        $rand = rand(1,10000);
        Log::channel('api_req')->info('Request: Server ' . $request->server . '. Start ('.$rand.')...');

        if (!isset($request->api_key) || $request->api_key != config('options.game_api_key', '')) {
            return response()->json([
                'status' => 'error',
                'msg'    => 'API key is invalid.',
            ],500);
        }

        if (!isset($request->server)) {
            return response()->json([
                'status' => 'error',
                'msg'    => 'server is missed',
            ],500);
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
            ],500);
        }

        if (!isset($request->data)) {
            return response()->json([
                'status' => 'error',
                'msg'    => 'data not find',
            ],500);
        }

        //Отправляем данные отдельным запросом на обработку и сохранение в БД
        $post = [
            'data'        => json_encode($request->data),
            'server_name' => $request->server,
            'server_id'   => $server_id,
            'rand'        => $rand,
        ];

        $ch = curl_init('https://rustresort.com/api/statistics/processStatistics_jr7wu23g4tv0dr');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

        curl_setopt($ch, CURLOPT_TIMEOUT_MS, 100);
        curl_setopt($ch,CURLOPT_HEADER,true);

        curl_exec($ch);
        curl_close($ch);

        Log::channel('api_req')->info('Request: Server ' . $request->server . '. Finish ('.$rand.').');

        return response()->json([
            'status' => 'success',
            'msg'    => 'ok',
        ]);
    }

    public function processStatistics(Request $request)
    {

        $rand = $request->rand;
        $server_id = $request->server_id;
        $server_name = $request->server_name;

        Log::channel('api_req')->info('Request Process: Server ' . $server_name . ' Start ('.$rand.')...');
        Log::channel('api')->info('Request Process: Data: ' . $request->data);
        $data = json_decode($request->data);

        foreach ($data as $player_id => $player) {
            //Log::channel('api')->info('Request Process: player: ' . print_r($player, 1));
            //Log::channel('api')->info('Request Process: player_id: ' . $player_id);

            //Обновляем имя пользователя
            $user = User::where('steam_id', $player_id)->first();
            if ($user) {
                $user->name = $player->Name;
                $user->save();
            }

            //Подсчитываем смерти от игроков
            $deaths_player = 0;
            foreach ($player->Death->DeathList as $value_req) {
                if (strpos($value_req, 'ИГРОК') !== FALSE) {
                    $deaths_player++;
                }
            }

            //Суммируем данные рейда
            $raid_list_req = (object)[];

            foreach ($player->RaidList as $raid) {
                $raid_list_req_find = FALSE;
                foreach ($raid_list_req as $name_db => $value_db) {
                    if ($raid->ObjName == $name_db) {
                        $raid_list_req->$name_db += 1;
                        $raid_list_req_find = TRUE;
                    }
                }
                if ($raid_list_req_find === FALSE) {
                    $obj_name = $raid->ObjName;
                    $raid_list_req->$obj_name = 1;
                }
            }

            //Удаляем записи позже 30 дней
            $date_old = date('Y-m-d', strtotime(date('Y-m-d')) - 60 * 60 * 24 * 30);
            $player_stats = Statistic::where('player_id', $player_id)->where('server', $server_id)->where('general', 0)->where('date', '<', $date_old)->get();
            foreach ($player_stats as $stat) {
                $stat->delete();
            }

            $player_stat_general = Statistic::where('player_id', $player_id)->where('server', $server_id)->where('general', 1)->first();
            if (!$player_stat_general) {
                $player_stat_general = new Statistic;
                $player_stat_general->date = date('Y-m-d');
                $player_stat_general->general = 1;
                $player_stat_general->name = $player->Name;
                $player_stat_general->player_id = $player_id;
                $player_stat_general->server = $server_id;
                $player_stat_general->resourse_list = json_encode($player->ResourseList);
                //$player_stat_general->death_list = json_encode($player->Death->DeathList);
                //$player_stat_general->kill_list = json_encode($player->Death->KillList);
                $player_stat_general->raid_list = json_encode($raid_list_req);
                $player_stat_general->deaths = count($player->Death->DeathList);
                $player_stat_general->kills = count($player->Death->KillList);
                $player_stat_general->deaths_player = $deaths_player;
                $player_stat_general->head_shots = $player->HeadShots;
                $player_stat_general->hits = $player->Hits;
                $player_stat_general->shoots = $player->Shoots;
                $player_stat_general->is_npc = $player->IsNpc;
                $player_stat_general->save();

            } else {

                //Добавляем данные в общую запись

                //Рейды
                $raid_list_db = json_decode($player_stat_general->raid_list);
                $raid_list = $this->collectList($raid_list_req, $raid_list_db);

                //Ресурсы
                $resourse_list_db = json_decode($player_stat_general->resourse_list);
                $resourse_list = $this->collectList($player->ResourseList, $resourse_list_db);


                //$death_list = json_decode($player_stat_general->death_list);
                //foreach ($player->Death->DeathList as $value_req) {
                //    $death_list[] = $value_req;
                //}

                //$kill_list = json_decode($player_stat_general->kill_list);
                //foreach ($player->Death->KillList as $value_req) {
                //    $kill_list[] = $value_req;
                //}

                $player_stat_general->name = $player->Name;
                $player_stat_general->resourse_list = json_encode($resourse_list);
                //$player_stat_general->death_list = json_encode($death_list);
                //$player_stat_general->kill_list = json_encode($kill_list);
                $player_stat_general->raid_list = json_encode($raid_list);
                $player_stat_general->deaths += count($player->Death->DeathList);
                $player_stat_general->kills += count($player->Death->KillList);
                $player_stat_general->deaths_player += $deaths_player;
                $player_stat_general->head_shots += $player->HeadShots;
                $player_stat_general->hits += $player->Hits;
                $player_stat_general->shoots += $player->Shoots;
                $player_stat_general->is_npc = $player->IsNpc;
                $player_stat_general->save();
            }

            $player_stat = Statistic::where('player_id', $player_id)->where('server', $server_id)->where('general', 0)->where('date', date('Y-m-d'))->first();
            if (!$player_stat) {

                $player_stat = new Statistic;
                $player_stat->general = 0;
                $player_stat->date = date('Y-m-d');
                $player_stat->name = $player->Name;
                $player_stat->player_id = $player_id;
                $player_stat->server = $server_id;
                $player_stat->resourse_list = json_encode($player->ResourseList);
                //$player_stat->death_list = json_encode($player->Death->DeathList);
                //$player_stat->kill_list = json_encode($player->Death->KillList);
                $player_stat->raid_list = json_encode($raid_list_req);
                $player_stat->deaths = count($player->Death->DeathList);
                $player_stat->kills = count($player->Death->KillList);
                $player_stat->deaths_player = $deaths_player;
                $player_stat->head_shots = $player->HeadShots;
                $player_stat->hits = $player->Hits;
                $player_stat->shoots = $player->Shoots;
                $player_stat->is_npc = $player->IsNpc;
                $player_stat->save();

            } else {

                //Добавляем данные в запись за день

                //Рейды
                $raid_list_db = json_decode($player_stat->raid_list);
                $raid_list = $this->collectList($raid_list_req, $raid_list_db);

                //Ресурсы
                $resourse_list_db = json_decode($player_stat->resourse_list);
                $resourse_list = $this->collectList($player->ResourseList, $resourse_list_db);

                //$death_list = json_decode($player_stat->death_list);
                //foreach ($player->Death->DeathList as $value_req) {
                //    $death_list[] = $value_req;
                //}

                //$kill_list = json_decode($player_stat->kill_list);
                //foreach ($player->Death->KillList as $value_req) {
                //    $kill_list[] = $value_req;
                //}

                $player_stat->name = $player->Name;
                $player_stat->resourse_list = json_encode($resourse_list);
                //$player_stat->death_list = json_encode($death_list);
                //$player_stat->kill_list = json_encode($kill_list);
                $player_stat->raid_list = json_encode($raid_list);
                $player_stat->deaths += count($player->Death->DeathList);
                $player_stat->kills += count($player->Death->KillList);
                $player_stat->deaths_player += $deaths_player;
                $player_stat->head_shots += $player->HeadShots;
                $player_stat->hits += $player->Hits;
                $player_stat->shoots += $player->Shoots;
                $player_stat->is_npc = $player->IsNpc;
                $player_stat->save();

            }
        }

        Log::channel('api_req')->info('Request Process: Server ' . $server_name . '. Finish ('.$rand.').');

        echo 'success';
    }

    private function collectList($list_req, $list)
    {
        foreach ($list_req as $name_req => $value_req) {
            $list_find = FALSE;
            foreach ($list as $name_db => $value_db) {
                if ($name_req == $name_db) {
                    $list->$name_req += intval($value_req);
                    $list_find = TRUE;
                }
            }
            if ($list_find === FALSE) {
                $list->$name_req = intval($value_req);
            }
        }

        return $list;
    }

    protected function ChangeBalace(Request $request)
    {
        if (!isset($request->api_key) || $request->api_key != config('options.game_api_key', '')) {
            $articles = Article::latest()->limit(3)->get();

            foreach ($articles as $article) {
                $article->public_url = route('news.show', $article);
            }

            return response()->json([
                'status'   => 'success',
                'articles' => $articles
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'msg' => __('API key is invalid.'),
            ], 500);
        }
    }

    private function curl_post_async($url, $params)
    {
        foreach ($params as $key => &$val) {
            if (is_array($val)) $val = implode(',', $val);
            $post_params[] = $key.'='.urlencode($val);
        }
        $post_string = implode('&', $post_params);

        $parts=parse_url($url);

        $fp = fsockopen($parts['host'],
            isset($parts['port'])?$parts['port']:80,
            $errno, $errstr, 30);

        $out = "POST ".$parts['path']." HTTP/1.1\r\n";
        $out.= "Host: ".$parts['host']."\r\n";
        $out.= "Content-Type: application/x-www-form-urlencoded\r\n";
        $out.= "Content-Length: ".strlen($post_string)."\r\n";
        $out.= "Connection: Close\r\n\r\n";
        if (isset($post_string)) $out.= $post_string;

        fwrite($fp, $out);
        fclose($fp);
    }

    public function test(Request $request) {

                $player_stat = new Statistic;
                $player_stat->general = 0;
                $player_stat->date = date('Y-m-d');
                $player_stat->name = 'test';
                $player_stat->player_id = 76561198363756480;
                $player_stat->server = 2;
                $player_stat->resourse_list = json_encode([]);
                //$player_stat->death_list = json_encode($player->Death->DeathList);
                //$player_stat->kill_list = json_encode($player->Death->KillList);
                $player_stat->raid_list = json_encode([]);
                $player_stat->deaths = 0;
                $player_stat->kills = 0;
                $player_stat->deaths_player = 0;
                $player_stat->head_shots = 0;
                $player_stat->hits = 0;
                $player_stat->shoots = 0;
                $player_stat->is_npc = 0;
                //$player_stat->save();

        dd($player_stat);

        echo 'success';
    }
}
