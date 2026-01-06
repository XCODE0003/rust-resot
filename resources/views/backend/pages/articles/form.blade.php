@extends('backend.layouts.backend')

@isset($article)
    @section('title', __('Панель управления') . ' - ' . __('Редактировать новость'))
    @section('headerDesc', __('Редактирование новости.'))
@else
    @section('title', __('Панель управления') . ' - ' . __('Добавить новость'))
    @section('headerDesc', __('Добавление новости.'))
@endisset

@section('headerTitle', __('Новости'))

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-head">

                        </div>
                        <form action="@isset($article){{ route('articles.update', $article) }}@else{{ route('articles.store') }}@endisset"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="type" value="news">

                            @isset($article)
                                @method('PATCH')
                                <input type="hidden" name="edit" value="1">
                                <input type="hidden" name="id" value="{{ $article->id }}">
                            @endisset

                            <div class="row g-4">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="form-label" for="path">{{ __('SEO Url') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="path" name="path"
                                                   @isset($article) value="{{ $article->path }}" @else value="{{ old('path') }}" @endisset required>
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
                                            $title = "title_" . $key;
                                            $description = "description_" . $key;
                                            $url = "url_" . $key;
                                            $meta_title = "meta_title_" . $key;
                                            $meta_description = "meta_description_" . $key;
                                            $meta_keywords = "meta_keywords_" . $key;
                                            $meta_h1 = "meta_h1_" . $key;
                                            $meta_h2 = "meta_h2_" . $key;
                                            $meta_h3 = "meta_h3_" . $key;
                                        @endphp

                                                <div class="row g-4">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label class="form-label" for="{{ $title }}">{{ __('Заголовок') }} ({{ $key }})</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control" id="{{ $title }}" name="{{ $title }}"
                                                                       @isset($article) value="{{ $article->$title }}" @else value="{{ old($title) }}" @endisset>
                                                                @error($title)
                                                                <div class="invalid-feedback">
                                                                    {{ $message }}
                                                                </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row g-4 col-description">
                                                    <div class="col-lg-12">
                                                        <div class="form-group">
                                                            <label class="form-label" for="{{ $description }}">{{ __('Описание') }} ({{ $key }})</label>
                                                            <div class="form-control-wrap">
                                                                <textarea type="text" class="form-control" id="{{ $description }}" name="{{ $description }}">@isset($article){{ $article->$description }}@else{{ old($description) }}@endisset</textarea>
                                                            </div>
                                                            @error($description)
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
                                                    <label class="form-label" for="{{ $meta_title }}">{{ __('Мета Заголовок') }} ({{ $key }})</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $meta_title }}" name="{{ $meta_title }}"
                                                               @isset($article) value="{{ $article->$meta_title }}" @else value="{{ old($meta_title) }}" @endisset>
                                                        @error($meta_title)
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
                                                    <label class="form-label" for="{{ $meta_description }}">{{ __('Мета Описание') }} ({{ $key }})</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $meta_description }}" name="{{ $meta_description }}"
                                                               @isset($article) value="{{ $article->$meta_description }}" @else value="{{ old($meta_description) }}" @endisset>
                                                        @error($meta_description)
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
                                                    <label class="form-label" for="{{ $meta_keywords }}">{{ __('Мета Ключевые слова') }} ({{ $key }})</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $meta_keywords }}" name="{{ $meta_keywords }}"
                                                               @isset($article) value="{{ $article->$meta_keywords }}" @else value="{{ old($meta_keywords) }}" @endisset>
                                                        @error($meta_keywords)
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
                                                    <label class="form-label" for="{{ $meta_h1 }}">{{ __('Мета H1') }} ({{ $key }})</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $meta_h1 }}" name="{{ $meta_h1 }}"
                                                               @isset($article) value="{{ $article->$meta_h1 }}" @else value="{{ old($meta_h1) }}" @endisset>
                                                        @error($meta_h1)
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
                                                    <label class="form-label" for="{{ $meta_h2 }}">{{ __('Мета H2') }} ({{ $key }})</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $meta_h2 }}" name="{{ $meta_h2 }}"
                                                               @isset($article) value="{{ $article->$meta_h2 }}" @else value="{{ old($meta_h2) }}" @endisset>
                                                        @error($meta_h2)
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
                                                    <label class="form-label" for="{{ $meta_h3 }}">{{ __('Мета H3') }} ({{ $key }})</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $meta_h3 }}" name="{{ $meta_h3 }}"
                                                               @isset($article) value="{{ $article->$meta_h3 }}" @else value="{{ old($meta_h3) }}" @endisset>
                                                        @error($meta_h3)
                                                        <div class="invalid-feedback">
                                                            {{ $message }}
                                                        </div>
                                                        @enderror
                                                    </div>
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
                                                                <option value="0" @if (isset($article) && $article->status == '0') selected @endif>{{ __('Для админов') }}</option>
                                                                <option value="1" @if (isset($article) && $article->status == '1') selected @endif>{{ __('Для всех') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{--
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <label class="form-label" for="title">{{ __('Порядок сортировки') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="sort" name="sort"
                                                                   @isset($article->sort) value="{{ $article->sort }}" @else value="{{ old('sort') }}" @endisset required>
                                                            @error('sort')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                                --}}


                                            </div>


                            <div class="row g-4">

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="image" class="form-label">@isset($article) {{ __('Заменить изображение') }} @else {{ __('Изображение') }} @endisset</label>
                                        @isset($article)
                                            <span>{{ $article->image }}</span>
                                        @endisset
                                        <div class="form-control-wrap">
                                            @isset($article)<img src="{{ $article->image_url }}" alt="image">@endisset
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
                            </div>

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
@push('scripts')
    <script>
        @foreach(getlangs() as $key => $value)
            CKEDITOR.config.allowedContent=true;
        CKEDITOR.replace( 'description_{{ $key }}', {
            language: '{{ app()->getLocale() }}',
            filebrowserBrowseUrl : '/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
            filebrowserUploadUrl : '/filemanager/dialog.php?type=2&editor=ckeditor&fldr=',
            filebrowserImageBrowseUrl : '/filemanager/dialog.php?type=1&editor=ckeditor&fldr='
        });

        @if(session()->has('theme') && session()->get('theme') == 'dark')
        CKEDITOR.addCss('.cke_editable { background-color: #0e1014; color: #942f06 }');
        @endif

        @endforeach
    </script>
@endpush