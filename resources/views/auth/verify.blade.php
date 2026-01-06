@extends('layouts.auth')
@section('title', __('Verify Your Email Address'))

@section('form')
    @if (session('status'))
        <div class="alert alert-fill alert-success alert-icon" role="alert">
            <em class="icon ni ni-check-circle"></em>
            <strong>{{ __('A fresh verification link has been sent to your email address.') }}</strong>
        </div>
    @endif

    <div class="nk-block-des text-primary">
        <p>{{ __('Before proceeding, please check your email for a verification link.') }}</p>
        <p>{{ __('If you did not receive the email') }},
        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <button type="submit" class="btn btn-dim btn-primary">{{ __('click here to request another') }}</button>
        </form>
        </p>
    </div>
@endsection
