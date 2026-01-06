<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\CasesItemRequest;
use App\Models\CasesItem;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CasesItemController extends Controller
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
        $casesitems = CasesItem::query();

        $search = request()->query('search');
        if (request()->has('search') && is_string($search)) {
            $casesitems->where('title', 'LIKE', "%{$search}%")
            ->orWhere('item_id', 'LIKE', "%{$search}%");
        }

        $category_id = $request->has('category_id') ? $request->get('category_id') : '0';
        if ($category_id > 0) {
            $casesitems->where('category_id', $category_id);
        }

        $sort_price = request()->query('sort_price');
        if (request()->has('sort_price') && is_string($sort_price)) {
            if ($sort_price == 'asc') {
                $casesitems = $casesitems->orderBy('price')->paginate();
            } else if ($sort_price == 'desc') {
                $casesitems = $casesitems->orderByDesc('price')->paginate();
            } else {
                $casesitems = $casesitems->orderBy('item_id')->paginate();
            }
        } else {
            $casesitems = $casesitems->orderBy('item_id')->paginate();
        }

        return view('backend.pages.cases.items.index', compact('casesitems', 'category_id'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.pages.cases.items.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CasesItemRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['image'] = $request->image->store('images', 'public');
        CasesItem::create($data);

        $this->alert('success', __('Предмет успешно добавлен'));
        return redirect()->route('casesitems.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CasesItem $casesitem)
    {
        return view('backend.pages.cases.items.form', compact('casesitem'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CasesItemRequest $request, CasesItem $casesitem): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['image'])) {
            Storage::disk('public')->delete($casesitem->image);
            $data['image'] = $request->image->store('images', 'public');
        }

        $casesitem->update($data);

        $this->alert('success', __('Предмет успешно обновлен'));
        return redirect()->route('casesitems.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CasesItem $casesitem)
    {
        Storage::disk('public')->delete($casesitem->image);

        $casesitem->delete();

        $this->alert('success', __('Предмет успешно удален'));
        return redirect()->route('casesitems.index');
    }
}