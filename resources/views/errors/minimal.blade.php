@extends('layouts.main')

@section('content')

    <div class="inner-header">@yield('code')</div>

    <div class="inner">
        <div class="container">
            <div class="terms err-page">
                <h1 class="nk-error-head">@yield('code')</h1>
                <h3 class="nk-error-title">@yield('message')</h3>
                <p class="nk-error-text">@yield('text')</p>
                <a href="{{ route('index') }}" class="btn btn-lg btn-primary mt-2">{{ __('Вернуться') }}</a>
            </div>
        </div>
    </div>

@endsection