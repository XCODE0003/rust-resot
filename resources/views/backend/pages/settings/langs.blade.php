@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Настройки'))
@section('headerTitle', __('Настройки'))
@section('headerDesc', __('Настройки языков') . '.')

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
                                                <label class="form-label" for="language1">{{ __('Отображаемые языки') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="hidden" name="setting_language1" value="" />
                                                    <input type="checkbox" class="lang-checkbox" id="language1" name="setting_language1" value="en" @if (config('options.language1') !== NULL && config('options.language1') === 'en') checked @endif>{{ __('Русский') }}<br>
                                                    <input type="hidden" name="setting_language2" value="" />
                                                    <input type="checkbox" class="lang-checkbox" name="setting_language2" value="ru" @if (config('options.language2') !== NULL && config('options.language2') === 'ru') checked @endif>{{ __('Английский') }}<br>
                                                    <input type="hidden" name="setting_language3" value="" />
                                                    <input type="checkbox" class="lang-checkbox" name="setting_language3" value="de" @if (config('options.language3') !== NULL && config('options.language3') === 'de') checked @endif>{{ __('Немецкий') }}<br>
                                                    <input type="hidden" name="setting_language4" value="" />
                                                    <input type="checkbox" class="lang-checkbox" name="setting_language4" value="fr" @if (config('options.language4') !== NULL && config('options.language4') === 'fr') checked @endif>{{ __('Французский') }}<br>
                                                    <input type="hidden" name="setting_language5" value="" />
                                                    <input type="checkbox" class="lang-checkbox" name="setting_language5" value="it" @if (config('options.language5') !== NULL && config('options.language5') === 'it') checked @endif>{{ __('Итальянский') }}<br>
                                                    <input type="hidden" name="setting_language6" value="" />
                                                    <input type="checkbox" class="lang-checkbox" name="setting_language6" value="es" @if (config('options.language6') !== NULL && config('options.language6') === 'es') checked @endif>{{ __('Испанский') }}<br>
                                                    <input type="hidden" name="setting_language7" value="" />
                                                    <input type="checkbox" class="lang-checkbox" name="setting_language7" value="uk" @if (config('options.language7') !== NULL && config('options.language7') === 'uk') checked @endif>{{ __('Украинский') }}<br>
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