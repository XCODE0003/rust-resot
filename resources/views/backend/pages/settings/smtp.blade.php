@extends('backend.layouts.backend')

@section('title', 'Панель управления - ' . __('Настройки'))
@section('headerTitle', __('Настройки'))
@section('headerDesc', __('Настройки') . ' SMTP.')

@section('wrap')
    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">

                    <div class="tab-pane" id="shop">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <form action="{{ route('backend.settings') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row g-4">

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="smtp_host">{{ __('Имя хоста SMTP') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="smtp_host"
                                                           name="setting_smtp_host" value="{{ config('options.smtp_host', "") }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="smtp_user">{{ __('Имя пользователя SMTP') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="smtp_user"
                                                           name="setting_smtp_user" value="{{ config('options.smtp_user', "") }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="smtp_password">{{ __('Пароль SMTP') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="password" class="form-control" id="smtp_password"
                                                           name="setting_smtp_password" value="{{ config('options.smtp_password', "") }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="smtp_port">{{ __('Порт SMTP') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="smtp_port"
                                                           name="setting_smtp_port" value="{{ config('options.smtp_port', "25") }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="smtp_from">{{ __('Email отправителя') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="smtp_from"
                                                           name="setting_smtp_from" value="{{ config('options.smtp_from', "") }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="smtp_name">{{ __('Имя отправителя') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="smtp_name"
                                                           name="setting_smtp_name" value="{{ config('options.smtp_name', "") }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-lg btn-primary ml-auto">{{ __('Отправить') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- .nk-block -->
@endsection
