@extends('layouts.main')

@section('title', __('Магазин') . ' - ' . config('options.main_title_'.app()->getLocale(), '') )

@prepend('meta')
    <meta name="description" content="You can see the guides about the game Rust.">
@endprepend

@section('content')

    <div class="inner-header">{{ __('Магазин') }}</div>

    <div class="inner">
        <div class="container">
            <div class="shop">
                <div style="display: flex; align-items: center; letter-spacing: 0px; justify-content: center;" class="shop-description">
                    <span style="line-height: 0px;">
                    {{ __('Выберите сервер, на котором вы играете') }}
                    </span>
                    <span style="font-family: 'Roboto';">:</span>
                </div>

                <div class="shop-list">

                    @foreach($servers as $server)
                        <div class="shop-pick">
                            <div class="shop-pick-content">
                                <div class="shop-pick-content-title">{{ $server->name }}</div>
                                <a href="{{ route('shop.item.show', $server->id) }}" class="shop-pick-content-button"><i class="fa-solid fa-cart-shopping"></i>{{ __('Выбрать') }}</a>
                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>
    </div>

@endsection