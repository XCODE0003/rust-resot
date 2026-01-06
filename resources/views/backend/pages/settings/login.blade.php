@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Настройки'))
@section('headerTitle', __('Настройки'))
@section('headerDesc', __('Настройки логина/регистрации') . ".")

@section('wrap')
    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <form action="{{ route('backend.settings') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row g-4">

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="change_login">{{ __('Изменять Логин аккаунта') }}</label>
                                                <div class="form-control-wrap">
                                                    <select id="change_login" name="setting_change_login" class="form-select">
                                                        <option value="0" @if (config('options.change_login') !== NULL && config('options.change_login') === '0') selected @endif>{{ __('Запрещено') }}</option>
                                                        <option value="1" @if (config('options.change_login') !== NULL && config('options.change_login') === '1') selected @endif>{{ __('Разрешено') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="change_email">{{ __('Изменять Email аккаунта') }}</label>
                                                <div class="form-control-wrap">
                                                    <select id="change_email" name="setting_change_email" class="form-select">
                                                        <option value="0" @if (config('options.change_email') !== NULL && config('options.change_email') === '0') selected @endif>{{ __('Запрещено') }}</option>
                                                        <option value="1" @if (config('options.change_email') !== NULL && config('options.change_email') === '1') selected @endif>{{ __('Разрешено') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="login_max_char">{{ __('Максимальное количество символов логина') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="login_max_char" name="setting_login_max_char"
                                                           value="{{ config('options.login_max_char', '16') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="password_max_char">{{ __('Максимальное количество символов пароля') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="password_max_char" name="setting_password_max_char"
                                                           value="{{ config('options.password_max_char', '16') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-4">
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

    <!-- .nk-block -->
@endsection
