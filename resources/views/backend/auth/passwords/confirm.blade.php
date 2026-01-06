@extends('layouts.auth')
@section('title', __('Reset Password'))

@section('form')
    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="password">{{ __('Пароль') }}</label>

                @if (Route::has('password.request'))
                    <a class="link link-primary link-sm" tabindex="-1" href="{{ route('password.request') }}">{{ __('Забыли пароль?') }}</a>
                @endif
            </div>
            <div class="form-control-wrap">
                <a tabindex="-1" href="#" class="form-icon form-icon-right passcode-switch" data-target="password">
                    <em class="passcode-icon icon-show icon ni ni-eye"></em>
                    <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                </a>
                <input id="password" type="password"
                       class="form-control form-control-lg @error('password') is-invalid @enderror" name="password"
                       required autocomplete="current-password" placeholder="{{ __('Введите пароль') }}">

                @error('password')
                <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <button class="btn btn-lg btn-primary btn-block">{{ __('Подтвердить пароль') }}</button>
        </div>

    </form>
    @if (Route::has('password.request'))
        <div class="form-note-s2 pt-4">
            <a href="{{ route('password.request') }}">{{ __('Забыли пароль?') }}</a>
        </div>
    @endif
@endsection
