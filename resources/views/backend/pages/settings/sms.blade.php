@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Настройки'))
@section('headerTitle', __('Настройки'))
@section('headerDesc', __('Настройки СМС шлюза (redsms.ru)'))

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
                                                <label class="form-label" for="sms_login">{{ __('Логин') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="sms_login"
                                                           name="setting_sms_login" value="{{ config('options.sms_login', "") }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="sms_phone">{{ __('Телефон') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="sms_phone"
                                                           name="setting_sms_phone" value="{{ config('options.sms_phone', "") }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="sms_api_key">{{ __('API ключ') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="sms_api_key"
                                                           name="setting_sms_api_key" value="{{ config('options.sms_api_key', "") }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="sms_sender_name">{{ __('Имя отправителя для СМС') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="sms_sender_name"
                                                           name="setting_sms_sender_name" value="{{ config('options.sms_sender_name', "") }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="sms_sender_viber">{{ __('Имя отправителя для Viber') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="sms_sender_viber"
                                                           name="setting_sms_sender_viber" value="{{ config('options.sms_sender_viber', "") }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="sms_timer">{{ __('Время до отправки нового смс, сек.') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="sms_timer"
                                                           name="setting_sms_timer" value="{{ config('options.sms_timer', "60") }}">
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
