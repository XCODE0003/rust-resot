@extends('backend.layouts.backend')

@isset($video)
    @section('title', __('Панель управления') . ' - ' . __('Редактировать видео'))
    @section('headerDesc', __('Редактирование Видео.'))
@else
    @section('title', __('Панель управления') . ' - ' . __('Добавить видео'))
    @section('headerDesc', __('Добавление видео.'))
@endisset

@section('headerTitle', __('Видео'))

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-head">
                        </div>
                        <form action="@isset($video){{ route('videos.update', $video) }}@else{{ route('videos.store') }}@endisset"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @isset($video)
                                @method('PATCH')
                                <input type="hidden" name="edit" value="1">
                            @endisset

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="title">{{ __('Заголовок') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="title" name="title"
                                                   @isset($video) value="{{ $video->title }}" @else value="{{ old('title') }}" @endisset required>
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
                                        <label class="form-label" for="language">{{ __('Язык') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="language" name="language" class="form-select @error('language') is-invalid @enderror">
                                                <option value="ru" @if (isset($video) && $video->language === 'ru') selected @endif>{{ __('Русский') }}</option>
                                                <option value="en" @if (isset($video) && $video->language === 'en') selected @endif>{{ __('Английский') }}</option>
                                                <option value="br" @if (isset($video) && $video->language === 'br') selected @endif>{{ __('Бразильский') }}</option>
                                                <option value="es" @if (isset($video) && $video->language === 'es') selected @endif>{{ __('Испанский') }}</option>
                                            </select>
                                            @error('language')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="url">{{ __('ID Видео на YouTube') }} ({{ __('примечание') }} 27JtRAIsaWO8)</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control @error('url') is-invalid @enderror"
                                                   id="url" name="url"
                                                   @isset($video) value="{{ $video->url }}" @else value="{{ old('url') }}" @endisset required
                                            >
                                            @error('url')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6" style="display: none;">
                                    <div class="form-group">
                                        <label for="image" class="form-label">@isset($video) {{ __('Заменить изображение') }} @else {{ __('Изображение') }} @endisset</label>
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
