<?php

namespace App\Http\Controllers;

use App\Models\WonItem;
use App\Models\Vip;
use Illuminate\Http\Request;
use App\Http\Controllers\InventoryController;

class ProfileController extends Controller
{
    public function __construct()
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|Response
     */
    public function index()
    {
        $won_items = WonItem::where('user_id', auth()->user()->id)->get();

        $items = [];
        $bonus_items = [];
        for($i=0;$i<200;$i++) {
            if (config('options.bonus_pecul_'.$i.'_title', '') != '') {
                $bonus_items[] = (object) [
                    'id' => $i,
                    'context' => (object) [
                        'name' => config('options.bonus_pecul_'.$i.'_title', ''),
                        'icon' => '/storage/' . config('options.bonus_pecul_'.$i.'_image', ''),
                        'quality_type' => config('options.bonus_pecul_'.$i.'_description', 1),
                        'skin_id' => config('options.bonus_pecul_'.$i.'_title', ''),
                    ],
                ];
            }
        }

        foreach ($bonus_items as $bonus_item) {
            $bonus_item_find = FALSE;
            foreach ($won_items as $won_item) {
                if ($bonus_item->context->skin_id == $won_item->item_id) {
                    $bonus_item_find = TRUE;
                }
            }
            if ($bonus_item_find === FALSE) {
                $items[] = $bonus_item;
            }
        }

        $services = [];

        $services_str = config('options.shop_services_show', '');
        if ($services_str !== '') {
            $services_arr = explode(',', $services_str);
            if (is_array($services_arr) && !empty($services_arr)) {
                foreach ($services_arr as $service_arr) {
                    $services[$service_arr] = Vip::where('user_id', auth()->user()->id)->where('service_name', $service_arr)->get();
                }
            }

        }

        if (isset(auth()->user()->id) && auth()->user()->id == 497) {
            foreach ($services as $key => $service) {
                foreach ($service as $key => $serv) {
                    //dd($serv);
                }
            }
        }

        $vips = Vip::where('user_id', auth()->user()->id)->where('service_name', 'VIP')->get();

        //Получаем инвентарь
        $inventory = new InventoryController;

        //Предметы
        $inventory_items = $inventory->getItemsList();

        //Товары магазина
        $inventory_shopitems = $inventory->getShopItemsList();

        //Услуги
        $inventory_services = $inventory->getServicesList();

        //Купоны
        $inventory_deposit_bonus_coupons = $inventory->getDepositBonusCouponsList();
        $inventory_balance_coupons = $inventory->getBalanceCouponsList();

        //Кейсы
        $inventory_cases = $inventory->getCasesList();

        return view('pages.cabinet.profile', compact('items', 'inventory_items', 'inventory_shopitems', 'inventory_services', 'inventory_deposit_bonus_coupons', 'inventory_balance_coupons', 'inventory_cases', 'vips', 'services'));
    }

    public function setTradeUrl(Request $request)
    {
        if (!$request->has('steam_trade_url') || !is_string($request->steam_trade_url)) {
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }

        if (strpos($request->steam_trade_url, 'https://steamcommunity.com/tradeoffer') === FALSE || strlen($request->steam_trade_url) < 70) {
            $this->alert('danger', __('Произошла ошибка! Вы указали неверную ссылку.'));
            return back();
        }

        auth()->user()->steam_trade_url = $request->steam_trade_url;
        auth()->user()->save();

        $this->alert('success', __('Ссылка для обмена успешно сохранена!'));
        return back();
    }

    public function cabinet()
    {
        return redirect()->route('account.profile');
    }
}
