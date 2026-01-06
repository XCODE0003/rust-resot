<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserVerify;
use App\Models\Referral;
use App\Services\Sms;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\Events\Verified;
use Illuminate\Validation\Rules\Password;
use Illuminate\Contracts\Validation\Validator as ReturnValidator;
use TimeHunter\LaravelGoogleReCaptchaV2\Validations\GoogleReCaptchaV2ValidationRule;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Session;
use Mail;

class RegisterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function index()
    {
        return view('auth.register');
    }


    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return ReturnValidator
     */

    protected function validator(array $data): ReturnValidator
    {
        if (array_key_exists('phone', $data)) $data['phone'] = $data['phone_code'] . preg_replace('![^0-9]+!', '', $data['phone']);
        return Validator::make($data, [
            'name'                 => ['required', 'string', 'min:3', 'max:32'],
            'email'                => ['sometimes', 'email', 'max:35', 'unique:users'],
            'password'             => ['required', 'confirmed', Password::min(8)],
            'sms_code'             => ['sometimes', 'required', 'string'],
            'phone'                => ['sometimes', 'string', 'max:20', 'unique:users'],
            'ok'                   => ['accepted'],
            //'recaptcha_v3'         => ['required_without:g-recaptcha-response', 'recaptchav3:register,0.5'],
            //'g-recaptcha-response' => [array_key_exists('g-recaptcha-response', $data) ? new GoogleReCaptchaV2ValidationRule() : 'nullable']
        ], [
            'ok.accepted' => __('Вы должны принять Политику конфиденциальности и Пользовательское соглашение.')
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data): User
    {
        return User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'],
            'pin'      => $data['pin'],
            'password' => Hash::make($data['password'])
        ]);
    }

    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails() || (session()->get('sms_code', NULL) != $request->input('sms_code'))) {

            return back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = array(
            'name'     => $request->input('name'),
            'email'    => $request->input('email') !== NULL ? $request->input('email') : "",
            'password' => $request->input('password'),
            'password_confirmation' => $request->input('password_confirmation'),
            'phone'    => $request->input('phone') !== NULL ? $request->input('phone_code') . preg_replace('![^0-9]+!', '', $request->input('phone')) : "",
            'pin'      => generationCode(),
        );


        $user = $this->create($data);

        Auth::guard()->login($user);

        //Начисляем бонус за реферала
        if (session()->has('ref_code')) {
            $res = $this->setBonus(session()->get('ref_code'));
        }

	//Отправляем письмо с верификацией эмейл
	if($request->input('email') !== NULL) {

		$token = Str::random(64);
		
        	$data_usv = UserVerify::create([
            		'user_id' => auth()->id(),
            		'token' => $token,
        	]);
		
		$email = $data["email"];
		if (Mail::send('emails.emailVerificationEmail', ['token' => $token], function($message) use($email) {
            			$message->to($email);
            			$message->subject('Email Verification Mail');
        		}) ) {
			//Success send email
		}
	}

        // event(new Registered($user));

        //Создаем файл с данными регистрации и передаем пользователю
        $login = ($data["phone"] != "") ? $data["phone"] : $data["email"];
        $reg_info = "Congratulations! You have successfully created a master account." . "\n\n";
        $reg_info .= "Your login: " . $login . "\n";
        $reg_info .= "Your password: " . $data["password"] . "\n";
        $reg_info .= "Your Pin code: " . $data["pin"] . "\n\n";
        $reg_info .= "Save this file in a secret location. And do not tell anyone your password and PIN code!" . "\n";
        $reg_txt_url = 'public/regs_txt/' . generationPassword() . '.txt';
        Storage::disk('local')->put($reg_txt_url, $reg_info);
        session()->put('reg_txt_url', $reg_txt_url);

        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect()->route('login');
    }


    public function sendcode(Request $request, Sms $sms)
    {

        $user_agent = $_SERVER['HTTP_USER_AGENT'] . " IP:" . $_SERVER['REMOTE_ADDR'];
        $user_agent = preg_replace("/[\\/().,:;?!\s]/ui", "", $user_agent);

        if (!cache()->has($user_agent)) {
            cache([$user_agent => date('d.m.Y H:i:s')], config("options.sms_timer", "60"));
        } else {
            $timer = cache($user_agent);
            $timer_diff = (strtotime($timer) + config("options.sms_timer", "60")) - strtotime(date('d.m.Y H:i:s'));
            return 'error_timer=' . $timer_diff;
        }

        if (strlen($request->input('phone')) < 10) {
            $result = array(
                "status"  => "error",
                "code"    => "400",
                "message" => __('Указан не верный номер телефона!'),
            );
            return $result;
        }

        $sms_code = generationCode();
        session()->put('sms_code', $sms_code);

        //return $sms_code;

        $result = $sms->send($request->input('phone'), __('Ваш код подтверждения: ') . $sms_code);

        return $result;
    }

    public function setBonus($ref_code)
    {

        if (!($referral = Referral::where('code', $ref_code)->first())) return FALSE;

        $history = json_decode($referral->history);
        $history[] = [
            'accrued'        => config('options.referral_percent_issued', '1'),
            'transaction_id' => '0',
            'amount'         => '0',
            'coins'          => '0',
            'payment_system' => 'for_register'
        ];

        $referral->total += config('options.referral_percent_issued', '1');
        $referral->history = json_encode($history);
        $referral->save();

        $user = User::find($referral->user_id);
        if ($user) {
            $user->balance += config('options.referral_percent_issued', '1');
            $user->save();
        }

        $user = User::find(auth()->id());
        if ($user) {
            $user->balance += config('options.referral_percent_registered', '1');
            $user->save();
        }

        Session::forget('ref_code');

        Log::channel('paymentslog')->info("Robot: The accrual of game currency via a referral link has been completed. Parameters: " . json_encode($referral));
        $this->alert('success', __('Вы успешно получили Бонус за регистрацию по реферальному коду!'));

        return TRUE;
    }
}

