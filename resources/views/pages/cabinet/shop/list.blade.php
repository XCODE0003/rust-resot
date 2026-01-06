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
                <div class="shop-description">{{ __('Выберите сервер, на котором вы играете') }}:</div>

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