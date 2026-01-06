@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Заявки на вывод предметов'))
@section('headerTitle', __('Заявки на вывод предметов'))
@section('headerDesc', __('Список Заявок на вывод предметов') . '.')

@php
    $title = "title_" . app()->getLocale();
@endphp

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group" style="justify-content: flex-end;">
                            <div class="card-tools" style="display: flex; flex-direction: row;margin-right: 25px;">
                                <div class="form-control-wrap" style="width: 200px;">
                                    <select class="form-select form-control" id="status" name="status">
                                        <option value="-1" @if(request()->query('status') == -1) selected @endif>{{ __('Все') }}</option>
                                        @foreach(getdelivery_statuses() as $index => $status)
                                            <option value="{{ $index }}" @if(request()->query('status') == $index) selected @endif>{{ $status }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="card-tools d-none d-md-inline">
                                <form method="GET">
                                    <div class="form-control-wrap">
                                        <div class="form-icon form-icon-left">
                                            <em class="icon ni ni-search"></em>
                                        </div>
                                        <input type="text" class="form-control" name="search" value="{{ request()->query('search') }}" placeholder="{{ __('Поиск') }}...">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="card-inner p-0 border-top">
                        <div class="nk-tb-list nk-tb-orders">
                            <div class="nk-tb-item nk-tb-head">
                                <div class="nk-tb-col"><span class="sub-text">{{ __('№') }}</span></div>
                                <div class="nk-tb-col"><span class="sub-text">{{ __('Пользователь') }}</span></div>
                                <div class="nk-tb-col"><span class="sub-text">{{ __('Предмет') }}</span></div>
                                <div class="nk-tb-col"><span class="sub-text">{{ __('Диапазон цены') }}</span></div>
                                <div class="nk-tb-col"><span class="sub-text">{{ __('Ограничение цены') }}</span></div>
                                <div class="nk-tb-col tb-col-md"><span class="sub-text">{{ __('Статус') }}</span></div>
                                <div class="nk-tb-col tb-col-md"><span class="sub-text">{{ __('Примечание') }}</span></div>
                                <div class="nk-tb-col tb-col-md"><span class="sub-text">{{ __('Дата запроса') }}</span></div>
                                <div class="nk-tb-col tb-col-md"><span class="sub-text">{{ __('Дата вывода') }}</span></div>
                                <div class="nk-tb-col tb-col-md"><span class="sub-text"></span></div>
                            </div>

                            @foreach($deliveryrequests as $deliveryrequest)
                            <div class="nk-tb-item">
                                <div class="nk-tb-col">
                                    <span class="tb-lead">
                                        {{ $deliveryrequest->id }}
                                    </span>
                                </div>
                                <div class="nk-tb-col">
                                    <span class="tb-lead">
                                        {{ $deliveryrequest->user->name }}
                                    </span>
                                </div>
                                <div class="nk-tb-col caseitem-block">
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
                                            ID: {{ $deliveryrequest->item_id }}
                                        </span>

                                        <span class="tb-sub caseitem-text-block-lead">
                                            {{ __('Цена') }}: {{ $deliveryrequest->price }}₽
                                        </span>
                                        <span class="tb-sub caseitem-text-block-lead">
                                            {{ __('Сервер') }}: {{ $deliveryrequest->server }}
                                        </span>
                                    </div>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        ${{ $deliveryrequest->price_min }} - ${{ $deliveryrequest->price_max }}
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <form action="{{ route('delivery_requests.pricecap.set', $deliveryrequest) }}" method="POST">
                                        @csrf
                                        <span class="tb-lead form-price_cap">
                                            $<input type="number" min="0" step="0.001" name="price_cap" value="{{ $deliveryrequest->price_cap }}">
                                        </span>
                                    </form>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ getdelivery_status($deliveryrequest->status) }}
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ $deliveryrequest->note }}
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        {{ $deliveryrequest->date_request }}
                                    </span>
                                </div>
                                <div class="nk-tb-col tb-col-md">
                                    <span class="tb-sub">
                                        @if($deliveryrequest->date_execution !== NULL){{ $deliveryrequest->date_execution }}@else{{ '-' }}@endif
                                    </span>
                                </div>
                                <div class="nk-tb-col nk-tb-col-action">
                                    <div class="drodown">
                                        <a href="#"
                                           class="btn btn-sm btn-icon btn-trigger dropdown-toggle" data-toggle="dropdown" title="{{ __('Изменить статус') }}">
                                            <em class="icon ni ni-more-h"></em>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                            <ul class="link-list-opt no-bdr">

                                                <li>
                                                    <a href="{{ $deliveryrequest->user->steam_trade_url }}">
                                                        <em class="icon ni ni-piority"></em>
                                                        <span>{{ __('Торговать') }}</span>
                                                    </a>
                                                </li>
                                                <li class="divider"></li>
                                                <li>
                                                    <a href="{{ route('delivery_requests.status.set.inprocessing', $deliveryrequest) }}">
                                                        <em class="icon ni ni-piority"></em>
                                                        <span>{{ __('В обработке') }}</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('delivery_requests.status.set.waxpeer_api', $deliveryrequest) }}">
                                                        <em class="icon ni ni-piority"></em>
                                                        <span>{{ __('WaxpeerAPI') }}</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('delivery_requests.status.set.skinsback_api', $deliveryrequest) }}">
                                                        <em class="icon ni ni-piority"></em>
                                                        <span>{{ __('SkinsbackAPI') }}</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('delivery_requests.status.set.canceled', $deliveryrequest) }}">
                                                        <em class="icon ni ni-piority"></em>
                                                        <span>{{ __('Отменена') }}</span>
                                                    </a>
                                                </li>
                                                <li class="divider"></li>
                                                <li>
                                                    <a href="{{ route('delivery_requests.status.set.completed', $deliveryrequest) }}">
                                                        <em class="icon ni ni-piority"></em>
                                                        <span>{{ __('Выполнена') }}</span>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-inner">
                        {{ $deliveryrequests->links('backend.layouts.pagination.main') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- .nk-block -->

@endsection
@push('scripts')
    <script>
        $(document).ready(function () {
            $('#status').on('change', function() {
                document.location.replace('{{ route('backend.delivery_requests') }}?status='+this.value);
            });
        });
    </script>
@endpush