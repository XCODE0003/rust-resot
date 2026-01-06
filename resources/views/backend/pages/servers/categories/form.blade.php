@extends('backend.layouts.backend')

@isset($servercategory)
    @section('title', __('Панель управления') . ' - ' . __('Редактировать категорию'))
    @section('headerDesc', __('Редактирование категории.'))
@else
    @section('title', __('Панель управления') . ' - ' . __('Добавить категорию'))
    @section('headerDesc', __('Добавление категории.'))
@endisset

@section('headerTitle', __('Категории серверов'))

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-head">

                        </div>
                        <form action="@isset($servercategory){{ route('servercategories.update', $servercategory) }}@else{{ route('servercategories.store') }}@endisset"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @isset($servercategory)
                                @method('PATCH')
                                <input type="hidden" name="edit" value="1">
                            @endisset

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="path">{{ __('Путь категории') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="path" name="path"
                                                   @isset($servercategory) value="{{ $servercategory->path }}" @else value="{{ old('path') }}" @endisset required>
                                            @error('path')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <!-- Tabs -->
                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="tab">
                                        @foreach(getlangs() as $key => $value)
                                            <a class="tablinks @if($loop->index == 0) active @endif" onclick="openTab(event, '{{ $key }}')">{{ $value }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>


                            <!-- Tab content -->
                            @foreach(getlangs() as $key => $value)
                                @php
                                    $title = "title_" . $key;
                                    $description = "description_" . $key;
                                @endphp
                                <div id="{{ $key }}" class="tabcontent" @if($loop->index == 0) style="display: block" @endif>

                                    <div class="row g-4">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label class="form-label" for="{{ $title }}">{{ __('Заголовок') }} ({{ $key }})</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="{{ $title }}"
                                                           name="{{ $title }}"
                                                           @isset($servercategory) value="{{ $servercategory->$title }}"
                                                           @else value="{{ old($title) }}" @endisset>
                                                    @error($title)
                                                    <div class="invalid-feedback">
                                                        {{ $message }}
                                                    </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row g-4">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label class="form-label"
                                                       for="{{ $description }}">{{ __('Описание') }} ({{ $key }})</label>
                                                <div class="form-control-wrap">
                                                        <textarea type="text" class="form-control"
                                                                  id="{{ $description }}"
                                                                  name="{{ $description }}">@isset($servercategory){{ $servercategory->$description }}@else{{ old($description) }}@endisset</textarea>
                                                </div>
                                                @error($description)
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
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="status">{{ __('Статус') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="status" name="status" class="form-select">
                                                <option value="0" @if (isset($servercategory) && $servercategory->status == '0') selected @endif>{{ __('Не активна') }}</option>
                                                <option value="1" @if (isset($servercategory) && $servercategory->status == '1') selected @endif>{{ __('Активна') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="title">{{ __('Порядок сортировки') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="sort" name="sort"
                                                   @isset($servercategory->sort) value="{{ $servercategory->sort }}" @else value="{{ old('sort') }}" @endisset required>
                                            @error('sort')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
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