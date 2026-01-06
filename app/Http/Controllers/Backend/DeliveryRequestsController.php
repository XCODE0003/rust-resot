<?php

namespace App\Http\Controllers\Backend;

use App\Models\DeliveryRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Inventory;
use App\Models\CasesItem;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DeliveryRequestsController extends Controller
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

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $deliveryrequests = DeliveryRequest::query();

        if (request()->has('status') && request()->query('status') >= 0) {
            $deliveryrequests->where('status', request()->query('status'));
        }

        $casesitems = CasesItem::query();
        $users = User::query();
        $search = request()->query('search');
        if (request()->has('search') && is_string($search)) {
            $casesitems->where('title', 'LIKE', "%{$search}%")
                ->orWhere('item_id', 'LIKE', "%{$search}%")->get();

            $users->where('name', 'LIKE', "%{$search}%")->get();

            $deliveryrequests->whereIn('item_id', $casesitems->pluck('item_id'))
                ->orWhereIn('user_id', $users->pluck('id'));
        }

        $deliveryrequests = $deliveryrequests->latest('date_request')->paginate();

        return view('backend.pages.delivery_requests.index', compact('deliveryrequests'));
    }

    public function setStatusInprocessing(DeliveryRequest $deliveryrequest): RedirectResponse
    {
        if ($deliveryrequest->status != 0) {
            $this->alert('danger', __('Нельзя изменить статус заявки в текущем статусе.'));
            return back();
        }

        $deliveryrequest->status = 1;
        $deliveryrequest->save();

        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Changed the request status. Request ID: ". $deliveryrequest->id ." New status: In processing");
        $this->alert('success', __('Вы успешно изменили статус заявки на') . ' ' . __('В обработке'));
        return back();
    }

    public function setPricecap(Request $request, DeliveryRequest $deliveryrequest)
    {
        if ($request->price_cap <= 0) {
            $this->alert('danger', __('Произошла ошибка! Попробуйте позже.'));
            return back();
        }

        $deliveryrequest->price_cap = $request->price_cap;
        $deliveryrequest->save();

        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Changed the request status. Request ID: ". $deliveryrequest->id ." New status: In processing");
        $this->alert('success', __('Вы успешно изменили статус заявки на') . ' ' . __('В обработке'));
        return back();
    }

    public function setStatusWaxpeerAPI(DeliveryRequest $deliveryrequest): RedirectResponse
    {
        if ($deliveryrequest->status != 0 && $deliveryrequest->status != 1) {
            $this->alert('danger', __('Нельзя отменить заявку в текущем статусе.'));
            return back();
        }

        $deliveryrequest->status = 4;
        $deliveryrequest->save();

        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Changed the request status. Request ID: ". $deliveryrequest->id ." New status: Waxpeer API In processing");
        $this->alert('success', __('Вы успешно изменили статус заявки на') . ' ' . __('WaxpeerAPI') . __('В обработке'));
        return back();
    }

    public function setStatusSkinsbackAPI(DeliveryRequest $deliveryrequest): RedirectResponse
    {
        if ($deliveryrequest->status != 0 && $deliveryrequest->status != 1) {
            $this->alert('danger', __('Нельзя отменить заявку в текущем статусе.'));
            return back();
        }

        $deliveryrequest->status = 8;
        $deliveryrequest->save();

        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Changed the request status. Request ID: ". $deliveryrequest->id ." New status: Skinsback API In processing");
        $this->alert('success', __('Вы успешно изменили статус заявки на') . ' ' . __('SkinsbackAPI') . __('В обработке'));
        return back();
    }

    public function setStatusCompleted(DeliveryRequest $deliveryrequest): RedirectResponse
    {
        if ($deliveryrequest->status != 0 && $deliveryrequest->status != 1 && $deliveryrequest->status != 12) {
            $this->alert('danger', __('Нельзя изменить статус заявки в текущем статусе.'));
            return back();
        }

        $deliveryrequest->status = 2;
        $deliveryrequest->date_execution = date('Y-m-d H:i:s');
        $deliveryrequest->save();

        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Changed the request status. Request ID: ". $deliveryrequest->id ." New status: Completed");
        $this->alert('success', __('Вы успешно изменили статус заявки на') . ' ' . __('Выполнена'));
        return back();
    }

    public function setStatusCanceled(DeliveryRequest $deliveryrequest): RedirectResponse
    {
        if (in_array($deliveryrequest->status, ['3', '5', '6', '8', '9'])) {
            $this->alert('danger', __('Нельзя отменить заявку в текущем статусе.'));
            return back();
        }

        $deliveryrequest->status = 3;
        $deliveryrequest->save();

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

        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Changed the request status. Request ID: ". $deliveryrequest->id ." New status: Canceled");
        $this->alert('success', __('Вы успешно изменили статус заявки на') . ' ' . __('Отменена'));
        return back();
    }
}
