@extends('backend.layouts.backend')

@section('title', 'Панель управления - ' . __('Настройки'))
@section('headerTitle', __('Настройки'))
@section('headerDesc', __('Настройки') . ' ' . 'Код аналитики и метрики' . '.')

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

                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label class="form-label" for="google_analitics">{{ __('Google аналитика') }}</label>
                                                <div class="form-control-wrap">
                                            <textarea type="text" class="form-control"
                                                      id="google_analitics" name="setting_google_analitics">{{ config('options.google_analitics') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label class="form-label" for="yandex_metric">{{ __('Яндекс метрика') }}</label>
                                                <div class="form-control-wrap">
                                            <textarea type="text" class="form-control"
                                                      id="yandex_metric" name="setting_yandex_metric">{{ config('options.yandex_metric') }}</textarea>
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
