@extends('layouts.main')

@section('title', __('Список Заявок на вывод предметов') . ' - ' . config('options.main_title_'.app()->getLocale(), '') )

@push('head')
    <link rel="stylesheet" href="/css/trading.css?ver=1.1">
@endpush

@section('content')

    <div class="inner-header">{{ __('Список Заявок на вывод предметов') }}</div>

    <div class="inner">
        <div class="container">

            <div class="trading">

                <div class="stats-content tabs">

                    <div class="stats-content-rank tab stat-tab active" id="1">

                        <div class="stats-rank-list">
                            <div class="stats-type">
                                <div class="deliveryrequest-id">{{ __('Имя') }}</div>
                                <div class="pvp-stats caseitem-block caseitem-block-title">{{ __('Предмет') }}</div>
                                <div class="pvp-stats">{{ __('Статус') }}</div>
                                <div class="pvp-stats">{{ __('Дата запроса') }}</div>
                                <div class="pvp-stats">{{ __('Дата вывода') }}</div>
                                <div class="pvp-stats" style="width: 6% !important;"></div>
                            </div>

                            <ul class="rank-list deliveryrequest-list">
                                @foreach($deliveryrequests as $deliveryrequest)
                                    <li style="align-items: center;">
                                        <div class="deliveryrequest-id">#{{ $deliveryrequest->id }}</div>
                                        <div class="pvp-stats caseitem-block">
                                            <div class="caseitem-img-block">
                                                <span class="tb-lead caseitem-list">
                                                    <img src="{{ $deliveryrequest->item_icon }}" alt="{{ $deliveryrequest->item }}">
                                                </span>
                                            </div>
                                            <div class="caseitem-text-block">
                                                <span class="tb-lead">
                                                    {{ $deliveryrequest->item }}
                                                </span>
                                                <span class="tb-sub caseitem-text-block-lead">
                                                    {{ __('Цена') }}: {{ $deliveryrequest->price }}₽
                                                </span>
                                            </div>

                                        </div>
                                        <div class="pvp-stats">{{ getdelivery_user_status($deliveryrequest->status) }}</div>
                                        <div class="pvp-stats">{{ $deliveryrequest->date_request }}</div>
                                        <div class="pvp-stats">@if($deliveryrequest->date_execution !== NULL){{ $deliveryrequest->date_execution }}@else{{ '-' }}@endif</div>
                                            @if($deliveryrequest->status == 1 || $deliveryrequest->status == 0)
                                                <button class="deliveryrequest-btn-cancel">
                                                    <a href="{{ route('delivery_requests.cancel', $deliveryrequest) }}">
                                                        <span>{{ __('Отменить') }}</span>
                                                    </a>
                                                </button>
                                            @endif
                                    </li>
                                @endforeach

                            </ul>

                            <div class="pagination">
                                {{ $deliveryrequests->links('layouts.pagination.stat-sort') }}
                            </div>
                        </div>
                    </div>

                </div>


            </div>


        </div>
    </div>

@endsection
@push('scripts')
    <script>
        $('#topup').on('click', function () {
            $('.balance-modal').show();
        });
    </script>
@endpush