<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\GuideCategoryRequest as CategoryRequest;
use App\Models\GuideCategory as Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class GuideCategoryController extends Controller
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
        $guidecategories = Category::query();

        $search = request()->query('search');
        if (request()->has('search') && is_string($search)) {
            $guidecategories->where('title_', 'LIKE', "%{$search}%");
        }

        $guidecategories = $guidecategories->latest()->paginate();

        return view('backend.pages.guides.categories.index', compact('guidecategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.pages.guides.categories.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->alert('success', __('Категория успешно добавлена'));

        Category::create($data);
        return redirect()->route('guidecategories.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $guidecategory)
    {
        return view('backend.pages.guides.categories.form', compact('guidecategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $guidecategory): RedirectResponse
    {
        $data = $request->validated();

        $this->alert('success', __('Категория успешно обновлена'));
        $guidecategory->update($data);
        return redirect()->route('guidecategories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $guidecategory)
    {
        $this->alert('success', __('Категория успешно удалена'));

        $guidecategory->delete();
        return back();
    }
}
