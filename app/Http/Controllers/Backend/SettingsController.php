<?php

namespace App\Http\Controllers\Backend;

use App\Models\Option;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;


class SettingsController extends Controller
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
     * Index page with form update settings
     */
    public function index() {
        return view('backend.pages.settings.site');
    }

    public function site() {
        return view('backend.pages.settings.site');
    }

    public function project_name() {
        return view('backend.pages.settings.project_name');
    }

    public function robots() {
        return view('backend.pages.settings.robots');
    }

    public function sitemap() {
        return view('backend.pages.settings.sitemap');
    }

    public function langs() {
        return view('backend.pages.settings.langs');
    }

    public function analitics() {
        return view('backend.pages.settings.analitics');
    }

    public function about() {
        return view('backend.pages.settings.about');
    }

    public function download() {
        return view('backend.pages.settings.download');
    }

    public function login() {
        return view('backend.pages.settings.login');
    }

    public function login_steam() {
        return view('backend.pages.settings.login_steam');
    }

    public function login_2fa() {

        //Get QR code for Google Authenticator
        $client = new Client();
        $queryUrl = "https://www.authenticatorapi.com/pair.aspx?AppName=" . config('app.name', '') . "&AppInfo=" . config('app.url', '') . "&SecretCode=" . config('options.ga_key', 'np57kf8rpwp3e4w9qwm8');
        $response = $client->get($queryUrl);
        $qrcode = (string)$response->getBody();
        if ($qrcode) {
            $qrcode = explode('<img', $qrcode);
            if ($qrcode[1]) {
                $qrcode = explode('</a>', '<img' . $qrcode[1]);
                if ($qrcode[0]) {
                    $qrcode = $qrcode[0];
                }
            }
        }

        return view('backend.pages.settings.login_2fa', compact('qrcode'));
    }

    public function policy() {
        return view('backend.pages.settings.policy');
    }

    public function forum() {
        return view('backend.pages.settings.forum');
    }

    public function social() {
        return view('backend.pages.settings.social');
    }

    public function donat(Request $request) {
        $server_id = $request->input('server') !==NULL ? $request->input('server') : '0';
        return view('backend.pages.settings.donat', compact('server_id'));
    }

    public function services() {
        return view('backend.pages.settings.services');
    }

    public function auction() {
        return view('backend.pages.settings.auction');
    }

    public function market() {
        return view('backend.pages.settings.market');
    }

    public function smtp() {
        return view('backend.pages.settings.smtp');
    }

    public function recaptcha() {
        return view('backend.pages.settings.recaptcha');
    }

    public function sms() {
        return view('backend.pages.settings.sms');
    }

    public function streams() {
        return view('backend.pages.settings.streams');
    }

    public function referrals() {
        return view('backend.pages.settings.referrals');
    }

    public function luckywheel() {
        return view('backend.pages.settings.luckywheel');
    }

    public function voting() {
        return view('backend.pages.settings.voting');
    }

    public function payments() {
        return view('backend.pages.settings.payments');
    }

    public function game_sessions() {
        return view('backend.pages.settings.game_sessions');
    }

    public function game_options() {
        return view('backend.pages.settings.game_options');
    }

    public function statistics_game_items() {
        return view('backend.pages.settings.statistics_game_items');
    }

    public function game_api() {
        return view('backend.pages.settings.game_api');
    }

    public function waxpeer_api() {
        return view('backend.pages.settings.waxpeer_api');
    }

    public function skinsback_api() {
        return view('backend.pages.settings.skinsback_api');
    }

    public function falling_snow() {
        return view('backend.pages.settings.falling_snow');
    }

    public function bonuses() {
        return view('backend.pages.settings.bonuses');
    }

    public function bonuses_monday() {
        return view('backend.pages.settings.bonuses_monday');
    }

    public function bonuses_thursday() {
        return view('backend.pages.settings.bonuses_thursday');
    }


    /**
     * Store settings and update
     */
    public function store(Request $request): RedirectResponse
    {
        foreach ($request->all() as $key => $value) {
            if ($value === NULL) $value = "";

            if (Str::is('setting_*', $key)) {

                if ($key == 'setting_robots_txt') {
                    Storage::disk('public_html')->put('robots.txt', $value);
                }
                if ($key == 'setting_sitemap_xml') {
                    Storage::disk('public_html')->put('sitemap.xml', $value);
                }

                $key_name = Str::after($key, 'setting_');
                if ($request->input('server') !== NULL) {
                    $key_name = "server_" . $request->input('server') . "_" . $key_name;
                }

                if (Str::is('*image', $key)) {
                    if ($request->file($key) !== NULL) {
                        $value = $request->file($key)->store('images', 'public');
                    } else {

                        $value = ($request->input($key . '_old') !== NULL) ? $request->input($key . '_old') : '';
                    }
                }


                Option::updateOrCreate(['key' => $key_name], [
                    'value' => $value,
                    'default_key' => $request->has("default_{$key}") ? $request->get("default_{$key}") : null
                ]);
            }

        }

        Log::channel('adminlog')->info('Admin ' . auth()->user()->name . ': Saved the settings. Parameters: ' . json_encode($request->all()));

        $this->alert('success', __('Настройки сайта успешно обновлены'));

        Cache::forget('options');

        return back();
    }
}