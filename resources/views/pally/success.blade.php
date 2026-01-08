@extends('layouts.main')

@section('content')
<div class="inner">
    <div class="container">
        <div class="inner-content" style="max-width: 800px; margin: 50px auto; padding: 40px; background: rgba(0,0,0,0.5); border-radius: 10px; text-align: center;">
            <div style="color: #4CAF50; font-size: 24px; margin-bottom: 20px;">
                <i class="fa-solid fa-check-circle" style="font-size: 48px; margin-bottom: 15px;"></i>
                <h2 style="color: #4CAF50; margin: 15px 0;">{{ __('Платёж успешно выполнен!') }}</h2>
            </div>
            <div style="color: #fff; font-size: 16px; margin-bottom: 30px;">
                <p>{{ __('Ваш заказ') }} <strong style="color: #4CAF50;">{{ $orderId }}</strong> {{ __('успешно оплачен.') }}</p>
                <p style="margin-top: 15px;">{{ __('Сумма платежа') }}: <strong style="color: #4CAF50;">{{ $amount }}</strong> {{ __('руб.') }}</p>
            </div>
            <div style="margin-top: 30px;">
                <a href="{{ route('cabinet') }}" style="display: inline-block; padding: 12px 30px; background: #4CAF50; color: #fff; text-decoration: none; border-radius: 5px; font-weight: bold; transition: background 0.3s;">
                    {{ __('Перейти в кабинет') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection