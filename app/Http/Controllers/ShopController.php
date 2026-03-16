<?php

namespace App\Http\Controllers;

use App\Models\ShopItem;
use App\Models\ShopCategory;
use App\Models\ShopSet;
use App\Models\ShopStatistic;
use App\Models\Server;
use App\Models\User;
use App\Models\Donate;
use App\Models\ShopCart;
use App\Models\Vip;
use App\Models\Shopping;
use App\Models\Cases;
use App\Models\Inventory;
use App\Models\ServicePurchase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use GuzzleHttp\Client;
use Qiwi\Api\BillPayments;
use App\Services\PayPal;
use URL;
use GameServer;

class ShopController extends Controller
{
    public function __construct()
    {
        $this->middleware('server.status');
    }

    public function index() {

        $servers = Cache::remember('page_shop_servers', '600', function () {
            return Server::query()->where('status', 1)->get();
        });

        return view('pages.cabinet.shop.list',  compact('servers'));

    }

    public function show($server_id) {
        $server = Server::query()->where('id', $server_id)->where('status', 1)->first();
        if (!$server) {
            abort('404');
        }

        $shopcategories = getshopcategories();

        $shopitems = [];
        $shopsets = [];
        foreach ($shopcategories as $shopcategory) {

            $shopitems_cat = ShopItem::where(function ($query) use ($server_id) {
                    return $query->where('server', $server_id)
                        ->orWhere('server', 0)
                        ->orWhere('servers', 'LIKE', '%"'.$server_id.'"%');
                })->where('status', 1)
                    ->where('category_id', $shopcategory->id)
                    ->orderBy('sort')
                    ->get();
            $shopitems[$shopcategory->id] = $shopitems_cat;

            $shopsets_cat = ShopSet::where(function ($query) use ($server_id) {
                    return $query->where('server', $server_id)
                        ->orWhere('server', 0)
                        ->orWhere('servers', 'LIKE', '%"'.$server_id.'"%');
                })->where('status', 1)
                    ->where('category_id', $shopcategory->id)
                    ->orderBy('sort')
                    ->get();
            $shopsets[$shopcategory->id] = $shopsets_cat;
        }

        if (isset(auth()->user()->id) && auth()->user()->id === 497) {
            $shopcases = Cases::where(function ($query) use ($server) {
                return $query->where('server', $server->id)
                    ->orWhere('server', 0);
            })->where('kind', 2)->orderBy('sort')->get();
        } else {
            $shopcases = Cases::where(function ($query) use ($server_id) {
                    return $query->where('server', $server_id)
                        ->orWhere('server', 0);
                })->where('status', 1)
                    ->where('kind', 2)
                    ->orderBy('sort')
                    ->get();
        }
        return view('pages.cabinet.shop.full', compact('server','shopitems', 'shopsets', 'shopcases', 'shopcategories'));
    }

    // public function show($server_id) {
    //     //Cache::forget('page_shop_server'.$server_id);
    //     $server = Cache::remember('page_shop_server'.$server_id, '600', function () use($server_id) {
    //         return Server::query()->where('id', $server_id)->where('status', 1)->first();
    //     });
    //     if (!$server) {
    //         abort('404');
    //     }

    //     $shopcategories = getshopcategories();

    //     $shopitems = [];
    //     $shopsets = [];
    //     foreach ($shopcategories as $shopcategory) {

    //         //Cache::forget('page_shop_shopitems_cat'.$shopcategory->id.$server_id);
    //         $shopitems_cat = Cache::remember('page_shop_shopitems_cat'.$shopcategory->id.$server_id, '600', function () use($shopcategory,$server_id) {
    //             return ShopItem::where(function ($query) use ($server_id) {
    //                 return $query->where('server', $server_id)
    //                     ->orWhere('server', 0)
    //                     ->orWhere('servers', 'LIKE', '%"'.$server_id.'"%');
    //             })->where('status', 1)->where('category_id', $shopcategory->id)->orderBy('sort')->get();
    //         });
    //         $shopitems[$shopcategory->id] = $shopitems_cat;

