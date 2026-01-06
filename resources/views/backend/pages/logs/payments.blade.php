@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Статистика платежей'))
@section('headerTitle', __('Журналы и логи'))
@section('headerDesc', __('Статистика платежей') . '.')

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
                                <h6 class="title">{{ __('Статистика платежей') }}</h6>
                            </div>
                            <div class="card-tools">
                                <ul class="card-tools-nav">
                                    <li @if($data["server_id"] == 0) class="active" @endif><a href="{{ route('logs.payments', ['type' => $data["type"], 'server_id' => 0, 'status' => $data["status"], 'user_id' => $data["user_id"], 'payment_system' => $data["payment_system"]]) }}"><span>{{ __('Все') }}</span></a></li>
                                    @foreach($data["servers"] as $server)
                                        <li @if($data["server_id"] == $server->id) class="active" @endif><a href="{{ route('logs.payments', ['type' => $data["type"], 'server_id' => $server->id, 'status' => $data["status"], 'user_id' => $data["user_id"], 'payment_system' => $data["payment_system"]]) }}"><span>{{ $server->name }}</span></a></li>
                                    @endforeach
                                </ul>
                                <ul class="card-tools-nav">
                                    <li @if($data["type"] == 'day') class="active" @endif><a href="{{ route('logs.payments', ['type' => 'day', 'server_id' => $data["server_id"], 'status' => $data["status"], 'user_id' => $data["user_id"], 'payment_system' => $data["payment_system"]]) }}"><span>{{ __('День') }}</span></a></li>
                                    <li @if($data["type"] == 'week') class="active" @endif><a href="{{ route('logs.payments', ['type' => 'week', 'server_id' => $data["server_id"], 'status' => $data["status"], 'user_id' => $data["user_id"], 'payment_system' => $data["payment_system"]]) }}"><span>{{ __('Неделя') }}</span></a></li>
                                    <li @if($data["type"] == 'month') class="active" @endif><a href="{{ route('logs.payments', ['type' => 'month', 'server_id' => $data["server_id"], 'status' => $data["status"], 'user_id' => $data["user_id"], 'payment_system' => $data["payment_system"]]) }}"><span>{{ __('Месяц') }}</span></a></li>
                                    <li @if($data["type"] == 'all') class="active" @endif><a href="{{ route('logs.payments', ['type' => 'all', 'server_id' => $data["server_id"], 'status' => $data["status"], 'user_id' => $data["user_id"], 'payment_system' => $data["payment_system"]]) }}"><span>{{ __('Все') }}</span></a></li>
                                </ul>
                                <ul class="card-tools-nav" style="justify-content: flex-end;">
                                    <div class="form-control-wrap" style="width: 250px;">
                                        <select id="status" name="status" class="form-select">
                                            <option value="-1">{{ __('Все') }}</option>
                                            <option value="0" @if($data["status"] == 0) selected @endif>{{ __('Не успешные') }}</option>
                                            <option value="1" @if($data["status"] == 1) selected @endif>{{ __('Успешные') }}</option>
                                        </select>
                                    </div>
                                    <div class="form-control-wrap" style="width: 250px;">
                                        <div class="input-group input-daterange">
                                            <input type="text" name="date_start" id="date_start" class="form-control"
                                                   value="{{date('d.m.Y', strtotime($data['date_start'])) }}"
                                            >
                                            <div class="input-group-addon" style="margin-top: 5px;">{{ __('до') }}</div>
                                            <input type="text" name="date_end" id="date_end" class="form-control"
                                                   value="{{date('d.m.Y', strtotime($data['date_end'])) }}"
                                            >
                                        </div>
                                    </div>
                                </ul>
                            </div>
                        </div>
                        <div class="nk-sale-data-group align-center justify-between gy-3 gx-5">
                            <div class="nk-sale-data">
                                <span class="amount">{{ $data["total_amount"] }} <small>{{ __('руб.') }}</small></span>
                            </div>
                            <div class="nk-sale-data">
                                <span class="amount sm">{{ $data["total_count"] }} <small>{{ __('платежа') }}</small></span>
                            </div>
                        </div>
                        <div class="nk-sales-ck large pt-4">
                            <canvas class="sales-overview-chart" id="paymentsOverview"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">

                <div class="card card-bordered card-full">
                        <div class="card-inner">
                            <div class="container container-table_wrapper" style="max-width: 100%">
                        <div class="card-title">
                            <h6 class="title">{{ __('Транзакции') }}</h6>
                        </div>
                        <table cellspacing=0 class="table table-bordered table-hover table-inverse table-striped" id="payments-table" width=100%>
                            <thead>
                            <tr>
                                <th>{{ __('No') }}
                                <th>{{ __('Пользователь') }}
                                <th>{{ __('Дата') }}
                                <th class="sum">{{ __('Сумма') }}
                                <th class="sum">{{ __('Бонус') }}
                                <th>{{ __('Платежная система') }}
                                <th>{{ __('Сервер') }}
                                <th>{{ __('Статус') }}
                            </thead>
                            <tfoot>
                            <tr>
                                <th>Итого</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                            </tfoot>
                            <tbody>
                            @foreach($data["transactions"] as $transaction)
                            <tr>
                                <td><span class="tb-lead"><a href="#">{{ $loop->iteration }}</a></span>
                                <td><span class="tb-lead"><a href="{{ route('logs.payments') }}/?type=all&server_id=0&status=1&user_id={{ $transaction->user_id }}">{{ $transaction->name ?: $transaction->name ?:__('Неизвестно') }}</a></span>
                                <td><span class="tb-sub">{{ $transaction->created_at }}</span>
                                <td><span class="tb-sub tb-amount">{{ $transaction->amount }} <span>{{ __('руб.') }}</span></span>
                                <td><span class="tb-sub tb-amount">{{ $transaction->bonus_amount }} <span>{{ __('руб.') }}</span></span>
                                <td><span class="badge badge-dot"><a href="{{ route('logs.payments') }}/?type=month&server_id=0&status=1&payment_system={{ $transaction->payment_system }}">{{ $transaction->payment_system }}</a></span>
                                <td><span class="badge badge-dot">{{ $transaction->server_name }}</span>
                                <td>@if ($transaction->status == 1) <span class="badge badge-dot badge-dot-xs badge-success">{{ __('Успешно') }} </span> @else <span class="badge badge-dot badge-dot-xs badge-danger">{{ __('Не успешно') }} </span> @endif
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
                $('#status').on('change', function() {
                    console.log('click');
                    document.location.replace('{{ route('logs.payments') }}?type={{ $data['type'] }}&server_id={{ $data['server_id'] }}&status='+$(this).val()+'&user_id={{ $data['user_id'] }}&payment_system={{ $data['payment_system'] }}');
                });
            });

            $('.input-daterange input').each(function () {
                $(this).datepicker({format: 'dd.mm.yyyy', locale: 'ru'});
            });

            $(document).on("change", "#date_start, #date_end", function () {
                if ($("#date_start").val() != '' && $("#date_end").val() != '') {
                    document.location.replace('{{ route('logs.payments') }}?type={{ $data['type'] }}&server_id={{ $data['server_id'] }}&status={{ $data['status'] }}&user_id={{ $data['user_id'] }}&payment_system={{ $data['payment_system'] }}&date_start=' + $('#date_start').val() + '&date_end=' + $('#date_end').val() + '&date_type=0');
                }
            });
        </script>


    <script>
        var paymentsOverview = {
            labels: [
                @foreach($data["payments"] as $payment)
                "{{ $payment["date"] }}",
                @endforeach
            ],
            dataUnit: 'руб.',
            lineTension: 0.1,
            datasets: [{
                label: "{{ __('Статистика платежей') }}",
                color: "#798bff",
                background: NioApp.hexRGB('#798bff', .3),
                data: [
                    @foreach($data["payments"] as $payment)
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
            $('#payments-table').DataTable({
                "lengthMenu": [[10, 25, 50, 100, 500, 1000], [10, 25, 50, 100, 500, 1000]],
                "footerCallback": function(row, data, start, end, display) {
                    var api = this.api();


                    //Сумма всех строк которые соответствуют фильтру поиска с учетом всех страниц в результате
                    /*
                    api.columns('.sum').every(function () {
                        var columnData = this.data();
                        var sum = 0;

                        var filteredIndexes = api.rows({ search: 'applied' }).indexes();

                        filteredIndexes.each(function (index) {
                            const match = columnData[index].match(/[\d.]+/);
                            var number = match ? parseFloat(match[0]) : 0;
                            sum += number;
                        });

                        console.log('Сумма для отфильтрованных строк в столбце:', sum);
                        $(this.footer()).html(sum.toFixed(2));
                    });
                    */

                    //Сумма всех строк текущей страницы
                    api.columns('.sum', { page: 'current' }).every(function () {
                        var sum = api
                            .cells( null, this.index(), { page: 'current'} )
                            .render('display')
                            .reduce(function (a, b) {
                                if (typeof b !== 'string') {
                                    return 0;
                                }
                                const match_b = b.match(/[\d.]+/);

                                var x = parseFloat(a) || 0;
                                var y = parseFloat(match_b[0]) || 0;
                                return x + y;
                            }, 0);
                        console.log(this.index() +' '+ sum);
                        $(this.footer()).html(sum);
                    });
                }

            });
        });

    </script>


    <script src=https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.20/js/jquery.dataTables.min.js></script>
    <script src=https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.20/js/dataTables.bootstrap4.min.js></script>
    @endpush

    <!-- .nk-block -->
@endsection
