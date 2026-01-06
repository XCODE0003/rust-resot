<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware('server.status');
    }

    public function index() {

        $faqs = Cache::remember('page_faqs', '600', function () {
            return Faq::query()->orderBy('sort')->paginate();
        });

        return view('pages.main.faq', compact('faqs'));
    }

}
