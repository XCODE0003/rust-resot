@extends('layouts.auth')
@section('title', __('Reset Password'))

@section('form')

    <link rel="stylesheet" href="/assets/css/intlTelInput.css"/>
    <script src="/assets/js/jquery-2.1.1.min.js"></script>

    @if (session('status'))
        <div class="alert alert-fill alert-success alert-icon" role="alert">
            <em class="icon ni ni-check-circle"></em>
            <strong>{{ session('status') }}</strong>
        </div>
    @endif


    {{-- Alert --}}
    @foreach (['danger', 'warning', 'success', 'info'] as $type)
        @if(Session::has('alert.' . $type))
            @foreach(Session::get('alert.' . $type) as $message)
                <div class="alert alert-fill alert-{{ $type }} alert-dismissible alert-icon">
                    @if ($type === 'danger')
                        <em class="icon ni ni-cross-circle"></em>
                    @elseif($type === 'success')
                        <em class="icon ni ni-check-circle"></em>
                    @else
                        <em class="icon ni ni-alert-circle"></em>
                    @endif
                    {{ $message }}
                    <button class="close" data-dismiss="alert"></button>
                </div>
            @endforeach
        @endif
    @endforeach
    {{-- End Alert --}}


    <div class="col-sm-12 tabs">
        <ul class="nav nav-tabs">
            <li class=""><a href="#tab-email" data-toggle="tab" aria-expanded="true" class="active"><em class="icon ni ni-email"></em>Email</a></li>
            @if (config('options.sms') !== NULL && config('options.sms') === "1" && config('options.sms_api_key') !== NULL && config('options.sms_api_key') !== "")
                <li class=""><a href="#tab-mobile" data-toggle="tab" aria-expanded="false"><em class="icon ni ni-phone"></em>{{ __('Мобильный') }}</a></li>
            @endif
        </ul>

        <div class="tab-content">
            <div class="tab-pane active" id="tab-email">

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="form-group">
                        <div class="form-label-group">
                            <label class="form-label" for="email">E-Mail</label>
                        </div>
                        <input id="email" type="email"
                               class="form-control form-control-lg @error('email') is-invalid @enderror" name="email"
                               value="{{ Auth::user()->email ?? old('email') }}" required autocomplete="email" placeholder="{{ __('Введите E-Mail') }}" autofocus>


                        @error('email')
                        <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-lg btn-primary btn-block">{{ __('Отправить ссылку сброса пароля') }}</button>
                    </div>

                </form>

            </div>


            @if (config('options.sms') !== NULL && config('options.sms') === "1" && config('options.sms_api_key') !== NULL && config('options.sms_api_key') !== "")

            <div class="tab-pane" id="tab-mobile">

                <form method="POST" action="{{ route('password.sms') }}">
                    @csrf

                    <input id="phone_code" name="phone_code" type="hidden" value="7">
                    <div class="form-group">
                        <label class="form-label" for="phone">{{ __('Телефон') }}</label>
                        <input type="tel" class="form-control form-control-lg @error('phone') is-invalid @enderror"
                               id="phone" name="phone" placeholder="{{ __('Введите номер телефона') }}" value="{{ Auth::user()->phone ?? old('phone') }}">
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
                                <input type="text" class="form-control" id="t-signup-sms-cod" name="sms_code"
                                       placeholder="XXXX">
                            </div>
                            <div class="invalid-feedback send-sms-msg">{{ __('Вы сможете запросить новое смс через') }} <span class="seconds">{{ config("options.sms_timer", "60") }}</span> {{ __('сек.') }}</div>

                            @error('sms_code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>


                    <div class="form-group">
                        <button type="submit" class="btn btn-lg btn-primary btn-block">{{ __('Отправить новый пароль') }}</button>
                    </div>

                </form>

            </div>

            @endif

        </div>
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
                let phone = phone_code+$("input[name=phone]").val();
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
