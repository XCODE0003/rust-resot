<?php
namespace App\Lib;

use App\Models\Account;
use App\Models\Characters;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Models\Auction;
use App\Models\UserDelivery;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use GuzzleHttp\Client;

class SteamApi
{

    public static function login()
    {
        $openid = new \LightOpenID(config('app.url', ''));

       //dd(file_get_contents('https://steamcommunity.com/openid'));

        if(!$openid->mode) {
            $openid->identity = 'https://steamcommunity.com/openid';
            return (object)[
                'status' => 'success',
                'data' => $openid->authUrl(),
            ];
        } elseif ($openid->mode == 'cancel') {
            return (object)[
                'status' => 'error',
                'data' => 'User has canceled authentication!',
            ];
        } else {
            if($openid->validate()) {
                $id = $openid->identity;
                $ptn = "/^https?:\/\/steamcommunity\.com\/openid\/id\/(7[0-9]{15,25}+)$/";
                preg_match($ptn, $id, $matches);

                $steamid = $matches[1];
                return (object)[
                    'status' => 'success',
                    'data' => $steamid,
                ];
                return $steamid;
            } else {
                return (object)[
                    'status' => 'error',
                    'data' => 'User is not logged in.',
                ];
            }
        }

    }

    public static function getUserInfo($steamid)
    {

        $url = file_get_contents("https://api.steampowered.com/ISteamUser/GetPlayerSummaries/v0002/?key=".config('options.steam_api_key', '')."&steamids=".$steamid);
        $content = json_decode($url, true);
        if (isset($content['response']['players'][0])) {
            return (object)[
                'status' => 'success',
                'data' => (object)$content['response']['players'][0],
            ];
        }
        return (object)[
            'status' => 'error',
            'data' => 'User not find.',
        ];

   }

    public static function logout()
    {
        session_unset();
        session_destroy();
    }
}
