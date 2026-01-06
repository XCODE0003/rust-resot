<?php

namespace App\View\Components\Main;

use App\Models\Video;
use App\Models\Stream;
use App\Services\Streams;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

class Videos extends Component
{
    public $videos;
    public $streams;
    public $channels;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->videos = Video::latest()->where('language', app()->getLocale())->limit(5)->get();
        $this->channels = Stream::latest()->where('language', app()->getLocale())->limit(5)->get();
        $this->channels = $this->channels->sortBy('sort');

        //Получаем стримы
        Cache::forget('streams');
        $this->streams = Cache::remember('streams', '180', function () {
            $stream = new Streams();
            foreach ($this->channels as $channel) {
                $this->streams[] = $stream->getStreams($channel->url);
            }

            return $this->streams;
        });
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        //dd($this->streams);
        return view('components.main.videos');
    }
}
