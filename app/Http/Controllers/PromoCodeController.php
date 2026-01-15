<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use App\Models\ShopCart;
use App\Models\ShopItem;
use App\Models\Vip;
use App\Models\Inventory;
use App\Models\Cases;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use GameServer;
use Illuminate\Support\Facades\Session;

class PromoCodeController extends Controller
{
    public function __construct()
    {
        //
    }

    public function index()
    {
        return view('pages.cabinet.promocode');
    }

    public function activate(Request $request)
    {
        $code = strval($request->input('code'));

        //Записываем блок в кеш
        $lock = Cache::lock('promocode_apply'.auth()->id().'_lock', 5);
        if ($lock->get()) {

            $promocode = PromoCode::where('code', $code)
                ->where('date_start', '<', date('Y-m-d H:i:s'))
                ->where('date_end', '>', date('Y-m-d H:i:s'))
                ->first();

            if (!$promocode) {
                $this->alert('danger', __('Неверный Промокод! Или срок кода истек!'));
                return back();
            }
            $used_users = json_decode($promocode->users);

            if ($promocode->type == 1) {
                if ($used_users !== NULL || !empty($used_users)) {
                    foreach ($used_users as $used_user) {
                        if ($used_user->user_id == auth()->user()->id) {
                            $this->alert('danger', __('Вы уже использовали этот Промокод!'));
                            return back();
                        }
                    }
                }
            } elseif ($promocode->type == 2) {
                if ($used_users !== NULL || !empty($used_users)) {
                    $this->alert('danger', __('Этот Промокод уже использовали!'));
                    return back();
                }
            }

            // Проверка на максимальное количество активаций
            if ($promocode->max_activations !== NULL && $promocode->max_activations > 0) {
                $current_activations = 0;
                if ($used_users !== NULL && !empty($used_users)) {
                    $current_activations = count($used_users);
                }

                if ($current_activations >= $promocode->max_activations) {
                    $this->alert('danger', __('Достигнуто максимальное количество активаций промокода!'));
                    $lock->release();
                    return back();
                }
            }

            //Сохраняем в промокод данные об активации
            $used_users_new = [];

            $user_id = auth()->user()->id;
            $used_users = json_decode($promocode->users);

            if ($used_users === NULL || empty($used_users)) {
                $used_users_new[] = [
                    'user_id'  => auth()->user()->id,
                    'date'  => date('Y-m-d H:i:s'),
                ];
            } else {
                $user_find = FALSE;

                if ($promocode->type == 3) {
                    $used_users_new = $used_users;
                } else {
                    foreach ($used_users as $used_user) {
                        if ($used_user->user_id == auth()->user()->id) {
                            $user_find = true;
                            $used_users_new[] = [
                                'user_id' => auth()->user()->id,
                                'date'    => date('Y-m-d H:i:s'),
                            ];
                        } else {
                            $used_users_new[] = $used_user;
                        }
                    }
                }

                if ($user_find === FALSE) {
                    $used_users_new[] = [
                        'user_id'  => auth()->user()->id,
                        'date'  => date('Y-m-d H:i:s'),
                    ];
                }
            }

            $promocode->users = json_encode($used_users_new);
            $promocode->save();


            //Выдаем пользователю награду
            //type_reward=1 - VIP, 2 - Бонус пополнения, 3 - Кейс, 5 - Товар магазина, 6 - % к пополению
            if ($promocode->type_reward == 1) {

                if (!isset($promocode->premium_period)) {
                    $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                    return back();
                }

                //Добавляем награду в инвентарь
                $inventory_item = [
                    'type' => 1,
                    'image' => 'images/vip.png',
                    'item_id' => NULL,
                ];
                $inventory = Inventory::create([
                    'type' => 1,
                    'item' => json_encode($inventory_item),
                    'amount' => 1,
                    'user_id' => auth()->id(),
                    'vip_period' => $promocode->premium_period,
                    'deposit_bonus' => 0,
                    'balance' => 0,
                ]);

                Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' successfully activated the Promo Code and got a VIP on ' . $promocode->premium_period . 'days.');
                $this->alert('success', __('Промокод успешно активирован!') . ' ' . __('Вы получили VIP на') . ' ' . $promocode->premium_period . __('дней'));

                $lock->release();
                return back();
            }
            else if ($promocode->type_reward == 2) {

                if ($promocode->bonus_amount <= 0) {
                    $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                    return back();
                }

                //Начисляем на баланс пользователя
                if (app()->getLocale() == 'ru') {
                    $bonus_amount = $promocode->bonus_amount * config('options.exchange_rate_usd', 70);
                } else {
                    $bonus_amount = $promocode->bonus_amount;
                }
                auth()->user()->increment('balance', $bonus_amount);


                Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' successfully activated the Promo Code and received a balance top up in the amount of $' . $promocode->bonus_amount);

                $this->alert('success', __('Промокод успешно активирован!') . ' ' . __('Ваш баланс был пополнен на сумму') . ' ' . $promocode->bonus_amount);

                $lock->release();
                return back();
            }
            else if ($promocode->type_reward == 3) {

                if ($promocode->bonus_case_id <= 0) {
                    $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                    return back();
                }

                $case_name = '';
                if ($promocode->bonus_case_id == 1) {
                    auth()->user()->increment('online_time', 60*60*100);
                    $case_name = __('Happy New Year');
                } else if ($promocode->bonus_case_id == 2) {
                    auth()->user()->increment('online_time_monday', 60*60*50);
                    $case_name = __('Monday');
                } else if ($promocode->bonus_case_id == 3) {
                    auth()->user()->increment('online_time_thursday', 60*60*75);
                    $case_name = __('Thursday');
                }

                Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' successfully activated the Promo Code and received it online time to open a case ' . $case_name);
                $this->alert('success', __('Промокод успешно активирован!') . ' ' . __('Вам был начислен 1 кейс') . ' ' . $case_name);

