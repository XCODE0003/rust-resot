@extends('layouts.main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if($isPaid ?? false)
                <div class="alert alert-success" role="alert">
                    <h4 class="alert-heading">{{ __('Платёж успешно выполнен!') }}</h4>
                    <p>{{ __('Ваш заказ') }} <strong>{{ $orderId ?? $uuid }}</strong> {{ __('успешно оплачен.') }}</p>
                    @if($amount)
                        <p class="mb-0">{{ __('Сумма платежа') }}: <strong>{{ $amount }}</strong></p>
                    @endif
                    <hr>
                    <p class="mb-0">{{ __('Благодарим за платёж!') }}</p>
                </div>
            @else
                <div class="alert alert-warning" role="alert">
                    <h4 class="alert-heading">{{ __('Платёж обрабатывается') }}</h4>
                    <p>{{ __('Ваш заказ') }} <strong>{{ $orderId ?? $uuid }}</strong> {{ __('в процессе обработки.') }}</p>
                    <p class="mb-0">{{ __('Пожалуйста, подождите подтверждения транзакции.') }}</p>
                </div>
            @endif
            <div class="text-center mt-4">
                <a href="{{ route('cabinet') }}" class="btn btn-primary">{{ __('Перейти в кабинет') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection

