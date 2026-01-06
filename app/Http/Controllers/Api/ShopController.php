<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ShopItem;
use App\Models\ShopCart;
use App\Models\Shopping;
use Illuminate\Support\Facades\Log;

class ShopController extends Controller
{

    protected function getUser(Request $request)
    {
        if (!$request->has('api_key') || $request->api_key != 'SjA2o7v@Bqn$mZNCBd2EX8Vwd!5') {
            return response()->json([
                'status' => 'error',
                'result'    => 'API key is invalid.',
            ]);
        }

        if (!$request->has('userID')) {
            return response()->json([
                'status' => 'error',
                'result'    => 'userID is missed',
            ]);
        }

        $user = User::where('steam_id', $request->userID)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'result'    => 'userID not find',
            ]);
        }

        $shopcart = ShopCart::where('user_id', $user->id)->first();
        if (!$shopcart) {
            $items = [];
        } else {
            $items_new = [];
            $items = json_decode($shopcart->items);
            foreach ($items as $item) {
                $item_info = ShopItem::find($item->item_id);
                if ($item_info) {
                    $items_new[] = $this->getBucketItem($item, $item_info);
                }
            }
            $items = $items_new;
        }

        return response()->json([
            'status' => 'success',
            'result' => [
                'UserBalance' => $user->balance,
                'bucket'      => $items,
            ],
        ]);

    }

    protected function deleteItem(Request $request)
    {
        Log::channel('api_req')->info('Request deleteItem: ' . json_encode($request->all()));

        if (!$request->has('api_key') || $request->api_key != 'SjA2o7v@Bqn$mZNCBd2EX8Vwd!5') {
            return response()->json([
                'status' => 'error',
                'result'    => 'API key is invalid.',
            ]);
        }

        if (!$request->has('userID') || !$request->has('ID')) {
            return response()->json([
                'status' => 'error',
                'result'    => 'userID|ID is missed',
            ]);
        }

        $user = User::where('steam_id', $request->userID)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'result'    => 'userID not find',
            ]);
        }

        $shopcart = ShopCart::where('user_id', $user->id)->first();
        if (!$shopcart) {
            return response()->json([
                'status' => 'error',
                'result'    => 'Bucket is empty!',
            ]);
        }

        $items_new = [];
        $items = json_decode($shopcart->items);
        $amount = 0;

        $item_delete = [];
        $item_find = FALSE;
        foreach ($items as $item) {
            if ($item->id == $request->ID) {
                $item_find = TRUE;
                $item_delete = $item;
            }
        }
        if ($item_find === FALSE) {
            return response()->json([
                'status' => 'error',
                'result' => 'Item not find in bucket',
            ]);
        }

        //Удаляем этот товар-услугу из инвентаря пользователя
        $shopitem = ShopItem::find($item_delete->item_id);
        if ($shopitem && $shopitem->is_command === 1) {
            $inventory = Inventory::where('user_id', $user->id)->where('shop_item_id', $item_delete->item_id)->where('variation_id', $item_delete->var_id)->first();
            if (!$inventory || $inventory->item === NULL) {
                return response()->json([
                    'status' => 'error',
                    'result' => 'Item not find in inventory',
                ]);
            }
            $inventory->delete();
        }

        //Сохраняем корзину без удаленного товара
        foreach ($items as $item) {
            if ($item->id != $request->ID) {
                $amount += $item->price * $item->quantity;
                $items_new[] = $item;
            }
        }

        $shopcart->total = $amount;
        $shopcart->items = json_encode($items_new);
        $shopcart->save();

        $item_info = ShopItem::find($item_delete->item_id);

        return response()->json([
            'status' => 'success',
            'result' => [
                'item' => $this->getBucketItem($item_delete, $item_info),
            ],
        ]);

    }

    protected function hasItem(Request $request)
    {
        if (!$request->has('api_key') || $request->api_key != 'SjA2o7v@Bqn$mZNCBd2EX8Vwd!5') {
            return response()->json([
                'status' => 'error',
                'result'    => 'API key is invalid.',
            ]);
        }

        if (!$request->has('userID') || !$request->has('itemID')) {
            return response()->json([
                'status' => 'error',
                'result'    => 'userID|itemID is missed',
            ]);
        }

        $user = User::where('steam_id', $request->userID)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'result'    => 'userID not find',
            ]);
        }

        $shopcart = ShopCart::where('user_id', $user->id)->first();
        if (!$shopcart) {
            return response()->json([
                'status' => 'error',
                'result'    => 'Bucket is empty!',
            ]);
        }

        $items_find = FALSE;
        $items_find_id = 0;
        $items = json_decode($shopcart->items);
        foreach ($items as $item) {
            if ($item->item_id == $request->itemID) {
                $items_find = TRUE;
                $items_find_id = $item->id;
            }
        }

        if ($items_find = TRUE) {
            return response()->json([
                'status' => 'success',
                'result' => [
                    'itemID' => $items_find_id,
                ],
            ]);
        }

        return response()->json([
            'status' => 'error',
            'result' => 'itemID not find',
        ]);


    }

    protected function changeBalace(Request $request)
    {
        Log::channel('api_req')->info('Request changeBalace: ' . json_encode($request->all()));

        if (!$request->has('api_key') || $request->api_key != 'SjA2o7v@Bqn$mZNCBd2EX8Vwd!5') {
            return response()->json([
                'status' => 'error',
                'result'    => 'API key is invalid.',
            ]);
        }

        if (!$request->has('userID') || !$request->has('Balance') || !$request->has('Type')) {
            return response()->json([
                'status' => 'error',
                'result'    => 'userID|Balance|Type is missed',
            ]);
        }

        if ($request->Type != 'plus' && $request->Type != 'minus') {
            return response()->json([
                'status' => 'error',
                'result'    => 'Type incorrect',
            ]);
        }

        $balance = intval($request->Balance);
        if ($balance <= 0) {
            return response()->json([
                'status' => 'error',
                'result'    => 'Balance incorrect',
            ]);
        }

        $user = User::where('steam_id', $request->userID)->first();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'result' => 'userID not find',
            ]);
        }

        if ($request->Type == 'minus') {
            $user->balance -= $balance;
            if ($user->balance < 0) {
                $user->balance = 0;
            }
        } else {
            $user->balance += $balance;
        }
        $user->save();

        return response()->json([
            'status' => 'success',
            'result' => [
                'UserBalance' => $user->balance,
            ],
        ]);
    }

    protected function reportService(Request $request)
    {
        Log::channel('api_req')->info('Request reportService: ' . json_encode($request->all()));
        $data = $request->all();
        foreach ($data as $key => $value) {
            $data = json_decode($key);
        }

        if (!isset($data->api_key) || $data->api_key != 'SjA2o7v@Bqn$mZNCBd2EX8Vwd!5') {
            return response()->json([
                'status' => 'error',
                'result'    => 'API key is invalid.',
            ], 400);
        }

        if (!isset($data->userID) || !isset($data->Service) || !isset($data->Status)) {
            return response()->json([
                'status' => 'error',
                'result'    => 'userID|Service|Status is missed',
            ], 400);
        }

        $shopping = Shopping::query()->where('command', 'LIKE', "%{$data->userID}%")->where('command', 'LIKE', "%{$data->Service}%")->where('status', 0)->latest()->first();

        if (!$shopping) {
            return response()->json([
                'status' => 'error',
                'result' => 'User service not found',
            ], 400);
        }

        if ($data->Status == 'success') {
            $shopping->update(['status' => 1]);
        }

        return response()->json([
            'status' => 'success',
            'result' => 'ok',
        ]);
    }

    protected function getBucketItem($item, $item_info)
    {
        $command = str_replace('%steamid%', $item->steam_id, $item_info->command);
        $command = str_replace('%var%', $item->var_id, $command);

        return [
            'ID' => strval($item->id),
            'Server' => $item->server_id,
            'Name' => $item_info->name_en,
            'ItemID' => $item->item_id,
            'RustID' => $item->rust_id,
            'SteamID' => $item->steam_id,
            'Amount' => $item->quantity,
            'ShortName' => $item_info->short_name,
            'Command' => $command,
            'WipeBlock' => $item_info->wipe_block,
            'ImageUrl' => $item_info->image_url,
            'IsBlueprint' => ($item_info->is_blueprint == 1) ? true : false,
            'IsCommand' => ($item_info->is_command == 1) ? true : false,
            'IsItem' => ($item_info->is_item == 1) ? true : false,
        ];
    }

    protected function getImageUrls(Request $request)
    {
        Log::channel('api_req')->info('Request getImageUrls: ' . json_encode($request->all()));
        Log::channel('api_req')->info('Request getImageUrls: ' . print_r($request->all(), 1));

        $data = $request->all();
        if (is_array($data)) {
            foreach ($data as $key => $d) {
                $obj = json_decode($key);
                $api_key = $obj->api_key;
            }
        }

        if (!isset($api_key) || $api_key != 'SjA2o7v@Bqn$mZNCBd2EX8Vwd!5') {
            return response()->json([
                'status' => 'error',
                'result'    => 'API key is invalid.',
            ]);
        }

        $shopitems = ShopItem::query()->get();
        $images = [];
        foreach ($shopitems as $shopitem) {
            $images[] = [
                'ImageUrl' => str_replace('//storage', '/storage', $shopitem->image_url),
            ];
        }

        return response()->json([
            'status' => 'success',
            'result' => $images,
        ]);
    }

    protected function getImageUrlsV2(Request $request)
    {
        Log::channel('api_req')->info('Request getImageUrls V2: ' . json_encode($request->all()));
        Log::channel('api_req')->info('Request getImageUrls V2: ' . print_r($request->all(), 1));

        $api_key = $request->api_key;
        if (!isset($api_key) || $api_key != 'SjA2o7v@Bqn$mZNCBd2EX8Vwd!5') {
            return response()->json([
                'status' => 'error',
                'result' => 'API key is invalid.',
            ]);
        }

        $shopitems = ShopItem::query()->get();
        $images = [];
        foreach ($shopitems as $shopitem) {
            $images[] = [
                'ImageUrl' => $shopitem->image_url,
            ];
        }

        return response()->json([
            'status' => 'success',
            'result' => $images,
        ], 200, [], JSON_UNESCAPED_SLASHES);
    }
}
