@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Статистика магазина'))
@section('headerTitle', __('Журналы и логи'))
@section('headerDesc', __('Статистика магазина') . '.')

@php
    $name = 'name_' . app()->getLocale();
@endphp

@section('wrap')
    <link href=https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.20/css/dataTables.bootstrap4.min.css rel=stylesheet>

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">

            <div class="col-12">

                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start gx-3 mb-3">
                            <div class="card-title">
                                <h6 class="title">{{ __('Статистика магазина') }}</h6>
                            </div>
                            <div class="card-tools">
                                <ul class="card-tools-nav">
                                    <li @if($data["server_id"] == 0) class="active" @endif><a href="{{ route('logs.shop', ['type' => $data["type"], 'server_id' => 0, 'item_id' => $data["item_id"]]) }}"><span>{{ __('Все') }}</span></a></li>
                                    @foreach($data["servers"] as $server)
                                        <li @if($data["server_id"] == $server->id) class="active" @endif><a href="{{ route('logs.shop', ['type' => $data["type"], 'server_id' => $server->id, 'item_id' => $data["item_id"]]) }}"><span>{{ $server->name }}</span></a></li>
                                    @endforeach
                                </ul>
                                <ul class="card-tools-nav">
                                    <li @if($data["type"] == 'day') class="active" @endif><a href="{{ route('logs.shop', ['type' => 'day', 'server_id' => $data["server_id"], 'item_id' => $data["item_id"]]) }}"><span>{{ __('День') }}</span></a></li>
                                    <li @if($data["type"] == 'week') class="active" @endif><a href="{{ route('logs.shop', ['type' => 'week', 'server_id' => $data["server_id"], 'item_id' => $data["item_id"]]) }}"><span>{{ __('Неделя') }}</span></a></li>
                                    <li @if($data["type"] == 'month') class="active" @endif><a href="{{ route('logs.shop', ['type' => 'month', 'server_id' => $data["server_id"], 'item_id' => $data["item_id"]]) }}"><span>{{ __('Месяц') }}</span></a></li>
                                    <li @if($data["type"] == 'all') class="active" @endif><a href="{{ route('logs.shop', ['type' => 'all', 'server_id' => $data["server_id"], 'item_id' => $data["item_id"]]) }}"><span>{{ __('Все') }}</span></a></li>
                                </ul>
                                <ul class="card-tools-nav" style="justify-content: flex-end;">
                                    <div class="form-control-wrap" style="width: 250px;">
                                        <select id="item_id" name="item_id" class="form-select" data-search="on">
                                            <option value="0">{{ __('Все') }}</option>
                                            @foreach($shopitems as $shopitem)
                                                <option value="{{ $shopitem->id }}" @if($shopitem->id == $data["item_id"]) selected @endif>{{ $shopitem->$name }} ({{ $shopitem->id }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </ul>
                            </div>
                        </div>
                        <div class="nk-sale-data-group align-center justify-between gy-3 gx-5">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $data["total_amount"] }} <small>{{ __('руб.') }}</small></span>
                            </div>
                            <div class="nk-sale-data">
                                <span class="amount sm">{{ $data["total_count"] }} <small>{{ __('покупки') }}</small></span>
                            </div>
                        </div>
                        <div class="nk-sales-ck large pt-4">
                            <canvas class="sales-overview-chart" id="purchasesOverview"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">

                <div class="card card-bordered card-full">
                        <div class="card-inner">
                            <div class="container container-table_wrapper" style="max-width: 100%">
                        <div class="card-title">
                            <h6 class="title">{{ __('Покупки') }}</h6>
                        </div>
                        <table cellspacing=0 class="table table-bordered table-hover table-inverse table-striped" id="purchases-table" width=100%>
                            <thead>
                            <tr>
                                <th>{{ __('No') }}
                                <th>{{ __('Название') }}
                                <th>{{ __('Покупатель') }}
                                <th>{{ __('Steam ID') }}
                                <th>{{ __('Получатель') }}
                                <th>{{ __('Steam ID') }}
                                <th>{{ __('Дата') }}
                                <th>{{ __('Сумма') }}
                                <th>{{ __('Количество') }}
                                <th>{{ __('Сервер') }}
                            <tfoot>
                            <tr>
                                <th>{{ __('No') }}
                                <th>{{ __('Название') }}
                                <th>{{ __('Покупатель') }}
                                <th>{{ __('Steam ID') }}
                                <th>{{ __('Получатель') }}
                                <th>{{ __('Steam ID') }}
                                <th>{{ __('Дата') }}
                                <th>{{ __('Сумма') }}
                                <th>{{ __('Количество') }}
                                <th>{{ __('Сервер') }}
                            <tbody>
                            @foreach($data["transactions"] as $transaction)
                            <tr>
                                <td><span class="tb-lead"><a href="#">{{ $loop->iteration }}</a></span>
                                <td><span class="tb-lead">{{ $transaction->item_name ?: ($transaction->set_name ? 'сет ' . $transaction->set_name :__('Неизвестно')) }}</span>
                                <td><span class="tb-lead">{{ $transaction->users_name }}</span>
                                <td><span class="tb-lead">{{ $transaction->users_steam_id }}</span>
                                <td><span class="tb-lead">{{ $transaction->name }}</span>
                                <td><span class="tb-lead">{{ $transaction->steam_id }}</span>
                                <td><span class="tb-sub">{{ $transaction->created_at }}</span>
                                <td><span class="tb-sub tb-amount">{{ $transaction->price }} <span>{{ __('руб.') }}</span></span>
                                <td><span class="tb-sub tb-amount">{{ $transaction->amount }} <span>{{ __('шт.') }}</span></span>
                                <td><span class="badge badge-dot">{{ $transaction->server_name }}</span>
                            @endforeach

                        </table>
                    </div>

                    </div>

                        </div>
                    </div>
                </div>

    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#item_id').on('change', function() {
                    document.location.replace('{{ route('logs.shop') }}?type={{ $data["type"] }}&server_id={{ $data["server_id"] }}'+'&item_id='+$(this).val());
                });
            });
        </script>

    <script>

        var purchasesOverview = {
            labels: [
                @foreach($data["purchases"] as $purchase)
                "{{ $purchase["date"] }}",
                @endforeach
            ],
            dataUnit: 'руб.',
            lineTension: 0.1,
            datasets: [{
                label: "{{ __('Статистика платежей') }}",
                color: "#798bff",
                background: NioApp.hexRGB('#798bff', .3),
                data: [
                    @foreach($data["purchases"] as $purchase)
                        "{{ $purchase["price"] }}",
                    @endforeach
                ],
            }]
        };

        function lineSalesOverview(selector, set_data) {
            var $selector = selector ? $(selector) : $('.sales-overview-chart');
            $selector.each(function () {
                var $self = $(this),
                    _self_id = $self.attr('id'),
                    _get_data = typeof set_data === 'undefined' ? eval(_self_id) : set_data;

                var selectCanvas = document.getElementById(_self_id).getContext("2d");
                var chart_data = [];

                for (var i = 0; i < _get_data.datasets.length; i++) {
                    chart_data.push({
                        label: _get_data.datasets[i].label,
                        tension: _get_data.lineTension,
                        backgroundColor: _get_data.datasets[i].background,
                        borderWidth: 2,
                        borderColor: _get_data.datasets[i].color,
                        pointBorderColor: "transparent",
                        pointBackgroundColor: "transparent",
                        pointHoverBackgroundColor: "#fff",
                        pointHoverBorderColor: _get_data.datasets[i].color,
                        pointBorderWidth: 2,
                        pointHoverRadius: 3,
                        pointHoverBorderWidth: 2,
                        pointRadius: 3,
                        pointHitRadius: 3,
                        data: _get_data.datasets[i].data
                    });
                }

                var chart = new Chart(selectCanvas, {
                    type: 'line',
                    data: {
                        labels: _get_data.labels,
                        datasets: chart_data
                    },
                    options: {
                        legend: {
                            display: _get_data.legend ? _get_data.legend : false,
                            labels: {
                                boxWidth: 30,
                                padding: 20,
                                fontColor: '#6783b8'
                            }
                        },
                        maintainAspectRatio: false,
                        tooltips: {
                            enabled: true,
                            callbacks: {
                                title: function title(tooltipItem, data) {
                                    return data['labels'][tooltipItem[0]['index']];
                                },
                                label: function label(tooltipItem, data) {
                                    return data.datasets[tooltipItem.datasetIndex]['data'][tooltipItem['index']] + ' ' + _get_data.dataUnit;
                                }
                            },
                            backgroundColor: '#eff6ff',
                            titleFontSize: 13,
                            titleFontColor: '#6783b8',
                            titleMarginBottom: 6,
                            bodyFontColor: '#9eaecf',
                            bodyFontSize: 12,
                            bodySpacing: 4,
                            yPadding: 10,
                            xPadding: 10,
                            footerMarginTop: 0,
                            displayColors: false
                        },
                        scales: {
                            yAxes: [{
                                display: true,
                                stacked: _get_data.stacked ? _get_data.stacked : false,
                                ticks: {
                                    beginAtZero: true,
                                    fontSize: 11,
                                    fontColor: '#9eaecf',
                                    padding: 10,
                                    callback: function callback(value, index, values) {
                                        return value;
                                    },
                                    min: 0,
                                    stepSize: 500
                                },
                                gridLines: {
                                    color: @if(session()->has('theme') && session()->get('theme') == 'dark') "#323131" @else "#e5ecf8" @endif,
                                    tickMarkLength: 0,
                                    zeroLineColor: @if(session()->has('theme') && session()->get('theme') == 'dark') "#323131" @else "#e5ecf8" @endif
                                }
                            }],
                            xAxes: [{
                                display: true,
                                stacked: _get_data.stacked ? _get_data.stacked : false,
                                ticks: {
                                    fontSize: 9,
                                    fontColor: '#9eaecf',
                                    source: 'auto',
                                    padding: 10
                                },
                                gridLines: {
                                    color: "transparent",
                                    tickMarkLength: 0,
                                    zeroLineColor: 'transparent'
                                }
                            }]
                        }
                    }
                });
            });
        } // init chart
        NioApp.coms.docReady.push(function () {
            lineSalesOverview();
        });

        </script>

    <script>
        $(document).ready(function() {
            $('#purchases-table').DataTable({
                "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
            });
        });
    </script>

    <script src=https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.20/js/jquery.dataTables.min.js></script>
    <script src=https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.20/js/dataTables.bootstrap4.min.js></script>
    @endpush

    <!-- .nk-block -->
@endsection
