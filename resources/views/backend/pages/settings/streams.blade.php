@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Настройки'))
@section('headerTitle', __('Настройки'))
@section('headerDesc', __('Настройки стримов') . ".")

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

                                        <div class="payments-group">
                                            <div class="payments-title">Yotube</div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                           for="youtube_key">{{ __('API Key') }}</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="youtube_key"
                                                               name="setting_youtube_key"
                                                               value="{{ config('options.youtube_key', "") }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="payments-group">
                                            <div class="payments-title">Twitch</div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                           for="twitch_client_id">{{ __('Client ID') }}</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="twitch_client_id"
                                                               name="setting_twitch_client_id"
                                                               value="{{ config('options.twitch_client_id', "") }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label class="form-label"
                                                           for="twitch_secret">{{ __('Secret') }}</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="twitch_secret"
                                                               name="setting_twitch_secret"
                                                               value="{{ config('options.twitch_secret', "") }}">
                                                    </div>
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
