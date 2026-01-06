@extends('backend.layouts.backend')

@isset($case)
    @section('title', __('Панель управления') . ' - ' . __('Редактировать кейс'))
    @section('headerDesc', __('Редактирование кейса.'))
@else
    @section('title', __('Панель управления') . ' - ' . __('Добавить кейс'))
    @section('headerDesc', __('Добавление кейса.'))
@endisset

@php
    $name = "name_" . app()->getLocale();
@endphp

@section('headerTitle', __('Кейсы'))

@section('wrap')

    <!-- .nk-block -->
    <div class="nk-block">
        <div class="row g-gs">
            <div class="col-12">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="card-head">

                        </div>
                        <form action="@isset($case){{ route('cases.update', $case) }}@else{{ route('cases.store') }}@endisset"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf
                            @isset($case)
                                @method('PATCH')
                                <input type="hidden" name="edit" value="1">
                            @endisset

                            <div class="row g-4">

                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-label" for="price">{{ __('Цена') }} (RUB)</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control" min="0" step="0.01" id="price" name="price"
                                                   @isset($case) value="{{ $case->price }}" @else value="{{ old('price') }}" @endisset required>
                                            @error('price')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-label" for="price_usd">{{ __('Цена') }} (USD)</label>
                                        <div class="form-control-wrap">
                                            <input type="number" class="form-control" min="0" step="0.01" id="price_usd" name="price_usd"
                                                   @isset($case) value="{{ $case->price_usd }}" @else value="{{ old('price_usd') }}" @endisset required>
                                            @error('price_usd')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-label" for="status">{{ __('Состояние') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="status" name="status" class="form-select">
                                                <option value="0" @if(isset($case) && $case->status == '0') selected @endif>{{ __('Выключить') }}</option>
                                                <option value="1" @if(isset($case) && $case->status == '1') selected @endif>{{ __('Включить') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="form-group">
                                        <label class="form-label" for="kind">{{ __('Вид кейса') }}</label>
                                        <div class="form-control-wrap">
                                            <select id="kind" name="kind" class="form-select">
                                                <option value="0" @if(isset($case) && $case->kind == '0') selected @endif>{{ __('Обычный') }}</option>
                                                <option value="1" @if(isset($case) && $case->kind == '1') selected @endif>{{ __('Бонусный') }}</option>
                                                <option value="2" @if(isset($case) && $case->kind == '2') selected @endif>{{ __('Магазин') }}</option>
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
                                            <a class="tablinks @if($loop->index == 0) active @endif" onclick="openTab(event, '{{ $key }}')">{{ $value }}</a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>


                            <!-- Tab content -->
                            @foreach(getlangs() as $key => $value)
                                @php
                                    $title = "title_" . $key;
                                    $subtitle = "subtitle_" . $key;
                                    $description = "description_" . $key;
                                @endphp
                                <div id="{{ $key }}" class="tabcontent" @if($loop->index == 0) style="display: block" @endif>

                                        <div class="row g-4">
                                            <div class="col-lg-12">
                                                <div class="form-group">
                                                    <label class="form-label" for="{{ $title }}">{{ __('Заголовок') }}
                                                        ({{ $key }})</label>
                                                    <div class="form-control-wrap">
                                                        <input type="text" class="form-control" id="{{ $title }}" name="{{ $title }}"
                                                               @isset($case) value="{{ $case->$title }}" @else value="{{ old($title) }}" @endisset>
                                                        @error('{{ $title }}')
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
                                                <label class="form-label" for="{{ $subtitle }}">{{ __('Подзаголовок') }}
                                                    ({{ $key }})</label>
                                                <div class="form-control-wrap">
                                                    <input type="text" class="form-control" id="{{ $subtitle }}" name="{{ $subtitle }}"
                                                           @isset($case) value="{{ $case->$subtitle }}" @else value="{{ old($subtitle) }}" @endisset>
                                                    @error('{{ $title }}')
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
                                                           for="{{ $description }}">{{ __('Описание') }} ({{ $key }}
                                                        )</label>
                                                    <div class="form-control-wrap">
                                                        <textarea type="text" class="form-control" id="{{ $description }}"
                                                                  name="{{ $description }}">@isset($case){{ $case->$description }}@else{{ old($description) }}@endisset</textarea>
                                                    </div>
                                                    @error('{{ $description }}')
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
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <label class="form-label" for="sort">{{ __('Порядок сортировки') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control @error('sort') is-invalid @enderror"
                                                                   id="sort" name="sort"
                                                                   @isset($case) value="{{ $case->sort }}" @else value="{{ old('sort') }}" @endisset required
                                                            >
                                                            @error('sort')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3 server-block" style="display: none;">
                                                    <div class="form-group">
                                                        <label class="form-label" for="server">{{ __('Сервер') }}</label>
                                                        <div class="form-control-wrap">
                                                            <select id="server" name="server" class="form-select">
                                                                <option value="0" selected>{{ __('Все сервера') }}</option>
                                                                @foreach(getservers() as $server)
                                                                    <option value="{{ $server->id }}"
                                                                            @if(isset($case) && $case->server == $server->id) selected @endif>{{ $server->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3 is_free-block" style="display: none;">
                                                    <div class="form-group">
                                                        <label class="form-label" for="is_free">{{ __('Открывать бесплатно') }}</label>
                                                        <div class="form-control-wrap">
                                                            <select id="is_free" name="is_free" class="form-select">
                                                                <option value="0" @if(isset($case) && $case->is_free == '0') selected @endif>{{ __('Нет') }}</option>
                                                                <option value="1" @if(isset($case) && $case->is_free == '1') selected @endif>{{ __('Да') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3 online_amount-block">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                               for="online_amount">{{ __('Количество часов онлайна') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control @error('online_amount') is-invalid @enderror" id="online_amount" name="online_amount" @isset($case) value="{{ $case->online_amount }}" @else value="0" @endisset>
                                                            @error('online_amount')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                        </div>
                                                    </div>
                                                <div class="col-lg-3 prizes_max-block">
                                                    <div class="form-group">
                                                        <label class="form-label"
                                                               for="prizes_max">{{ __('Количество призов') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control @error('prizes_max') is-invalid @enderror" id="prizes_max" name="prizes_max" @isset($case) value="{{ $case->prizes_max }}" @else value="0" @endisset>
                                                            @error('prizes_max')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-3 case_servers-block">
                                                    <div class="form-group">
                                                        <label class="form-label" for="case_servers">{{ __('Учитывать онлайн с Серверов') }}</label>
                                                        <div class="form-control-wrap">
                                                            @foreach(getservers() as $server)
                                                                <input type="hidden" name="case_server_{{ $loop->iteration }}_id" value="" />
                                                                <input type="checkbox" class="lang-checkbox" id="case_server_{{ $loop->iteration }}_id" name="case_server_{{ $loop->iteration }}_id" value="{{ $server->id }}" @if(isset($servers) && in_array($server->id, $servers)) checked @endif>{{ $server->name }}<br>
                                                            @endforeach
                                                            </div>
                                                    </div>
                                                </div>

                                            </div>



                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <label for="image" class="form-label">@isset($case) {{ __('Заменить изображение') }} @else {{ __('Изображение') }} @endisset</label>
                                        <div class="form-control-wrap">
                                            @isset($case)
                                                <span class="case-image"><img src="{{ $case->image_url }}" alt="{{ $case->id }}"></span>
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

                                <!-- itemitems -->
                                <div class="col-items">
                                    <div class="margin-bottom-50"></div>
                                    <div id="items">
                                        <div class="g-4 item" data-item="" id="item_" style="display: none;">
                                            <div class="row g-4">
                                                <div class="col-lg-2 case_type-block">
                                                    <div class="form-group">
                                                        <label class="form-label" for="case_item__var">{{ __('Тип') }}</label>
                                                        <div class="form-control-wrap">
                                                            <select id="case_item__var" class="case_type" @if(isset($case) && $case->kind === 2) name="case_item__var_off" @else name="case_item__var" @endif>
                                                                <option value="0">{{ __('Предмет') }}</option>
                                                                <option value="1">{{ __('VIP') }}</option>
                                                                <option value="2">{{ __('Бонус пополнения') }}</option>
                                                                <option value="3">{{ __('Баланс на аккаунт') }}</option>
                                                                <option value="5">{{ __('Товары магазина') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 case_type_shop-block" @if(!isset($case) || $case->kind !== 2) style="display: none;" @endif>
                                                    <div class="form-group">
                                                        <label class="form-label" for="case_item__var">{{ __('Тип') }}</label>
                                                        <div class="form-control-wrap">
                                                            <select id="case_item__var_shop" class="case_type_shop" @if(!isset($case) || $case->kind !== 2) name="case_item__var_off" @else name="case_item__var" @endif>
                                                                <option value="5">{{ __('Товары магазина') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 case-item_">
                                                    <div class="form-group">
                                                        <label class="form-label" for="case_item__id">{{ __('Skin ID') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="number" class="form-control" id="case_item__id"
                                                                   name="case_item__id" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 case-item_">
                                                    <div class="form-group">
                                                        <label class="form-label" for="case_item__name">{{ __('Название') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="text" class="form-control" id="case_item__name"
                                                                   name="case_item__name" value="" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <label class="form-label" for="case_item__drop_percent">{{ __('Шанс выигрыша') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="number" min="0" step="0.000001" max="100" class="form-control" id="case_item__drop_percent"
                                                                   name="case_item__drop_percent" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-1">
                                                    <div class="form-group">
                                                        <label class="form-label" for="case_item__available">{{ __('Доступно') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="number" min="0" class="form-control" id="case_item__available"
                                                                   name="case_item__available" value="">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 case-item_">
                                                    <div class="form-group">
                                                        <label class="form-label" for="case_item__image">{{ __('Изображение') }}</label>
                                                        <div class="form-control-wrap form-input-file form-control" style="background-color: #f5f6fa;">
                                                        </div>
                                                    </div>
                                                </div>


                                                {{-- VIP --}}
                                                <div class="col-lg-2 vip_case-item_" style="display: none;">
                                                    <div class="form-group">
                                                        <label class="form-label" for="case_item__vip_period">{{ __('Период VIP') }}</label>
                                                        <div class="form-control-wrap">
                                                            <select id="case_item__vip_period" name="case_item__vip_period">
                                                                <option value="3">{{ __('3 days') }}</option>
                                                                <option value="14">{{ __('14 days') }}</option>
                                                                <option value="30">{{ __('30 days') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>


                                                {{-- Deposit Bonus --}}
                                                <div class="col-lg-2 deposit_case-item_" style="display: none;">
                                                    <div class="form-group">
                                                        <label class="form-label" for="case_item__deposit_bonus">{{ __('Бонус') }}, %</label>
                                                        <div class="form-control-wrap">
                                                            <select id="case_item__deposit_bonus" name="case_item__deposit_bonus">
                                                                <option value="5">5%</option>
                                                                <option value="10">10%</option>
                                                                <option value="20">20%</option>
                                                                <option value="40">40%</option>
                                                                <option value="60">60%</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>


                                                {{-- Account balance --}}
                                                <div class="col-lg-2 balance_case-item_" style="display: none;">
                                                    <div class="form-group">
                                                        <label class="form-label" for="case_item__balance">{{ __('Баланс') }}, $</label>
                                                        <div class="form-control-wrap">
                                                            <select id="case_item__balance" name="case_item__balance">
                                                                <option value="1">$1</option>
                                                                <option value="2">$2</option>
                                                                <option value="3">$3</option>
                                                                <option value="10">$10</option>
                                                                <option value="15">$15</option>
                                                                <option value="20">$20</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>


                                                {{-- SHOP ITEM --}}
                                                <div class="col-lg-2 shop_case-item_" style="display: none;">
                                                    <div class="form-group">
                                                        <label class="form-label" for="case_item__shop_id">{{ __('Товар магазина') }}</label>
                                                        <div class="form-control-wrap">
                                                            <select id="case_item__shop_id" name="case_item__shop_id" class="form-select case_shop" data-search="on">
                                                                <option value="0">{{ __('Не выбрано') }}</option>
                                                                @foreach($shopitems as $shopitem)
                                                                    <option value="{{ $shopitem->id }}">{{ $shopitem->$name }} ({{ $shopitem->id }})</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-2 shop_case-item__var shop_case-item_" style="display: none;">
                                                    <div class="form-group">
                                                        <label class="form-label" for="case_item__shop_var">{{ __('Вариация товара') }}</label>
                                                        <div class="form-control-wrap">
                                                            <select id="case_item__shop_var" name="case_item__shop_var">
                                                                <option value="0">{{ __('Не выбрано') }}</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-1 shop_case-item__qty" style="display: none;">
                                                    <div class="form-group">
                                                        <label class="form-label" for="case_item__shop_min">{{ __('Мин') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="number" class="form-control" id="case_item__shop_min"
                                                                   name="case_item__shop_min" value="0">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-lg-1 shop_case-item__qty" style="display: none;">
                                                    <div class="form-group">
                                                        <label class="form-label" for="case_item__shop_max">{{ __('Макс') }}</label>
                                                        <div class="form-control-wrap">
                                                            <input type="number" class="form-control" id="case_item__shop_max"
                                                                   name="case_item__shop_max" value="0">
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-lg-1">
                                                    <div class="form-group delete-bonus">
                                                        <a class="btn delete" data-donat="item_" onClick="deleteitem('item_')">{{ __('Удалить') }}</a>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        @foreach($items as $item)
                                            <div class="g-4 item" data-item="{{ $loop->iteration }}" id="item_{{ $loop->iteration }}">
                                                <div class="row g-4">
                                                    <div class="col-lg-2 case_type-block">
                                                        <div class="form-group">
                                                            <label class="form-label" for="case_item_{{ $loop->iteration }}_var">{{ __('Тип') }}</label>
                                                            <div class="form-control-wrap">
                                                                <select id="case_item_{{ $loop->iteration }}_var" class="case_type" @if(isset($case) && $case->kind === 2) name="case_item_{{ $loop->iteration }}_var_off" @else name="case_item_{{ $loop->iteration }}_var" @endif>
                                                                    <option value="0" @if(isset($item->var) && $item->var == 0) selected @endif>{{ __('Предмет') }}</option>
                                                                    <option value="1" @if(isset($item->var) && $item->var == 1) selected @endif>{{ __('VIP') }}</option>
                                                                    <option value="2" @if(isset($item->var) && $item->var == 2) selected @endif>{{ __('Бонус пополнения') }}</option>
                                                                    <option value="3" @if(isset($item->var) && $item->var == 3) selected @endif>{{ __('Баланс на аккаунт') }}</option>
                                                                    <option value="5" @if(isset($item->var) && $item->var == 5) selected @endif>{{ __('Товары магазина') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 case_type_shop-block" @if(!isset($case) || $case->kind !== 2) style="display: none;" @endif>
                                                        <div class="form-group">
                                                            <label class="form-label" for="case_item_{{ $loop->iteration }}_var_shop">{{ __('Тип') }}</label>
                                                            <div class="form-control-wrap">
                                                                <select id="case_item_{{ $loop->iteration }}_var_shop" class="case_type_shop" @if(isset($case) && $case->kind === 2) name="case_item_{{ $loop->iteration }}_var" @else name="case_item_{{ $loop->iteration }}_var_off" @endif>
                                                                    <option value="5" @if(isset($item->var) && $item->var == 5) selected @endif>{{ __('Товары магазина') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 case-item_{{ $loop->iteration }}" @if(!isset($item->var) || $item->var != 0) style="display: none;" @endif>
                                                        <div class="form-group">
                                                            <label class="form-label"
                                                                   for="case_item_{{ $loop->iteration }}_id">{{ __('Skin ID') }}
                                                                ({{ $loop->iteration }})</label>
                                                            <div class="form-control-wrap">
                                                                <input type="number" class="form-control"
                                                                       id="case_item_{{ $loop->iteration }}_id"
                                                                       name="case_item_{{ $loop->iteration }}_id" value="{{ $item->item_id }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 case-item_{{ $loop->iteration }}" @if(!isset($item->var) || $item->var != 0) style="display: none;" @endif>
                                                        <div class="form-group">
                                                            <label class="form-label"
                                                                   for="case_item_{{ $loop->iteration }}_name">{{ __('Название') }}
                                                                ({{ $loop->iteration }})</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control"
                                                                       id="case_item_{{ $loop->iteration }}_name"
                                                                       name="case_item_{{ $loop->iteration }}_name" value="{{ get_skin($item->item_id)->name }}" readonly>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2">
                                                        <div class="form-group">
                                                            <label class="form-label"
                                                                   for="case_item_{{ $loop->iteration }}_drop_percent">{{ __('Шанс выигрыша') }}
                                                                ({{ $loop->iteration }})</label>
                                                            <div class="form-control-wrap">
                                                                <input type="text" class="form-control"
                                                                       id="case_item_{{ $loop->iteration }}_drop_percent"
                                                                       name="case_item_{{ $loop->iteration }}_drop_percent" value="{{ $item->drop_percent }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-1">
                                                        <div class="form-group">
                                                            <label class="form-label" for="case_item_{{ $loop->iteration }}_available">{{ __('Доступно') }}</label>
                                                            <div class="form-control-wrap">
                                                                <input type="number" min="0" class="form-control" id="case_item_{{ $loop->iteration }}_available"
                                                                       name="case_item_{{ $loop->iteration }}_available" value="{{ $item->available }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 case-item_{{ $loop->iteration }}" @if(!isset($item->var) || $item->var != 0) style="display: none;" @endif>
                                                        <div class="form-group">
                                                            <label class="form-label" for="case_item_{{ $loop->iteration }}_image">{{ __('Изображение') }}</label>
                                                            <div class="form-control-wrap form-input-file form-control" style="background-color: #f5f6fa;">
                                                                <img style="width: 30px;" src="{{ getImageUrl(get_skin($item->item_id)->image) }}"/>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    {{-- VIP --}}
                                                    <div class="col-lg-2 vip_case-item_{{ $loop->iteration }}" @if(!isset($item->var) || $item->var != 1) style="display: none;" @endif>
                                                        <div class="form-group">
                                                            <label class="form-label" for="case_item_{{ $loop->iteration }}_vip_period">{{ __('Период VIP') }}</label>
                                                            <div class="form-control-wrap">
                                                                <select id="case_item_{{ $loop->iteration }}_vip_period" name="case_item_{{ $loop->iteration }}_vip_period">
                                                                    <option value="3" @if(isset($item->vip_period) && $item->vip_period == 3) selected @endif>{{ __('3 days') }}</option>
                                                                    <option value="14" @if(isset($item->vip_period) && $item->vip_period == 10) selected @endif>{{ __('14 days') }}</option>
                                                                    <option value="30" @if(isset($item->vip_period) && $item->vip_period == 30) selected @endif>{{ __('30 days') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    {{-- Deposit Bonus --}}
                                                    <div class="col-lg-2 deposit_case-item_{{ $loop->iteration }}" @if(!isset($item->var) || $item->var != 2) style="display: none;" @endif>
                                                        <div class="form-group">
                                                            <label class="form-label" for="case_item_{{ $loop->iteration }}_deposit_bonus">{{ __('Бонус') }}, %</label>
                                                            <div class="form-control-wrap">
                                                                <select id="case_item_{{ $loop->iteration }}_deposit_bonus" name="case_item_{{ $loop->iteration }}_deposit_bonus">
                                                                    <option value="5" @if(isset($item->deposit_bonus) && $item->deposit_bonus == 5) selected @endif>5%</option>
                                                                    <option value="10" @if(isset($item->deposit_bonus) && $item->deposit_bonus == 10) selected @endif>10%</option>
                                                                    <option value="20" @if(isset($item->deposit_bonus) && $item->deposit_bonus == 20) selected @endif>20%</option>
                                                                    <option value="40" @if(isset($item->deposit_bonus) && $item->deposit_bonus == 40) selected @endif>40%</option>
                                                                    <option value="60" @if(isset($item->deposit_bonus) && $item->deposit_bonus == 60) selected @endif>60%</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    {{-- Account balance --}}
                                                    <div class="col-lg-2 balance_case-item_{{ $loop->iteration }}" @if(!isset($item->var) || $item->var != 3) style="display: none;" @endif>
                                                        <div class="form-group">
                                                            <label class="form-label" for="case_item_{{ $loop->iteration }}_balance">{{ __('Баланс') }}, $</label>
                                                            <div class="form-control-wrap">
                                                                <select id="case_item_{{ $loop->iteration }}_balance" name="case_item_{{ $loop->iteration }}_balance">
                                                                    <option value="1" @if(isset($item->balance) && $item->balance == 1) selected @endif>$1</option>
                                                                    <option value="2" @if(isset($item->balance) && $item->balance == 2) selected @endif>$2</option>
                                                                    <option value="3" @if(isset($item->balance) && $item->balance == 3) selected @endif>$3</option>
                                                                    <option value="10" @if(isset($item->balance) && $item->balance == 10) selected @endif>$10</option>
                                                                    <option value="15" @if(isset($item->balance) && $item->balance == 15) selected @endif>$15</option>
                                                                    <option value="20" @if(isset($item->balance) && $item->balance == 20) selected @endif>$20</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    {{-- SHOP ITEM --}}
                                                    <div class="col-lg-2 shop_case-item_{{ $loop->iteration }}" @if(!isset($item->var) || $item->var != 5) style="display: none;" @endif>
                                                        <div class="form-group">
                                                            <label class="form-label" for="case_item_{{ $loop->iteration }}_shop_id">{{ __('Товар магазина') }}</label>
                                                            <div class="form-control-wrap">
                                                                <select id="case_item_{{ $loop->iteration }}_shop_id" name="case_item_{{ $loop->iteration }}_shop_id" class="form-select case_shop" data-search="on">
                                                                    <option value="0">{{ __('Не выбрано') }}</option>
                                                                    @foreach($shopitems as $shopitem)
                                                                        <option value="{{ $shopitem->id }}" @if(isset($item->shop_id) && $item->shop_id == $shopitem->id) selected @endif>{{ $shopitem->$name }} ({{ $shopitem->id }})</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-2 shop_case-item_{{ $loop->iteration }}_var shop_case-item_{{ $loop->iteration }}" @if(!isset($item->var) || $item->var != 5 || empty(getshopitemvariation($item->shop_id))) style="display: none;" @endif>
                                                        <div class="form-group">
                                                            <label class="form-label" for="case_item_{{ $loop->iteration }}_shop_var">{{ __('Вариация товара') }}</label>
                                                            <div class="form-control-wrap">
                                                                <select id="case_item_{{ $loop->iteration }}_shop_var" name="case_item_{{ $loop->iteration }}_shop_var">
                                                                    <option value="0">{{ __('Не выбрано') }}</option>
                                                                    @if(isset($item->shop_id))
                                                                        @foreach(getshopitemvariation($item->shop_id) as $variation)
                                                                            <option value="{{ $variation->variation_id }}" @if(isset($variation->variation_id) && $variation->variation_id == $item->shop_var) selected @endif>{{ $variation->variation_name }}</option>
                                                                        @endforeach
                                                                    @endif
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="col-lg-1 shop_case-item_{{ $loop->iteration }}_qty" @if(!isset($item->var) || $item->var != 5 || !empty(getshopitemvariation($item->shop_id))) style="display: none;" @endif>
                                                        <div class="form-group">
                                                            <label class="form-label" for="case_item_{{ $loop->iteration }}_shop_min">{{ __('Мин') }}</label>
                                                            <div class="form-control-wrap">
                                                                <input type="number" class="form-control"
                                                                       id="case_item_{{ $loop->iteration }}_shop_min"
                                                                       name="case_item_{{ $loop->iteration }}_shop_min" value="@if(isset($item->shop_var) && isset(explode('-', $item->shop_var)[0])){{ explode('-', $item->shop_var)[0] }}@endif">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-1 shop_case-item_{{ $loop->iteration }}_qty" @if(!isset($item->shop_var) || !isset($item->var) || $item->var != 5 || !empty(getshopitemvariation($item->shop_id))) style="display: none;" @endif>
                                                        <div class="form-group">
                                                            <label class="form-label" for="case_item_{{ $loop->iteration }}_shop_max">{{ __('Макс') }}</label>
                                                            <div class="form-control-wrap">
                                                                <input type="number" class="form-control"
                                                                       id="case_item_{{ $loop->iteration }}_shop_max"
                                                                       name="case_item_{{ $loop->iteration }}_shop_max" value="@if(isset($item->shop_var) && isset(explode('-', $item->shop_var)[1])){{ explode('-', $item->shop_var)[1] }}@endif">
                                                            </div>
                                                        </div>
                                                    </div>


                                                    {{-- END SHOP ITEM --}}

                                                    <div class="col-lg-1">
                                                        <div class="form-group delete-bonus">
                                                            <a class="btn delete" data-donat="item_{{ $loop->iteration }}"
                                                               onClick="deleteitem('item_{{ $loop->iteration }}')">{{ __('Удалить') }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>

                                    <div class="row g-4">
                                        <div class="col-lg-12">
                                            <div class="form-group add-bonus">
                                                <a class="btn add additem">{{ __('Добавить') }}</a>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                                {{-- end items --}}

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

            let item_id = 1;
            let item_id_next = 1;
            let item_html = '';
            let sear = '';
            let repl = '';
            $('.additem').on('click', function(){
                item_id = $('.item:last').data('item');
                item_id_next = item_id + 1;
                item_id = '';
                sear = new RegExp('item_' + item_id, 'g');
                repl = 'item_' + item_id_next;
                item_html = $('#item_'+item_id).html().replace(sear,repl);
                sear = new RegExp('{{ __("Skin ID") }} ' + item_id, 'g');
                item_html = item_html.replace(sear,'{{ __("Skin ID") }} ' + item_id_next);

                $temp = $('<div>').html(item_html);
                $temp.find('.select2').remove();
                item_html = $temp.html();

                console.log(item_html);

                $('#items').append('<div class="g-4 item" data-item="'+item_id_next+'" id="item_' + item_id_next + '">' + item_html + '</div>');

                //Отображаем нужные поля для кейсов магазина
                if ($('#kind').val() === '2') {
                    for (let item_index = 0; item_index < 200; item_index++) {
                        $('.case-item_' + item_index).hide();
                        $('.vip_case-item_' + item_index).hide();
                        $('.deposit_case-item_' + item_index).hide();
                        $('.balance_case-item_' + item_index).hide();
                        $('.shop_case-item_' + item_index).show();

                        if ($('#case_item_' + item_index + '_shop_var').val() !== null) {
                            $('.shop_case-item_var_' + item_index).show();
                            $('.shop_case-item_qty_' + item_index).hide();
                        } else {
                            $('.shop_case-item_var_' + item_index).hide();
                            $('.shop_case-item_qty_' + item_index).show();
                        }
                    }

                }

                $('#case_item_' + item_id_next+'_shop_id').select2();
            });

            //Скрываем поля, если это кейс магазина
            if ($('#kind').val() === '2')
            {
                $('.case_type-block').hide();
                $('.case_type_shop-block').show();
                $('.online_amount-block').hide();
                $('.prizes_max-block').hide();
                $('.case_servers-block').hide();
                $('.is_free-block').show();
                $('.server-block').show();

                //Отображаем нужные для магазина поля в товарах
                for (let item_index=0;item_index<200;item_index++) {
                    $('.case-item_' + item_index).hide();
                    $('.vip_case-item_' + item_index).hide();
                    $('.deposit_case-item_' + item_index).hide();
                    $('.balance_case-item_' + item_index).hide();
                    $('.shop_case-item_' + item_index).show();
                }
            }
        });

        //item
        function deleteitem(item){
            $('#'+item).remove();
        }

        $(document).on('change', '.case_type', function(e){
            let item_index = $(this).parent().parent().parent().parent().parent().data('item');
            console.log(item_index);
            if ($(this).find('option:selected').val() == '0') {
                $('.vip_case-item_' + item_index).hide();
                $('.balance_case-item_' + item_index).hide();
                $('.deposit_case-item_' + item_index).hide();
                $('.shop_case-item_' + item_index).hide();
                $('.shop_case-item_qty_' + item_index).hide();
                $('.case-item_' + item_index).show();
            } else if ($(this).find('option:selected').val() == '1') {
                $('.case-item_' + item_index).hide();
                $('.balance_case-item_' + item_index).hide();
                $('.deposit_case-item_' + item_index).hide();
                $('.shop_case-item_' + item_index).hide();
                $('.shop_case-item_qty_' + item_index).hide();
                $('.vip_case-item_' + item_index).show();
            } else if ($(this).find('option:selected').val() == '2') {
                $('.case-item_' + item_index).hide();
                $('.vip_case-item_' + item_index).hide();
                $('.balance_case-item_' + item_index).hide();
                $('.shop_case-item_' + item_index).hide();
                $('.shop_case-item_qty_' + item_index).hide();
                $('.deposit_case-item_' + item_index).show();
            } else if ($(this).find('option:selected').val() == '3') {
                $('.case-item_' + item_index).hide();
                $('.vip_case-item_' + item_index).hide();
                $('.deposit_case-item_' + item_index).hide();
                $('.shop_case-item_' + item_index).hide();
                $('.shop_case-item_qty_' + item_index).hide();
                $('.balance_case-item_' + item_index).show();
            } else if ($(this).find('option:selected').val() == '5') {
                $('.case-item_' + item_index).hide();
                $('.vip_case-item_' + item_index).hide();
                $('.deposit_case-item_' + item_index).hide();
                $('.balance_case-item_' + item_index).hide();
                $('.shop_case-item_' + item_index).show();
                $('.shop_case-item_qty_' + item_index).hide();
            }
        });

        $(document).on('change', '.case_shop', function(e){
            let item_index = $(this).parent().parent().parent().parent().parent().data('item');
            console.log(item_index);
            let sel = $(this);
            $.ajax({
                type: "POST",
                url: "{{ route('shopitems.getVariations') }}",
                dataType: "json",
                data: { shopitem_id: sel.val(), _token: $('input[name="_token"]').val() }
            }).done(function( data ) {
                if (data.status == 'success') {
                    $('#case_item_'+item_index+'_shop_var').html('');
                    $.each(data.result , function() {
                        $('#case_item_'+item_index+'_shop_var').append(new Option($(this)[0].variation_name, $(this)[0].variation_id));
                    });

                    console.log($('#case_item_' + item_index + '_shop_var').val());

                    if ($('#case_item_' + item_index + '_shop_var').val() !== null) {
                        $('.shop_case-item_' + item_index + '_var').show();
                        $('.shop_case-item_' + item_index + '_qty').hide();
                    } else {
                        $('.shop_case-item_' + item_index + '_var').hide();
                        $('.shop_case-item_' + item_index + '_qty').show();
                    }
                }
            });
        });


        $(document).on('change', '#kind', function(e){
            console.log($(this).find('option:selected').val());
            if ($(this).find('option:selected').val() == '2') {
                $('.case_type-block').hide();
                $('.case_type_shop-block').show();
                $('.online_amount-block').hide();
                $('.prizes_max-block').hide();
                $('.case_servers-block').hide();
                $('.server-block').show();
                $('.is_free-block').show();
                $('.case_type').attr('name', 'case_item__var_off');
                $('.case_type_shop').attr('name', 'case_item__var');

                for (let item_index=0;item_index<200;item_index++) {
                    $('.case-item_' + item_index).hide();
                    $('.vip_case-item_' + item_index).hide();
                    $('.deposit_case-item_' + item_index).hide();
                    $('.balance_case-item_' + item_index).hide();
                    $('.shop_case-item_' + item_index).show();
                }
            } else {
                $('.case_type_shop-block').hide();
                $('.case_type-block').show();
                $('.online_amount-block').show();
                $('.prizes_max-block').show();
                $('.case_servers-block').show();
                $('.is_free-block').hide();
                $('.server-block').hide();
                $('.case_type_shop').attr('name', 'case_item__var_off');
                $('.case_type').attr('name', 'case_item__var');

                $('.case_type').val('0');
                for (let item_index=0;item_index<200;item_index++) {
                    $('.vip_case-item_' + item_index).hide();
                    $('.balance_case-item_' + item_index).hide();
                    $('.deposit_case-item_' + item_index).hide();
                    $('.shop_case-item_' + item_index).hide();
                    $('.case-item_' + item_index).show();
                }
            }
        });
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
