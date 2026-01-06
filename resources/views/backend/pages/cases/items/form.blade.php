@extends('backend.layouts.backend')

@isset($casesitem)
    @section('title', __('Панель управления') . ' - ' . __('Редактировать предмет'))
@section('headerDesc', __('Редактирование предмета.'))
@else
    @section('title', __('Панель управления') . ' - ' . __('Добавить предмет'))
@section('headerDesc', __('Добавление предмета.'))
@endisset

@section('headerTitle', __('Предметы кейсов'))

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-head">

                        </div>
                        <form action="@isset($casesitem){{ route('casesitems.update', $casesitem) }}@else{{ route('casesitems.store') }}@endisset"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @isset($casesitem)
                                @method('PATCH')
                                <input type="hidden" name="edit" value="1">
                                <input type="hidden" name="id" value="{{ $casesitem->id }}">
                                <input type="hidden" name="source" value="{{ $casesitem->source }}">
                            @else
                                <input type="hidden" name="source" value="site">
                            @endisset

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="quality_type">{{ __('Тип качества') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="quality_type" name="quality_type" class="form-select">
                                                @foreach(get_quality_types() as $id => $name)
                                                    <option value="{{ $id }}" @if(isset($casesitem) && $casesitem->quality_type == $id) selected @endif>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="item_id">{{ __('Skin ID') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="item_id" name="item_id"
                                                   @isset($casesitem) value="{{ $casesitem->item_id }}" @else value="{{ old('item_id') }}" @endisset required>
                                            @error('item_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-label" for="price">{{ __('Цена') }} (RUB)</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control" min="0" step="0.01" id="price" name="price"
                                                   @isset($casesitem) value="{{ $casesitem->price }}" @else value="{{ old('price') }}" @endisset required>
                                            @error('price')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-label" for="price_usd">{{ __('Цена') }} (USD)</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control" min="0" step="0.01" id="price_usd" name="price_usd"
                                                   @isset($casesitem) value="{{ $casesitem->price_usd }}" @else value="{{ old('price_usd') }}" @endisset required>
                                            @error('price_usd')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="form-group">
                                        <label class="form-label" for="status">{{ __('Состояние') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="status" name="status" class="form-select">
                                                <option value="0" @if(isset($casesitem) && $casesitem->status == '0') selected @endif>{{ __('Не активный') }}</option>
                                                <option value="1" @if(isset($casesitem) && $casesitem->status == '1') selected @endif>{{ __('Активный') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>


                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="title">{{ __('Заголовок') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="title" name="title"
                                                           @isset($casesitem) value="{{ $casesitem->title }}" @else value="{{ old('title') }}" @endisset>
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
                                                <label class="form-label" for="subtitle">{{ __('Подзаголовок') }}</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="subtitle" name="subtitle"
                                                           @isset($casesitem) value="{{ $casesitem->subtitle }}" @else value="{{ old('subtitle') }}" @endisset>
                                                    @error('subtitle')
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
                                                <label class="form-label" for="description">{{ __('Описание') }}</label>
                                                <div class="form-control-wrap">
                                                        <textarea type="text" class="form-control" id="description"
                                                            name="description">@isset($casesitem){{ $casesitem->description }}@else{{ old('description') }}@endisset</textarea>
                                                </div>
                                                @error('description')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="sort">{{ __('Порядок сортировки') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control @error('sort') is-invalid @enderror"
                                                   id="sort" name="sort"
                                                   @isset($casesitem) value="{{ $casesitem->sort }}" @else value="{{ old('sort') }}" @endisset required
                                            >
                                            @error('sort')
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
                                            <select id="category_id" name="category_id" class="form-select">
                                                @foreach(get_caseitem_categories() as $category_id => $category_name)
                                                    <option value="{{ $category_id }}" @if(isset($casesitem) && $casesitem->category_id == $category_id) selected @endif>{{ $category_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="image" class="form-label">@isset($casesitem) {{ __('Заменить изображение') }} @else {{ __('Изображение') }} @endisset</label>
                                        <div class="form-control-wrap">
                                            @isset($casesitem)
                                                <span><img src="{{ $casesitem->image_url }}" alt="{{ $casesitem->id }}"></span>
                                            @endisset
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