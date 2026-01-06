<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\User;
use App\Models\DeliveryRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Models\Server;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Lib\WaxpeerAPI;
use App\Lib\SkinsbackAPI;


class DeliveryRequestController extends Controller
{

    //Проверка заявок на вывод через Waxpeer и покупка их в маркете Waxpeer
    protected function buyItemsFromWaxpeer(Request $request)
    {
        if (!$request->has('token') || $request->token != 'ZFghyxDL71z94WgY') die('error');

        $deliveryrequests = DeliveryRequest::where('status', 4)->latest('date_request')->get();

        foreach ($deliveryrequests as $deliveryrequest) {
            Log::channel('waxpeer_buy')->info("Buy Item on Waxpeer. Parameters: " . json_encode($deliveryrequest));

            $user = User::find($deliveryrequest->user_id);
            if (!$user || !$this->verifySteamTradeUrl($user->steam_trade_url)) continue;

            $game = 'rust';

            $item_name = $deliveryrequest->item;
            $price_cap = $deliveryrequest->price_cap * 1000;

            //$items = WaxpeerAPI::getItemMinPriceByName($game, $item_name);
            //dd($items);
            //if (!$items) continue;

            $delivery_id = WaxpeerAPI::buyOneP2PName($item_name, $game, $price_cap, $deliveryrequest->id, $user->steam_trade_url);
            if ($delivery_id) {
                $deliveryrequest->delivery_id = $delivery_id;
                $deliveryrequest->status = 5;
                $deliveryrequest->save();
            }

        }

        die('finish');
    }

    //Проверка статуса покупок на маркете Waxpeer
    protected function checkStatusFromWaxpeer(Request $request)
    {
        if (!$request->has('token') || $request->token != 'ZFghyxDL71z94WgY') die('error');

        $deliveryrequests = DeliveryRequest::where('status', 5)->get();

        foreach ($deliveryrequests as $deliveryrequest) {
            Log::channel('waxpeer_buy')->info("Check Delivery status on Waxpeer. Parameters: " . json_encode($deliveryrequest));

            $result = WaxpeerAPI::checkManySteam($deliveryrequest->delivery_id);
            Log::channel('waxpeer_buy')->info("Delivery status result: " . json_encode($result));

            if ($result && $result['status']) {
                if ($result['status'] == 5) {
                    $deliveryrequest->status = 6;
                    $deliveryrequest->date_execution = date('Y-m-d H:i:s');
                    Log::channel('adminlog')->info("WaxpeerAPI: Changed the request status. Request ID: ". $deliveryrequest->id ." New status: WaxpeerAPI. Completed.");

                } elseif ($result['status'] == 6) {
                    $deliveryrequest->status = 7;

                    $item = [
                        'type' => $deliveryrequest->item_type,
                        'image' => $deliveryrequest->item_icon,
                        'item_id' => $deliveryrequest->item_id,
                    ];
                    $inventory_item = new Inventory;
                    $inventory_item->type = 0; //0 - item, 1 - case
                    $inventory_item->item = json_encode($item);
                    $inventory_item->user_id = $deliveryrequest->user_id;
                    $inventory_item->amount = $deliveryrequest->amount;
                    $inventory_item->save();

                    Log::channel('adminlog')->info("WaxpeerAPI: Changed the request status. Request ID: ". $deliveryrequest->id ." New status: WaxpeerAPI. Canceled. Reason: " . $result['reason']);
                }
                $deliveryrequest->note = $result['reason'] ?? '';
                $deliveryrequest->save();
            }
        }

        die('finish');
    }

