@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Логи пользователя'))
@section('headerTitle', __('Журналы и логи'))
@section('headerDesc', __('Логи пользователя') . '.')

@section('wrap')
    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">

                <h6>{{ __('Логи игровой валюты') }}</h6>
                <div class="card card-bordered">
                    <div class="card-inner logs-block">
                        <pre>{{ $payments_log }}</pre>
                    </div>
                </div>

                <h6 style="margin-top: 50px;">{{ __('Логи администраторов') }}</h6>
                <div class="card card-bordered">
                    <div class="card-inner logs-block">
                        <pre>{{ $admin_log }}</pre>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- .nk-block -->
@endsection