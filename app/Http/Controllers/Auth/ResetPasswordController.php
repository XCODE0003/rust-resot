	<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Session;
use App\Services\Sms;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator as ReturnValidator;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
     protected $redirectTo = RouteServiceProvider::HOME;

    protected function validator(array $data): ReturnValidator
    {
        return Validator::make($data, [
            'sms_code' => ['required', 'string'],
            'phone' => ['required', 'exists:users', 'string', 'max:20'],
        ]);
    }


    public function sms(Request $request, Sms $sms)
    {
        $data = array(
            'sms_code' => $request->input('sms_code'),
            'phone' => $request->input('phone') ? $request->input('phone_code') . $request->input('phone') : "",
        );

        $validator = $this->validator($data);

        //Проверяем валидацию и что смс код совпадает
        if ($validator->fails() || session()->get('sms_code', '') != $request->input('sms_code')) {
            $this->alert('danger', __('Ошибка! Пароль не изменён. Проверьте правильность формы или кода из смс.'));
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        //Генерирую случайный пароль
        $new_password = generationPassword();

        User::where('phone', $data['phone'])
            ->update(['password' => Hash::make($new_password)]);
        $this->alert('success', __('Вы успешно изменили пароль!'));

        $result = $sms->send($data['phone'], __('Ваш новый пароль: ') . $new_password);

        return back();

    }


}
