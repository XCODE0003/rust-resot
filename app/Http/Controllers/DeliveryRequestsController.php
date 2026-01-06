<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRequest;
use App\Models\Inventory;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
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
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function list()
    {
        $deliveryrequests = DeliveryRequest::query()->where('user_id', auth()->id());

        if (request()->has('status') && request()->query('status') >= 0) {
            $deliveryrequests->where('status', request()->query('status'));
        }

        $deliveryrequests = $deliveryrequests->latest('date_request')->paginate();

        return view('pages.cabinet.delivery_requests', compact('deliveryrequests'));
    }

    public function cancel(DeliveryRequest $deliveryrequest): RedirectResponse
    {
        if ($deliveryrequest->user_id != auth()->id()) {
            abort(404);
        }

        //Записываем блок в кеш
        $lock = Cache::lock('delivery_request'.auth()->id().$deliveryrequest->id.'_lock', 5);
        if ($lock->get()) {

            if ($deliveryrequest->status != 0) {
                $this->alert('danger', __('Произошла ошибка! Отменить заявку уже нельзя.'));
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

            Log::channel('paymentslog')->info("User: " . auth()->user()->name . ": Changed the request status. Request ID: ". $deliveryrequest->id ." New status: Canceled");
            $this->alert('success', __('Вы успешно отменили заявку на вывод предмета!'));

            $lock->release();
            return back();
        }
        else {
            Log::channel('paymentslog')->info('Robot: Player ' . auth()->user()->name . ' (' . auth()->user()->email . ') ' . ' could not cancel deliveryrequest: ' . $deliveryrequest->id . ' due to blocking.');
            $this->alert('danger', __('Произошла ошибка! Отменить заявку уже нельзя.'));
            return back();
        }

    }
}