                $lock->release();
                return back();
            }
            else if ($promocode->type_reward == 4) {

                $case = Cases::where('id', $promocode->case_id)->first();
                if (!$case) {
                    $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                    return back();
                }

                //Добавляем кейс в инвентарь
                Inventory::create([
                    'type' => 4,
                    'case_id' => $promocode->case_id,
                    'amount' => 1,
                    'user_id' => auth()->id(),
                ]);

                Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' successfully activated the Promo Code and received a case ' . $case->title_en);
                $this->alert('success', __('Промокод успешно активирован!') . ' ' . __('Вам был начислен 1 кейс') . ' ' . $case->title_en);

                $lock->release();
                return back();
            }
            else if ($promocode->type_reward == 5) {

                $shopitem = ShopItem::where('id', $promocode->shop_item_id)->first();
                if (!$shopitem) {
                    $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                    return back();
                }

                //Добавляем товар в инвентарь
                Inventory::create([
                    'type' => 5,
                    'shop_item_id' => $promocode->shop_item_id,
                    'variation_id' => $promocode->variation_id,
                    'amount' => 1,
                    'user_id' => auth()->id(),
                ]);

                $name = "name_" . app()->getLocale();
                Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' successfully activated the Promo Code and received a Shop Item ' . $shopitem->name_en);
                $this->alert('success', __('Промокод успешно активирован!') . ' ' . __('Вам был начислен 1 товар') . ' ' . $shopitem->$name);

                $lock->release();
                return back();
            }
            else if ($promocode->type_reward == 6) {

                if ($promocode->bonus_amount <= 0) {
                    $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                    return back();
                }

                //Запоминаем % пополнения промокода
                session()->put('deposit_bonus', $promocode->bonus_amount);

                Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' successfully activated the Promo Code and received '.$promocode->bonus_amount.'% to replenish the balance.');
                $this->alert('success', __('Промокод успешно активирован!') . ' ' . __('Вы получите') . ' +'.$promocode->bonus_amount.'% ' .__('при пополнении баланса'));

                $lock->release();
                return redirect()->route('account.profile', ['topup' => 1]);
            }
            else if ($promocode->type_reward == 7) {
                if ($promocode->bonus_amount <= 0) {
                    $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                    return back();
                }
                if (app()->getLocale() == 'ru') {
                    $bonus_amount = $promocode->bonus_amount * config('options.exchange_rate_usd', 70);
                } else {
                    $bonus_amount = $promocode->bonus_amount;
                }
                //Начисляем на баланс пользователя
                auth()->user()->increment('balance', $promocode->bonus_amount);

                Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' successfully activated the Promo Code and received a balance top up in the amount of $' . $promocode->bonus_amount);
                $this->alert('success', __('Промокод успешно активирован!'));

                $lock->release();
                return redirect()->route('account.profile');
            }
        }
        else {
            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' could not activate promocode: ' . $request->input('code') . ' due to blocking.');
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }

        $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
        return back();
    }

}
