<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\FaqRequest;
use App\Models\Faq;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class FaqController extends Controller
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
        $faqs = Faq::query();

        $search = request()->query('search');
        if (request()->has('search') && is_string($search)) {
            $faqs->where('title_', 'LIKE', "%{$search}%");
        }

        $faqs = $faqs->latest()->paginate();

        return view('backend.pages.faqs.index', compact('faqs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.pages.faqs.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FaqRequest $request): RedirectResponse
    {
        $data = $request->validated();
        Faq::create($data);

        $this->alert('success', __('Запись успешно добавлена'));

        return redirect()->route('faqs.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Faq $faq)
    {
        return view('backend.pages.faqs.form', compact('faq'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FaqRequest $request, Faq $faq): RedirectResponse
    {
        $data = $request->validated();

        $faq->update($data);
        $this->alert('success', __('Запись успешно обновлена'));

        return redirect()->route('faqs.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq)
    {
        $faq->delete();
        $this->alert('success', __('Запись успешно удалена'));

        return redirect()->route('faqs.index');
    }
}
