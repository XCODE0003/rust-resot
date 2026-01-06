@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Настройки'))
@section('headerTitle', __('Настройки'))
@section('headerDesc', __('Название проекта') . '.')

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
                                                <label class="form-label" for="title">{{ __('Название проекта') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="title" name="setting_title" value="{{ config('options.title', 'LA2') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tabs -->
                                    <div class="row g-4">
                                        <div class="col-lg-12">
                                            <div class="tab">
                                                @foreach(getlangs() as $key => $value)
                                                    @if($loop->index == 0)
                                                        <a class="tablinks active" onclick="openTab(event, '{{ $key }}')">{{ $value }}</a>
                                                    @else
                                                        <a class="tablinks" onclick="openTab(event, '{{ $key }}')">{{ $value }}</a>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Tab content -->
                                    @foreach(getlangs() as $key => $value)
                                        @if($loop->index == 0)
                                            <div id="{{ $key }}" class="tabcontent" style="display: block">
                                                @else
                                                    <div id="{{ $key }}" class="tabcontent">
                                                        @endif

                                                        <div class="row g-4">
                                                            <div class="col-lg-12">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="meta_decription_{{ $key }}">Meta {{ __('описание') }} ({{ $key }})</label>
                                                                    <div class="form-control-wrap">
                                                                        <input type="text" class="form-control" id="meta_decription_{{ $key }}" name="setting_meta_decription_{{ $key }}"
                                                                               value="{{ config('options.meta_decription_' . $key, '') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row g-4">
                                                            <div class="col-lg-12">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="meta_keywords_{{ $key }}">Meta {{ __('ключевые слова') }} ({{ $key }})</label>
                                                                    <div class="form-control-wrap">
                                                                        <input type="text" class="form-control" id="meta_keywords_{{ $key }}" name="setting_meta_keywords_{{ $key }}"
                                                                               value="{{ config('options.meta_keywords_' . $key, '') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row g-4">
                                                            <div class="col-lg-12">
                                                                <div class="form-group">
                                                                    <label class="form-label"
                                                                           for="copyright_description_{{ $key }}">{{ __('Описание копирайта') }}</label>
                                                                    <div class="form-control-wrap">
                                                                        <textarea type="text" class="form-control" id="copyright_description_{{ $key }}" name="setting_copyright_description_{{ $key }}">{{ config('options.copyright_description_'.$key) }}</textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endforeach

                                                    <div class="row g-4">
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <button type="submit" class="btn btn-lg btn-primary ml-auto">{{ __('Отправить') }}</button>
                                                            </div>
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
