<?php

namespace App\View\Components\Cabinet;

use Illuminate\View\Component;

class MenuItem extends Component
{
    public $title;
    public $icon;
    public $route;
    public $pattern;
    public $href;
    public $server;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($title, $route = null, $icon = null, $href = null, $pattern = null, $server = null)
    {
        $this->route = $route;
        $this->pattern = $pattern ?? $route;
        $this->icon = $icon;
        $this->title = $title;
        $this->href = $href;
        $this->server = $server;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.cabinet.menu-item');
    }
}
