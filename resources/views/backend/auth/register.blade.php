@extends('layouts.auth')
@section('title', 'Регистрация')

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
    <form action="{{ route('register') }}" method="POST">
        @csrf
        <div class="form-group">
            <label class="form-label" for="name">Имя</label>
            <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror"
                   id="name" name="name" placeholder="Введите своё имя" value="{{ old('name') }}">
            @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="email">E-Mail</label>
            <input type="text" class="form-control form-control-lg @error('email') is-invalid @enderror"
                   id="email" name="email" placeholder="Введите E-Mail" value="{{ old('email') }}">
            @error('email')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label" for="password">Пароль</label>
            <div class="form-control-wrap">
                <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch" data-target="password">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
                       id="password" name="password"
                       placeholder="Введите пароль">
                @error('password')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="password_confirmation">Подтвердите пароль</label>
            <div class="form-control-wrap">
                <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch" data-target="password_confirmation">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input type="password" class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror"
                       id="password_confirmation" name="password_confirmation"
                       placeholder="Введите пароль ещё раз">
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
                    Я согласен с <a tabindex="-1" href="#">политикой конфиденциальности</a> &amp;
                    <a tabindex="-1" href="#">пользовательским соглашением</a>.
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
            <button type="submit" class="btn btn-lg btn-primary btn-block">Зарегистрироваться</button>
        </div>
    </form>
    <div class="form-note-s2 pt-4">
        У Вас уже есть аккаунт?
        <a href="{{ route('login') }}"><strong>Авторизоваться</strong></a>
    </div>
@endsection
