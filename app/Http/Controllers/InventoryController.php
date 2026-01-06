<?php

namespace App\Http\Controllers;

use App\Models\Cases;
use App\Models\ShopItem;
use App\Models\ShopCart;
use App\Models\User;
use App\Models\Inventory;
use App\Models\DeliveryRequest;
use App\Models\Vip;
use App\Models\Server;
use App\Models\Shopping;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Lib\WaxpeerAPI;
use Illuminate\Support\Facades\Session;
use GameServer;

class InventoryController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index() {
        //
    }

    public function sell(Request $request)
    {
        if (!$request->has('inventory_id')) {
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }

        //Записываем блок в кеш
        $lock = Cache::lock('inventory_sell'.auth()->id().'_lock', 5);
        if ($lock->get()) {

            $inventory = Inventory::where('user_id', auth()->user()->id)->where('id', $request->inventory_id)->where('type', 0)->where('amount', '>', 0)->first();
            if (!$inventory || $inventory->item === NULL) {
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                return back();
            }

            $item = json_decode($inventory->item);
            if(!isset($item->item_id)) {
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                return back();
            }

            $item_name = get_skin($item->item_id)->name;
            $item_price = get_skin($item->item_id)->price;
            $item_price_usd = get_skin($item->item_id)->price_usd;
            if ($item_price < 0 || $item_price_usd < 0) {
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                return back();
            }

            //Начисляем на баланс пользователя
            if (app()->getLocale() == 'ru') {
                $balance = auth()->user()->balance;
                $balance_new = $balance + abs(floatval($item_price));
            } else {
                $balance = auth()->user()->balance / config('options.exchange_rate_usd', 70);
                $balance_new = $balance + abs(floatval($item_price_usd));
                $balance_new = $balance_new * config('options.exchange_rate_usd', 70);
            }
            auth()->user()->balance = $balance_new;
            auth()->user()->save();


            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . 'successfully sell item from inventory: ' . $item_name . ' Price: ' . $item_price);

            $inventory->delete();

            $this->alert('success', __('Вы успешно продали') . ' ' . $item_name . '.');

            $lock->release();
            return back();

        }
        else {
            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' failed to sell item from inventory : ' . $request->inventory_id . ' due to blocking.');
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }
    }

    public function send(Request $request)
    {
        if (!$request->has('inventory_id')) {
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }

        //Записываем блок в кеш
        $lock = Cache::lock('inventory_send'.auth()->id().'_lock', 5);
        if ($lock->get()) {

            $inventory = Inventory::where('user_id', auth()->user()->id)->where('id', $request->inventory_id)->where('type', 0)->where('amount', '>', 0)->first();
            if (!$inventory || $inventory->item === NULL) {
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                return back();
            }

            $item = json_decode($inventory->item);
            if(!isset($item->item_id)) {
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                return back();
            }

            $item_name = get_skin($item->item_id)->name;
            $item_price = get_skin($item->item_id)->price;

            //Получаем диапазон цены с WaxpeerAPI
            $price_range = WaxpeerAPI::getItemRangePriceByName('rust', $item_name);

            //Создаем задачу на доставку
            DeliveryRequest::create([
                'user_id' => auth()->id(),
                'item_id' => $item->item_id,
                'item' => $item_name,
                'item_icon' => getImageUrl($item->image),
                'item_type' => $item->type,
                'amount' => $inventory->amount,
                'price' => abs($item_price),
                'price_min' => abs($price_range['min']),
                'price_max' => abs($price_range['max']),
                'price_cap' => abs($price_range['min']),
                'server' => 'Inventory',
                'status' => 8, //SkinsbackAPI В обработке
                'date_request' => date('Y-m-d H:i:s'),
            ]);

            $inventory->delete();

            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' success to apply for the withdrawal of the item: ' . $inventory->id . '.');
            $this->alert('success', __('Вы успешно подали заявку на вывод предмета') . ' ' . $item_name . '.');

            $lock->release();
            return back();

        }
        else {
            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' failed to apply for the withdrawal of the item: ' . $request->inventory_id . ' due to blocking.');
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }
    }

    public function activate(Request $request)
    {
        if (!$request->has('inventory_id') || !$request->has('server_id')) {
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }
        $server_id = $request->server_id;

        //Записываем блок в кеш
        $lock = Cache::lock('inventory_activate'.auth()->id().'_lock', 5);
        if ($lock->get()) {

            $inventory = Inventory::where('user_id', auth()->user()->id)->where('id', $request->inventory_id)->where('type', 1)->where('amount', '>', 0)->first();
            if (!$inventory || $inventory->item === NULL) {
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                return back();
            }

            $item = json_decode($inventory->item);
            if(!isset($item->type)) {
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                return back();
            }

            list($price, $price_usd, $name) = get_coupon_price($inventory->type, $inventory->vip_period);
            if ($price < 0 || $price_usd < 0) {
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                return back();
            }

            //Начисляем VIP пользователю
            $command = 'addgroup '. auth()->user()->steam_id.' vip ' . $inventory->vip_period . 'd';

            if (!GameServer::transferServiceGameServer($command, $server_id)) {
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                return back();
            }

            //Записываем данные о Vip в бд
            $vip = Vip::where('user_id', auth()->id())->where('server_id', $server_id)->first();
            if (!$vip) {
                $vip = new Vip;
                $vip->user_id = auth()->id();
                $vip->server_id = $server_id;
                $vip_date = date('Y-m-d H:i:s');
            } else {
                $vip_date = (strtotime($vip->date) < strtotime(date('Y-m-d H:i:s'))) ? date('Y-m-d H:i:s') : $vip->date;
            }

            $vip_date_new = strtotime($vip_date) + 60*60*24*abs(intval($inventory->vip_period));
            $vip->date = date('Y-m-d H:i:s', $vip_date_new);
            $vip->save();

            $inventory->delete();

            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' success to activate VIP from inventory : ' . $inventory->id . '.');
            $this->alert('success', __('Вы успешно активировали') . ' VIP.');

            $lock->release();
            return back();

        }
        else {
            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' failed to activate VIP from inventory : ' . $request->inventory_id . ' due to blocking.');
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }

    }

    public function apply(Request $request)
    {
        if (!$request->has('inventory_id')) {
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }

        //Записываем блок в кеш
        $lock = Cache::lock('inventory_apply'.auth()->id().'_lock', 5);
        if ($lock->get()) {

            $inventory = Inventory::where('user_id', auth()->user()->id)->where('id', $request->inventory_id)->whereIn('type', ['2', '3'])->where('amount', '>', 0)->first();
            if (!$inventory || $inventory->item === NULL) {
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                return back();
            }

            $item = json_decode($inventory->item);
            if(!isset($item->type)) {
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                return back();
            }

            if ($inventory->type == 2) {
                list($price, $price_usd, $name) = get_coupon_price($inventory->type, $inventory->deposit_bonus);
            } else {
                list($price, $price_usd, $name) = get_coupon_price($inventory->type, $inventory->balance);
            }


            //Начисляем пользователю
            if ($inventory->type == 2) {
                list($price, $price_usd, $name) = get_coupon_price($inventory->type, $inventory->deposit_bonus);

                Session::put('deposit_bonus', $inventory->deposit_bonus);

                Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' successfully to apply coupon and received a deposit bonus for top up balance, +' . $inventory->deposit_bonus . '%');
                $this->alert('success', __('Купон успешно применен!') . ' ' . __('Вы получите бонус при пополнении баланса') . ' +' . $inventory->deposit_bonus . '%');

                $inventory->delete();

                $lock->release();
                return redirect()->route('account.profile', 'topup=1');

            } elseif ($inventory->type == 3) {

                //Начисляем на баланс пользователя
                $inventory_balance = $inventory->balance * config('options.exchange_rate_usd', 70);
                auth()->user()->increment('balance', $inventory_balance);


                Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' successfully to apply coupon and received a balance top up in the amount of $' . $inventory->balance);
                $this->alert('success', __('Купон успешно применен!') . ' ' . __('Ваш баланс был пополнен на сумму') . ' $' . $inventory->balance);

                $inventory->delete();

                $lock->release();
                return back();
            }

            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();

        }
        else {
            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' failed to apply coupon from inventory : ' . $request->inventory_id. ' due to blocking.');
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }
    }

    public function activateShopItem(Request $request)
    {
        if (!$request->has('inventory_id') || !$request->has('server_id')) {
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже 1.'));
            return back();
        }
        $server_id = $request->server_id;

        //Записываем блок в кеш
        $lock = Cache::lock('inventory_activate'.auth()->id().'_lock', 5);
        if ($lock->get()) {

            $inventory = Inventory::where('user_id', auth()->user()->id)->where('id', $request->inventory_id)->where('type', 5)->where('amount', '>', 0)->first();
            if (!$inventory) {
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                return back();
            }

            $shopitem = ShopItem::where('id', $inventory->shop_item_id)->where('status', 1)->first();
            if (!$shopitem) {
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                return back();
            }

            //Удаляем этот товар-услугу из корзины покупок пользователя
            $shopcart = ShopCart::where('user_id', auth()->id())->first();
            if ($shopcart) {
                $items_new = [];
                if ($shopcart->items !== NULL) {
                    $items = json_decode($shopcart->items);
                    foreach ($items as $item) {
                        if ($item->item_id !== $shopitem->id || $item->var_id !== $inventory->variation_id || $item->steam_id !== auth()->user()->steam_id) {
                            $items_new[] = $item;
                        }
                    }
                    $shopcart->items = json_encode($items_new);
                    $shopcart->save();
                }
            }

            //Начисляем команду пользователю
            $command = str_replace('%steamid%', auth()->user()->steam_id, $shopitem->command);
            $command = str_replace('%var%', $inventory->variation_id, $command);

            if (!GameServer::transferServiceGameServer($command, $server_id)) {

                //Сохраняем покупку в бд, чтобы позже повторить, когда игрок зайдет в игру
                Shopping::create([
                    'user_id' => auth()->id(),
                    'command' => $command,
                    'server'  => $server_id,
                    'status'  => 0,
                ]);
            }

            //Записываем данные о Vip в бд
            $vip = Vip::where('user_id', auth()->id())->where('server_id', $server_id)->where('service_name', $shopitem->name_en)->first();
            if (!$vip) {
                $vip = new Vip;
                $vip->user_id = auth()->id();
                $vip->server_id = $server_id;
                $vip->service_name = $shopitem->name_en;
                $vip_date = date('Y-m-d H:i:s');
            } else {
                $vip_date = (strtotime($vip->date) < strtotime(date('Y-m-d H:i:s'))) ? date('Y-m-d H:i:s') : $vip->date;
            }

            $vip_date_new = strtotime($vip_date) + 60*60*24*abs(intval($inventory->variation_id));
            $vip->date = date('Y-m-d H:i:s', $vip_date_new);
            $vip->save();

            $inventory->delete();

            $name = 'name_' . app()->getLocale();
            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' success to activate ShopItem from inventory : ' . $inventory->id . '.');
            $this->alert('success', __('Вы успешно активировали') . ' ' . $shopitem->$name . '.');

            $lock->release();
            return back();

        }
        else {
            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' failed to activate ShopItem from inventory : ' . $request->inventory_id . ' due to blocking.');
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }

    }

    public function sendShopItem(Request $request)
    {
        if (!$request->has('inventory_id') || !$request->has('server_id')) {
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже 1.'));
            return back();
        }
        $server_id = $request->server_id;
        $server = Server::where('id', $server_id)->where('status', 1)->first();
        if (!$server) {
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже 2.'));
            return back();
        }

        //Записываем блок в кеш
        $lock = Cache::lock('inventory_activate'.auth()->id().'_lock', 5);
        if ($lock->get()) {

            $inventory = Inventory::where('user_id', auth()->user()->id)->where('id', $request->inventory_id)->where('type', 5)->where('amount', '>', 0)->first();
            if (!$inventory || $inventory->item === NULL) {
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже 3.'));
                return back();
            }

            //$shopitem = ShopItem::where('id', $inventory->shop_item_id)->where('status', 1)->first();
            $shopitem = ShopItem::where('id', $inventory->shop_item_id)->where('status', 0)->first();
            if (!$shopitem) {
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже 4.'));
                return back();
            }

            //Проверяем, что товар - кладем в корзину
            if ($shopitem->is_item == 1) {

                //Добавляем купленный товар в корзину покупок пользователя
                $shopcart = ShopCart::where('user_id', auth()->id())->first();
                if (!$shopcart) {
                    $shopcart = new ShopCart;
                    $shopcart->user_id = auth()->id();
                    $shopcart->total = 0;
                    $shopcart->items_index = 1;
                    $items = [];
                } else {
                    $items = json_decode($shopcart->items);
                }

                $items[] = [
                    'id'        => $shopcart->items_index,
                    'price'     => $shopitem->price,
                    'item_id'   => $shopitem->id,
                    'rust_id'   => $shopitem->item_id,
                    'var_id'    => 0,
                    'quantity'  => $shopitem->amount,
                    'steam_id'  => auth()->user()->steam_id,
                    'server_id' => $server->name,
                ];

                $shopcart->items = json_encode($items);
                $shopcart->total += 1;
                $shopcart->items_index += 1;
                $shopcart->save();

                $inventory->delete();

                $name = 'name_' . app()->getLocale();
                Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' success to sent ShopItem from inventory : ' . $inventory->id . '.');
                $this->alert('success', __('Вы успешно отправили') . ' ' . $shopitem->$name . '.' . __('в игру'));

                $lock->release();
                return back();
            }
            else {
                Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' failed to sent VIP from inventory : ' . $request->inventory_id);
                $this->alert('danger', __('Произошла ошибка! Попробуйте позже 5.'));
                return back();
            }

        }
        else {
            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' failed to sent VIP from inventory : ' . $request->inventory_id . ' due to blocking.');
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже 6.'));
            return back();
        }

    }

    //type: 0-Items from cases, 1-VIP, 2-Balance bonus, 3-Bonus case online time, 4-case, 5-ShopItems
    public function getItemsList() {
        return Inventory::where('user_id', auth()->id())->where('type', 0)->get();
    }

    public function getShopItemsList() {
        return Inventory::where('user_id', auth()->id())->where('type', 5)->get();
    }

    public function getServicesListOLD() {
        return DB::table('inventories')
            ->join('shop_items', 'inventories.shop_item_id', '=', 'shop_items.id')
            ->where('user_id', auth()->id())
            ->where('type', 1)
            ->get();
    }

    public function getServicesList() {
        return Inventory::where('user_id', auth()->id())->where('type', 1)->get();
    }

    public function getDepositBonusCouponsList() {
        return Inventory::where('user_id', auth()->id())->where('type', 2)->get();
    }

    public function getBalanceCouponsList() {
        return Inventory::where('user_id', auth()->id())->where('type', 3)->get();
    }

    public function getCasesList() {
        return DB::table('inventories')
            ->select('inventories.id as id', 'case_id', 'type', 'user_id', 'price', 'price_usd', 'image', 'title_en', 'title_ru', 'title_de', 'title_es', 'title_fr', 'title_it', 'title_uk')
            ->join('cases', 'inventories.case_id', '=', 'cases.id')
            ->where('user_id', auth()->id())
            ->where('type', 4)
            ->get();
    }

}