    //Проверка заявок на вывод через Skinsback и покупка их в Skinsback
    protected function buyItemsFromSkinsback(Request $request)
    {
        if (!$request->has('token') || $request->token != 'ZFghyxDL71z94WgY') die('error');

        $deliveryrequests = DeliveryRequest::where('status', 8)->latest('date_request')->get();

        foreach ($deliveryrequests as $deliveryrequest) {
            Log::channel('skinsback_buy')->info("Buy Item on Skinsback. Parameters: " . json_encode($deliveryrequest));

            $user = User::find($deliveryrequest->user_id);
            if (!$user || !$this->verifySteamTradeUrl($user->steam_trade_url)) continue;

            $game = 'rust';

            $item_name = $deliveryrequest->item;
            $price_cap = $deliveryrequest->price_cap;

            $delivery_id = SkinsbackAPI::buyOneP2PName($item_name, $game, $price_cap, $deliveryrequest->id, $user->steam_trade_url);
            if ($delivery_id) {
                $deliveryrequest->delivery_id = $delivery_id;
                $deliveryrequest->status = 9;
            } else {
                $deliveryrequest->status = 12;
            }
            $deliveryrequest->save();
        }

        die('finish');
    }

    //Проверка статуса покупок на маркете Skinsback
    protected function checkStatusFromSkinsback(Request $request)
    {
        if (!$request->has('token') || $request->token != 'ZFghyxDL71z94WgY') die('error');

        $deliveryrequests = DeliveryRequest::where('status', 9)->get();

        foreach ($deliveryrequests as $deliveryrequest) {
            Log::channel('skinsback_buy')->info("Check Delivery status on Skinsback. Parameters: " . json_encode($deliveryrequest));

            $result = SkinsbackAPI::checkManyProjectID($deliveryrequest->delivery_id);
            Log::channel('skinsback_buy')->info("Delivery status result: " . json_encode($result));

            if ($result && $result['offer_status']) {
                if ($result['offer_status'] == 'accepted') {
                    $deliveryrequest->status = 10;
                    $deliveryrequest->date_execution = date('Y-m-d H:i:s');
                    Log::channel('adminlog')->info("SkinsbackAPI: Changed the request status. Request ID: ". $deliveryrequest->id ." New status: SkinsbackAPI. Completed.");

                } elseif (in_array($result['offer_status'], ['canceled', 'timeout', 'invalid_trade_token', 'user_not_tradable', 'trade_create_error'])) {
                    $deliveryrequest->status = 11;

                    if ($result['offer_status'] == 'canceled') {
                        $result['reason'] = 'the trade was rejected by the user';
                    } elseif ($result['offer_status'] == 'timeout') {
                        $result['reason'] = 'timeout (5 minutes)';
                    } elseif ($result['offer_status'] == 'invalid_trade_token') {
                        $result['reason'] = 'invalid trade token';
                    } elseif ($result['offer_status'] == 'user_not_tradable') {
                        $result['reason'] = 'the user is blocked in the trade system';
                    } elseif ($result['offer_status'] == 'trade_create_error') {
                        $result['reason'] = 'error of creating a trade (try again)';
                    }

                    $item = [
                        'type' => $deliveryrequest->item_type,
                        'image' => $deliveryrequest->item_icon,
                        'item_id' => $deliveryrequest->item_id,
                    ];
                    $inventory_item = new Inventory;
                    $inventory_item->type = 0; //0 - item, 1 - case
                    $inventory_item->item = json_encode($item);
                    $inventory_item->user_id = $deliveryrequest->user_id;
                    $inventory_item->amount = $deliveryrequest->amount;
                    $inventory_item->save();

                    Log::channel('adminlog')->info("SkinsbackAPI: Changed the request status. Request ID: ". $deliveryrequest->id ." New status: SkinsbackAPI. Canceled. Reason: " . $result['reason']);
                }
                $deliveryrequest->note = $result['reason'] ?? '';
                $deliveryrequest->save();
            }
        }

        die('finish');
    }

    protected function verifySteamTradeUrl($url)
    {
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $queries);
        if (isset($queries['token']) && $queries['partner']) {
            return TRUE;
        } else {
            return  FALSE;
        }
    }
}
