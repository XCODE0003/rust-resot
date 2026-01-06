<?php

namespace App\Http\Controllers\Backend;

use App\Models\User;
use App\Models\CasesItem;
use App\Models\CaseopenHistory;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CaseopenHistoryController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('can:support');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $histories = CaseopenHistory::query()->latest('date')->paginate();

        return view('backend.pages.caseopen_history.index', compact('histories'));
    }

}
