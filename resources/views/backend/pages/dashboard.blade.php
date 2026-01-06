@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Dashboard'))
@section('headerTitle', __('Dashboard'))
@section('headerDesc', __('Основные показатели сервера') . '.')
@section('wrap')

                <!-- .nk-block-head -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-xxl-6">
                            <div class="row g-gs" id="visits">

                                <div class="row g-gs">

                                    <div class="col-12">

                                        <div class="card card-bordered">
                                            <div class="card-inner">
                                                <div class="card-title-group align-start gx-3 mb-3">
                                                    <div class="card-title">
                                                        <h6 class="title">{{ __('Статистика посещений') }}</h6>
                                                    </div>
                                                </div>
                                                <div class="nk-sale-data-group align-center justify-between gy-3 gx-5">
                                                    <div class="nk-sale-data">
                                                        <span class="amount"></span>
                                                    </div>
                                                    <div class="nk-sale-data">
                                                        <span class="amount sm">{{ $data["statistics"]["total_visits"] }} <small>{{ __('посещений') }}</small></span>
                                                    </div>
                                                </div>
                                                <div class="nk-sales-ck large pt-4">
                                                    <canvas class="sales-overview-chart" id="visitsOverview"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">

                                        <div class="card card-bordered">
                                            <div class="card-inner">
                                                <div class="card-title-group align-start gx-3 mb-3">
                                                    <div class="card-title">
                                                        <h6 class="title">{{ __('Статистика посетителей') }}</h6>
                                                    </div>
                                                </div>
                                                <div class="nk-sale-data-group align-center justify-between gy-3 gx-5">

                                                    <div class="nk-sale-data">
                                                        <span class="amount"></span>
                                                    </div>
                                                    <div class="nk-sale-data">
                                                        <span class="amount sm">{{ $data["statistics"]["total_visitors"] }} <small>{{ __('посетителей') }}</small></span>
                                                    </div>
                                                </div>
                                                <div class="nk-sales-ck large pt-4">
                                                    <canvas class="sales-overview-chart" id="visitorsOverview"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                </div>

                            </div><!-- .row -->
                        </div><!-- .col -->


                        <div class="col-xxl-6">

                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="card-title-group align-start gx-3 mb-3">
                                        <div class="card-title">
                                            <h6 class="title">{{ __('Статистика регистраций') }}</h6>
                                            <p>{{ __('за все время') }}.</p>
                                        </div>

                                        <div class="card-tools">
                                            <ul class="card-tools-nav">
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="nk-sale-data-group align-center justify-between gy-3 gx-5">
                                        <div class="nk-sale-data">
                                <span class="amount sm">{{ $data["registrations"]["total_accounts_count"] }}
                                    <small>{{ __('аккаунтов') }}</small></span>
                                        </div>
                                    </div>
                                    <div class="nk-sales-ck large pt-4">
                                        <canvas class="sales-overview-chart" id="regAccountsOverview"></canvas>
                                    </div>

                                    <div class="bottom-marge"></div>

                                    <div class="nk-sale-data-group align-center justify-between gy-3 gx-5">
                                        <div class="nk-sale-data">
                                <span class="amount sm">{{ $data["registrations"]["total_users_count"] }}
                                    <small>{{ __('пользователей') }}</small></span>
                                        </div>
                                    </div>
                                    <div class="nk-sales-ck large pt-4">
                                        <canvas class="sales-overview-chart" id="regUsersOverview"></canvas>
                                    </div>

                                </div>
                            </div>

                        </div><!-- .col -->



                        <div class="col-xxl-6">
                            <div class="card card-bordered">

                        <div class="card-inner">

                            <div class="card-title-group align-start gx-3 mb-3">
                                <div class="card-title">
                                    <h6 class="title">{{ __('Статистика платежей') }}</h6>
                                </div>
                                <div class="card-tools">
                                    <ul class="card-tools-nav">
                                    </ul>
                                </div>
                            </div>
                            <div class="nk-sale-data-group align-center justify-between gy-3 gx-5">
                                <div class="nk-sale-data">
                                    <span class="amount">{{ $data["payments"]["total_amount"] }} <small>{{ __('руб.') }}</small></span>
                                </div>
                                <div class="nk-sale-data">
                                    <span class="amount sm">{{ $data["payments"]["total_count"] }} <small>{{ __('платежа') }}</small></span>
                                </div>
                            </div>
                            <div class="nk-sales-ck large full-ck">
                                <canvas class="sales-overview-chart" id="paymentsOverview"></canvas>
                            </div>
                        </div>

                            </div>
                        </div>

                        <div class="col-xxl-6">
                            <div class="card card-bordered">

                                <div class="card-inner p-0 border-top">
                                    <div class="nk-tb-list nk-tb-orders">
                                        <div class="nk-tb-item nk-tb-head">
                                            <div class="nk-tb-col"><span>{{ __('No') }}</span></div>
                                            <div class="nk-tb-col tb-col-sm"><span>{{ __('Пользователь') }}</span></div>
                                            <div class="nk-tb-col tb-col-md"><span>{{ __('Дата') }}</span></div>
                                            <div class="nk-tb-col"><span>{{ __('Сумма') }}</span></div>
                                            <div class="nk-tb-col"><span>{{ __('Платежная система') }}</span></div>
                                            <div class="nk-tb-col"><span class="d-none d-sm-inline">{{ __('Статус') }}</span></div>
                                        </div>

                                        @foreach($data["payments"]["transactions"] as $transaction)
                                            @if ($loop->index < 5)

                                            <div class="nk-tb-item">
                                                <div class="nk-tb-col">
                                                    <span class="tb-lead"><a href="#">{{ $loop->iteration }}</a></span>
                                                </div>
                                                <div class="nk-tb-col tb-col-sm">
                                                    <div class="user-card">
                                                        <div class="user-name">
                                                            <span class="tb-lead">{{ $transaction->name ?: __('Неизвестно') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="nk-tb-col tb-col-md">
                                                    <span class="tb-sub">{{ $transaction->created_at }}</span>
                                                </div>
                                                <div class="nk-tb-col">
                                                    <span class="tb-sub tb-amount">{{ $transaction->amount }} <span>{{ __('руб.') }}</span></span>
                                                </div>
                                                <div class="nk-tb-col">
                                                    <span class="badge badge-dot">{{ $transaction->payment_system }}</span>
                                                </div>
                                                <div class="nk-tb-col">
                                                    @if ($transaction->status == 1)
                                                        <span class="badge badge-dot badge-dot-xs badge-success">{{ __('Успешно') }} </span>
                                                    @else
                                                        <span class="badge badge-dot badge-dot-xs badge-danger">{{ __('Не успешно') }} </span>
                                                    @endif
                                                </div>
                                            </div>
                                            @endif

                                        @endforeach

                                    </div>
                                </div>

                                <div class="card-inner-sm border-top text-center d-sm-none">
                                    <a href="{{ route('logs.payments') }}" class="btn btn-link btn-block">{{ __('Смотреть подробнее') }}</a>
                                </div>
                            </div><!-- .card -->
                        </div><!-- .col -->

                        <!-- .card -->
                        </div><!-- .col -->
                    </div><!-- .row -->

                <!-- .nk-block -->


                @push('scripts')
                <script>

                    var visitsOverview = {
                        labels: [
                            @foreach($data["statistics"]["visits"] as $visit)
                                "{{ $visit["date"] }}",
                            @endforeach
                        ],
                        dataUnit: 'п.',
                        lineTension: 0.1,
                        datasets: [{
                            label: "{{ __('Статистика посещений') }}",
                            color: "#798bff",
                            background: NioApp.hexRGB('#798bff', .3),
                            data: [
                                @foreach($data["statistics"]["visits"] as $visit)
                                    "{{ $visit["count"] }}",
                                @endforeach
                            ],
                        }]
                    };

                    var visitorsOverview = {
                        labels: [
                            @foreach($data["statistics"]["visitors"] as $visitors)
                                "{{ $visitors["date"] }}",
                            @endforeach
                        ],
                        dataUnit: 'п.',
                        lineTension: 0.1,
                        datasets: [{
                            label: "{{ __('Статистика посещений') }}",
                            color: "#798bff",
                            background: NioApp.hexRGB('#798bff', .3),
                            data: [
                                @foreach($data["statistics"]["visitors"] as $visitors)
                                    "{{ $visitors["count"] }}",
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


                <script>

                    var paymentsOverview = {
                        labels: [
                            @foreach($data["payments"]["payments"] as $payment)
                                "{{ $payment["date"] }}",
                            @endforeach
                        ],
                        dataUnit: 'руб',
                        lineTension: 0.1,
                        datasets: [{
                            label: "{{ __('Статистика платежей') }}",
                            color: "#798bff",
                            background: NioApp.hexRGB('#798bff', .3),
                            data: [
                                @foreach($data["payments"]["payments"] as $payment)
                                    "{{ $payment["amount"] }}",
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
                                                color: "#e5ecf8",
                                                tickMarkLength: 0,
                                                zeroLineColor: '#e5ecf8'
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

                    var regUsersOverview = {
                        labels: [
                            @foreach($data["registrations"]["users"] as $user)
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
                                @foreach($data["registrations"]["users"] as $user)
                                    "{{ $user["count"] }}",
                                @endforeach
                            ],
                        }]
                    };

                    var regAccountsOverview = {
                        labels: [
                            @foreach($data["registrations"]["accounts"] as $account)
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
                                @foreach($data["registrations"]["accounts"] as $account)
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
@endsection