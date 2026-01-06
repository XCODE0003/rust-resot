@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Логи игровой валюты'))
@section('headerTitle', __('Журналы и логи'))
@section('headerDesc', __('Логи игровой валюты') . '.')

@section('wrap')
    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">

                <div class="card card-bordered">
                    <div class="card-inner logs-block">
                        <pre>{{ $log }}</pre>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- .nk-block -->
@endsection