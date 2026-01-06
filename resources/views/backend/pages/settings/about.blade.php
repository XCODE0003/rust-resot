@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Настройки'))
@section('headerTitle', __('Настройки'))
@section('headerDesc', __('Настройки') . ' ' . __('О проекте'))

@section('wrap')
    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <form action="{{ route('backend.settings') }}" method="POST" enctype="multipart/form-data">
                                    @csrf


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
                                                                    <label class="form-label" for="about_title_{{ $key }}">{{ __('Заголовок') }} ({{ $key }})</label>
                                                                    <div class="form-control-wrap">
                                                                        <input type="text" class="form-control" id="about_title_{{ $key }}" name="setting_about_title_{{ $key }}"
                                                                               value="{{ config('options.about_title_' . $key, '') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row g-4">
                                                            <div class="col-lg-12">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="about_welcome_{{ $key }}">{{ __('Подзаголовок') }} ({{ $key }})</label>
                                                                    <div class="form-control-wrap">
                                                                        <input type="text" class="form-control" id="about_welcome_{{ $key }}" name="setting_about_welcome_{{ $key }}"
                                                                               value="{{ config('options.about_welcome_' . $key, '') }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row g-4">
                                                            <div class="col-lg-12">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="about_description_{{ $key }}">{{ __('Описание') }}</label>
                                                                    <div class="form-control-wrap">
                                                                        <textarea type="text" class="form-control"
                                                                            id="about_description_{{ $key }}" name="setting_about_description_{{ $key }}">{{ config('options.about_description_'.$key) }}</textarea>
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
