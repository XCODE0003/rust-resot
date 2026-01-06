<?php

namespace App\Http\Controllers\Backend\Auth;

use App\Events\SessionRegenerate;
use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator as ReturnValidator;
use TimeHunter\LaravelGoogleReCaptchaV2\Validations\GoogleReCaptchaV2ValidationRule;

class BackendLoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function index(Request $request)
    {
        //session()->put('test', 'ok');
        //dd(session('test', 'fail'));
        return view('backend.auth.login');
    }

    public function login_2fa(Request $request)
    {
        $user = session()->get('user');
        if (!$user) {
            return redirect()->route('backend.login');
        }

        return view('backend.auth.login_2fa');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function authenticate(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            //'recaptcha_v3' => ['required_without:g-recaptcha-response', 'recaptchav3:login,0.5'],
            //'g-recaptcha-response' => [array_key_exists('g-recaptcha-response', $request->all()) ? new GoogleReCaptchaV2ValidationRule() : 'nullable']
        ]);

        if (config('options.ga_status', '0') == '1') {
            $user = User::where('email', $request->input('email'))->first();
            if ($user) {
                if (Hash::check($request->input('password'), $user->password)) {
                    session()->put('user', $user);
                    session()->put('email', $request->input('email'));
                    session()->put('password', $request->input('password'));
                    return redirect()->route('backend.login_2fa');
                }
            }
        } else {
            if (Auth::attempt($request->only(['email', 'password']), !$request->has('remember'))) {
                $request->session()->regenerate();
                return redirect()->route('backend');
            }
        }

        $this->alert('danger', __('Пользователя с данным E-Mail не существует или пароль неверный.'));

        return back();
    }

    public function authenticate_2fa(Request $request): RedirectResponse
    {
        $user = session()->get('user');
        if (!$user || !session()->has('email') || !session()->has('password')) {
            return redirect()->route('backend.login');
        }

        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        //Get QR code for Google Authenticator
        $client = new Client();
        $queryUrl = "https://www.authenticatorApi.com/Validate.aspx?Pin=" . $request->input('code_2fa') . "&SecretCode=" . config('options.ga_key', '');
        $response = $client->get($queryUrl);
        $qrcode = (string)$response->getBody();

        if ($qrcode === 'True') {
            $data = [];
            $data['email'] = session()->get('email');
            $data['password'] = session()->get('password');
            if (Auth::attempt($data, !$request->has('remember'))) {
                $request->session()->regenerate();
                return redirect()->route('backend');
            }
        }

        $this->alert('danger', __('Код авторизации не верный! Попробуйте еще раз.'));

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
        return redirect()->route('backend.login');
    }

    protected function validator(array $data): ReturnValidator
    {
        return Validator::make($data, [
            'code_2fa' => ['required', 'string', 'min:6'],
            //'recaptcha_v3'         => ['required_without:g-recaptcha-response', 'recaptchav3:register,0.5'],
            //'g-recaptcha-response' => [array_key_exists('g-recaptcha-response', $data) ? new GoogleReCaptchaV2ValidationRule() : 'nullable']
        ]);
    }
}