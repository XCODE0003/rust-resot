<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ShopController;
use App\Models\ShopItem;
use App\Models\Cases;
use App\Models\CasesItem;
use App\Models\User;
use App\Models\WonItem;
use App\Models\Inventory;
use App\Models\PlayersOnline;
use App\Models\CaseopenHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CasesController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index()
    {
        $cases = Cases::where('status', 1)->where('kind', 0)->latest()->get();

        return view('pages.cabinet.cases.list', compact('cases'));
    }

    public function show(Cases $case)
    {
        if (($case->status === 0 || $case->kind === 2) && auth()->user()->role != 'admin') {
            abort(404);
        }

        $items = [];
        $casesitems = json_decode($case->items);

        $i = 0;
        foreach ($casesitems as $casesitem) {
            $price_usd = NULL;
            $i++;

            $quality_type = 1;
            if ($casesitem->var == 0) {
                $case_item = CasesItem::where('item_id', $casesitem->item_id)->first();
                if (!$case_item) continue;
                $price = $case_item->price;
                $price_usd = $case_item->price_usd;
                $name = $case_item->title;
                $quality_type = $case_item->quality_type;
            } else {
                list($price, $name) = $this->getItemPriceName($casesitem);
            }

            if ($price_usd === NULL) $price_usd = $price / config('options.exchange_rate_usd', 70);


            //Собираем для сортировки по цене
            $items_tmp[] = [
                'id' => $i,
                'price' => $price,
            ];

            if ($casesitem->var == 0) {
                $image = getImageUrl(get_skin($casesitem->item_id)->image);
            } else {
                $image = getImageUrl($casesitem->image);
            }

            $items_bd[] = (object) [
                'id' => $i,
                'context' => (object) [
                    'name' => $name,
                    'quality_type' => $quality_type,
                    'chance' => $casesitem->drop_percent,
                    'icon' => $image,
                    'skin_id' => $casesitem->item_id,
                    'price' => $price,
                    'price_usd' => $price_usd,
                    'wipe_block' => 0,
                ],
            ];
        }

        //Сортируем по типу предмета
        usort($items_tmp, "sort_items_price_desc");
        foreach ($items_tmp as $item_tmp) {
            foreach ($items_bd as $item_bd) {
                if ($item_tmp['id'] == $item_bd->id) {
                    $items[] = $item_bd;
                }
            }
        }

        return view('pages.cabinet.cases.details', compact('case', 'items'));
    }

    public function show_shop(Cases $case, Request $request)
    {
        if ($case->status === 0 && isset(auth()->user()->role) && auth()->user()->role != 'admin') {
            abort(404);
        }

        $server_id = $request->get('server');
        $items = [];
        $casesitems = json_decode($case->items);

        $i = 0;
        foreach ($casesitems as $casesitem) {
            $price_usd = NULL;
            $i++;

            $item_amount = 1;
            $quality_type = 1;
            if ($casesitem->var == 0) {
                $case_item = CasesItem::where('item_id', $casesitem->item_id)->first();
                if (!$case_item) continue;
                $price = $case_item->price;
                $price_usd = $case_item->price_usd;
                $name = $case_item->title;
                $quality_type = $case_item->quality_type;
            } else {
                list($price, $name) = $this->getItemPriceName($casesitem);
            }


            $shopitem = ShopItem::find($casesitem->shop_id);
            if ($shopitem) {
                $item_amount = $shopitem->amount;
            }

            if ($price_usd === NULL) $price_usd = $price / config('options.exchange_rate_usd', 70);


            //Собираем для сортировки по цене
            $items_tmp[] = [
                'id' => $i,
                'price' => $price,
            ];

            if ($casesitem->var == 0) {
                $image = getImageUrl(get_skin($casesitem->item_id)->image);
            } else {
                $image = getImageUrl($casesitem->image);
            }

            if ($casesitem->shop_var !== NULL && str_contains($casesitem->shop_var, '-')) {
                $item_amount = $casesitem->shop_var;
            }

            $items_bd[] = (object) [
                'id' => $i,
                'context' => (object) [
                    'name' => $name,
                    'quality_type' => $quality_type,
                    'amount' => $item_amount,
                    'chance' => $casesitem->drop_percent,
                    'icon' => $image,
                    'skin_id' => $casesitem->item_id,
                    'price' => $price,
                    'price_usd' => $price_usd,
                    'wipe_block' => $shopitem->wipe_block,
                ],
            ];
        }

        //Сортируем по типу предмета
        usort($items_tmp, "sort_items_price_desc");
        foreach ($items_tmp as $item_tmp) {
            foreach ($items_bd as $item_bd) {
                if ($item_tmp['id'] == $item_bd->id) {
                    $items[] = $item_bd;
                }
            }
        }

        //Если кейс имеет фри открытия, то проверяем открывал игрок его сегодня
        $free_open = FALSE;
        if (isset(auth()->user()->id) && $case->is_free === 1) {
            $caseopen_history = CaseopenHistory::where('user_id', auth()->id())->where('case_id', $case->id)->where('date', '>', date('Y-m-d H:i:s', strtotime('-1 day')))->first();
            if (!$caseopen_history) {
                $free_open = TRUE;
            }
        }

        return view('pages.cabinet.cases.details_shop', compact('case', 'items', 'server_id', 'free_open'));
    }

    public function open(Request $request)
    {
        if (!$request->has('case_id')) {
            return response()->json([
                'status' => 'error',
                'msg' => __('Произошла ошибка! Попробуйте позже.'),
            ]);
        }

        if (auth()->user()->role == 'admin') {
            $case = Cases::where('id', $request->case_id)->first();
        } else {
            $case = Cases::where('id', $request->case_id)->where('status', 1)->first();
        }

        if (!$case) {
            return response()->json([
                'status' => 'error',
                'msg' => __('Произошла ошибка! Попробуйте позже.'),
            ]);
        }

        $title = "title_" . app()->getLocale();

        //Записываем блок в кеш
        $lock = Cache::lock('case_open'.auth()->id().$case->id.'_lock', 30);
        if ($lock->get()) {

            //Если кейс имеет фри открытия, то проверяем открывал игрок его сегодня
            $free_open = FALSE;
            if (isset(auth()->user()->id) && $case->is_free === 1) {
                $caseopen_history = CaseopenHistory::where('user_id', auth()->user()->id)->where('case_id', $case->id)->where('date', '>', date('Y-m-d H:i:s', strtotime('-1 day')))->first();
                if (!$caseopen_history) {
                    $free_open = TRUE;
                }
            }

            if (($case->kind === 0 || $case->kind === 2) && auth()->user()->balance < $case->price && $free_open === FALSE) {
                return response()->json([
                    'status' => 'error',
                    'msg' => __('Недостаточно средств на балансе'),
                ]);
            }

            if ($case->kind === 1 && (getOnlineTimeCase($case->id) < ($case->online_amount * 60*60))) {
                return response()->json([
                    'status' => 'error',
                    'msg' => __('Вы недостаточно наиграли онлайна на серверах'),
                ]);
            }

            if ($case->kind < 0 || $case->kind > 2) {
                return response()->json([
                    'status' => 'error',
                    'msg' => __('Произошла ошибка! Попробуйте позже.'),
                ]);
            }

            //Расчитываем выигранный предмет
            $items = [];
            $casesitems = json_decode($case->items);

            $i = 0;
            foreach ($casesitems as $casesitem) {
                if ($casesitem->available < 1) continue;
                $i++;

                list($price, $name) = $this->getItemPriceName($casesitem);

                if ($casesitem->var == 0) {
                    $image = getImageUrl(get_skin($casesitem->item_id)->image);
                } else {
                    $image = getImageUrl($casesitem->image);
                }

                $items[] = [
                    'id' => $i,
                    'context' => [
                        'name' => $name,
                        'quality_type' => get_skin($casesitem->item_id)->quality_type,
                        'chance' => $casesitem->drop_percent,
                        'icon' => getImageUrl($image),
                        'image' => $image,
                        'skin_id' => $casesitem->item_id,
                        'var' => $casesitem->var,
                        'vip_period' => $casesitem->vip_period,
                        'deposit_bonus' => $casesitem->deposit_bonus,
                        'balance' => $casesitem->balance,
                        'shop_id' => (isset($casesitem->shop_id)) ? $casesitem->shop_id : 0,
                        'shop_var' => (isset($casesitem->shop_var)) ? $casesitem->shop_var : 0,
                    ],
                ];
            }

            //Расчитываем выигранный предмет
            $win_index = get_random_item($items);
            if (isset($items[$win_index])) {
                $win_item = $items[$win_index];
            } else {
                $win_item = $items[0];
            }


            if ($case->kind === 2) {
                //Если кейс магазина, то выдаем сразу команду или кладем в корзину

                //Получаем случайное кол-во предмета
                if ($win_item['context']['shop_var'] !== NULL && str_contains($win_item['context']['shop_var'], '-')) {
                    $min_max = explode('-', $win_item['context']['shop_var']);
                    $amount_min = (isset($min_max[0])) ? $min_max[0] : 1;
                    $amount_max = (isset($min_max[1])) ? $min_max[1] : 1;
                    $win_item['context']['shop_var'] = 'rnd='.rand($amount_min, $amount_max);
                }

                if (!$request->has('server_id')) {
                    return response()->json([
                        'status' => 'error',
                        'msg' => __('Произошла ошибка! Попробуйте позже.'),
                    ]);
                }
                $server = intval($request->server_id);

                //Начисляем купленый товар
                $shopController = new ShopController;

                if (!$shopController->send_item($win_item['context']['shop_id'], $win_item['context']['shop_var'], auth()->user()->steam_id, $server, 1, !$free_open)) {
                    return response()->json([
                        'status' => 'error',
                        'msg' => __('Произошла ошибка! Попробуйте позже.'),
                    ]);
                }

            } else {

                //Добавляем выигрыш в инвентарь
                $inventory_item = [
                    'type'    => $win_item['context']['quality_type'],
                    'image'   => $win_item['context']['image'],
                    'item_id' => $win_item['context']['skin_id'],
                ];
                $inventory = Inventory::create([
                    'type'          => $win_item['context']['var'],
                    'item'          => json_encode($inventory_item),
                    'amount'        => 1,
                    'user_id'       => auth()->id(),
                    'vip_period'    => $win_item['context']['vip_period'],
                    'deposit_bonus' => $win_item['context']['deposit_bonus'],
                    'balance'       => $win_item['context']['balance'],
                    'shop_item_id'  => $win_item['context']['shop_id'],
                    'variation_id'  => $win_item['context']['shop_var'],
                ]);

                if (!$inventory) {
                    return response()->json([
                        'status' => 'error',
                        'msg'    => __('Произошла ошибка! Попробуйте позже.'),
                    ]);
                }
            }

            //Уменьшаем остаток выигранного предмета
            $casesitems_new = [];
            foreach ($casesitems as $casesitem) {

                //Если кейс магазина, то вычитаем по shop_id
                if ($case->kind === 2) {
                    if ($casesitem->shop_id == $win_item['context']['shop_id']) {
                        $casesitem->available--;
                    }
                } else {
                    if ($casesitem->item_id == $win_item['context']['skin_id']) {
                        $casesitem->available--;
                    }
                }
                $casesitems_new[] = $casesitem;
            }
            $case->items = json_encode($casesitems_new);
            $case->save();

            $win_item_id = ($win_item['context']['skin_id']) ?: ($win_item['context']['shop_id']) ?: 0;

            //Записываем предмет в список выигрышей
            WonItem::create([
                'user_id'   => auth()->user()->id,
                'item'      => $win_item['context']['name'],
                'item_id'   => $win_item_id,
                'item_icon' => $win_item['context']['icon'],
                'server'    => $case->title_en,
                'issued'    => 0,
            ]);


            //Если бонусный кейс, то списываем онлайн, иначе списываем деньги
            if ($case->kind === 1) {

                //Уменьшаем время онлайна
                $servers = json_decode($case->servers);
                $online_time = $case->online_amount * 60*60;
                foreach ($servers as $server) {
                    if ($server === NULL || $online_time === 0) continue;
                    $players_online = PlayersOnline::where('user_id', auth()->user()->id)->where('server', $server)->latest()->first();
                    if ($players_online) {
                        if ($online_time > $players_online->online_time) {
                            $online_time -= $players_online->online_time;
                            $players_online->online_time = 0;
                        } else {
                            $players_online->online_time -= abs($online_time);
                            $online_time = 0;
                        }
                        $players_online->save();
                    }
                }

            }
            else {

                //Если это фри открытие, то деньги не списываем
                if ($free_open === TRUE) {

                    //Получаем случайное кол-во предмета
                    $var_amount = 1;
                    if ($win_item['context']['shop_var'] !== NULL && str_contains($win_item['context']['shop_var'], 'rnd')) {
                        $var_amount = str_replace('rnd=', '', $win_item['context']['shop_var']);
                    }

                    CaseopenHistory::create([
                        'case_id'     => $case->id,
                        'user_id'     => auth()->user()->id,
                        'item_id'     => $win_item_id,
                        'item_amount' => $var_amount,
                        'date'        => date('Y-m-d H:i:s'),
                    ]);

                    Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' free open Case: ' . $case->title_en);

                }
                else {

                    //Уменьшаем баланс пользователя
                    if (app()->getLocale() == 'ru') {
                        $balance = auth()->user()->balance;
                        $balance_new = $balance - abs(floatval($case->price));
                    } else {
                        $balance = auth()->user()->balance / config('options.exchange_rate_usd', 70);
                        $balance_new = $balance - abs(floatval($case->price_usd));
                        $balance_new = $balance_new * config('options.exchange_rate_usd', 70);
                    }
                    auth()->user()->balance = $balance_new;
                    auth()->user()->save();
                }

            }

            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . 'successfully won an Item: ' . $win_item['context']['name'] . ' ('.$win_item_id.') from a Case: ' . $case->title_en);

            $lock->release();
            return response()->json([
                'status' => 'success',
                'msg' => __('Поздравляем! Вы выиграли') . ' ' . $win_item['context']['name'] . '.',
                'win_index' => $win_item['id'],
                'win_item' => $win_item,
            ]);

        } else {
            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' could not open case: ' . $case->id . ' due to blocking.');
            return response()->json([
                'status' => 'error',
                'msg' => __('Произошла ошибка! Попробуйте позже.'),
            ]);
        }
    }

    public function open_pay(Request $request)
    {
        if (!$request->has('case_id')) {
            return response()->json([
                'status' => 'error',
                'msg' => __('Произошла ошибка! Попробуйте позже.'),
            ]);
        }

        $case = Cases::where('id', $request->case_id)->where('status', 1)->first();
        if (!$case) {
            return response()->json([
                'status' => 'error',
                'msg' => __('Произошла ошибка! Попробуйте позже.'),
            ]);
        }

        //Записываем блок в кеш
        $lock = Cache::lock('case_open_pay'.auth()->id().$case->id.'_lock', 30);
        if ($lock->get()) {

            $title = "title_" . app()->getLocale();

            if (auth()->user()->balance < $case->price) {
                return response()->json([
                    'status' => 'error',
                    'msg' => __('Недостаточно средств на балансе'),
                ]);
            }

            //Расчитываем выигранный предмет
            $items = [];
            $casesitems = json_decode($case->items);

            $i = 0;
            foreach ($casesitems as $casesitem) {
                if ($casesitem->available < 1) continue;
                $i++;

                list($price, $name) = $this->getItemPriceName($casesitem);

                $items[] = [
                    'id' => $i,
                    'context' => [
                        'name' => $name,
                        'quality_type' => get_skin($casesitem->item_id)->quality_type,
                        'chance' => $casesitem->drop_percent,
                        'icon' => getImageUrl(get_skin($casesitem->item_id)->image),
                        'image' => get_skin($casesitem->item_id)->image,
                        'skin_id' => $casesitem->item_id,
                        'var' => $casesitem->var,
                        'vip_period' => $casesitem->vip_period,
                        'deposit_bonus' => $casesitem->deposit_bonus,
                        'balance' => $casesitem->balance,
                        'shop_id' => (isset($casesitem->shop_id)) ? $casesitem->shop_id : 0,
                        'shop_var' => (isset($casesitem->shop_var)) ? $casesitem->shop_var : 0,
                    ],
                ];
            }

            //Расчитываем выигранный предмет
            $win_index = get_random_item($items);
            if (isset($items[$win_index])) {
                $win_item = $items[$win_index];
            } else {
                $win_item = $items[0];
            }

            if ($case->kind === 2) {

                //Получаем случайное кол-во предмета
                if ($win_item['context']['shop_var'] !== NULL && str_contains($win_item['context']['shop_var'], '-')) {
                    $min_max = explode('-', $win_item['context']['shop_var']);
                    $amount_min = (isset($min_max[0])) ? $min_max[0] : 1;
                    $amount_max = (isset($min_max[1])) ? $min_max[1] : 1;
                    $win_item['context']['shop_var'] = 'rnd='.rand($amount_min, $amount_max);
                }

                //Если кейс магазина, то выдаем сразу команду или кладем в корзину

                if (!$request->has('server_id')) {
                    return response()->json([
                        'status' => 'error',
                        'msg' => __('Произошла ошибка! Попробуйте позже.'),
                    ]);
                }
                $server = intval($request->server_id);

                //Начисляем купленый товар
                $shopController = new ShopController;

                if (!$shopController->send_item($win_item['context']['shop_id'], $win_item['context']['shop_var'], auth()->user()->steam_id, $server, 1, true)) {
                    return response()->json([
                        'status' => 'error',
                        'msg' => __('Произошла ошибка! Попробуйте позже.'),
                    ]);
                }

            } else {

                //Добавляем выигрыш в инвентарь
                $inventory_item = [
                    'type'    => $win_item['context']['quality_type'],
                    'image'   => $win_item['context']['image'],
                    'item_id' => $win_item['context']['skin_id'],
                ];
                $inventory = Inventory::create([
                    'type'          => $win_item['context']['var'],
                    'item'          => json_encode($inventory_item),
                    'amount'        => 1,
                    'user_id'       => auth()->id(),
                    'vip_period'    => $win_item['context']['vip_period'],
                    'deposit_bonus' => $win_item['context']['deposit_bonus'],
                    'balance'       => $win_item['context']['balance'],
                    'shop_item_id'  => $win_item['context']['shop_id'],
                    'variation_id'  => $win_item['context']['shop_var'],
                ]);

                if (!$inventory) {
                    return response()->json([
                        'status' => 'error',
                        'msg'    => __('Произошла ошибка! Попробуйте позже.'),
                    ]);
                }

            }

            //Уменьшаем баланс пользователя
            if (app()->getLocale() == 'ru') {
                $balance = auth()->user()->balance;
                $balance_new = $balance - abs(floatval($case->price));
            } else {
                $balance = auth()->user()->balance / config('options.exchange_rate_usd', 70);
                $balance_new = $balance - abs(floatval($case->price_usd));
                $balance_new = $balance_new * config('options.exchange_rate_usd', 70);
            }
            auth()->user()->balance = $balance_new;
            auth()->user()->save();


            //Уменьшаем остаток выигранного предмета
            $casesitems_new = [];
            foreach ($casesitems as $casesitem) {

                //Если кейс магазина, то вычитаем по shop_id
                if ($case->kind === 2) {
                    if ($casesitem->shop_id == $win_item['context']['shop_id']) {
                        $casesitem->available--;
                    }
                } else {
                    if ($casesitem->item_id == $win_item['context']['skin_id']) {
                        $casesitem->available--;
                    }
                }

                $casesitems_new[] = $casesitem;
            }
            $case->items = json_encode($casesitems_new);
            $case->save();

            //Записываем предмет в список выигрышей
            WonItem::create([
                'user_id'   => auth()->user()->id,
                'item'      => $win_item['context']['name'],
                'item_id'   => ($win_item['context']['skin_id']) ?: 0,
                'item_icon' => $win_item['context']['icon'],
                'server'    => $case->title_en,
                'issued'    => 0,
            ]);

            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . 'successfully won an Item: ' . $win_item['context']['name'] . ' ('.$win_item['context']['skin_id'].') is Pay Open from a Case: ' . $case->title_en);

            $lock->release();
            return response()->json([
                'status' => 'success',
                'msg' => __('Поздравляем! Вы выиграли') . ' ' . $win_item['context']['name'] . '.',
                'win_index' => $win_item['id'],
                'win_item' => $win_item,
            ]);

        }
        else {
            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' could not open pay case: ' . $case->id . ' due to blocking.');
            return response()->json([
                'status' => 'error',
                'msg' => __('Произошла ошибка! Попробуйте позже.'),
            ]);
        }

    }

    public function account_show(Inventory $inventory)
    {
        if ($inventory->id <= 0 || $inventory->user_id != auth()->id()) {
            abort(404);
        }

        $case = Cases::where('id', $inventory->case_id)->first();
        if (!$case) {
            abort(404);
        }

        $items = [];
        $casesitems = json_decode($case->items);

        $i = 0;
        foreach ($casesitems as $casesitem) {
            $i++;

            $quality_type = 1;
            if ($casesitem->var == 0) {
                $case_item = CasesItem::where('item_id', $casesitem->item_id)->first();
                if (!$case_item) continue;
                $price = $case_item->price;
                $price_usd = $case_item->price_usd;
                $name = $case_item->title;
                $quality_type = $case_item->quality_type;
            } else {
                list($price, $name) = $this->getItemPriceName($casesitem);
            }

            if (!isset($price_usd)) $price_usd = $price / config('options.exchange_rate_usd', 70);

            //Собираем для сортировки по цене
            $items_tmp[] = [
                'id' => $i,
                'price' => floatval($price),
            ];

            if ($casesitem->var == 0) {
                $image = getImageUrl(get_skin($casesitem->item_id)->image);
            } else {
                $image = getImageUrl($casesitem->image);
            }

            $items_bd[] = (object) [
                'id' => $i,
                'context' => (object) [
                    'name' => $name,
                    'quality_type' => $quality_type,
                    'chance' => $casesitem->drop_percent,
                    'icon' => $image,
                    'skin_id' => $casesitem->item_id,
                    'price' => $price,
                    'price_usd' => $price_usd,
                ],
            ];
        }

        //Сортируем по типу предмета
        usort($items_tmp, "sort_items_price_desc");
        foreach ($items_tmp as $item_tmp) {
            foreach ($items_bd as $item_bd) {
                if ($item_tmp['id'] == $item_bd->id) {
                    $items[] = $item_bd;
                }
            }
        }

        return view('pages.cabinet.cases.account_details', compact('inventory', 'case', 'items'));
    }

    public function account_open(Request $request)
    {
        if (!$request->has('inventory_id')) {
            return response()->json([
                'status' => 'error',
                'msg' => __('Произошла ошибка! Попробуйте позже.'),
            ]);
        }

        $inventory = Inventory::where('id', $request->inventory_id)->where('user_id', auth()->id())->first();
        if (!$inventory) {
            return response()->json([
                'status' => 'error',
                'msg' => __('Произошла ошибка! Попробуйте позже.'),
            ]);
        }

        $case = Cases::where('id', $inventory->case_id)->first();
        if (!$case) {
            return response()->json([
                'status' => 'error',
                'msg' => __('Произошла ошибка! Попробуйте позже.'),
            ]);
        }

        //Записываем блок в кеш
        $lock = Cache::lock('account_open'.auth()->id().$case->id.'_lock', 5);
        if ($lock->get()) {

            $title = "title_" . app()->getLocale();

            //Расчитываем выигранный предмет
            $items = [];
            $casesitems = json_decode($case->items);

            $i = 0;
            foreach ($casesitems as $casesitem) {
                if ($casesitem->available < 1) continue;
                $i++;

                list($price, $name) = $this->getItemPriceName($casesitem);

                $items[] = [
                    'id' => $i,
                    'context' => [
                        'name' => $name,
                        'quality_type' => get_skin($casesitem->item_id)->quality_type,
                        'chance' => $casesitem->drop_percent,
                        'icon' => getImageUrl(get_skin($casesitem->item_id)->image),
                        'image' => get_skin($casesitem->item_id)->image,
                        'skin_id' => $casesitem->item_id,
                        'var' => $casesitem->var,
                        'vip_period' => $casesitem->vip_period,
                        'deposit_bonus' => $casesitem->deposit_bonus,
                        'balance' => $casesitem->balance,
                        'shop_id' => (isset($casesitem->shop_id)) ? $casesitem->shop_id : 0,
                        'shop_var' => (isset($casesitem->shop_var)) ? $casesitem->shop_var : 0,
                    ],
                ];
            }

            //Расчитываем выигранный предмет
            $win_index = get_random_item($items);
            if (isset($items[$win_index])) {
                $win_item = $items[$win_index];
            } else {
                $win_item = $items[0];
            }

            //Удаляем кейс из инвентаря
            $inventory->delete();

            //Добавляем выигрыш в инвентарь
            $inventory_item = [
                'type' => $win_item['context']['quality_type'],
                'image' => $win_item['context']['image'],
                'item_id' => $win_item['context']['skin_id'],
            ];
            Inventory::create([
                'type' => $win_item['context']['var'],
                'item' => json_encode($inventory_item),
                'amount' => 1,
                'user_id' => auth()->id(),
                'vip_period' => $win_item['context']['vip_period'],
                'deposit_bonus' => $win_item['context']['deposit_bonus'],
                'balance' => $win_item['context']['balance'],
                'shop_item_id' => $win_item['context']['shop_id'],
                'variation_id' => $win_item['context']['shop_var'],
            ]);

            //Записываем предмет в список выигрышей
            WonItem::create([
                'user_id'   => auth()->user()->id,
                'item'      => $win_item['context']['name'],
                'item_id'   => ($win_item['context']['skin_id']) ?: 0,
                'item_icon' => $win_item['context']['icon'],
                'server'    => $case->title_en,
                'issued'    => 0,
            ]);

            //Уменьшаем остаток выигранного предмета
            $casesitems_new = [];
            foreach ($casesitems as $casesitem) {
                if ($casesitem->item_id == $win_item['context']['skin_id'] && $casesitem->var == $win_item['context']['var'] && $casesitem->vip_period == $win_item['context']['vip_period'] && $casesitem->deposit_bonus == $win_item['context']['deposit_bonus'] && $casesitem->balance == $win_item['context']['balance'] && $casesitem->shop_id == $win_item['context']['shop_id'] && $casesitem->shop_var == $win_item['context']['shop_var']) {
                    $casesitem->available--;
                }
                $casesitems_new[] = $casesitem;
            }
            $case->items = json_encode($casesitems_new);
            $case->save();

            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . 'successfully won an Item: ' . $win_item['context']['name'] . ' ('.$win_item['context']['skin_id'].') from a Case: ' . $case->title_en);

            $lock->release();
            return response()->json([
                'status' => 'success',
                'msg' => __('Поздравляем! Вы выиграли') . ' ' . $win_item['context']['name'] . '.',
                'win_index' => $win_item['id'],
                'win_item' => $win_item,
            ]);
        }
        else {
            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' could not open case from inventory: ' . $inventory->case_id . ' due to blocking.');
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }

    }

    public function sell(Request $request)
    {
        if (!$request->has('inventory_id')) {
            return response()->json([
                'status' => 'error',
                'msg' => __('Произошла ошибка! Попробуйте позже.'),
            ]);
        }

        //Записываем блок в кеш
        $lock = Cache::lock('item_sell'.auth()->id().$request->inventory_id.'_lock', 5);
        if ($lock->get()) {

            $inventory = Inventory::where('user_id', auth()->user()->id)->where('id', $request->inventory_id)->where('amount', '>', 0)->first();
            if (!$inventory) {
                return response()->json([
                    'status' => 'error',
                    'msg' => __('Произошла ошибка! Попробуйте позже.'),
                ]);
            }

            if ($inventory->amount < 0 && $inventory->user_id != auth()->user()->id) {
                return response()->json([
                    'status' => 'error',
                    'msg' => __('Произошла ошибка! Попробуйте позже.'),
                ]);
            }

            $casesitem = CasesItem::where('item_id', $inventory->item_id)->first();

            if (!$casesitem) {
                return response()->json([
                    'status' => 'error',
                    'msg' => __('Произошла ошибка! Попробуйте позже.'),
                ]);
            }

            $title = "title_" . app()->getLocale();

            //Начисляем на баланс пользователя
            if (app()->getLocale() == 'ru') {
                $balance = auth()->user()->balance;
                $balance_new = $balance + abs(floatval($casesitem->price));
            } else {
                $balance = auth()->user()->balance / config('options.exchange_rate_usd', 70);
                $balance_new = $balance + abs(floatval($casesitem->price_usd));
                $balance_new = $balance_new * config('options.exchange_rate_usd', 70);
            }
            auth()->user()->balance = $balance_new;
            auth()->user()->save();


            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . 'successfully sell item from inventory: ' . $inventory->item_id);

            $inventory->delete();

            $lock->release();
            return response()->json([
                'status' => 'success',
                'msg' => __('Вы успешно продали') . ' ' . $casesitem->$title . '.',
                'sell_item' => $casesitem,
                'balance' => number_format(auth()->user()->balance, 2, '.', ' '),
            ]);

        }
        else {
            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' could not sell item from inventory: ' . $request->inventory_id . ' due to blocking.');
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }

    }

    public function getCaseItemsForRoll(Request $request)
    {
        $case_id = $request->has('case_id') ? $request->case_id : 0;

        if (auth()->user()->role == 'admin') {
            $case = Cases::where('id', $case_id)->first();
        } else {
            $case = Cases::where('id', $case_id)->first();
            if (!$case || $case->status == 0) {
                return response()->json([
                    'status' => 'error',
                    'msg' => 'Case not found',
                ]);
            }
        }

        $items = [];
        $casesitems = json_decode($case->items);

        $i = 0;
        foreach ($casesitems as $casesitem) {
            if ($casesitem->available < 1) continue;
            $i++;

            list($price, $name) = $this->getItemPriceName($casesitem);

            if ($casesitem->var == 0) {
                $image = getImageUrl(get_skin($casesitem->item_id)->image);
            } else {
                $image = getImageUrl($casesitem->image);
            }

            $items[] = (object) [
                'id' => $i,
                'context' => (object) [
                    'name' => $name,
                    'icon' => $image,
                ],
            ];
        }

        return response()->json([
            'status' => 'success',
            'msg' => 'ok',
            'result' => $items,
        ]);
    }

    public function getItemPriceName($casesitem)
    {
        $price = 0;
        $name = '';

        if ($casesitem->var == 1) {
            list($price, $price_usd, $name) = get_coupon_price($casesitem->var, $casesitem->vip_period);
        } else if ($casesitem->var == 2) {
            list($price, $price_usd, $name) = get_coupon_price($casesitem->var, $casesitem->deposit_bonus);
        } else if ($casesitem->var == 3) {
            list($price, $price_usd, $name) = get_coupon_price($casesitem->var, $casesitem->balance);
        } else if ($casesitem->var == 5) {
            list($price, $price_usd, $name) = get_coupon_price($casesitem->var, $casesitem->shop_id, $casesitem->shop_var);
        } else {
            $price = get_skin($casesitem->item_id)->price;
            $name = get_skin($casesitem->item_id)->name;
        }

        return [$price, $name];
    }

}
