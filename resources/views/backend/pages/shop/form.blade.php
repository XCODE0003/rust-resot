@extends('backend.layouts.backend')

@isset($shopitem)
    @section('title', __('Панель управления') . ' - ' . __('Редактировать предмет'))
    @section('headerDesc', __('Редактирование предмета.'))
@else
    @section('title', __('Панель управления') . ' - ' . __('Добавить предмет'))
    @section('headerDesc', __('Добавление предмета.'))
@endisset

@php
    if(isset($shopitem->variations)) {
        $variations = json_decode($shopitem->variations);
    } else {
        $variations = [];
    }
@endphp

@section('headerTitle', __('Предметы магазина'))

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-head">

                        </div>
                        <form action="@isset($shopitem){{ route('shopitems.update', $shopitem) }}@else{{ route('shopitems.store') }}@endisset"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @isset($shopitem)
                                @method('PATCH')
                                <input type="hidden" name="edit" value="1">
                            @endisset

                            <div class="row g-4">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="form-label" for="category_id">{{ __('Категория') }}</label>
                                    <div class="form-control-wrap">
                                        @php $title = "title_" .app()->getLocale(); @endphp
                                        <select id="category_id" name="category_id" class="form-select">
                                            @foreach(getshopcategories() as $category)
                                                <option value="{{ $category->id }}"
                                                        @if(isset($shopitem) && $shopitem->category_id == $category->id) selected @endif>{{ $category->$title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="short_name">{{ __('ShortName Rust') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="short_name" name="short_name"
                                                   @isset($shopitem) value="{{ $shopitem->short_name }}" @else value="{{ old('short_name') }}" @endisset>
                                            @error('short_name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="server">{{ __('Сервер') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="server" name="server" class="form-select">
                                                <option value="-1" @if(isset($shopitem) && $shopitem->server == '-1') selected @endif>{{ __('Нет') }}</option>
                                                <option value="0" @if(isset($shopitem) && $shopitem->server == '0') selected @endif>{{ __('Все сервера') }}</option>
                                                @foreach(getservers() as $server)
                                                    <option value="{{ $server->id }}" @if(isset($shopitem) && $shopitem->server == $server->id) selected @endif>{{ $server->name }}</option>
                                                @endforeach
                                                @if(isset($shopitem) && $shopitem->server == 8)
                                                    <option value="8" selected>{{ __('Сервер') }} 8</option>
                                                @endif
                                            </select>
                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="servers">{{ __('Cервера') }}</label>
                                        <div class="form-control-wrap">
                                            @foreach(getservers() as $server)
                                                <input type="hidden" name="server_{{ $loop->iteration }}_id" value="" />
                                                <input type="checkbox" class="lang-checkbox" id="server_{{ $loop->iteration }}_id" name="server_{{ $loop->iteration }}_id" value="{{ $server->id }}" @if(isset($servers) && in_array($server->id, $servers)) checked @endif>{{ $server->name }}<br>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6" style="display: none;">
                                    <div class="form-group">
                                        <label class="form-label" for="rs_id">{{ __('Rust ID') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="rs_id" name="rs_id"
                                                   @isset($shopitem) value="{{ $shopitem->rs_id }}" @else value="0" @endisset>
                                            @error('rs_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="item_id">Rust {{ __('ID предмета') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="item_id" name="item_id"
                                                   @isset($shopitem) value="{{ $shopitem->item_id }}" @else value="0" @endisset required>
                                            @error('item_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="command">Rust {{ __('Команда') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="command" name="command"
                                                   @isset($shopitem) value="{{ $shopitem->command }}" @else value="{{ old('command') }}" @endisset>
                                            @error('command')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="amount">{{ __('Количество') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="number" min="1" class="form-control" id="amount" name="amount"
                                                   @isset($shopitem) value="{{ $shopitem->amount }}" @else value="{{ old('amount') }}" @endisset required>
                                            @error('amount')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>


                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="price">{{ __('Цена') }} (RUB)</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control" min="0" step="0.01" id="price" name="price"
                                                   @isset($shopitem) value="{{ $shopitem->price }}" @else value="{{ old('price') }}" @endisset required>
                                            @error('price')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="price_usd">{{ __('Цена') }} (USD)</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control" min="0" step="0.01" id="price_usd" name="price_usd"
                                                   @isset($shopitem) value="{{ $shopitem->price_usd }}" @else value="{{ old('price_usd') }}" @endisset required>
                                            @error('price_usd')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="status">{{ __('Состояние') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="status" name="status" class="form-select">
                                                <option value="0" @if(isset($shopitem) && $shopitem->status == '0') selected @endif>{{ __('Выключить') }}</option>
                                                <option value="1" @if(isset($shopitem) && $shopitem->status == '1') selected @endif>{{ __('Включить') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="is_blueprint">{{ __('IsBlueprint') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="is_blueprint" name="is_blueprint" class="form-select">
                                                <option value="0" @if(isset($shopitem) && $shopitem->is_blueprint == '0') selected @endif>{{ __('Нет') }}</option>
                                                <option value="1" @if(isset($shopitem) && $shopitem->is_blueprint == '1') selected @endif>{{ __('Да') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="is_command">{{ __('IsCommand') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="is_command" name="is_command" class="form-select">
                                                <option value="0" @if(isset($shopitem) && $shopitem->is_command == '0') selected @endif>{{ __('Нет') }}</option>
                                                <option value="1" @if(isset($shopitem) && $shopitem->is_command == '1') selected @endif>{{ __('Да') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6" style="display: none;">
                                    <div class="form-group">
                                        <label class="form-label" for="is_item">{{ __('IsItem') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="is_item" name="is_item" class="form-select">
                                                <option value="0" @if(isset($shopitem) && $shopitem->is_item == '0') selected @endif>{{ __('Нет') }}</option>
                                                <option value="1" @if(isset($shopitem) && $shopitem->is_item == '1') selected @endif>{{ __('Да') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="can_gift">{{ __('Можно дарить') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="can_gift" name="can_gift" class="form-select">
                                                <option value="0" @if(isset($shopitem) && $shopitem->can_gift == '0') selected @endif>{{ __('Нет') }}</option>
                                                <option value="1" @if(isset($shopitem) && $shopitem->can_gift == '1') selected @endif>{{ __('Да') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label class="form-label" for="wipe_block">{{ __('Вайп блока') }}</label>
                                        <div class="form-control-wrap">
                                            <input type="text" class="form-control" id="wipe_block" name="wipe_block"
                                                   @isset($shopitem) value="{{ $shopitem->wipe_block }}" @else value="{{ old('wipe_block') }}" @endisset>
                                            @error('wipe_block')
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
                                    $name = "name_" . $key;
                                    $short_description = "short_description_" . $key;
                                    $description = "description_" . $key;
                                @endphp
                                <div id="{{ $key }}" class="tabcontent" @if($loop->index == 0) style="display: block" @endif>

                                        <div class="row g-4">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label class="form-label" for="{{ $name }}">{{ __('Название') }} ({{ $key }})</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $name }}"
                                                               name="{{ $name }}"
                                                               @isset($shopitem) value="{{ $shopitem->$name }}"
                                                               @else value="{{ old($name) }}" @endisset>
                                                        @error($name)
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
                                                           for="{{ $short_description }}">{{ __('Краткое описание') }} ({{ $key }})</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $short_description }}"
                                                               name="{{ $short_description }}"
                                                               @isset($shopitem) value="{{ $shopitem->$short_description }}"
                                                               @else value="{{ old($short_description) }}" @endisset>
                                                    @error($short_description)
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
                                                                  name="{{ $description }}">@isset($shopitem){{ $shopitem->$description }}@else{{ old($description) }}@endisset</textarea>
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
                                                        <label class="form-label" for="sort">{{ __('Порядок сортировки') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control @error('sort') is-invalid @enderror"
                                                                   id="sort" name="sort"
                                                                   @isset($shopitem) value="{{ $shopitem->sort }}" @else value="{{ old('sort') }}" @endisset required>
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
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="image" class="form-label">@isset($shopitem) {{ __('Заменить изображение') }} @else {{ __('Изображение') }} @endisset</label>
                                        <div class="form-control-wrap">
                                            @isset($shopitem)
                                                <span><img src="{{ $shopitem->image_url }}" alt="{{ $shopitem->id }}" width="200"></span>
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
                            </div>

                                <div id="service-variations" style="@if(isset($shopitem) && $shopitem->category_id != 4){{ 'display: none;' }}@endif margin-top: 50px;">

                                <div class="payments-title">{{ __('Вариации услуги') }}</div>

                                <!-- variations -->
                                <div class="col-variations">
                                    <div class="margin-bottom-50"></div>
                                    <div id="variations">
                                        <div class="g-4 variation" data-variation="" id="variation_" style="display: none;">
                                            <div class="row g-4">
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label class="form-label" for="variation__id">{{ __('ID вариации') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="number" class="form-control" id="variation__id"
                                                                   name="variation__id" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label class="form-label" for="variation__name">{{ __('Название вариации') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="variation__name"
                                                                   name="variation__name" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-label" for="variation__price">{{ __('Цена вариации') }} (RUB)</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="variation__price"
                                                                   name="variation__price" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-label" for="variation__price_usd">{{ __('Цена вариации') }} (USD)</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="variation__price_usd"
                                                                   name="variation__price_usd" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group delete-bonus">
                                                        <a class="btn delete" data-donat="variation_" onClick="deletevariation('variation_')">{{ __('Удалить вариацию') }}</a>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        @if(isset($shopitem) && $shopitem->category_id == 4)
                                            @foreach($variations as $variation)
                                                <div class="g-4 variation" data-variation="{{ $loop->iteration }}" id="variation_{{ $loop->iteration }}">
                                                    <div class="row g-4">
                                                        <div class="col-lg-3">
                                                            <div class="form-group">
                                                                <label class="form-label" for="variation_{{ $loop->iteration }}_id">{{ __('ID вариации') }} ({{ $loop->iteration }})</label>
                                                                <div class="form-control-wrap">
                                                                    <input type="number" class="form-control" id="variation_{{ $loop->iteration }}_id"
                                                                           name="variation_{{ $loop->iteration }}_id" value="{{ $variation->variation_id }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <div class="form-group">
                                                                <label class="form-label" for="variation_{{ $loop->iteration }}_name">{{ __('Название вариации') }} ({{ $loop->iteration }})</label>
                                                                <div class="form-control-wrap">
                                                                    <input type="text" class="form-control" id="variation_{{ $loop->iteration }}_name"
                                                                           name="variation_{{ $loop->iteration }}_name" value="{{ $variation->variation_name }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-label" for="variation_{{ $loop->iteration }}_price">{{ __('Цена вариации') }} ({{ $loop->iteration }}) (RUB)</label>
                                                                <div class="form-control-wrap">
                                                                    <input type="text" class="form-control" id="variation_{{ $loop->iteration }}_price"
                                                                           name="variation_{{ $loop->iteration }}_price" value="{{ $variation->variation_price }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-label" for="variation_{{ $loop->iteration }}_price_usd">{{ __('Цена вариации') }} ({{ $loop->iteration }}) (USD)</label>
                                                                <div class="form-control-wrap">
                                                                    <input type="text" class="form-control" id="variation_{{ $loop->iteration }}_price_usd"
                                                                           name="variation_{{ $loop->iteration }}_price_usd" value="@if(isset($variation->variation_price_usd)){{ $variation->variation_price_usd }}@endif">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group delete-bonus">
                                                                <a class="btn delete" data-donat="variation_{{ $loop->iteration }}" onClick="deletevariation('variation_{{ $loop->iteration }}')">{{ __('Удалить вариацию') }}</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-lg-12">
                                            <div class="form-group add-bonus">
                                                <a class="btn add addvariation">{{ __('Добавить вариацию') }}</a>
                                            </div>
                                        </div>
                                    </div>


                                </div>

                                {{-- end variations --}}
                                </div>



                                    <div id="items-quantities" style="@if(isset($shopitem) && $shopitem->category_id == 4){{ 'display: none;' }}@endif margin-top: 50px;">

                                        <div class="payments-title">{{ __('Вариации количества') }}</div>

                                        <!-- quantities -->
                                        <div class="col-quantitys">
                                            <div class="margin-bottom-50"></div>
                                            <div id="quantitys">
                                                <div class="g-4 quantity" data-quantity="" id="quantity_" style="display: none;">
                                                    <div class="row g-4">
                                                        <div class="col-lg-3">
                                                            <div class="form-group">
                                                                <label class="form-label" for="quantity__id">{{ __('ID вариации') }}</label>
                                                                <div class="form-control-wrap">
                                                                    <input type="number" class="form-control" id="quantity__id"
                                                                           name="quantity__id" value="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-3">
                                                            <div class="form-group">
                                                                <label class="form-label" for="quantity__amount">{{ __('Количество вариации') }}</label>
                                                                <div class="form-control-wrap">
                                                                    <input type="number" min="1" class="form-control" id="quantity__amount"
                                                                           name="quantity__amount" value="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-label" for="quantity__price">{{ __('Цена вариации') }}</label>
                                                                <div class="form-control-wrap">
                                                                    <input type="text" class="form-control" id="quantity__price"
                                                                           name="quantity__price" value="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group">
                                                                <label class="form-label" for="quantity__price_usd">{{ __('Цена вариации') }}</label>
                                                                <div class="form-control-wrap">
                                                                    <input type="text" class="form-control" id="quantity__price_usd"
                                                                           name="quantity__price_usd" value="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-2">
                                                            <div class="form-group delete-bonus">
                                                                <a class="btn delete" data-donat="quantity_" onClick="deletequantity('quantity_')">{{ __('Удалить количество') }}</a>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                @if(isset($shopitem) && $shopitem->category_id != 4)
                                                @foreach($variations as $quantity)
                                                    <div class="g-4 quantity" data-quantity="{{ $loop->iteration }}" id="quantity_{{ $loop->iteration }}">
                                                        <div class="row g-4">
                                                            <div class="col-lg-3">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="quantity_{{ $loop->iteration }}_id">{{ __('ID вариации') }} ({{ $loop->iteration }})</label>
                                                                    <div class="form-control-wrap">
                                                                        <input type="number" class="form-control" id="quantity_{{ $loop->iteration }}_id"
                                                                               name="quantity_{{ $loop->iteration }}_id" value="{{ $quantity->quantity_id }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="quantity_{{ $loop->iteration }}_amount">{{ __('Количество вариации') }} ({{ $loop->iteration }})</label>
                                                                    <div class="form-control-wrap">
                                                                        <input type="number" min="1" class="form-control" id="quantity_{{ $loop->iteration }}_amount"
                                                                               name="quantity_{{ $loop->iteration }}_amount" value="{{ $quantity->quantity_amount }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="quantity_{{ $loop->iteration }}_price">{{ __('Цена вариации') }} ({{ $loop->iteration }})</label>
                                                                    <div class="form-control-wrap">
                                                                        <input type="text" class="form-control" id="quantity_{{ $loop->iteration }}_price"
                                                                               name="quantity_{{ $loop->iteration }}_price" value="{{ $quantity->quantity_price }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <div class="form-group">
                                                                    <label class="form-label" for="quantity_{{ $loop->iteration }}_price_usd">{{ __('Цена вариации') }} ({{ $loop->iteration }})</label>
                                                                    <div class="form-control-wrap">
                                                                        <input type="text" class="form-control" id="quantity_{{ $loop->iteration }}_price_usd"
                                                                               name="quantity_{{ $loop->iteration }}_price_usd" value="@if(isset($quantity->quantity_price_usd)){{ $quantity->quantity_price_usd }}@endif">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-2">
                                                                <div class="form-group delete-bonus">
                                                                    <a class="btn delete" data-donat="quantity_{{ $loop->iteration }}" onClick="deletequantity('quantity_{{ $loop->iteration }}')">{{ __('Удалить количество') }}</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                @endif

                                            </div>

                                            <div class="row g-4">
                                                <div class="col-lg-12">
                                                    <div class="form-group add-bonus">
                                                        <a class="btn add addquantity">{{ __('Добавить количество') }}</a>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>
                                        {{-- end quantities --}}

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


@push('scripts')
    <script>
        $(document).ready(function () {

            let variation_id = 1;
            let variation_id_next = 1;
            let variation_html = '';
            let sear = '';
            let repl = '';
            $('.addvariation').on('click', function(){
                variation_id = $('.variation:last').data('variation');
                variation_id_next = variation_id + 1;
                variation_id = '';
                sear = new RegExp('variation_' + variation_id, 'g');
                repl = 'variation_' + variation_id_next;
                variation_html = $('#variation_'+variation_id).html().replace(sear,repl);
                sear = new RegExp('{{ __("ID вариации") }} ' + variation_id, 'g');
                variation_html = variation_html.replace(sear,'{{ __("ID вариации") }} ' + variation_id_next);

                $('#variations').append('<div class="g-4 variation" data-variation="'+variation_id_next+'" id="variation_' + variation_id_next + '">' + variation_html + '</div>');
            });

            if ($('#category_id').find('option:selected').val() == '4') {
                $('#service-variations').show();
            }
        });

        $('#category_id').on('change', function () {
            if ($(this).find('option:selected').val() == '4') {
                $('#items-quantities').hide();
                $('#service-variations').show();
            } else {
                $('#service-variations').hide();
                $('#items-quantities').show();
            }
        });

        //variation
        function deletevariation(variation){
            $('#'+variation).remove();
        }
    </script>

    <script>
        $(document).ready(function () {

            let quantity_id = 1;
            let quantity_id_next = 1;
            let quantity_html = '';
            let sear = '';
            let repl = '';
            $('.addquantity').on('click', function(){
                quantity_id = $('.quantity:last').data('quantity');
                quantity_id_next = quantity_id + 1;
                quantity_id = '';
                sear = new RegExp('quantity_' + quantity_id, 'g');
                repl = 'quantity_' + quantity_id_next;
                quantity_html = $('#quantity_'+quantity_id).html().replace(sear,repl);
                sear = new RegExp('{{ __("ID вариации") }} ' + quantity_id, 'g');
                quantity_html = quantity_html.replace(sear,'{{ __("ID вариации") }} ' + quantity_id_next);

                $('#quantitys').append('<div class="g-4 quantity" data-quantity="'+quantity_id_next+'" id="quantity_' + quantity_id_next + '">' + quantity_html + '</div>');
            });

            if ($('#category_id').find('option:selected').val() == '4') {
                $('#service-quantitys').show();
            }
        });

        //quantity
        function deletequantity(quantity){
            $('#'+quantity).remove();
        }
    </script>

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