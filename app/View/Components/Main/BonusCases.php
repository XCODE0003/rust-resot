<?php

namespace App\View\Components\Main;

use App\Models\Cases;
use App\Models\PlayersOnline;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class BonusCases extends Component
{
    public $cases;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->cases = Cache::remember('component_bonus_cases', '600', function () {
            return Cases::where('status', 1)->where('kind', 1)->latest()->limit(10)->get();
        });
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.main.bonus_cases');
    }
}
