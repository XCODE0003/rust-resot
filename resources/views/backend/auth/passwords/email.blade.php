@extends('layouts.auth')
@section('title', __('Reset Password'))

@section('form')
    @if (session('status'))
        <div class="alert alert-fill alert-success alert-icon" role="alert">
            <em class="icon ni ni-check-circle"></em>
            <strong>{{ session('status') }}</strong>
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <div class="form-label-group">
                <label class="form-label" for="email">{{ __('E-Mail') }}</label>
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
            <button type="submit" class="btn btn-lg btn-primary btn-block">{{ __('Отправить ссылку для сброса пароля') }}</button>
        </div>

    </form>
@endsection
