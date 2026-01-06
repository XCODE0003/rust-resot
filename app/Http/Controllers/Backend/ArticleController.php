<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\ArticleRequest;
use App\Models\Article;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ArticleController extends Controller
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
        $articles = Article::query();

        $search = request()->query('search');
        if (request()->has('search') && is_string($search)) {
            $articles->where('title_'.app()->getLocale(), 'LIKE', "%{$search}%");
        }

        if ($request->has('date') && $request->get('date') != '') {
            $articles->where('created_at', '>', $request->get('date').' 00:00:00')->where('created_at', '<', $request->get('date').' 23:59:59');
        }

        $articles = $articles->latest()->paginate();

        return view('backend.pages.articles.index', compact('articles'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.pages.articles.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ArticleRequest $request): RedirectResponse
    {
        $data = $request->validated();

        //Автозамена пробела в path на подчеркивание
        $data['path'] = str_replace(' ', '_', $data['path']);


        $data['image'] = $request->image->store('images', 'public');
        Article::create($data);

        $this->alert('success', __('Новость успешно добавлена'));
        return redirect()->route('articles.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        return view('backend.pages.articles.form', compact('article'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ArticleRequest $request, Article $article): RedirectResponse
    {
        $data = $request->validated();

        //Автозамена пробела в path на подчеркивание
        $data['path'] = str_replace(' ', '_', $data['path']);

        if (isset($data['image'])) {
            Storage::disk('public')->delete($article->image);
            $data['image'] = $request->image->store('images', 'public');
        }

        $article->update($data);

        $this->alert('success', __('Новость успешно обновлена'));
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article)
    {
        Storage::disk('public')->delete($article->image);
        $article->delete();

        $this->alert('success', __('Новость успешно удалена'));
        return back();
    }
}
