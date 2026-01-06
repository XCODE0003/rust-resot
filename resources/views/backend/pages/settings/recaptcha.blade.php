@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Настройки'))
@section('headerTitle', __('Настройки'))
@section('headerDesc', __('Настройки Recaptcha') . ".")

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
                                                <label class="form-label" for="recaptcha_sitekey">Google recaptcha {{ __('ключ сайта') }}:</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="recaptcha_sitekey" name="setting_recaptcha_sitekey"
                                                           value="{{ config('options.recaptcha_sitekey', '') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="recaptcha_secret">Google recaptcha {{ __('секретный ключ') }}:</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="recaptcha_secret" name="setting_recaptcha_secret"
                                                           value="{{ config('options.recaptcha_secret', '') }}">
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

    <!-- .nk-block -->
@endsection
