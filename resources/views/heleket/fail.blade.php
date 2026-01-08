@extends('layouts.main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">{{ __('Ошибка оплаты') }}</h4>
                <p>{{ __('Оплата по заказу') }} <strong>{{ $orderId ?? $uuid }}</strong> {{ __('не прошла.') }}</p>
                @if($amount)
                    <p class="mb-0">{{ __('Сумма платежа') }}: <strong>{{ $amount }}</strong></p>
                @endif
                @if($status)
                    <p class="mb-0">{{ __('Статус') }}: <strong>{{ $status }}</strong></p>
                @endif
                <hr>
                <p class="mb-0">{{ __('Пожалуйста, попробуйте ещё раз или обратитесь в службу поддержки.') }}</p>
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('cabinet') }}" class="btn btn-primary">{{ __('Перейти в кабинет') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection


