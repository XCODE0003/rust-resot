@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Настройки'))
@section('headerTitle', __('Настройки'))
@section('headerDesc', __('Настройки') . ' ' . __('Скачать') . '.')

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
                                                <label class="form-label" for="patch_link_google">{{ __('Ссылка на патч для сервера с') }} Google Drive</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="patch_link_google" name="setting_patch_link_google"
                                                           value="{{ config('options.patch_link_google', '#') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="patch_link_yandex">{{ __('Ссылка на патч для сервера с') }} Yandex Disk</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="patch_link_yandex" name="setting_patch_link_yandex"
                                                           value="{{ config('options.patch_link_yandex', '#') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="patch_link_filemail">{{ __('Ссылка на патч для сервера с') }} FIleMail</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="patch_link_filemail" name="setting_patch_link_filemail"
                                                           value="{{ config('options.patch_link_filemail', '#') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="patch_link_meganz">{{ __('Ссылка на патч для сервера с') }} MegaNZ</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="patch_link_meganz" name="setting_patch_link_meganz"
                                                           value="{{ config('options.patch_link_meganz', '#') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="patch_link_torrentfile">{{ __('Ссылка на патч для сервера с') }} Torrent File</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="patch_link_torrentfile" name="setting_patch_link_torrentfile"
                                                           value="{{ config('options.patch_link_torrentfile', '#') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="updater_link">{{ __('Ссылка на Апдейтер') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="updater_link" name="setting_updater_link"
                                                           value="{{ config('options.updater_link', '#') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="link_nvidea">{{ __('Ссылка на скачивание') }} Drivers Nvidea</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="link_nvidea" name="setting_link_nvidea"
                                                           value="{{ config('options.link_nvidea', '#') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="link_amd">{{ __('Ссылка на скачивание') }} Drivers AMD</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="link_amd" name="setting_link_amd"
                                                           value="{{ config('options.link_amd', '#') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="link_directx">{{ __('Ссылка на скачивание') }} Drivers DirectX</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="link_directx" name="setting_link_directx"
                                                           value="{{ config('options.link_directx', '#') }}">
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
