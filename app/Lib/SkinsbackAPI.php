<?php
namespace App\Lib;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class SkinsbackAPI {

    private static $api_url = 'https://skinsback.com/api.php';

    public static function getSteamItems($game)
    {
        return FALSE;
    }

    public static function getSteamItemsList($game, $page)
    {
        return FALSE;
    }

    public static function getItems($game)
    {
        $params = array(
            'method' => 'market_pricelist',
            'shopid' => config('options.skinsback_client_id', ''),
            'game' => $game,
        );
        $params['sign'] = self::buildSignature($params, config('options.skinsback_client_secret', ''));

        $client = new Client();
        $options = [
            'form_params' => $params
        ];

        $response = $client->request('POST', self::$api_url, $options);
        $data = json_decode($response->getBody()->getContents(), TRUE);
        if ($data['status'] === "success") {
            return $data['items'];
        }

        return FALSE;
    }

    public static function searchItemsByName($game, $item_name)
    {
        $params = array(
            'method' => 'market_search',
            'shopid' => config('options.skinsback_client_id', ''),
            'game' => $game,
            'name' => $item_name,
        );
        $params['sign'] = self::buildSignature($params, config('options.skinsback_client_secret', ''));

        $client = new Client();
        $options = [
            'form_params' => $params
        ];

        $response = $client->request('POST', self::$api_url, $options);
        $data = json_decode($response->getBody()->getContents(), TRUE);
        if ($data['status'] === "success") {
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

        $params = array(
            'method' => 'market_buy',
            'shopid' => config('options.skinsback_client_id', ''),
            'partner' => $partner,
            'token' => $token,
            'id' => $item_id,
            'max_price' => $item_price,
            'custom_id' => $project_id,
        );
        $params['sign'] = self::buildSignature($params, config('options.skinsback_client_secret', ''));

        $client = new Client();
        $options = [
            'form_params' => $params
        ];

        $response = $client->request('POST', self::$api_url, $options);
        $data = json_decode($response->getBody()->getContents(), TRUE);

        Log::channel('skinsback_buy')->info("Buy Item on Skinsback. Response: " . json_encode($data));

        if ($data['status'] === "success") {
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

        $params = array(
            'method' => 'market_buy',
            'shopid' => config('options.skinsback_client_id', ''),
            'partner' => $partner,
            'token' => $token,
            'game' => $game,
            'name' => $item_name,
            'max_price' => $item_price,
            'custom_id' => $project_id,
        );
        $params['sign'] = self::buildSignature($params, config('options.skinsback_client_secret', ''));

        $client = new Client();
        $options = [
            'form_params' => $params
        ];

        $response = $client->request('POST', self::$api_url, $options);
        $data = json_decode($response->getBody()->getContents(), TRUE);

        Log::channel('skinsback_buy')->info("Buy Item on Skinsback. Response: " . json_encode($data));

        if ($data['status'] === "success") {
            return $data['buy_id'];
        }

        return FALSE;
    }

    public static function checkManyProjectID($project_id)
    {
        $params = array(
            'method' => 'market_getinfo',
            'shopid' => config('options.skinsback_client_id', ''),
            'buy_id' => $project_id,
        );
        $params['sign'] = self::buildSignature($params, config('options.skinsback_client_secret', ''));

        $client = new Client();
        $options = [
            'form_params' => $params
        ];

        $response = $client->request('POST', self::$api_url, $options);
        $data = json_decode($response->getBody()->getContents(), TRUE);

        if (isset($data['status']) && $data['status'] == "success" && isset($data['offer_status'])) {
            return $data;
        }

        return FALSE;
    }

    public static function checkManySteam($id)
    {
        return FALSE;
    }

    public static function getUser()
    {
        return FALSE;
    }

    protected static function buildSignature($params, $secret_key)
    {
        ksort($params);

        $paramsString = '';
        foreach ($params as $key => $value) {
            if ($key == 'sign') continue;
            if (is_array($value)) {
                continue;
            }
            $paramsString .= $key . ':' . $value . ';';
        }
        $sign = hash_hmac('sha1', $paramsString, $secret_key);

        return $sign;
    }

}
