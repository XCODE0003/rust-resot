@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Настройки'))
@section('headerTitle', __('Настройки'))
@section('headerDesc', __('Падающий снег') . '.')

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
                                                <label class="form-label" for="snowfall_status">{{ __('Статус') }}</label>
                                                <div class="form-control-wrap">
                                                    <select id="snowfall_status" name="setting_snowfall_status" class="form-select">
                                                        <option value="0" @if(config('options.snowfall_status', '0') === '0') selected @endif>{{ __('Отключено') }}</option>
                                                        <option value="1" @if(config('options.snowfall_status', '0') === '1') selected @endif>{{ __('Включено') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="snowfall_flakecount">{{ __('Количество снежинок') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="number" min="0" max="1000" class="form-control" id="snowfall_flakecount" name="setting_snowfall_flakecount" value="{{ config('options.snowfall_flakecount', '100') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="snowfall_minsize">{{ __('Минимальный размер снежинки') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="number" min="0" max="100" class="form-control" id="snowfall_minsize" name="setting_snowfall_minsize" value="{{ config('options.snowfall_minsize', '2') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="snowfall_maxsize">{{ __('Максимальный размер снежинки') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="number" min="0" max="100" class="form-control" id="snowfall_maxsize" name="setting_snowfall_maxsize" value="{{ config('options.snowfall_maxsize', '10') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="snowfall_minspeed">{{ __('Минимальная скорость снежинки') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="number" min="0" max="1000" class="form-control" id="snowfall_minspeed" name="setting_snowfall_minspeed" value="{{ config('options.snowfall_minspeed', '10') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="snowfall_maxspeed">{{ __('Максимальная скорость снежинки') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="number" min="0" max="1000" class="form-control" id="snowfall_maxspeed" name="setting_snowfall_maxspeed" value="{{ config('options.snowfall_maxspeed', '50') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="snowfall_round">{{ __('Округлять снежинки') }}</label>
                                                <div class="form-control-wrap">
                                                    <select id="snowfall_round" name="setting_snowfall_round" class="form-select">
                                                        <option value="true" @if(config('options.snowfall_round', 'false') === 'true') selected @endif>{{ __('Да') }}</option>
                                                        <option value="false" @if(config('options.snowfall_round', 'false') === 'false') selected @endif>{{ __('Нет') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="snowfall_shadow">{{ __('Тень снежинок') }}</label>
                                                <div class="form-control-wrap">
                                                    <select id="snowfall_shadow" name="setting_snowfall_shadow" class="form-select">
                                                        <option value="true" @if(config('options.snowfall_shadow', 'false') === 'true') selected @endif>{{ __('Да') }}</option>
                                                        <option value="false" @if(config('options.snowfall_shadow', 'false') === 'false') selected @endif>{{ __('Нет') }}</option>
                                                    </select>
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