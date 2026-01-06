@extends('layouts.main')

@section('title', __('Активировать промокод'))

@section('content')

    <div class="inner-header">{{ __('Активировать промокод') }}</div>

    <div class="inner">
        <div class="container">
            <div class="ticket">
            <form method="POST" action="{{ route('promocode.activate') }}" enctype="multipart/form-data">
                @csrf
                <div class="ticket-content-server">
                    <div class="server-content">
                        <div class="server-content-ticket active">

                            <h1>{{ __('Промокод') }}:</h1>
                            <input type="text" id="code" name="code" value="">
                            <div class="ticket-submit">
                                <button type="submit">{{ __('Активировать') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>

@endsection