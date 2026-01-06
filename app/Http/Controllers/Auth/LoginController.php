<?php

namespace App\Http\Controllers\Auth;

use App\Events\SessionRegenerate;
use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\User;
use App\Services\Sms;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use TimeHunter\LaravelGoogleReCaptchaV2\Validations\GoogleReCaptchaV2ValidationRule;
use GuzzleHttp\Client;
use App\Lib\SteamApi;


class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout', 'authenticateSteam');
    }

    public function index(Request $request)
    {
        session()->put('auth_login', 1);
        return view('auth.login');
    }

    public function login_2fa(Request $request)
    {
        return view('auth.login_2fa');
    }

    public function login_steam(Request $request)
    {
        $result = SteamApi::login();

        if ($result->status == 'success') {
            $steamid = $result->data;
            session()->put('steamid', $steamid);

            $result = SteamApi::getUserInfo($steamid);
            if ($result->status == 'success') {
                $user_steam = $result->data;
                session()->put('user_steam', $user_steam);
            }

            //Check if the user exists, otherwise create a new user
            $password = $user_steam->steamid.'0kf7v6xi34';
            $user = User::where('steam_id', $steamid)->first();
            if (!$user) {
                $user = new User;
                $user->steam_id = $user_steam->steamid;
                $user->password = Hash::make($password);
            }

            $user->name = $user_steam->personaname;
            $user->avatar = $user_steam->avatarfull;
            $user->save();
        }

        if (Auth::login($user, !$request->has('remember'))) {
            $request->session()->regenerate();
            return redirect()->route('index');
        }

        return redirect()->route('index');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function authenticate(Request $request): RedirectResponse
    {
/*
        $request->validate([
            'email'                => ['email'],
            'phone'                => ['string', 'max:20'],
            'password'             => ['required'],
            'recaptcha_v3'         => ['required_without:g-recaptcha-response', 'recaptchav3:login,0.5'],
            'g-recaptcha-response' => [array_key_exists('g-recaptcha-response', $request->all()) ? new GoogleReCaptchaV2ValidationRule() : 'nullable']
        ]);
*/

        //Проверяем, по email или телефону идет авторизация
        if ($request->input('email') !== NULL) {
            $user = User::where('email', $request->input('email'))->first();
            if ($user && config('options.ga_users_status', '0') == '1' && $user->status_2fa == '1') {
                if (Hash::check($request->input('password'), $user->password)) {
                    session()->put('user', $user);
                    session()->put('email', $request->input('email'));
                    session()->put('password', $request->input('password'));
                    return redirect()->route('user.login_2fa');
                }
            } else {
                if (Auth::attempt($request->only(['email', 'password']), !$request->has('remember'))) {
                    $request->session()->regenerate();
                    return redirect()->route('cabinet');
                }
            }

            $this->alert('danger', __('Пользователя с данным E-Mail не существует или пароль неверный.'));
            return back();

        } else {

            $user = User::where('phone', $request->input('phone'))->first();
            if ($user && config('options.ga_users_status', '0') == '1' && $user->status_2fa == '1') {
                if (Hash::check($request->input('password'), $user->password)) {
                    session()->put('user', $user);
                    session()->put('phone', $request->input('phone'));
                    session()->put('password', $request->input('password'));
                    return redirect()->route('user.login_2fa');
                }
            } else {
                if (Auth::attempt($request->only(['phone', 'password']), !$request->has('remember'))) {
                    $request->session()->regenerate();
                    return redirect()->route('cabinet');
                }
            }

            $this->alert('danger', __('Пользователя с данным номером телефона не существует или пароль неверный.'));
            return back();
        }

    }

    public function authenticate_2fa(Request $request): RedirectResponse
    {
        $user = session()->get('user');

        if (!$user || (!session()->has('email') && !session()->has('phone')) || !session()->has('password')) {
            return redirect()->route('login');
        }

        //Get QR code for Google Authenticator
        $client = new Client();
        $queryUrl = "https://www.authenticatorApi.com/Validate.aspx?Pin=" . $request->input('code_2fa') . "&SecretCode="  . config('app.name', '') . $user->secretcode_2fa;
        $response = $client->get($queryUrl);
        $qrcode = (string)$response->getBody();

        if ($qrcode === 'True') {

            //Проверяем, по email или телефону идет авторизация
            if (session()->get('email') !== NULL) {
                $data['email'] = session()->get('email');
            } else {
                $data['phone'] = session()->get('phone');
            }
            $data['password'] = session()->get('password');
            if (Auth::attempt($data, !$request->has('remember'))) {
                $request->session()->regenerate();
                return redirect()->route('cabinet');
            }

        }

        $this->alert('danger', __('Код авторизации неверный! Попробуйте еще раз.'));
        return back();
    }

    public function authenticateSteam(Request $request)
    {
        $result = SteamApi::login();
        if ($result->status == 'success') {
            return Redirect::to($result->data);
        }

        return back();
    }

    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('index');
    }
}