    //         //Cache::forget('page_shop_shopsets_cat'.$shopcategory->id.$server_id);
    //         $shopsets_cat = Cache::remember('page_shop_shopsets_cat'.$shopcategory->id.$server_id, '600', function () use($shopcategory,$server_id) {
    //             return ShopSet::where(function ($query) use ($server_id) {
    //                 return $query->where('server', $server_id)
    //                     ->orWhere('server', 0)
    //                     ->orWhere('servers', 'LIKE', '%"'.$server_id.'"%');
    //             })->where('status', 1)->where('category_id', $shopcategory->id)->orderBy('sort')->get();
    //         });
    //         $shopsets[$shopcategory->id] = $shopsets_cat;
    //     }

    //     if (isset(auth()->user()->id) && auth()->user()->id === 497) {
    //         $shopcases = Cases::where(function ($query) use ($server) {
    //             return $query->where('server', $server->id)
    //                 ->orWhere('server', 0);
    //         })->where('kind', 2)->orderBy('sort')->get();
    //     } else {

    //         //Cache::forget('page_shop_shopcases'.$server_id);
    //         $shopcases = Cache::remember('page_shop_shopcases'.$server_id, '600', function () use($server_id) {
    //             return Cases::where(function ($query) use ($server_id) {
    //                 return $query->where('server', $server_id)
    //                     ->orWhere('server', 0);
    //             })->where('status', 1)->where('kind', 2)->orderBy('sort')->get();
    //         });
    //     }

    //     return view('pages.cabinet.shop.full', compact('server','shopitems', 'shopsets', 'shopcases', 'shopcategories'));
    // }
    public function show_test() {

        abort(404);


        $server = Server::query()->where('id', 8)->first();
        $shopitems = ShopItem::where(function ($query) use ($server) {
            return $query->where('server', $server->id)
                ->orWhere('server', 0);
        })->orderBy('sort')->get();

        return view('pages.cabinet.shop.test', compact('server','shopitems'));

    }

    public function buy_item(Request $request)
    {

        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            // $this->alert('danger', __('Ошибка валидатора'));
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }

