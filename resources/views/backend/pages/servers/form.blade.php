@php
    if(isset($server)) {
        $options = json_decode($server->options);
    }
@endphp

@extends('backend.layouts.backend')

@isset($server)
    @section('title', __('Панель управления') . ' - ' . __('Редактировать игровой сервер'))
    @section('headerDesc', __('Редактирование игрового сервера ') . $server->name . '.')
@else
    @section('title', __('Панель управления') . ' - ' . __('Добавить игровой сервер'))
    @section('headerDesc', __('Добавление игрового сервера.'))
@endisset

@section('headerTitle', __('Игровой сервер'))

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-head">

                        </div>
                        <form action="@isset($server){{ route('servers.update', $server) }}@else{{ route('servers.store') }}@endisset"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @isset($server)
                                @method('PATCH')
                                <input type="hidden" name="edit" value="1">
                            @endisset

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="name">{{ __('Название') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="name" name="name"
                                                   @isset($server->name) value="{{ $server->name }}" @else value="{{ old('title') }}" @endisset required>
                                            @error('title')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="ip">{{ __('IP:PORT Сервера') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="ip" name="ip"
                                                   @isset($options->ip) value="{{ $options->ip }}" @else value="" @endisset required>
                                            @error('ip')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                                <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="status">{{ __('Статус') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="status" name="status" class="form-select">
                                                <option value="0" @if (isset($server->status) && $server->status == '0') selected @endif>{{ __('Скрыть') }}</option>
                                                <option value="1" @if (isset($server->status) && $server->status == '1') selected @endif>{{ __('Отобразить') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="rsworld_db_type">{{ __('Тип игрового сервера') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="rsworld_db_type" name="rsworld_db_type" class="form-select">
                                                <option value="1" @if (isset($options->rustworld_db_type) && $options->rustworld_db_type === '1') selected @endif>{{ __('Тип') }} 1</option>
                                            </select>
                                            @error('rsworld_db_type')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="rcon_ip">{{ __('Адрес подключения к RCon IP:PORT') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="rcon_ip" name="rcon_ip"
                                                   @isset($options->rcon_ip) value="{{ $options->rcon_ip }}" @else value="" @endisset required>
                                            @error('rcon_ip')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="rcon_passw">{{ __('Пароль RCon') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="rcon_passw" name="rcon_passw"
                                                   @isset($options->rcon_passw) value="{{ $options->rcon_passw }}" @else value="" @endisset>
                                            @error('rcon_passw')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="sort">{{ __('Порядок сортировки') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="sort" name="sort"
                                                   @isset($server->sort) value="{{ $server->sort }}" @else value="0" @endisset required>
                                            @error('ip')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="category_id">{{ __('Категория') }}</label>
                                        <div class="form-control-wrap">
                                            @php $title = "title_" .app()->getLocale(); @endphp
                                            <select id="category_id" name="category_id" class="form-select">
                                                @foreach(getservercategories() as $category)
                                                    <option value="{{ $category->id }}"
                                                            @if(isset($server) && $server->category_id == $category->id) selected @endif>{{ $category->$title }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="image" class="form-label">@isset($server) {{ __('Заменить изображение') }} @else {{ __('Изображение') }} @endisset</label>
                                        @isset($server)
                                            <span>{{ $server->image }}</span>
                                        @endisset

                                        @isset($server) <img src="{{ $server->image_url }}" alt="image"> @endisset
                                        <div class="form-control-wrap">
                                            <div class="custom-file">
                                                <input class="custom-file-input @error('image') is-invalid @enderror" id="image" name="image" type="file" >
                                                <label class="custom-file-label" for="image">{{ __('Изображение') }}</label>
                                                @error('image')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="next_wipe">{{ __('Дата следующего вайпа') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="datetime-local" class="form-control" id="next_wipe" name="next_wipe"
                                                   @isset($server->next_wipe) value="{{ $server->next_wipe }}" @else value="0" @endisset required>
                                            @error('next_wipe')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
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
