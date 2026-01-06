@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Настройки'))
@section('headerTitle', __('Настройки'))
@section('headerDesc', __('Настройки форума') . ".")

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
                                                <label class="form-label" for="forum_link">{{ __('Ссылка Форум') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="forum_link" name="setting_forum_link"
                                                           value="{{ config('options.forum_link', '#') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="forum_type">{{ __('Тип движка форума') }}</label>
                                                <div class="form-control-wrap">
                                                    <select id="forum_type" name="setting_forum_type" class="form-select">
                                                        <option value="1" @if (config('options.forum_type') !== NULL && config('options.forum_type') === '1') selected @endif>{{ __('XenForo 1+') }}</option>
                                                        <option value="2" @if (config('options.forum_type') !== NULL && config('options.forum_type') === '2') selected @endif>{{ __('IPS 4') }}</option>
                                                        <option value="3" @if (config('options.forum_type') !== NULL && config('options.forum_type') === '3') selected @endif>{{ __('XenForo 2+') }}</option>
                                                        <option value="4" @if (config('options.forum_type') !== NULL && config('options.forum_type') === '4') selected @endif>{{ __('IPS 3') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="forum_api_key">{{ __('API ключ') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="forum_host" name="setting_forum_api_key"
                                                           value="{{ config('options.forum_api_key', '#') }}">
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
