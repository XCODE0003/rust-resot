<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\ServerCategoryRequest as CategoryRequest;
use App\Models\ServerCategory as Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ServerCategoryController extends Controller
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
        $servercategories = Category::query();

        $search = request()->query('search');
        if (request()->has('search') && is_string($search)) {
            $servercategories->where('title_', 'LIKE', "%{$search}%");
        }

        $servercategories = $servercategories->latest()->paginate();

        return view('backend.pages.servers.categories.index', compact('servercategories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.pages.servers.categories.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->alert('success', __('Категория успешно добавлена'));

        Category::create($data);
        return redirect()->route('servercategories.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $servercategory)
    {
        return view('backend.pages.servers.categories.form', compact('servercategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $servercategory): RedirectResponse
    {
        $data = $request->validated();

        $this->alert('success', __('Категория успешно обновлена'));
        $servercategory->update($data);
        return redirect()->route('servercategories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $servercategory)
    {
        $this->alert('success', __('Категория успешно удалена'));

        $servercategory->delete();
        return back();
    }
}
