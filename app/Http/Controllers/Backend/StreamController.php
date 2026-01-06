<?php

namespace App\Http\Controllers\Backend;

use App\Http\Requests\VideoRequest;
use App\Lib\CacheD;
use App\Models\Stream;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class StreamController extends Controller
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

        $streams = Stream::query();

        $search = request()->query('search');
        if (request()->has('search') && is_string($search)) {
            $streams->where('title', 'LIKE', "%{$search}%");
        }

        $streams = $streams->latest()->paginate();

        return view('backend.pages.streams.index', compact('streams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('backend.pages.streams.form');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VideoRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->input('image') !== NULL) {
            $data['image'] = $request->image->store('videos', 'public');
        }

        Stream::create($data);
        $this->alert('success', __('Новый стрим успешно добавлен'));
        return redirect()->route('streams.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Stream $stream)
    {
        return view('backend.pages.streams.form', compact('stream'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VideoRequest $request, Stream $stream): RedirectResponse
    {
        $data = $request->validated();

        if (isset($data['image'])) {
            Storage::disk('public')->delete($stream->image);
            $data['image'] = $request->image->store('videos', 'public');
        }

        $this->alert('success', __('Стрим успешно обновлен'));
        $stream->update($data);
        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stream $stream): RedirectResponse
    {
        $this->alert('success', __('Стрим успешно удален'));

        if ($stream->image !== NULL) {
            Storage::disk('public')->delete($stream->image);
        }
        $stream->delete();
        return back();
    }
}
