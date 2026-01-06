<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Http\Requests\Profile2FARequest;
use App\Http\Requests\SecurityRequest;
use App\Models\Session;
use App\Models\User;
use App\Models\Account;
use App\Services\Sms;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Validation\Validator as ReturnValidator;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Mail;

class UserSettingsController extends Controller
{
    public function security() {
        return view('pages.cabinet.user.security');
    }

    protected function validator(array $data): ReturnValidator
    {
        if (array_key_exists('phone', $data)) $data['phone'] = $data['phone_code'] . preg_replace('![^0-9]+!', '', $data['phone']);
        return Validator::make($data, [
            'sms_code' => ['required', 'string'],
            'phone' => ['string', 'min:10', 'max:20', 'unique:users'],
        ]);
    }

    public function security_store(SecurityRequest $request): RedirectResponse
    {

        if ($request->input('pin') !== NULL) {
            //Сверяем пин код мастер аккаунта
            if (auth()->user()->pin !== $request->input('pin')) {
                $this->alert('danger', __('Не верный Pin код!'));
                return back();
            }
        }

        $request->user()->update(['password' => Hash::make($request->input('new_password'))]);
        $this->alert('success', __('Вы успешно изменили пароль!'));
        return back();
    }

    public function pin() {
        return view('pages.cabinet.user.pin');
    }

    public function pin_store(SecurityRequest $request): RedirectResponse
    {
        $request->user()->update(['pin' => $request->input('pin')]);
        $this->alert('success', __('Вы успешно сбросили Pin код'));
        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Successfully reset PIN code");
        return back();
    }

    public function reset_pin(Request $request, Sms $sms): RedirectResponse
    {
        User::where('id', auth()->id())->firstOrFail();
        $pin = generationCode();

        switch ($request->input('method')){
            case 'email':

                //Отсылаем письмо с новым пин кодом на почту!
                $email = auth()->user()->email;
                $mail_text = __('Ваш Pin код для игрового аккаунта') . " " . $request->input('login') . __(' успешно сброшен!') . " \n";
                $mail_text .= __('Новый Pin код:') . " " . $pin . "\n";
                $mail_text .= __('Если Вы не сбрасывали Pin код, то обратитесь к администратору!');

		try {
                Mail::raw($mail_text, function($message) use($email) {
                    $message->to($email);
                    $message->subject(__('Ваш Pin код успешно сброшен!'));
                });
		} catch (\Exception $ex) {
        		$this->alert('danger', __('Ошибка при отправке письма! Попробуйте позже!'));
			return back();
		}

            case 'phone':

                //Отсылаем смс с новым пин кодом на почту!
                $phone = auth()->user()->phone;
                $result = $sms->send($phone, __('Ваш новый Pin код:') . " " . $pin);
        }

        $request->user()->update(['pin' => $pin]);
        $this->alert('success', __('Вы успешно сбросили Pin код'));
        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Successfully reset PIN code");

        return back();
    }

    public function set_phone(Request $request): RedirectResponse {

        $validator = $this->validator($request->all());
        if ( $validator->fails() || (session()->get('sms_code', NULL) != $request->input('sms_code')) ) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $phone = $request->input('phone_code') . preg_replace('![^0-9]+!', '', $request->input('phone'));

        $request->user()->update(['phone' => $phone]);
        $this->alert('success', __('Вы успешно привязали номер телефона!'));
        Log::channel('adminlog')->info(auth()->user()->role . " " . auth()->user()->name . ": Successfully linked phone number " . $phone);

        return back();
    }

    public function profile() {
        return view('pages.cabinet.user.profile');
    }

    public function profile_2fa() {

        //If activated 2FA Get QR code for Google Authenticator
        $qrcode = '';
        if (auth()->user()->status_2fa == '1') {
            $client = new Client();
            $queryUrl = "https://www.authenticatorapi.com/pair.aspx?AppName=" . config('app.name', '') . "-" . auth()->user()->name . "&AppInfo=" . config('app.url', '') . "&SecretCode=" . config('app.name', '') . auth()->user()->secretcode_2fa;
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
        }

        return view('pages.cabinet.user.profile_2fa', compact('qrcode'));
    }

    public function set_profile_2fa(Profile2FARequest $request): RedirectResponse
    {
        session()->put('prof2fa_action', '1');

        $user = User::find(auth()->user()->id);

        //Get QR code for Google Authenticator
        $client = new Client();
        $queryUrl = "https://www.authenticatorApi.com/Validate.aspx?Pin=" . $request->input('code_2fa') . "&SecretCode=" . config('app.name', '') . $user->secretcode_2fa;
        $response = $client->get($queryUrl);
        $qrcode = (string)$response->getBody();

        if ($qrcode === 'True') {
            $user->status_2fa = 1;
            $user->save();

            $this->alert('success', __('Вы успешно активировали 2FA!'));
            return back();
        }

        $this->alert('danger', __('Код авторизации неверный! Попробуйте еще раз.'));
        return back();
    }

    public function profile_store(ProfileRequest $request): RedirectResponse
    {

        $data = $request->validated();
        $user = $request->user();

        //Если разрешено изменять email
        if (config('options.change_email', '0') == '1') {
            if ($data['email'] !== $user->email) {
                $user->email_verified_at = NULL;
            }
        }

        $user->fill($data);

        $user->save();

        $this->alert('success', __('Вы успешно обновили информацию профиля!'));

        return back();
    }

    public function activity() {
        $sessions = Session::latest('last_activity')->limit(20)->where('user_id', auth()->id())->get();
        return view('pages.cabinet.user.activity', compact('sessions'));
    }

    public function activity_destroy($id) {
        Session::where('user_id', auth()->id())->where('id', $id)->delete();
        return back();
    }

    public function sendcode(Request $request, Sms $sms)
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'] . " IP:" . $_SERVER['REMOTE_ADDR'];
        $user_agent = preg_replace ("/[\\/().,:;?!\s]/ui","",$user_agent);

        if (!cache()->has($user_agent)) {
            cache([$user_agent => date('d.m.Y H:i:s')], config("options.sms_timer", "60"));
        } else {
            $timer = cache($user_agent);
            $timer_diff = (strtotime($timer) + config("options.sms_timer", "60")) - strtotime(date('d.m.Y H:i:s'));
            return 'error_timer=' . $timer_diff;
        }

        if (strlen($request->input('phone')) < 10) {
            $result = array(
                "status" => "error",
                "code" => "400",
                "message" => __('Указан не верный номер телефона!'),
            );
            return $result;
        }

        $sms_code = generationCode();
        session()->put('sms_code', $sms_code);

        //return $sms_code;

        $result = $sms->send($request->input('phone'), __('Ваш код подтверждения:') . " " . $sms_code);

        return $result;
    }

    public function get2FACode()
    {
        if (auth()->user()->secretcode_2fa !== NULL) {
            $code = auth()->user()->secretcode_2fa;

        } else {
            $code = generationReferralCode();
            for ($i = 0; $i < 10; $i++) {
                if (User::where('secretcode_2fa', $code)->first()) {
                    $code = generationReferralCode();
                }
            }
        }

        //Get QR code for Google Authenticator
        $client = new Client();
        $queryUrl = "https://www.authenticatorapi.com/pair.aspx?AppName=" . config('app.name', '') . "-". auth()->user()->name ."&AppInfo=" . config('app.url', '') . "&SecretCode=" . config('app.name', '') . $code;
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

        $user = User::find(auth()->user()->id);
        $user->secretcode_2fa = $code;
        $user->save();

        return $qrcode;
    }
}
