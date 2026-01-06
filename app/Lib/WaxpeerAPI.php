<?php
namespace App\Lib;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class WaxpeerAPI {

    private static $api_url = 'https://api.waxpeer.com/v1';

    public static function getSteamItems($game)
    {
        $client = new Client();
        $response = $client->request('GET', self::$api_url . '/get-steam-items?api='.config('options.waxpeer_api_key', '').'&game='.$game.'&highest_offer=1');
        $data = json_decode($response->getBody()->getContents(), true);
        if ($data['success'] === TRUE) {
            return $data['items'];
        }

        return FALSE;
    }

    public static function getSteamItemsList($game, $page)
    {
        $skip = $page * 100;
        $client = new Client();
        $response = $client->request('GET', self::$api_url . '/get-items-list?api='.config('options.waxpeer_api_key', '').'&game='.$game.'&skip=' . $skip);
        $data = json_decode($response->getBody()->getContents(), true);
        if ($data['success'] === TRUE) {
            return $data['items'];
        }

        return FALSE;
    }

    public static function searchItemsByName($game, $item_name)
    {
        $client = new Client();
        $response = $client->request('GET', self::$api_url . '/search-items-by-name?api=' . config('options.waxpeer_api_key', '') . '&game=' . $game . '&names=' . $item_name);
        $data = json_decode($response->getBody()->getContents(), TRUE);
        if ($data['success'] === TRUE) {
            return $data['items'];
        }

        return FALSE;
    }

    public static function getItemMinPriceByName($game, $item_name)
    {
        $items = self::searchItemsByName($game, $item_name);
        if (!$items) return FALSE;

        usort($items, "sort_items_price");
        return $items;
    }

    public static function getItemRangePriceByName($game, $item_name)
    {
        $items = self::getItemMinPriceByName($game, $item_name);
        if (!$items) return [
            'min' => 0,
            'max' => 0,
        ];

        $min = 99999999999999999999;
        $max = 0;
        foreach ($items as $item) {
            if ($item['price'] > $max) $max = $item['price'];
            if ($item['price'] < $min) $min = $item['price'];
        }

        return [
            'min' => $min/1000,
            'max' => $max/1000,
        ];
    }

    public static function buyItemOneP2P($item_id, $item_price, $project_id, $steam_trade_url)
    {
        //$project_id - номер заказа = id заявки на вывод
        $query = parse_url($steam_trade_url, PHP_URL_QUERY);
        parse_str($query, $queries);
        $token = $queries['token'];
        $partner = $queries['partner'];

        $client = new Client();
        $response = $client->request('GET', self::$api_url . '/buy-one-p2p?api=' . config('options.waxpeer_api_key', '') . '&project_id=' . $project_id . '&item_id=' . $item_id . '&token=' . $token . '&price=' . $item_price . '&partner=' . $partner);
        $data = json_decode($response->getBody()->getContents(), TRUE);

        Log::channel('waxpeer_buy')->info("Buy Item on Waxpeer. Response: " . json_encode($data));

        if ($data['success'] === TRUE) {
            return $data['id'];
        }

        return FALSE;
    }

    public static function buyOneP2PName($item_name, $game, $item_price, $project_id, $steam_trade_url)
    {
        //$project_id - номер заказа = id заявки на вывод
        $query = parse_url($steam_trade_url, PHP_URL_QUERY);
        parse_str($query, $queries);
        $token = $queries['token'];
        $partner = $queries['partner'];

        $client = new Client();
        $response = $client->request('GET', self::$api_url . '/buy-one-p2p-name?api=' . config('options.waxpeer_api_key', '') . '&project_id=' . $project_id . '&name=' . $item_name . '&game=' . $game . '&token=' . $token . '&price=' . $item_price . '&partner=' . $partner);
        $data = json_decode($response->getBody()->getContents(), TRUE);

        Log::channel('waxpeer_buy')->info("Buy Item on Waxpeer. Response: " . json_encode($data));

        if ($data['success'] === TRUE) {
            return $data['id'];
        }

        return FALSE;
    }

    public static function checkManyProjectID($project_id)
    {
        $client = new Client();
        $response = $client->request('GET', self::$api_url . '/check-many-project-id?api=' . config('options.waxpeer_api_key', '') . '&id=' . $project_id);
        $data = json_decode($response->getBody()->getContents(), TRUE);

        if ($data['success'] === TRUE && $data['trades'][0]) {
            return $data['trades'][0];
        }

        return FALSE;
    }

    public static function checkManySteam($id)
    {
        $client = new Client();
        $response = $client->request('GET', self::$api_url . '/check-many-steam?api=' . config('options.waxpeer_api_key', '') . '&id=' . $id);
        $data = json_decode($response->getBody()->getContents(), TRUE);

        if ($data['success'] === TRUE && $data['trades'][0]) {
            return $data['trades'][0];
        }

        return FALSE;
    }


    public static function getUser()
    {
        $client = new Client();
        $response = $client->request('GET', self::$api_url . '/user?api=' . config('options.waxpeer_api_key', ''));
        $data = json_decode($response->getBody()->getContents(), TRUE);
        dd($data);

        if ($data['success'] === TRUE) {
            return $data['items'];
        }

        return FALSE;
    }
}
