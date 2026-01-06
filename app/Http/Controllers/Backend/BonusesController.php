<?php

namespace App\Http\Controllers\Backend;

use App\Models\WonItem;
use App\Models\CasesItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class BonusesController extends Controller
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
    public function index(Request $request)
    {
        $wonitems = WonItem::query()->where('issued', 0);

        $casesitems = CasesItem::query();
        $search = request()->query('search');
        if (request()->has('search') && is_string($search)) {
            $casesitems->where('title', 'LIKE', "%{$search}%")
                ->orWhere('item_id', 'LIKE', "%{$search}%")->get();
            $wonitems->whereIn('item_id', $casesitems->pluck('item_id'));
        }

        $wonitems = $wonitems->latest()->paginate();

        return view('backend.pages.wonitems.index', compact('wonitems'));
    }

    public function issued(WonItem $wonitem)
    {
        $wonitem->issued = 1;
        $wonitem->save();

        $this->alert('success', __('Вы успешно отметили выигрыш как выданный.'));
        return back();
    }

}
