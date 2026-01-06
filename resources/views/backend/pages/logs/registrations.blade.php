@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Статистика регистраций'))
@section('headerTitle', __('Журналы и логи'))
@section('headerDesc', __('Статистика регистраций') . '.')

@section('wrap')
    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">

                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-title-group align-start gx-3 mb-3">
                            <div class="card-title">
                                <h6 class="title">{{ __('Статистика регистраций') }}</h6>
                                <p>{{ __('за все время') }}.</p>
                            </div>

                            <div class="card-tools">
                                <ul class="card-tools-nav">
                                    @foreach($data["servers"] as $server)
                                        <li @if($data["server_id"] == $server->id) class="active" @endif><a
                                                    href="{{ route('logs.registrations', ['server_id' => $server->id]) }}"><span>{{ $server->name }}</span></a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <div class="nk-sale-data-group align-center justify-between gy-3 gx-5">
                            <div class="nk-sale-data">
                                <span class="amount sm">{{ $data["total_accounts_count"] }}
                                    <small>{{ __('аккаунтов') }}</small></span>
                            </div>
                        </div>
                        <div class="nk-sales-ck large pt-4">
                            <canvas class="sales-overview-chart" id="regAccountsOverview"></canvas>
                        </div>

                        <div class="bottom-marge"></div>

                        <div class="nk-sale-data-group align-center justify-between gy-3 gx-5">
                            <div class="nk-sale-data">
                                <span class="amount sm">{{ $data["total_users_count"] }}
                                    <small>{{ __('пользователей') }}</small></span>
                            </div>
                        </div>
                        <div class="nk-sales-ck large pt-4">
                            <canvas class="sales-overview-chart" id="regUsersOverview"></canvas>
                        </div>

                    </div>
                </div>


            </div>

        </div>
    </div>

    @push('scripts')
        <script>

            var regUsersOverview = {
                labels: [
                    @foreach($data["users"] as $user)
                        "{{ $user["date"] }}",
                    @endforeach
                ],
                dataUnit: 'u',
                lineTension: 0.1,
                datasets: [{
                    label: "{{ __('Статистика регистраций') }}",
                    color: "#798bff",
                    background: NioApp.hexRGB('#798bff', .3),
                    data: [
                        @foreach($data["users"] as $user)
                            "{{ $user["count"] }}",
                        @endforeach
                    ],
                }]
            };

            var regAccountsOverview = {
                labels: [
                    @foreach($data["accounts"] as $account)
                        "{{ $account["date"] }}",
                    @endforeach
                ],
                dataUnit: 'u',
                lineTension: 0.1,
                datasets: [{
                    label: "{{ __('Статистика регистраций') }}",
                    color: "#798bff",
                    background: NioApp.hexRGB('#798bff', .3),
                    data: [
                        @foreach($data["accounts"] as $account)
                            "{{ $account["count"] }}",
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
                                        stepSize: 10
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
    @endpush

    <!-- .nk-block -->
@endsection