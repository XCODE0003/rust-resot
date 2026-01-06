<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class Streams
{

    public function __construct()
    {
        require_once base_path('vendor') . '/Twitch/TwitchApi.php';
    }

    public static function getStreams($url)
    {
        $data = [];
        //Разбираем урл, получаем тип стрима youtube/twitch и id канала
        if (strpos($url, "twitch.tv")) {

            //return [];

            $channel = str_replace("https://www.twitch.tv/", "", $url);

            $client_id = config('options.twitch_client_id', "");
            $secret = config('options.twitch_secret', "");

            $twitch = new \Twitch\TwitchApi($client_id, $secret);
            $result = $twitch->getStreams(['user_login' => $channel]);

            if (!isset($result["data"][0]["id"]) || strpos($result["data"][0]["user_login"], 'content=offline') !== FALSE) return [];
            $data = [
                'stream_id' => $result["data"][0]["id"],
                'stream_status' => 'on',
                'stream_title' => $result["data"][0]["title"],
                'stream_url' => 'https://player.twitch.tv/?channel=' . $result["data"][0]["user_login"] . '&parent=' . $_SERVER['SERVER_NAME'] . '&autoplay=false',
            ];
            return $data;

        } elseif (strpos($url, "youtube.com")) {

            $channel = str_replace("https://www.youtube.com/channel/", "", $url);

            $youtube_key = config('options.youtube_key', "");

            //$channel_data = @file_get_contents('https://www.googleapis.com/youtube/v3/channels?part=snippet&id=' . $channel . '&key=' . $youtube_key);
            //if (!isset($channel_data)) return [];
            //$channel_data = json_decode($channel_data, TRUE);
            //$data['channel_name'] = $channel_data['items']['0']['snippet']['title'];

            $current_stream = @file_get_contents('https://www.googleapis.com/youtube/v3/search?part=snippet,id&channelId=' . $channel . '&eventType=live&type=video&key=' . $youtube_key);
            if (!isset($current_stream)) return [];
            $current_stream = json_decode($current_stream, TRUE);

            if (!isset($current_stream['items'])) return [];
            if (count($current_stream['items']) > 0) {
                $data['stream_id'] = $current_stream['items']['0']['id']['videoId'];
                $data['stream_status'] = 'on';
                $data['stream_title'] = $current_stream['items']['0']['snippet']['title'];
                $data['stream_url'] = 'https://www.youtube.com/embed/' . $current_stream['items']['0']['id']['videoId'];

            }
            return $data;

        } else {
            return [];
        }


    }

}