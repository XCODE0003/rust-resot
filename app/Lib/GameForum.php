<?php
namespace App\Lib;

use App\Lib\CacheD;
use Illuminate\Support\Facades\DB;

class GameForum {

    public static function getPosts() {

        //Проверяем тип форума
        switch (config('options.forum_type')) {

            case '1':
                return DB::connection('xenforo')
                    ->table('thread')
                    ->join('post', 'post.post_id', '=', 'thread.last_post_id')
                    ->select('post.post_id','post.user_id','post.username','thread.last_post_date', 'thread.title', 'thread.thread_id')
                    ->latest('thread.last_post_date')->limit(5)->get();

            case '2':
                $ch = curl_init();
                $queryData = http_build_query(array(
                    "api_key" => config('options.forum_api_key'),
                    "method" => "last_post",
                    "count" => "5",
                ) );
                curl_setopt($ch, CURLOPT_URL, config('options.forum_link') . '/api-ips4.php');
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=utf-8', 'Accept: application/json'));
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $queryData);
                curl_setopt($ch, CURLOPT_TIMEOUT, 300);
                $curl_res = curl_exec($ch);
                $result = json_decode($curl_res);

                if (isset($result->post)) {
                    return $result->post;
                }
                return false;

            default:
                return false;
        }
    }

}
