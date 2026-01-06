@extends('layouts.auth')
@section('title', __('Сбросить пароль'))

@section('form')
    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="email">E-Mail</label>
            </div>
            <input id="email" type="email"
                   class="form-control form-control-lg @error('email') is-invalid @enderror" name="email"
                   value="{{ $email ?: old('email') }}" placeholder="{{ __('Введите E-Mail') }}" required autocomplete="email" autofocus>

            @error('email')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="password">{{ __('Пароль') }}</label>
            </div>
            <div class="form-control-wrap">
                <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch" data-target="password">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input id="password" type="password"
                       class="form-control form-control-lg @error('password') is-invalid @enderror" name="password"
                       required autocomplete="new-password" placeholder="{{ __('Введите пароль') }}">

                @error('password')
                <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="password-confirm">{{ __('Подтвердите пароль') }}</label>
            </div>
            <div class="form-control-wrap">
                <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch" data-target="password-confirm">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input id="password-confirm" type="password"
                       class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror" name="password_confirmation"
                       required autocomplete="new-password" placeholder="{{ __('Подтвердите пароль') }}">
            </div>
        </div>

        <div class="form-group">
            <button class="btn btn-lg btn-primary btn-block">{{ __('Сбросить пароль') }}</button>
        </div>

    </form>
    @if (Route::has('login') && !Auth::check())
        <div class="form-note-s2 pt-4">
            {{ __('Вспомнили пароль?') }} <a href="{{ route('login') }}">{{ __('Авторизоваться') }}</a>
        </div>
    @endif
@endsection
