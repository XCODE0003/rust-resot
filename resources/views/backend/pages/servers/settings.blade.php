@extends('backend.layouts.backend')

@section('title', __('Панель управления') . ' - ' . __('Настростроить игровой сервер'))
@section('headerTitle', __('Игровой сервер'))
@section('headerDesc', __('Настройки игрового сервера ') . $server->name . '.')

@section('wrap')
    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                        <div class="card card-bordered">
                            <div class="card-inner">
                                <form action="{{ route('backend.settings') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="server" value="{{ $server->id }}">

                                    <div class="row g-4">

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="max_online">{{ __('Максимальный онлайн') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="max_online" name="setting_max_online" value="{{ config('options.server_'.$server->id.'_max_online', '1000') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="mul_online">{{ __('Множитель онлайна') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="mul_online" name="setting_mul_online"
                                                           value="{{ config('options.server_'.$server->id.'_mul_online', '1') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label" for="next_wipe_time">{{ __('Время до следующего Вайпа') }}, {{ __('часов') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="number" min="0" step="1" class="form-control" id="next_wipe_time" name="setting_next_wipe_time"
                                                           value="{{ config('options.server_'.$server->id.'_next_wipe_time', '0') }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-3">
                                            <div class="form-group">
                                                <label class="form-label" for="next_wipe_time_add">{{ __('Дополнительное время') }}, {{ __('часов') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="number" min="0" step="1" class="form-control" id="next_wipe_time_add" name="setting_next_wipe_time_add"
                                                           value="{{ config('options.server_'.$server->id.'_next_wipe_time_add', '0') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="opening_date">{{ __('Дата открытия') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="datetime-local" class="form-control" id="opening_date" name="setting_opening_date"
                                                           value="{{ config('options.server_'.$server->id.'_opening_date', '0') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="plate">{{ __('Табличка') }}</label>
                                                <div class="form-control-wrap">
                                                    <select id="plate" name="setting_plate" class="form-select">
                                                        <option value="0" @if(config('options.server_'.$server->id.'_plate', '0') == '0') selected @endif>{{ __('Нету') }}</option>
                                                        <option value="1" @if(config('options.server_'.$server->id.'_plate', '0') == '1') selected @endif>{{ __('Скоро откроется') }}</option>
                                                        <option value="2" @if(config('options.server_'.$server->id.'_plate', '0') == '2') selected @endif>{{ __('Технические работы') }}</option>
                                                    </select>
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
                                            @php
                                                $description = "description_" . $key;
                                                $long_description = "long_description_" . $key;
                                            @endphp

                                                            <div class="row g-4">
                                                                <div class="col-lg-12">
                                                                    <div class="form-group">
                                                                        <label class="form-label" for="{{ $description }}">{{ __('Краткое описание сервера') }} ({{ $key }})</label>
                                                                        <div class="form-control-wrap">
                                                                            <textarea type="text" class="form-control" id="{{ $description }}" name="setting_{{ $description }}">{{ config('options.server_'.$server->id.'_'.$description, '') }}</textarea>
                                                                        </div>
                                                                        @error('{{ $description }}')
                                                                        <div class="invalid-feedback">
                                                                            {{ $message }}
                                                                        </div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row g-4">
                                                                <div class="col-lg-12">
                                                                    <div class="form-group">
                                                                        <label class="form-label" for="{{ $long_description }}">{{ __('Полное описание сервера') }} ({{ $key }})</label>
                                                                        <div class="form-control-wrap">
                                                                            <textarea type="text" class="form-control" id="{{ $long_description }}" name="setting_{{ $long_description }}">{{ config('options.server_'.$server->id.'_'.$long_description, '') }}</textarea>
                                                                        </div>
                                                                        @error('{{ $long_description }}')
                                                                        <div class="invalid-feedback">
                                                                            {{ $message }}
                                                                        </div>
                                                                        @enderror
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
                                </form>
                            </div>
                        </div>
                    </div>

                    </div>
                </div>

    <!-- .nk-block -->
@endsection
