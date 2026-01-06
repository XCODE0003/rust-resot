<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use App\Models\ShopItem;
use App\Models\ShopPurchase;
use App\Models\Warehouse;
use App\Models\Reflink;
use App\Http\Requests\BalanceRequest;
use App\Http\Requests\ItemRequest;
use App\Http\Requests\MuteRequest;
use App\Http\Requests\ChangeUserPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use GameServer;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('can:admin');
    }

    public function index() {
        $users = User::query();

        $search = request()->query('search');
        if ($search) {
            $users->where('name', 'LIKE', "%{$search}%");
            $users->orWhere('email', 'LIKE', "%{$search}%");
            $users->orWhere('phone', 'LIKE', "%{$search}%");
            $users->orWhere('steam_id', 'LIKE', "%{$search}%");
        }

        $mute_status = request()->query('mute_status');
        if ($mute_status > 0) {
            $users->where('mute', $mute_status);
        }
        $role = request()->query('role');

        if ($role != '' && $role != 'all') {
            if ($role == 'analyst') $role = 'investor';
            $users->where('role', $role);
        }

        $users = $users->paginate();

        return view('backend.pages.users.list', compact('users'));
    }

    public function details(User $user) {

        $data = [];
        foreach(getservers() as $server) {
            $options = json_decode($server->options);

            $data[] = [
                "server" => $server,
                "accounts" => [],
                "characters" => [],
            ];
        }

        return view('backend.pages.users.info', compact('data', 'user'));
    }

    public function warehouse(User $user)
    {
        $items = Warehouse::where('user_id', $user->id)->where('amount', '>', 0)->get();
        return view('backend.pages.users.warehouse', compact('items', 'user'));
    }

    public function warehouse_update(Request $request)
    {
        $item_id = abs(intval($request->item_id));
        $quantity = abs(intval($request->item_quantity));
        $user_id = abs(intval($request->user_id));
        $server_id = abs(intval($request->server_id));

        $user = User::where('id', $user_id)->first();
        $warehouse = Warehouse::where('id', $item_id)->where('user_id', $user_id)->where('server', $server_id)->first();

        if (!$user || !$warehouse) {
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }

        $quantity_old = $warehouse->amount;
        $warehouse->amount = $quantity;

        if ($warehouse->save()) {
            $this->alert('success', __('Вы успешно изменили количество предмета на складе МА!'));
            Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Изменил количество предмета ID: " . $warehouse->type . " с ".$quantity_old." на ".$quantity." шт. для пользователя {$user->name} ({$user->email})");
            return back();
        }

        $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
        return back();
    }

    public function warehouse_delete(Request $request)
    {
        $item_id = abs(intval($request->item_id));
        $user_id = abs(intval($request->user_id));
        $server_id = abs(intval($request->server_id));

        $user = User::where('id', $user_id)->first();
        $warehouse = Warehouse::where('id', $item_id)->where('user_id', $user_id)->where('server', $server_id)->first();

        if (!$user || !$warehouse) {
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }

        $item_id_old = $warehouse->type;
        $quantity_old = $warehouse->amount;

        if ($warehouse->delete()) {
            $this->alert('success', __('Вы успешно удалили предмет со склада МА!'));
            Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Удалил со склада предмет ID: " . $item_id_old . " в количестве ".$quantity_old." шт. для пользователя {$user->name} ({$user->email})");
            return back();
        }

        $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
        return back();
    }

    public function admin(User $user): RedirectResponse
    {
        $user->role = 'admin';
        $user->save();
        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Appointed {$user->name} as administrator");
        $this->alert('success', __('Вы успешно назначили') . ' ' . $user->name . ' ' . __('администратором'));
        return back();
    }

    public function support(User $user): RedirectResponse
    {
        $user->role = 'support';
        $user->save();
        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Appointed {$user->name} support agent");
        $this->alert('success', __('Вы успешно назначили') . ' ' . $user->name . ' ' . __('агентом поддержки'));
        return back();
    }

    public function investor(User $user): RedirectResponse
    {
        $user->role = 'investor';
        $user->save();
        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Appointed {$user->name} as investor");
        $this->alert('success', __('Вы успешно назначили') . ' ' . $user->name . ' ' . __('доступ Инвестора'));
        return back();
    }

    public function user(User $user): RedirectResponse
    {
        $user->role = 'user';
        $user->save();
        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Appointed {$user->name} regular user");
        $this->alert('success', __('Вы успешно назначили') . ' ' . $user->name . ' ' . __('обычным пользователем'));
        return back();
    }

    public function setBalance(BalanceRequest $request): RedirectResponse
    {
        $user = User::where('id', $request->input('user_id'))->firstOrFail();
        $user->balance = $request->input('balance');
        $user->save();

        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Изменил баланс пользователя {$user->name} на " . $request->input('balance') . ' ' . __('руб.'));
        $this->alert('success', __('Вы успешно изменили баланс пользователю') . ' ' . $user->name . ' ' . __('на') . ' ' . $request->input('balance') . ' ' . __('руб.'));
        return back();
    }

    public function setItem(ItemRequest $request): RedirectResponse
    {
        $quantity = abs(intval($request->item_quantity));
        $shopitem = ShopItem::where('l2_id', abs(intval($request->item_id)))->first();

        $user = User::where('id', $request->input('user_id'))->first();
        if (!$user || !$shopitem) {
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }

        //Record data on the purchase of a one-time item
        if ($shopitem->purchase_type == '1') {
            $shop_purchases = ShopPurchase::where('item_id', $shopitem->id)->where('user_id', $user->id)->first();
            if (!$shop_purchases) {
                $shop_purchases = new ShopPurchase;
            } else {
                if (strtotime($shop_purchases->validity) > strtotime(date('Y-m-d H:i:s'))) {
                    $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
                    return back();
                }
            }
            $shop_purchases->item_id = $shopitem->id;
            $shop_purchases->user_id = $user->id;
            $shop_purchases->validity = date('Y-m-d H:i:s', strtotime('+' . $shopitem->use_time . ' day', strtotime(date('Y-m-d H:i:s'))));
            $shop_purchases->save();
        }

        //Check if the product is already there, then add the quantity to it
        $warehouse = Warehouse::where('type', $shopitem->l2_id)->where('user_id', $user->id)->where('server', 1)->first();
        if ($warehouse) {
            $warehouse->amount += $quantity;
        } else {

            //We add the goods to the warehouse
            $warehouse = new Warehouse;
            $warehouse->type = $shopitem->l2_id;
            $warehouse->user_id = $user->id;
            $warehouse->amount = $quantity;
            $warehouse->enchant = 0;
            $warehouse->intensive_item_type = 0;
            $warehouse->variation_opt2 = 0;
            $warehouse->variation_opt1 = 0;
            $warehouse->wished = 0;
            $warehouse->ident = 0;
            $warehouse->bless = 0;
            $warehouse->server = 1;
        }

        if ($warehouse->save()) {

            //Add buy_pack in user
            if ($user && in_array($shopitem->id, ['19','20','21'])) {
                $user->buy_pack = 1;
                $user->save();
            }

            $this->alert('success', __('Предмет успешно отправлен на склад МА!'));
            Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Добавил предмет " . $shopitem->title_en . " для пользователя {$user->name} ({$user->email}) в количестве " . $quantity);
            return back();
        }

        $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
        return back();
    }

    public function mute(MuteRequest $request): RedirectResponse
    {
        $user = User::where('id', $request->input('user_id'))->firstOrFail();

        switch ($request->input('mute_date')) {
            case 0:
                $mute_date = strtotime(date('Y-m-d H:s:i')) + 60*60*24;
                break;
            case 1:
                $mute_date = strtotime(date('Y-m-d H:s:i')) + 60*60*24*7;
                break;
            case 2:
                $mute_date = strtotime(date('Y-m-d H:s:i')) + 60*60*24*30;
                break;
            case 3:
                $mute_date = strtotime(date('Y-m-d H:s:i')) + 60*60*24*30*12*100;
                break;
            default:
                $mute_date = strtotime(date('Y-m-d H:s:i')) + 60*60*24;
        }

        $mute_date = date('Y-m-d H:s:i', $mute_date);

        $user->mute = 1;
        $user->mute_reason = $request->input('mute_reason');
        $user->mute_date = $mute_date;
        $user->save();

        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Выдал мут пользователю {$user->name} на срок до {$mute_date} с причиной {$request->input('mute_reason')}");

        $this->alert('success', __('Вы успешно выдали мут пользователю') . ' ' . $user->name . ' ' . __('на срок до') . ' ' . $mute_date . ' ' . __('с причиной') . ' ' . $request->input('mute_reason') . '.');
        return back();
    }

    public function unmute(User $user): RedirectResponse
    {
        $user->mute = 0;
        $user->mute_reason = '';
        $user->mute_date = NULL;
        $user->save();

        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Снял мут с пользователя {$user->name}.");

        $this->alert('success', __('Вы успешно сняли мут с пользователя') . ' ' . $user->name . '.');
        return back();
    }

    public function backend_change_password(ChangeUserPasswordRequest $request): RedirectResponse
    {
        $user = User::where('id', $request->input('user_id'))->first();

        if (!$user) {
            $this->alert('danger', __('Ошибка! Повторите позже!'));
            return back();
        }

        //Делаем проверку, что в пароле используются только латинские буквы
        $chr_en = "a-zA-Z0-9\s`~!@#$%^&*()_+-={}|:;<>?,.\/\"\'\\\[\]";
        if (!preg_match("/^[$chr_en]+$/", $request->input('new_password'))) {
            $this->alert('danger', __('Ошибка! Используйте только латинские буквы в пароле!'));
            return back();
        }

        $new_password = Hash::make($request->input('new_password'));

        //Устанавливаем новый пароль
        $user->password = $new_password;
        $user->save();

        $this->alert('success', __('Вы успешно сменили пароль Мастер Аккаунта: ') . $request->input('login'));

        return back();
    }

    public function getUserByName(Request $request)
    {
        $users = User::where('name', 'LIKE', "%{$request->user_name}%")->get();

        return response()->json([
            'status' => 'success',
            'users' => $users,
        ]);
    }

}