        $item_id = intval($request->item_id);
        //Записываем блок в кеш
        $lock = Cache::lock('shop_buy_item'.auth()->id().$item_id.'_lock', 30);
        if ($lock->get()) {

            $server_id = $request->server_id;
            $shopitem = ShopItem::where('id', $item_id)->where(function ($query) use ($server_id) {
                return $query->where('server', $server_id)
                    ->orWhere('server', 0)
                    ->orWhere('servers', 'LIKE', '%"' . $server_id . '"%');
            })->where('status', 1)->first();

            if (!$shopitem) {
                //$this->alert('danger', __('Нет товара'));
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                return back();
            }

            //Проверяем, если товар нельзя купить в подарок, то steam_id должен совпадать с steam_id пользователя
            if ($shopitem->can_gift !== 1 && $request->has('steam_id') && $request->steam_id != '') {
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                return back();
            }

            $qty = 1;

            // Получаем цену со скидкой для базовой цены товара
            $priceData = getShopItemPrice($shopitem, 'rub');
            $discountPercent = $priceData['discount_percent'];
            $price = $priceData['discount_price'];
            $price_usd = getShopItemPrice($shopitem, 'usd')['discount_price'];

            if ($request->var_id > 0) {
                $variations = json_decode($shopitem->variations);

                //Если товар с кол-вом
                if (empty($variations)) {
                    $qty = abs(intval($request->qty));
                    $price = $qty * $price;
                    $price_usd = $qty * $price_usd;
                } else {

                    $variation_find = FALSE;
                    if (isset($variations[0]->variation_id)) {
                        foreach ($variations as $variation) {
                            if ($variation->variation_id == $request->var_id) {
                                $variation_find = TRUE;
                                $varPrice = floatval($variation->variation_price);
                                $varPriceUsd = floatval($variation->variation_price_usd);
                                // Применяем скидку к цене вариации
                                if ($discountPercent > 0) {
                                    $price = $varPrice * (1 - $discountPercent / 100);
                                    $price_usd = $varPriceUsd * (1 - $discountPercent / 100);
                                } else {
                                    $price = $varPrice;
                                    $price_usd = $varPriceUsd;
                                }
                                $amount = 1;
                            }
                        }
                    } elseif (isset($variations[0]->quantity_id)) {
                        foreach ($variations as $variation) {
                            if ($variation->quantity_id == $request->var_id) {
                                $variation_find = TRUE;
                                $varPrice = floatval($variation->quantity_price);
                                $varPriceUsd = floatval($variation->quantity_price_usd);
                                // Применяем скидку к цене вариации
                                if ($discountPercent > 0) {
                                    $price = $varPrice * (1 - $discountPercent / 100);
                                    $price_usd = $varPriceUsd * (1 - $discountPercent / 100);
                                } else {
                                    $price = $varPrice;
                                    $price_usd = $varPriceUsd;
                                }
                                $amount = intval($variation->quantity_amount);
                            }
                        }
                    }

                    if ($variation_find === FALSE) {
                        //$this->alert('danger', __('Нет вариации'));
                        $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                        return back();
                    }
                }
            } else {
                //Если товар с кол-вом
                $qty = abs(intval($request->qty));
                $price = $qty * $price;
                $price_usd = $qty * $price_usd;
            }

            session()->put('donate_prev_url', url()->previous());

            //Задаем всегда покупку с внутреннего баланса
            $request->payment_id = 20;

            $lock->release();
            return $this->setPayment($request, $shopitem, $price, $price_usd, $qty);
        }
        $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
        return back();
    }

    /**
     * AJAX покупка товара с баланса
     */
    public function buy_item_ajax(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Необходимо авторизоваться')
                ], 401);
            }

            $validator = $this->validator($request->all());

            if ($validator->fails()) {
                Log::channel('rcon_master')->warning('Shop buy_item_ajax validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'request' => $request->except(['_token']),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first() ?: __('Произошла ошибка! Попробуйте позже.'),
                    'errors' => $validator->errors()
                ]);
            }

            $item_id = intval($request->item_id);
            $lock = Cache::lock('shop_buy_item'.auth()->id().$item_id.'_lock', 30);

            if (!$lock->get()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Запрос уже обрабатывается, подождите.')
                ]);
            }

            $server_id = $request->server_id;
            $shopitem = ShopItem::where('id', $item_id)->where(function ($query) use ($server_id) {
                return $query->where('server', $server_id)
                    ->orWhere('server', 0)
                    ->orWhere('servers', 'LIKE', '%"' . $server_id . '"%');
            })->where('status', 1)->first();

            if (!$shopitem) {
                $lock->release();
                return response()->json([
                    'success' => false,
                    'message' => __('Товар не найден')
                ]);
            }

            // Проверяем подарок
            if ($shopitem->can_gift !== 1 && $request->has('steam_id') && $request->steam_id != '') {
                $lock->release();
                return response()->json([
                    'success' => false,
                    'message' => __('Этот товар нельзя подарить')
                ]);
            }

            $qty = 1;
            $priceData = getShopItemPrice($shopitem, 'rub');
            $discountPercent = $priceData['discount_percent'];
            $price = $priceData['discount_price'];

            if ($request->var_id > 0) {
                $variations = json_decode($shopitem->variations);

                if (empty($variations)) {
                    $qty = abs(intval($request->qty));
                    $price = $qty * $price;
                } else {
                    $variation_find = FALSE;
                    if (isset($variations[0]->variation_id)) {
                        foreach ($variations as $variation) {
                            if ($variation->variation_id == $request->var_id) {
                                $variation_find = TRUE;
                                $varPrice = floatval($variation->variation_price);
                                if ($discountPercent > 0) {
                                    $price = $varPrice * (1 - $discountPercent / 100);
                                } else {
                                    $price = $varPrice;
                                }
                            }
                        }
                    } elseif (isset($variations[0]->quantity_id)) {
                        foreach ($variations as $variation) {
                            if ($variation->quantity_id == $request->var_id) {
                                $variation_find = TRUE;
                                $varPrice = floatval($variation->quantity_price);
                                if ($discountPercent > 0) {
                                    $price = $varPrice * (1 - $discountPercent / 100);
                                } else {
                                    $price = $varPrice;
                                }
                                $qty = intval($variation->quantity_amount);
                            }
                        }
                    }

                    if (!$variation_find) {
                        $lock->release();
                        return response()->json([
                            'success' => false,
                            'message' => __('Вариация не найдена')
                        ]);
                    }
                }
            } else {
                $qty = abs(intval($request->qty));
                $price = $qty * $price;
            }

            // Проверяем баланс (приводим к float для корректного сравнения)
            $balance = floatval(auth()->user()->balance);
            $priceFloat = floatval($price);

            if ($balance < $priceFloat) {
                $lock->release();
                return response()->json([
                    'success' => false,
                    'message' => __('Недостаточно средств на балансе'),
                    'need_balance' => true,
                    'price' => number_format($priceFloat, 2, ',', ' '),
                    'balance' => number_format($balance, 2, '.', '')
                ]);
            }

            // Определяем steam_id получателя
            $steam_id = $request->steam_id ?? '';
            if (empty($steam_id)) {
                $steam_id = auth()->user()->steam_id;
            }

            // Сначала списываем баланс (быстро) и освобождаем lock — RCON может быть медленным
            auth()->user()->decrement('balance', $priceFloat);
            $lock->release();

            // Отправляем товар (RCON — может занимать несколько секунд, lock уже снят)
            if ($this->send_item(auth()->id(), $shopitem->id, $request->var_id, $steam_id, $server_id, $qty, true, false, $priceFloat)) {
                auth()->user()->refresh();
                $newBalance = floatval(auth()->user()->balance);

                if (app()->getLocale() != 'ru') {
                    $formattedBalance = $newBalance / config('options.exchange_rate_usd', 70);
                } else {
                    $formattedBalance = $newBalance;
                }
                $formattedBalance = number_format($formattedBalance, 2, '.', ' ');

                if (app()->getLocale() == 'ru') {
                    $formattedBalance = $formattedBalance . ' руб.';
                } else {
                    $formattedBalance = '$' . $formattedBalance;
                }

                $name = "name_" . app()->getLocale();
                return response()->json([
                    'success' => true,
                    'message' => __('Товар успешно куплен!'),
                    'item_name' => $shopitem->$name,
                    'new_balance' => $formattedBalance
                ]);
            }

            // Ошибка отправки — возвращаем средства
            auth()->user()->increment('balance', $priceFloat);
            return response()->json([
                'success' => false,
                'message' => __('Произошла ошибка при отправке товара')
            ]);
        } catch (\Exception $e) {
            Log::channel('rcon_master')->error('Shop buy_item_ajax exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['_token']),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    public function send_item($user_id, $item_id, $var_id=0, $steam_id='', $server_id=1, $qty=1, $save_statistics=true, $show_alert=true, $paid_price=null)
    {
        /*
        $shopitem = ShopItem::where('id', $item_id)->where(function ($query) use ($server_id) {
            return $query->where('server', $server_id)
                ->orWhere('server', 0);
        })->where('status', 1)->first();
        */

        $shopitem = ShopItem::where('id', $item_id)->where('status', 1)->first();

        $server = Server::where('id', $server_id)->where('status', 1)->first();

        if (!$shopitem || !$server) {
            if ($show_alert) $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return FALSE;
        }

        $buy_user = User::find($user_id);
        if (!$buy_user) {
            if ($show_alert) $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return FALSE;
        }

        if (!str_contains($var_id, 'rnd') && $var_id > 0) {
            $variations = json_decode($shopitem->variations);
            foreach ($variations as $variation) {
                if ($variation->variation_id == $var_id) {
                    $var_name = $variation->variation_name;
                }
            }
        }

        //Check if the user exists, otherwise create a new user
        $password = $steam_id . '0kf7v6xi34';
        $user = User::where('steam_id', $steam_id)->first();
        if (!$user) {
            $user = new User;
            $user->steam_id = $steam_id;
            $user->password = Hash::make($password);
            $user->name = $steam_id;
            $user->avatar = NULL;
            $user->save();
        }

        $user_id = $user->id;

        //Сохраняем покупку в статистику магазина
        if ($save_statistics) {
            ShopStatistic::create([
                'item_id'  => $shopitem->id,
                'amount'   => $qty,
                'price'    => $shopitem->price * $qty,
                'user_id'  => $buy_user->id,
                'steam_id' => $steam_id,
                'server'   => $server_id,
            ]);
        }


        //Проверяем, это услуга и куплена в подарок - отправляем ркон команду в игру, иначе товар - кладем в корзину, а услугу добавляем в инвентарь и в корзину
        //Если сервер не MODDED 2X, то сразу отправляем ркон комманду
        if ( ($shopitem->is_command == 1 && $buy_user->steam_id !== $steam_id) || ($shopitem->is_command == 1 && !in_array($server_id, [8]))) {

            $command = str_replace('%steamid%', $steam_id, $shopitem->command);
            $command = str_replace('%var%', $var_id, $command);

            //Сохраняем покупку в бд, чтобы можно было получить из игры
            ServicePurchase::create([
                'user_id' => $user_id,
                'command' => $command,
                'server'  => $server->name,
            ]);

            if (!GameServer::transferServiceGameServer($command, $server_id)) {

                //Сохраняем покупку в бд, чтобы позже повторить, когда игрок зайдет в игру
                Shopping::create([
                    'user_id' => $user_id,
                    'command' => $command,
                    'server'  => $server_id,
                    'status'  => 0,
                ]);
            }

            //Записываем данные о Vip в бд
            $vip = Vip::where('user_id', $user->id)->where('server_id', $server_id)->where('service_name', $shopitem->name_en)->first();
            if (!$vip) {
                $vip = new Vip;
                $vip->user_id = $user->id;
                $vip->server_id = $server_id;
                $vip->service_name = $shopitem->name_en;
                $vip_date = date('Y-m-d H:i:s');
            } else {
                $vip_date = (strtotime($vip->date) < strtotime(date('Y-m-d H:i:s'))) ? date('Y-m-d H:i:s') : $vip->date;
            }

            $vip_date_new = strtotime($vip_date) + 60*60*24*abs(intval($var_id));
            $vip->date = date('Y-m-d H:i:s', $vip_date_new);
            $vip->save();

            $server_name = isset(getserver($server_id)->name) ? getserver($server_id)->name : $server_id;

            if (isset($var_name)) {
                Log::channel('paymentslog')->info('Robot: Player ' . $user->name . ' (' . $user->email . ') ' . 'successfully bought in the store Product ' . $shopitem->name_en . ' (item_id: ' . $shopitem->item_id . '), Variation ' . $var_name . ' in quantity ' . $shopitem->amount . '. Server: ' . $server_name . '. SteamID: ' . $steam_id . '. Command: ' . $command);
            } else {
                Log::channel('paymentslog')->info('Robot: Player ' . $user->name . ' (' . $user->email . ') ' . 'successfully bought in the store Product ' . $shopitem->name_en . ' (item_id: ' . $shopitem->item_id . ') in quantity ' . $shopitem->amount . '. Server: ' . $server_name . '. SteamID: ' . $steam_id . '. Command: ' . $command);
            }

            if ($show_alert) $this->alert('success', __('Товар успешно куплен! Он был отправлен вам в игру!'));
            return TRUE;

        }
        else {

            //Добавляем услугу в Инвентарь
            if ($shopitem->is_command == 1) {

                $inventory_item = [
                    'type'    => '1',
                    'image'   => $shopitem->image_url,
                    'item_id' => NULL,
                ];
                $inventory = Inventory::create([
                    'type'          => 5,
                    'item'          => json_encode($inventory_item),
                    'amount'        => 1,
                    'user_id'       => $buy_user->id,
                    'vip_period'    => 0,
                    'deposit_bonus' => 0,
                    'balance'       => 0,
                    'shop_item_id'  => $shopitem->id,
                    'variation_id'  => $var_id,
                ]);
            }


            //Добавляем купленный товар в корзину покупок пользователя
            $shopcart = ShopCart::where('user_id', $user_id)->first();
            if (!$shopcart) {
                $shopcart = new ShopCart;
                $shopcart->user_id = $user_id;
                $shopcart->total = 0;
                $shopcart->items_index = 1;
                $items = [];
            } else {
                $items = json_decode($shopcart->items);
            }

            //Получаем случайное кол-во предмета
            if ($var_id !== NULL && str_contains($var_id, 'rnd')) {
                $var_amount = str_replace('rnd=', '', $var_id);
                if ($var_amount > 0) {
                    $shopitem->amount = $var_amount;
                }
            }

            // Используем цену покупки, если передана, иначе базовую цену
            $item_price = ($paid_price !== null) ? ($paid_price / $qty) : $shopitem->price;

            for($i=0; $i<$qty; $i++) {
                $items[] = [
                    'id'        => $shopcart->items_index,
                    'price'     => $item_price,
                    'item_id'   => $shopitem->id,
                    'rust_id'   => $shopitem->item_id,
                    'var_id'    => $var_id,
                    'wipe_block' => $shopitem->wipe_block,
                    'quantity'  => $shopitem->amount,
                    'steam_id'  => $steam_id,
                    'server_id' => $server->name,
                ];
                $shopcart->items_index += 1;
            }

            $shopcart->items = json_encode($items);
            $shopcart->total += 1;
            $shopcart->save();

            $server_name = isset(getserver($server_id)->name) ? getserver($server_id)->name : $server_id;

            if (isset($var_name)) {
                Log::channel('paymentslog')->info('Robot: Player ' . $user->name . ' (' . $user->email . ') ' . 'successfully bought in the store Product ' . $shopitem->name_en . ' (item_id: ' . $shopitem->item_id . '), Variation ' . $var_name . ' in quantity ' . $qty . '. Server: ' . $server_name . '. SteamID: ' . $steam_id);
            } else {
                Log::channel('paymentslog')->info('Robot: Player ' . $user->name . ' (' . $user->email . ') ' . 'successfully bought in the store Product ' . $shopitem->name_en . ' (item_id: ' . $shopitem->item_id . ') in quantity ' . $qty . '. Server: ' . $server_name . '. SteamID: ' . $steam_id);
            }

            if ($show_alert) $this->alert('success', __('Товар успешно куплен! Проверьте свою корзину покупок.'));
            return TRUE;
        }

        if ($show_alert) $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
        return FALSE;
    }

    public function buy_set_ajax(Request $request)
    {
        try {
            if (!auth()->check()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Необходимо авторизоваться')
                ], 401);
            }

            $validator = $this->validatorSet($request->all());

            if ($validator->fails()) {
                Log::channel('rcon_master')->warning('Shop buy_set_ajax validation failed', [
                    'errors' => $validator->errors()->toArray(),
                    'request' => $request->except(['_token']),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first() ?: __('Произошла ошибка! Попробуйте позже.'),
                    'errors' => $validator->errors()
                ]);
            }

            $set_id = intval($request->set_id);
            $qty = abs(intval($request->qty));
            $steam_id = ($request->has('steam_id') && $request->steam_id != '') ? $request->steam_id : auth()->user()->steam_id;

            $lock = Cache::lock('shop_buy_set'.auth()->id().$set_id.'_lock', 30);

            if (!$lock->get()) {
                return response()->json([
                    'success' => false,
                    'message' => __('Запрос уже обрабатывается, подождите.')
                ]);
            }

            $server_id = $request->server_id;
            $shopset = ShopSet::where('id', $set_id)->where(function ($query) use ($server_id) {
                return $query->where('server', $server_id)
                    ->orWhere('server', 0)
                    ->orWhere('servers', 'LIKE', '%"' . $server_id . '"%');
            })->where('status', 1)->first();

            if (!$shopset) {
                $lock->release();
                return response()->json([
                    'success' => false,
                    'message' => __('Сет не найден')
                ]);
            }

            // Проверяем подарок
            if ($shopset->can_gift !== 1 && $request->has('steam_id') && $request->steam_id != '') {
                $lock->release();
                return response()->json([
                    'success' => false,
                    'message' => __('Этот сет нельзя подарить')
                ]);
            }

            // Рассчитываем цену со скидкой
            $priceData = getShopSetPrice($shopset, 'rub');
            $price = $qty * $priceData['discount_price'];

            // Проверяем баланс
            $balance = floatval(auth()->user()->balance);
            $priceFloat = floatval($price);

            if ($balance < $priceFloat) {
                $lock->release();
                return response()->json([
                    'success' => false,
                    'message' => __('Недостаточно средств на балансе'),
                    'need_balance' => true,
                    'price' => number_format($priceFloat, 2, ',', ' '),
                    'balance' => number_format($balance, 2, '.', '')
                ]);
            }

            // Сначала списываем баланс и освобождаем lock — RCON может быть медленным
            auth()->user()->decrement('balance', $priceFloat);
            $lock->release();

            // Отправляем сет (RCON — lock уже снят)
            if ($this->send_set(auth()->id(), $shopset->id, $steam_id, $server_id, $qty)) {
                auth()->user()->refresh();
                $newBalance = floatval(auth()->user()->balance);

                if (app()->getLocale() != 'ru') {
                    $formattedBalance = $newBalance / config('options.exchange_rate_usd', 70);
                } else {
                    $formattedBalance = $newBalance;
                }
                $formattedBalance = number_format($formattedBalance, 2, '.', ' ');

                if (app()->getLocale() == 'ru') {
                    $formattedBalance = $formattedBalance . ' руб.';
                } else {
                    $formattedBalance = '$' . $formattedBalance;
                }

                $name = "name_" . app()->getLocale();
                return response()->json([
                    'success' => true,
                    'message' => __('Товар успешно куплен!'),
                    'item_name' => $shopset->$name,
                    'new_balance' => $formattedBalance
                ]);
            }

            // Ошибка отправки — возвращаем средства
            auth()->user()->increment('balance', $priceFloat);
            return response()->json([
                'success' => false,
                'message' => __('Произошла ошибка! Попробуйте позже.')
            ]);
        } catch (\Exception $e) {
            Log::channel('rcon_master')->error('Shop buy_set_ajax exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->except(['_token']),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Ошибка: ' . $e->getMessage()
            ], 500);
        }
    }

    public function buy_set(Request $request)
    {
        $validator = $this->validatorSet($request->all());

        if ($validator->fails()) {
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }

        $set_id = intval($request->set_id);
        $qty = abs(intval($request->qty));
        $steam_id = ($request->has('steam_id') && $request->steam_id != '') ? $request->steam_id : auth()->user()->steam_id;

        //Записываем блок в кеш
        $lock = Cache::lock('shop_buy_set'.auth()->id().$set_id.'_lock', 30);
        if ($lock->get()) {

            $server_id = $request->server_id;
            $shopset = ShopSet::where('id', $set_id)->where(function ($query) use ($server_id) {
                return $query->where('server', $server_id)
                    ->orWhere('server', 0)
                    ->orWhere('servers', 'LIKE', '%"' . $server_id . '"%');
            })->where('status', 1)->first();

            if (!$shopset) {
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                return back();
            }

            //Проверяем, если товар нельзя купить в подарок, то steam_id должен совпадать с steam_id пользователя
            if ($shopset->can_gift !== 1 && $request->has('steam_id') && $request->steam_id != '') {
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                return back();
            }

            // Рассчитываем цену со скидкой
            $currency = (app()->getLocale() == 'ru') ? 'rub' : 'usd';
            $priceData = getShopSetPrice($shopset, $currency);
            $price = $qty * $priceData['discount_price'];

            // Для проверки баланса используем цену в рублях
            $priceRub = (app()->getLocale() == 'ru') ? $price : ($price * config('options.exchange_rate_usd', 70));

            if (auth()->user()->balance < $priceRub) {
                $lock->release();
                $this->alert('danger', __('Недостаточно средств на балансе'));
                return back();
            }

            // Сначала списываем баланс и освобождаем lock — RCON может быть медленным
            auth()->user()->decrement('balance', $priceRub);
            $lock->release();

            // Отправляем сет (RCON — lock уже снят)
            if ($this->send_set(auth()->id(), $shopset->id, $steam_id, $server_id, $qty)) {
                return back();
            }

            // Ошибка отправки — возвращаем средства
            auth()->user()->increment('balance', $priceRub);
        }
        $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
        return back();
    }

    public function send_set($user_id, $set_id, $steam_id='', $server_id=1, $qty=1, $save_statistics=true)
    {
        $shopset = ShopSet::where('id', $set_id)->where('status', 1)->first();
        $server = Server::where('id', $server_id)->where('status', 1)->first();

        if (!$shopset || !$server) {
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return FALSE;
        }

        $buy_user = User::find($user_id);
        if (!$buy_user) {
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return FALSE;
        }

        //Check if the user exists, otherwise create a new user
        $password = $steam_id . '0kf7v6xi34';
        $user = User::where('steam_id', $steam_id)->first();
        if (!$user) {
            $user = new User;
            $user->steam_id = $steam_id;
            $user->password = Hash::make($password);
            $user->name = $steam_id;
            $user->avatar = NULL;
            $user->save();
        }

        $user_id = $user->id;

        //Сохраняем покупку в статистику магазина
        if ($save_statistics) {
            ShopStatistic::create([
                'set_id'   => $shopset->id,
                'amount'   => $qty,
                'price'    => $shopset->price * $qty,
                'user_id'  => $buy_user->id,
                'steam_id' => $steam_id,
                'server'   => $server_id,
            ]);
        }

        $items = $shopset->items !== null ? json_decode($shopset->items) : [];
        foreach ($items as $item) {

            $shopitem = ShopItem::find($item->id);
            if (!$shopitem) continue;

            $shopitem->amount = $item->amount;

            //Добавляем купленный товар в корзину покупок пользователя
            $shopcart = ShopCart::where('user_id', $user_id)->first();
            if (!$shopcart) {
                $shopcart = new ShopCart;
                $shopcart->user_id = $user_id;
                $shopcart->total = 0;
                $shopcart->items_index = 1;
                $items = [];
            } else {
                $items = json_decode($shopcart->items);
            }

            for ($i = 0; $i < $qty; $i++) {
                $items[] = [
                    'id'         => $shopcart->items_index,
                    'price'      => $shopitem->price,
                    'item_id'    => $shopitem->id,
                    'rust_id'    => $shopitem->item_id,
                    'var_id'     => 0,
                    'wipe_block' => $shopitem->wipe_block,
                    'quantity'   => $shopitem->amount,
                    'steam_id'   => $steam_id,
                    'server_id'  => $server->name,
                ];
                $shopcart->items_index += 1;
            }

            $shopcart->items = json_encode($items);
            $shopcart->total += 1;
            $shopcart->save();
        }

        $server_name = isset(getserver($server_id)->name) ? getserver($server_id)->name : $server_id;
        Log::channel('paymentslog')->info('Robot: Player ' . $user->name . ' (' . $user->email . ') ' . 'successfully bought in the store Set ' . $shopset->name_en . ' (set_id: ' . $shopset->id . ') in quantity ' . $qty . '. Server: ' . $server_name . '. SteamID: ' . $steam_id);

        $this->alert('success', __('Сет успешно куплен! Проверьте свою корзину покупок.'));
        return true;
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'server_id'  => ['required', 'string', 'max:20'],
            'item_id'    => ['required', 'string', 'max:20'],
            'var_id'     => ['required', 'string', 'max:20'],
            'payment_id' => ['required', 'string', 'max:10'],
            'qty'     => ['required', 'string', 'max:10'],
            'steam_id'   => ['nullable', 'string', 'max:20'],
            //'term'       => ['accepted'],
            //'policy'     => ['accepted'],
        ], [
            'term.accepted'   => __('Вы должны принять Условия Обслуживания.'),
            'policy.accepted' => __('Вы должны принять Соглашение о Политике.'),
        ]);
    }
    protected function validatorSet(array $data)
    {
        return Validator::make($data, [
            'server_id'  => ['required', 'string', 'max:20'],
            'set_id'     => ['required', 'string', 'max:20'],
            'payment_id' => ['required', 'string', 'max:10'],
            'qty'        => ['required', 'string', 'max:10'],
            'steam_id'   => ['nullable', 'string', 'max:20'],
        ]);
    }

    /* PaymentsMethodTrait */
    //В трейте Методы оплаты с вызовом редиректа на оплату setPayment();
    // Везовы колбеков в \App\Http\Controllers\Api\PaymentsController
    //Редиректы после оплаты в \App\Http\Controllers\PaymentsController

    use \App\Traits\PaymentsMethodTrait;
}
