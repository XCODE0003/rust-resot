@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Настройки'))
@section('headerTitle', __('Настройки'))
@section('headerDesc', __('Настройки Социальных виджетов') . ".")

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
                                                <label class="form-label" for="twitter_link">{{ __('Ссылка') }} Twitter</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="twitter_link" name="setting_twitter_link"
                                                           value="{{ config('options.twitter_link', '#') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="vk_link">{{ __('Ссылка') }} VKontakte</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="vk_link" name="setting_vk_link"
                                                           value="{{ config('options.vk_link', '#') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="discord_link">{{ __('Ссылка Discord') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="discord_link" name="setting_discord_link"
                                                           value="{{ config('options.discord_link', '#') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="steam_link">{{ __('Ссылка') }} Steam</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="steam_link" name="setting_steam_link"
                                                           value="{{ config('options.steam_link', '#') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="youtube_link">{{ __('Ссылка YouTube') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="youtube_link" name="setting_youtube_link"
                                                           value="{{ config('options.youtube_link', '#') }}">
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
