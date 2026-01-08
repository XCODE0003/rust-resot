@extends('layouts.main')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">{{ __('Платёж успешно выполнен!') }}</h4>
                <p>{{ __('Ваш заказ') }} <strong>{{ $orderId }}</strong> {{ __('успешно оплачен.') }}</p>
                <p class="mb-0">{{ __('Сумма платежа') }}: <strong>{{ $amount }}</strong> {{ __('руб.') }}</p>
                <hr>
                <p class="mb-0">{{ __('Благодарим за платёж!') }}</p>
            </div>
            <div class="text-center mt-4">
                <a href="{{ route('cabinet') }}" class="btn btn-primary">{{ __('Перейти в кабинет') }}</a>
            </div>
        </div>
    </div>
</div>
@endsection


