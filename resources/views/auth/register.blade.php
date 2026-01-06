@extends('layouts.auth')
@section('title', __('Регистрация'))

@push('head')
    @error('recaptcha_v3')
    @else
        {!! RecaptchaV3::initJs() !!}
    @enderror
@endpush

@push('scripts')

    @error('recaptcha_v3')
    {!!  GoogleReCaptchaV2::render('form_register') !!}
    @enderror
@endpush

@section('form')

    <link rel="stylesheet" href="/assets/css/intlTelInput.css"/>
    <script src="/assets/js/jquery-2.1.1.min.js"></script>

    <div class="col-sm-12 tabs">
        <ul class="nav nav-tabs">
            <li class=""><a href="#tab-email" data-toggle="tab" aria-expanded="true" class="active"><em class="icon ni ni-email"></em>Email</a></li>

            @if (config('options.sms') !== NULL && config('options.sms') === "1" && config('options.sms_api_key') !== NULL && config('options.sms_api_key') !== "")
                <li class=""><a href="#tab-mobile" data-toggle="tab" aria-expanded="false"><em class="icon ni ni-phone"></em>{{ __('Мобильный') }}</a></li>
            @endif
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="tab-email">

                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-label" for="name">{{ __('Имя') }} <small style="display: block;font-size: 11px;">({{ __('используйте латинские буквы и введите не более 14 символов') }})</small></label>
                        <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror"
                               id="name" name="name" placeholder="{{ __('Введите своё имя') }}" value="{{ old('name') }}">
                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">E-Mail</label>
                        <input type="text" class="form-control form-control-lg @error('email') is-invalid @enderror"
                               id="email" name="email" placeholder="{{ __('Введите E-Mail') }}" value="{{ old('email') }}">
                        @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password">{{ __('Пароль') }} <small style="display: block;font-size: 11px;">({{ __('используйте латинские буквы и введите не менее 8 символов') }})</small></label>
                        <div class="form-control-wrap">
                            <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch" data-target="password">
                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                            </a>
                            <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
                                   id="password" name="password"
                                   placeholder="{{ __('Введите пароль') }}">
                            @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">{{ __('Подтвердите пароль') }}</label>
                        <div class="form-control-wrap">
                            <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch" data-target="password_confirmation">
                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                            </a>
                            <input type="password" class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror"
                                   id="password_confirmation" name="password_confirmation"
                                   placeholder="{{ __('Введите пароль ещё раз') }}">
                            @error('password_confirmation')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-control-xs custom-checkbox flex-wrap">
                            <input type="checkbox" class="custom-control-input @error('ok') is-invalid @enderror" id="ok" name="ok" value="1">
                            <label class="custom-control-label" for="ok">
                                {{ __('Я согласен с') }} <a tabindex="-1" href="{{ route('policy') }}">{{ __('политикой конфиденциальности') }}</a> &amp;
                                <a tabindex="-1" href="{{ route('term') }}">{{ __('пользовательским соглашением') }}</a>.
                            </label>
                            @error('ok')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    @error('recaptcha_v3')
                    <div id="form_register" class="mb-2"></div>
                    @else
                        {!! RecaptchaV3::field('register', 'recaptcha_v3') !!}
                        @enderror
                        <div class="form-group">
                            <button type="submit" class="btn btn-lg btn-primary btn-block">{{ __('Зарегистрироваться') }}</button>
                        </div>
                </form>

            </div>


            @if (config('options.sms') !== NULL && config('options.sms') === "1" && config('options.sms_api_key') !== NULL && config('options.sms_api_key') !== "")
            <div class="tab-pane" id="tab-mobile">

                <form action="{{ route('register') }}" method="POST">
                    @csrf
                    <input id="phone_code" name="phone_code" type="hidden" value="7">
                    <div class="form-group">
                        <label class="form-label" for="name">{{ __('Имя') }} <small style="display: block;font-size: 11px;">({{ __('используйте латинские буквы и введите не менее 8 символов') }})</small></label>
                        <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror"
                               id="name" name="name" placeholder="{{ __('Введите своё имя') }}" value="{{ old('name') }}">
                        @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="phone">{{ __('Телефон') }}</label>
                        <input type="tel" class="form-control form-control-lg @error('phone') is-invalid @enderror"
                               id="phone" name="phone" placeholder="{{ __('Введите номер телефона') }}" value="{{ old('phone') }}">
                        @error('phone')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group row">
                        <div class="col-12"><label for="t-signup-sms-cod">{{ __('SMS код') }}</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <button type="button" class="btn btn-lg btn-primary btn-block send-sms"><i
                                                class="fa fa-envelope" aria-hidden="true"></i> {{ __('Получить SMS код') }}
                                    </button>
                                </div>
                                <input type="text" class="form-control @error('sms_code') is-invalid @enderror"  id="t-signup-sms-cod" name="sms_code"
                                       placeholder="XXXX"></div>
                        </div>
                        <div class="invalid-feedback send-sms-msg">{{ __('Вы сможете запросить новое смс через') }} <span class="seconds">{{ config("options.sms_timer", "60") }}</span> {{ __('сек.') }}</div>

                        @error('sms_code')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">{{ __('Пароль') }} <small style="display: block;font-size: 11px;">({{ __('используйте латинские буквы и введите не менее 8 символов') }})</small></label>
                        <div class="form-control-wrap">
                            <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch" data-target="password2">
                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                            </a>
                            <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
                                   id="password2" name="password"
                                   placeholder="{{ __('Введите пароль') }}">
                            @error('password')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">{{ __('Подтвердите пароль') }}</label>
                        <div class="form-control-wrap">
                            <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch" data-target="password_confirmation2">
                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                            </a>
                            <input type="password" class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror"
                                   id="password_confirmation2" name="password_confirmation"
                                   placeholder="{{ __('Введите пароль ещё раз') }}">
                            @error('password_confirmation')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="custom-control custom-control-xs custom-checkbox flex-wrap">
                            <input type="checkbox" class="custom-control-input @error('ok') is-invalid @enderror" id="ok2" name="ok" value="1">
                            <label class="custom-control-label" for="ok2">
                                {{ __('Я согласен с') }} <a tabindex="-1" href="{{ route('policy') }}">{{ __('политикой конфиденциальности') }}</a> &amp;
                                <a tabindex="-1" href="{{ route('term') }}">{{ __('пользовательским соглашением') }}</a>.
                            </label>
                            @error('ok')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                    @error('recaptcha_v3')
                    <div id="form_register" class="mb-2"></div>
                    @else
                        {!! RecaptchaV3::field('register', 'recaptcha_v3') !!}
                        @enderror
                        <div class="form-group">
                            <button type="submit" class="btn btn-lg btn-primary btn-block">{{ __('Зарегистрироваться') }}</button>
                        </div>
                </form>

            </div>
            @endif

        </div>


    </div>


    <div class="form-note-s2 pt-4">
        {{ __('У Вас уже есть аккаунт?') }}
        <a href="{{ route('login') }}"><strong>{{ __('Авторизоваться') }}</strong></a>
    </div>


    <script src="/assets/js/intlTelInput.min.js"></script>
    <script> document.addEventListener("DOMContentLoaded", function (event) {
            let input = document.querySelector("#phone");
            let iti = window.intlTelInput(input, {
                initialCountry: "ru",
                nationalMode: false,
                placeholderNumberType: "MOBILE",
                preferredCountries: ["ru", "ua", "by", "md", "kz", "uz", "az", "pl", "am", "ge", "bg", "be", "tj", "kg", "lt", "lv", "ee", "ro", "tm", "ch", "de"],
                separateDialCode: true,
                utilsScript: "/assets/js/utils.js",
            });
            $('#phone_code').val(iti.getSelectedCountryData().dialCode);
            $('.separate-dial-code').on('click', '.selected-flag, .country', function () {
                $('#phone_code').val(iti.getSelectedCountryData().dialCode);
            });
            $('.send-sms').on('click', function (e) {

                e.preventDefault();
                let phone_code = $("input[name=phone_code]").val();
                let phone = phone_code+$("input[name=phone]").val().replace(/\D/g, '');
                let captcha = $("input[name=recaptcha_v3]").val();

                console.log(phone);

                $("input[name=phone]").removeClass('error');
                if (phone.length < 10) {
                    $("input[name=phone]").addClass('error');
                    return false;
                }

                $.ajax({
                    type: "POST",
                    url: "{{ route('register.sendcode') }}",
                    data: { phone: phone, captcha: captcha },
                    headers: { 'X-CSRF-Token': $("input[name=_token]").val() }
                }).done(function( msg ) {
                    let time = '{{ config("options.sms_timer", "60") }}';
                    if (msg.indexOf('error_timer') !== -1) {
                        time = msg.replace('error_timer=', '');
                        console.log( time );
                    }

                    $('.send-sms').text('{{ __("SMS код отправлен!") }}');
                    $(".send-sms").prop('disabled',true);
                    $('.seconds').text(time);
                    $(".send-sms-msg").show();
                    Timer(time);
                });

            });
        });
    </script>

    <script>
        function Timer(seconds) {
            console.log(seconds);
            var _Seconds = seconds,
                int;
            int = setInterval(function() {
                if (_Seconds > 0) {
                    _Seconds--;
                    $('.seconds').text(_Seconds);
                } else {
                    clearInterval(int);
                    $(".send-sms-msg").hide();
                    $('.send-sms').text('{{ __("Получить SMS код") }}');
                    $(".send-sms").prop('disabled',false);
                }
            }, 1000);
        }
    </script>
@endsection
