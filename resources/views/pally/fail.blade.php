@extends('layouts.main')

@section('content')
<div class="inner">
    <div class="container">
        <div class="inner-content" style="max-width: 800px; margin: 50px auto; padding: 40px; background: rgba(0,0,0,0.5); border-radius: 10px; text-align: center;">
            <div style="color: #f44336; font-size: 24px; margin-bottom: 20px;">
                <i class="fa-solid fa-times-circle" style="font-size: 48px; margin-bottom: 15px;"></i>
                <h2 style="color: #f44336; margin: 15px 0;">{{ __('Ошибка оплаты') }}</h2>
            </div>
            <div style="color: #fff; font-size: 16px; margin-bottom: 30px;">
                <p>{{ __('Оплата по заказу') }} <strong style="color: #f44336;">{{ $orderId }}</strong> {{ __('не прошла.') }}</p>
                <p style="margin-top: 15px;">{{ __('Сумма платежа') }}: <strong style="color: #f44336;">{{ $amount }}</strong> {{ __('руб.') }}</p>
                <p style="margin-top: 15px;">{{ __('Пожалуйста, попробуйте ещё раз или обратитесь в службу поддержки.') }}</p>
            </div>
            <div style="margin-top: 30px;">
                <a href="{{ route('cabinet') }}" style="display: inline-block; padding: 12px 30px; background: #f44336; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background 0.3s;">
                    {{ __('Перейти в кабинет') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection