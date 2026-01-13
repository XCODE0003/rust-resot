@extends('layouts.main')

@section('title', __('Магазин') . ' - ' . $server->name . config('options.main_title_'.app()->getLocale(), '') )

@php
    $title = "title_" . app()->getLocale();
    $name = "name_" .app()->getLocale();
    $description = "description_" .app()->getLocale();
    $short_description = "short_description_" .app()->getLocale();
@endphp

@section('content')

    @if(isset(auth()->user()->role) && auth()->user()->role === 'admin' || 1==1)

        @if(isset(auth()->user()->role) && auth()->user()->role === 'admin' || 1==1)
            @foreach($shopcases as $shopcase)
                <div id="sb__popup-case-{{ $shopcase->id }}" class="sb__popup sb__popup-shop">
                    <div class="sb-popup_back"></div>
                    <!-- content -->
                    <div class="sb-popup__content spc">
                        <div class="spc__close">
                            <i class="fa-solid fa-xmark"></i>
                        </div>

                        <div class="spc__info"></div>

                    </div>
                </div>
            @endforeach
        @endif

        @foreach($shopcategories as $shopcategory)

            @foreach($shopitems[$shopcategory->id] as $shopitem)
            <div id="sb__popup-item-{{ $shopitem->id }}" class="sb__popup">
            <div class="sb-popup_back"></div>
            <!-- content -->
            <div class="sb-popup__content spc shop-item-modal">
                <div class="spc__close">
                    <i class="fa-solid fa-xmark"></i>
                </div>
                <div class="spc__mg">
                    <img src="{{ $shopitem->image_url }}" alt="{{ $shopitem->$name }}"/>
                </div>
                <div class="spc__title">{{ str_replace('ELITEPACK', 'ELITE PACK', str_replace('BUILDINGSKINS', 'BUILDING SKINS', $shopitem->$name)) }}</div>
                <div class="spc__text">{{ $shopitem->$short_description }}</div>
                {!! $shopitem->$description !!}

                @if(isset($shopitem->wipe_block) && $shopitem->wipe_block !== NULL)
                    <div class="spc__text">
                        {{ __('Игровое ограничение ( вайп блок )') }}: {{ $shopitem->wipe_block }} {{ plural_form($shopitem->wipe_block, [__('час'), __('часа'), __('часов')]) }}
                    </div>
                    <div class="spc__text shop-item-text">
                        {{ __('Что бы получить любые купленные предметы, перейдите на игровой сервер и пропишите команду в радиусе действия вашего шкафа') }}: <span class="shop-item-command">/store</span>
                    </div>
                @endif

                <form action="{{ route('shop.item.buy') }}" method="POST" id="topup-balance-form-{{ $shopitem->id }}">
                    @csrf
                    <input type="hidden" name="server_id" value="{{ $server->id }}">
                    <input type="hidden" name="item_id" value="{{ $shopitem->id }}">
                    <input type="hidden" name="payment_id" value="20">
                    <input type="hidden" name="qty" value="1" class="item-qty-value">
                    <input type="hidden" name="amount" value="{{ $shopitem->amount }}" class="item-amount-value">
                    <input type="hidden" name="item_price" value="@if(app()->getLocale() == 'ru'){{ number_format($shopitem->price, 0, '.', '') }}@else{{ number_format($shopitem->price_usd, 2, '.', '') }}@endif" class="item-price-value">

                    @if($shopitem->can_gift === 1)
                        <div class="spc__gift {{ $shopitem->can_gift }}">
                            <div class="spc-gift__text"><span></span>{{ __('Купить в подарок') }}</div>
                            <div class="spc-gift__user">
                                <input type="text" name="steam_id" class="steam-id" placeholder="Steam ID64" value=""/>
                                <p>
                                    <span class="steam-id-error">*{{ __('Вы должны ввести только цифры SteamID') }}.</span>
                                    <span class="steam-id-notice">*{{ __('Вы сделаете подарок этому пользователю') }}.</span>
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="mt-3"></div>
                    @endif

                    @php $first_item_var_id = 0; @endphp
                        @php $variations = json_decode($shopitem->variations) @endphp
                        @if(!empty($variations))
                        <div class="spc__days">
                            <div class="spc__text">{{ __('Срок') }}:</div>
                            <div class="spc-days__day">
                                <div class="spc-days-day__select">
                                    <select class="spcdds__variation variation-shopitem">

                                        @foreach($variations as $variation)
                                            @if(isset($variation->variation_id))
                                                @if($loop->first)
                                                    @php $first_item_var_id = $variation->variation_id; @endphp
                                                @endif

                                                @if(app()->getLocale() == 'ru')
                                                    <option value="1" data-varprice="{{ $variation->variation_price }}" data-varid="{{ $variation->variation_id }}">{{ translate($variation->variation_name) }}</option>
                                                @else
                                                    <option value="1" data-varprice="{{ $variation->variation_price_usd }}" data-varid="{{ $variation->variation_id }}">{{ translate($variation->variation_name) }}</option>
                                                @endif
                                            @elseif(isset($variation->quantity_id))
                                                @if($loop->first)
                                                    @php $first_item_var_id = $variation->quantity_id; @endphp
                                                @endif

                                                @if(app()->getLocale() == 'ru')
                                                    <option value="1" data-varprice="{{ $variation->quantity_price }}" data-varid="{{ $variation->quantity_id }}">{{ translate($variation->variation_name) }}</option>
                                                @else
                                                    <option value="1" data-varprice="{{ $variation->quantity_price_usd }}" data-varid="{{ $variation->quantity_id }}">{{ translate($variation->variation_name) }}</option>
                                                @endif
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @else

                            <div class="spc__text item-amount-group"><span class="item-amount-text">{{ __('Количество') }}:</span>
                                <div class="spc__text item-amount-block">
                                    <span class="amount-minus">-</span><span>x<span class="item-amount">{{ $shopitem->amount }}</span></span><span class="amount-plus">+</span>
                                </div>
                            </div>

                        @endif


                    <input type="hidden" class="var_id" name="var_id" value="{{ $first_item_var_id }}">

                    <!-- cost -->
                    <div class="spc__price">
                        <div class="spc-price__text">{{ __('Стоимость') }}:</div>
                        <div class="spc-price__value"><span class="buy-item-price">@if(app()->getLocale() == 'ru'){{ number_format($shopitem->price, 0, '.', '') }}@else{{ number_format($shopitem->price_usd, 2, '.', '') }}@endif</span> @if(app()->getLocale() == 'ru'){{__('₽')}}@else{{ 'USD' }}@endif</div>
                    </div>

                    @if(!isset(auth()->user()->id))
                        <a href="{{ route('login') }}" class="spc__buy">{{ __('Купить') }}</a>
                    @else

                        @if(auth()->user()->balance >= $shopitem->price)
                            <a class="spc__buy btn-buy-item" data-id="{{ $shopitem->id }}">{{ __('Купить') }}</a>
                        @else
                            <a href="{{ route('account.profile', ['topup' => 1]) }}" class="spc__buy">{{ __('Пополнить баланс') }}</a>
                        @endif

                    @endif

                </form>
            </div>
        </div>
        @endforeach


            @foreach($shopsets[$shopcategory->id] as $shopset)
                <div id="sb__popup-item-{{ $shopset->id }}" class="sb__popup">
                    <div class="sb-popup_back"></div>
                    <!-- content -->
                    <div class="sb-popup__content spc shop-item-modal">
                        <div class="spc__close">
                            <i class="fa-solid fa-xmark"></i>
                        </div>
                        <div class="spc__mg">
                            <img src="{{ $shopset->image_url }}" alt="{{ $shopset->$name }}"/>
                        </div>
                        <div class="spc__title">{{ $shopset->$name }}</div>
                        <div class="spc__text">{{ $shopset->$short_description }}</div>

                        <div class="spc__text shop-item-text">
                            {{ __('Что бы получить любые купленные предметы, перейдите на игровой сервер и пропишите команду в радиусе действия вашего шкафа') }}: <span class="shop-item-command">/store</span>
                        </div>

                        <section class="page-bonus-block set-block">

                            @foreach($shopset->items_arr as $category_id => $items)
                                <div class="page-bonus-title">
                                    <span>{{ getshopcategory($category_id)->$title }}</span>
                                </div>

                                <div class="page-bonus-prizes">
                                    @foreach($items as $item)
                                         <div class="page-bonus-prize type-">
                                            <div class="quality-type"></div>
                                            <img src="{{ getImageUrl($item->image) }}" alt="{{ $item->$name }}">
                                            <span class="item-amount">x{{ $item->qty }}</span>
                                            <span>{{ $item->$name }}</span>

                                            <span class="wipe-block tooltip" style="right: 15px;top: 38px;">
                                                <span class="tooltiptext">{{ __('Блокировка после вайпа:') }} {{ $item->wipe_block }} {{ plural_form($item->wipe_block, [__('час'), __('часа'), __('часов')]) }} </span>
                                                <img src="/images/info_white.svg" class="icon ni ni-info">
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </section>

                        @if(isset($shopset->wipe_block) && $shopset->wipe_block !== NULL)
                            <div class="spc__text">
                                {{ __('Игровое ограничение ( вайп блок )') }}: {{ $shopset->wipe_block }} {{ plural_form($shopset->wipe_block, [__('час'), __('часа'), __('часов')]) }}
                            </div>
                            <div class="spc__text shop-item-text">
                                {{ __('Что бы получить любые купленные предметы, перейдите на игровой сервер и пропишите команду в радиусе действия вашего шкафа') }}: <span class="shop-item-command">/store</span>
                            </div>
                        @endif

                        <form action="{{ route('shop.set.buy') }}" method="POST" id="topup-balance-form-{{ $shopset->id }}">
                            @csrf
                            <input type="hidden" name="server_id" value="{{ $server->id }}">
                            <input type="hidden" name="set_id" value="{{ $shopset->id }}">
                            <input type="hidden" name="payment_id" value="20">
                            <input type="hidden" name="qty" value="1" class="item-qty-value">
                            <input type="hidden" name="amount" value="{{ $shopset->amount }}" class="item-amount-value">
                            <input type="hidden" name="item_price" value="@if(app()->getLocale() == 'ru'){{ number_format($shopset->price, 0, '.', '') }}@else{{ number_format($shopset->price_usd, 2, '.', '') }}@endif" class="item-price-value">

                            @if($shopset->can_gift === 1)
                                <div class="spc__gift {{ $shopset->can_gift }}">
                                    <div class="spc-gift__text"><span></span>{{ __('Купить в подарок') }}</div>
                                    <div class="spc-gift__user">
                                        <input type="text" name="steam_id" class="steam-id" placeholder="Steam ID64" value=""/>
                                        <p>
                                            <span class="steam-id-error">*{{ __('Вы должны ввести только цифры SteamID') }}.</span>
                                            <span class="steam-id-notice">*{{ __('Вы сделаете подарок этому пользователю') }}.</span>
                                        </p>
                                    </div>
                                </div>
                            @else
                                <div class="mt-3"></div>
                            @endif

                                <div class="spc__text item-amount-group"><span class="item-amount-text">{{ __('Количество') }}:</span>
                                    <div class="spc__text item-amount-block">
                                        <span class="amount-minus">-</span><span>x<span class="item-amount">{{ $shopset->amount }}</span></span><span class="amount-plus">+</span>
                                    </div>
                                </div>

                            <!-- cost -->
                            <div class="spc__price">
                                <div class="spc-price__text">{{ __('Стоимость') }}:</div>
                                <div class="spc-price__value"><span class="buy-item-price">@if(app()->getLocale() == 'ru'){{ number_format($shopset->price, 0, '.', '') }}@else{{ number_format($shopset->price_usd, 2, '.', '') }}@endif</span> @if(app()->getLocale() == 'ru'){{__('₽')}}@else{{ 'USD' }}@endif</div>
                            </div>

                            @if(!isset(auth()->user()->id))
                                <a href="{{ route('login') }}" class="spc__buy">{{ __('Купить') }}</a>
                            @else

                                @if(auth()->user()->balance >= $shopset->price)
                                    <a class="spc__buy btn-buy-item" data-id="{{ $shopset->id }}">{{ __('Купить') }}</a>
                                @else
                                    <a href="{{ route('account.profile', ['topup' => 1]) }}" class="spc__buy">{{ __('Пополнить баланс') }}</a>
                                @endif

                            @endif

                        </form>
                    </div>
                </div>
            @endforeach

        @endforeach

        <div class="inner-header spb—sp-inner">{{ __('Магазин') }}</div>

        <div class="inner spb—sp-inner spb-sp-inner-in">
            <div class="container">
                <div class="shop-item shop-item--special">
                    <div class="inner-nav">
                        <a href="{{ route('shop') }}"><i class="fa-solid fa-cart-shopping"></i> {{ __('Магазин') }}</a>
                        <a class="active">{{ $server->name }}</a>
                    </div>

                    <div class="shop-item-server"><p>{{ __('Магазин') }}: <span>{{ $server->name }}</span></p></div>

                    <div class="shop-item-server">

                    </div>

                    <div class="container">
                        <div class="stats tabs">

                            <div class="stats-nav tab-nav" @if(!in_array($server->id, [1,2,3,8])) style="display: none;" @endif>
                                <ul>
                                    <li class="active"><span data-href="#main_0">{{ __('Все') }}</span></li>
                                    @foreach($shopcategories as $shopcategory)
                                        @if($shopcategory->id == 6) @continue @endif
                                        <li><span data-href="#main_{{ $shopcategory->id }}">
                                                @if($shopcategory->id == 19)
                                                    <div class="label cat-label">{{ __('New') }}</div>
                                                @endif
                                                {{ $shopcategory->$title }}
                                            </span></li>
                                    @endforeach
                                    {{--
                                    <li style="margin-bottom: 15px !important;"><span data-href="#main_9999">{{ __('Кейсы') }}</span></li>
                                    --}}
                                </ul>

                                <div class="stats-icon">
                                    <i class="fa-solid fa-chevron-down"></i>
                                </div>
                            </div>

                            <div class="tab active" id="main_0">

                                <div class="stats-content tabs">
                                    <div class="shop-item-list">
                                        @if(isset(auth()->user()->role) && auth()->user()->role === 'admin' || 1==1)
                                        @foreach($shopcases as $shopcase)
                                            <div class="item">
                                                <div class="shop-item-buy sib-special shopcase-buy" data-id="{{ $shopcase->id }}">
                                                    <div class="shop-item-buy-name shop-item-buy-name--title">{{ $shopcase->$title }}</div>
                                                    <div class="shop-item-buy-name shop-item-image">
                                                        <img src="{{ $shopcase->image_url }}" alt="{{ $shopcase->$title }}">
                                                    </div>

                                                    <div class="shop-item-buy__content">

                                                        <div class="shop-item-buy__content--price">
                                                            <div class="shop-item-buy__content--price--cost">
                                                                @if($shopcase->is_free === 1)
                                                                    @if(free_case_left_time($shopcase->id) > 0)
                                                                        <span class="buy-item-price">{!! format_seconds_from_day(free_case_left_time($shopcase->id)) !!}</span>
                                                                    @else
                                                                        <span class="buy-item-price">{{ __('Бесплатно') }}</span>
                                                                    @endif
                                                                @else
                                                                    <span class="buy-item-price">@if(app()->getLocale() == 'ru'){{ number_format($shopcase->price, 0, '.', '') }}@else{{ number_format($shopcase->price_usd, 0, '.', '') }}@endif</span> @if(app()->getLocale() == 'ru'){{__('₽')}}@else{{ 'USD' }}@endif
                                                                @endif
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @endif

                                            @foreach($shopcategories as $shopcategory)
                                                @if($shopcategory->id == 6) @continue @endif

                                                    @foreach($shopitems[$shopcategory->id] as $shopitem)
                                                        <div class="item">
                                                            <div class="shop-item-buy sib-special shopitem-buy" data-id="{{ $shopitem->id }}">
                                                                <div class="shop-item-buy-name shop-item-buy-name--title">{{ str_replace('ELITEPACK', 'ELITE PACK', str_replace('BUILDINGSKINS', 'BUILDING SKINS', $shopitem->$name)) }}</div>
                                                                <div class="shop-item-buy-name shop-item-image">
                                                                    <img src="{{ $shopitem->image_url }}" alt="{{ $shopitem->$name }}">
                                                                </div>

                                                                @if(isset($shopitem->wipe_block) && $shopitem->wipe_block !== NULL)
                                                                    <span class="wipe-block tooltip" style="left: 12px;top: 12px;position: absolute;border: none;">
                                                                        <span class="tooltiptext" style="font-family: auto;font-size: 12px;">{{ __('Блокировка после вайпа:') }} {{ $shopitem->wipe_block }} {{ plural_form($shopitem->wipe_block, [__('час'), __('часа'), __('часов')]) }} </span>
                                                                        <img src="/images/info_white.svg" class="icon ni ni-info">
                                                                    </span>
                                                                @endif


                                                                <div class="shop-item-buy__content">

                                                                    <div class="shop-item-buy__content--days d-flex">
                                                                        @php $variations = json_decode($shopitem->variations) @endphp
                                                                        <div class="shop-item-buy__content--days__text">{{ __('Срок') }}:</div>
                                                                        <div class="shop-item-buy__content--days__days">
                                                                            <div class="select">
                                                                                <select class="variation">
                                                                                    @php $first_item_var_id = 0; @endphp
                                                                                    @foreach($variations as $variation)
                                                                                        @if(isset($variation->variation_id))
                                                                                            @if($loop->first)
                                                                                                @php $first_item_var_id = $variation->variation_id; @endphp
                                                                                            @endif

                                                                                            @if(app()->getLocale() == 'ru')
                                                                                                <option value="1" data-varprice="{{ $variation->variation_price }}" data-varid="{{ $variation->variation_id }}">{{ translate($variation->variation_name) }}</option>
                                                                                            @else
                                                                                                <option value="1" data-varprice="{{ $variation->variation_price_usd }}" data-varid="{{ $variation->variation_id }}">{{ translate($variation->variation_name) }}</option>
                                                                                            @endif
                                                                                        @elseif(isset($variation->quantity_id))
                                                                                            @if($loop->first)
                                                                                                @php $first_item_var_id = $variation->quantity_id; @endphp
                                                                                            @endif

                                                                                            @if(app()->getLocale() == 'ru')
                                                                                                <option value="1" data-varprice="{{ $variation->quantity_price }}" data-varid="{{ $variation->quantity_id }}">{{ translate($variation->variation_name) }}</option>
                                                                                            @else
                                                                                                <option value="1" data-varprice="{{ $variation->quantity_price_usd }}" data-varid="{{ $variation->quantity_id }}">{{ translate($variation->variation_name) }}</option>
                                                                                            @endif
                                                                                        @endif
                                                                                    @endforeach
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    @if($shopitem->is_command !== 1)
                                                                        <div class="shop-item-buy__content--amount">
                                                                            <span class="item-amount">x{{ $shopitem->amount }}</span>
                                                                        </div>
                                                                    @endif
                                                                    <div class="shop-item-buy__content--price">
                                                                        <div class="shop-item-buy__content--price--cost">
                                                                            <span class="buy-item-price">@if(app()->getLocale() == 'ru'){{ number_format($shopitem->price, 0, '.', '') }}@else{{ number_format($shopitem->price_usd, 2, '.', '') }}@endif</span> @if(app()->getLocale() == 'ru'){{__('₽')}}@else{{ 'USD' }}@endif
                                                                        </div>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                    @foreach($shopsets[$shopcategory->id] as $shopset)
                                                        <div class="item">
                                                            <div class="shop-item-buy sib-special shopitem-buy" data-id="{{ $shopset->id }}">
                                                                <div class="shop-item-buy-name shop-item-buy-name--title">{{ $shopset->$name }}</div>
                                                                <div class="shop-item-buy-name shop-item-image">
                                                                    <div class="label">{{ __('New') }}</div>
                                                                    <img src="{{ $shopset->image_url }}" alt="{{ $shopset->$name }}">
                                                                </div>

                                                                @if(isset($shopset->wipe_block) && $shopset->wipe_block !== NULL)
                                                                    <span class="wipe-block tooltip" style="left: 12px;top: 12px;position: absolute;border: none;">
                                                                        <span class="tooltiptext" style="font-family: auto;font-size: 12px;">{{ __('Блокировка после вайпа:') }} {{ $shopset->wipe_block }} {{ plural_form($shopset->wipe_block, [__('час'), __('часа'), __('часов')]) }} </span>
                                                                        <img src="/images/info_white.svg" class="icon ni ni-info">
                                                                    </span>
                                                                @endif

                                                                <div class="shop-item-buy__content">
                                                                    <div class="shop-item-buy__content--price">
                                                                        <div class="shop-item-buy__content--price--cost">
                                                                            <span class="buy-item-price">@if(app()->getLocale() == 'ru'){{ number_format($shopset->price, 0, '.', '') }}@else{{ number_format($shopset->price_usd, 2, '.', '') }}@endif</span> @if(app()->getLocale() == 'ru'){{__('₽')}}@else{{ 'USD' }}@endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                            @endforeach
                                    </div>
                                </div>
                            </div>

                            @foreach($shopcategories as $shopcategory)
                                @if($shopcategory->id == 6) @continue @endif
                                <div class="tab" id="main_{{ $shopcategory->id }}">

                                    <div class="stats-content tabs">
                                        <div class="shop-item-list">

                                        @foreach($shopitems[$shopcategory->id] as $shopitem)
                                            <div class="item">
                                                <div class="shop-item-buy sib-special shopitem-buy" data-id="{{ $shopitem->id }}">
                                                    <div class="shop-item-buy-name shop-item-buy-name--title">{{ $shopitem->$name }}</div>
                                                    <div class="shop-item-buy-name shop-item-image">
                                                        <img src="{{ $shopitem->image_url }}" alt="{{ $shopitem->$name }}">
                                                    </div>

                                                    @if(isset($shopitem->wipe_block) && $shopitem->wipe_block !== NULL)
                                                        <span class="wipe-block tooltip" style="left: 12px;top: 12px;position: absolute;border: none;">
                                                            <span class="tooltiptext" style="font-family: auto;font-size: 12px;">{{ __('Блокировка после вайпа:') }} {{ $shopitem->wipe_block }} {{ plural_form($shopitem->wipe_block, [__('час'), __('часа'), __('часов')]) }} </span>
                                                            <img src="/images/info_white.svg" class="icon ni ni-info">
                                                        </span>
                                                    @endif

                                                    <div class="shop-item-buy__content">

                                                        <div class="shop-item-buy__content--days d-flex">
                                                            @php $variations = json_decode($shopitem->variations) @endphp
                                                            <div class="shop-item-buy__content--days__text">{{ __('Срок') }}:</div>
                                                            <div class="shop-item-buy__content--days__days">
                                                                <div class="select">
                                                                    <select class="variation">
                                                                        @php $first_item_var_id = 0; @endphp
                                                                        @foreach($variations as $variation)
                                                                            @if(isset($variation->variation_id))
                                                                                @if($loop->first)
                                                                                    @php $first_item_var_id = $variation->variation_id; @endphp
                                                                                @endif

                                                                                @if(app()->getLocale() == 'ru')
                                                                                    <option value="1" data-varprice="{{ $variation->variation_price }}" data-varid="{{ $variation->variation_id }}">{{ translate($variation->variation_name) }}</option>
                                                                                @else
                                                                                    <option value="1" data-varprice="{{ $variation->variation_price_usd }}" data-varid="{{ $variation->variation_id }}">{{ translate($variation->variation_name) }}</option>
                                                                                @endif
                                                                            @elseif(isset($variation->quantity_id))
                                                                                @if($loop->first)
                                                                                    @php $first_item_var_id = $variation->quantity_id; @endphp
                                                                                @endif

                                                                                @if(app()->getLocale() == 'ru')
                                                                                    <option value="1" data-varprice="{{ $variation->quantity_price }}" data-varid="{{ $variation->quantity_id }}">{{ translate($variation->variation_name) }}</option>
                                                                                @else
                                                                                    <option value="1" data-varprice="{{ $variation->quantity_price_usd }}" data-varid="{{ $variation->quantity_id }}">{{ translate($variation->variation_name) }}</option>
                                                                                @endif
                                                                            @endif
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        @if($shopitem->is_command !== 1)
                                                            <div class="shop-item-buy__content--amount">
                                                                <span class="item-amount">x{{ $shopitem->amount }}</span>
                                                            </div>
                                                        @endif
                                                        <div class="shop-item-buy__content--price">
                                                            <div class="shop-item-buy__content--price--cost">
                                                                <span class="buy-item-price">@if(app()->getLocale() == 'ru'){{ number_format($shopitem->price, 0, '.', '') }}@else{{ number_format($shopitem->price_usd, 2, '.', '') }}@endif</span> @if(app()->getLocale() == 'ru'){{__('₽')}}@else{{ 'USD' }}@endif
                                                            </div>
                                                        </div>



                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                            @foreach($shopsets[$shopcategory->id] as $shopset)
                                                <div class="item">
                                                    <div class="shop-item-buy sib-special shopitem-buy" data-id="{{ $shopset->id }}">
                                                        <div class="shop-item-buy-name shop-item-buy-name--title">{{ $shopset->$name }}</div>
                                                        <div class="shop-item-buy-name shop-item-image">
                                                            <div class="label">{{ __('New') }}</div>
                                                            <img src="{{ $shopset->image_url }}" alt="{{ $shopset->$name }}">
                                                        </div>

                                                        @if(isset($shopset->wipe_block) && $shopset->wipe_block !== NULL)
                                                            <span class="wipe-block tooltip" style="left: 12px;top: 12px;position: absolute;border: none;">
                                                            <span class="tooltiptext" style="font-family: auto;font-size: 12px;">{{ __('Блокировка после вайпа:') }} {{ $shopset->wipe_block }} {{ plural_form($shopset->wipe_block, [__('час'), __('часа'), __('часов')]) }} </span>
                                                            <img src="/images/info_white.svg" class="icon ni ni-info">
                                                        </span>
                                                        @endif

                                                        <div class="shop-item-buy__content">

                                                            <div class="shop-item-buy__content--price">
                                                                <div class="shop-item-buy__content--price--cost">
                                                                    <span class="buy-item-price">@if(app()->getLocale() == 'ru'){{ number_format($shopset->price, 0, '.', '') }}@else{{ number_format($shopset->price_usd, 2, '.', '') }}@endif</span> @if(app()->getLocale() == 'ru'){{__('₽')}}@else{{ 'USD' }}@endif
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            <div class="tab" id="main_9999">

                                <div class="stats-content tabs">
                                    <div class="shop-item-list">
                                        @if(isset(auth()->user()->role) && auth()->user()->role === 'admin' || 1==1)
                                        @foreach($shopcases as $shopcase)
                                            <div class="item">
                                                <div class="shop-item-buy sib-special shopcase-buy" data-id="{{ $shopcase->id }}">
                                                    <div class="shop-item-buy-name shop-item-buy-name--title">{{ $shopcase->$title }}</div>
                                                    <div class="shop-item-buy-name shop-item-image">
                                                        <img src="{{ $shopcase->image_url }}" alt="{{ $shopcase->$title }}">
                                                    </div>

                                                    <div class="shop-item-buy__content">

                                                        <div class="shop-item-buy__content--price">
                                                            <div class="shop-item-buy__content--price--cost">
                                                                <span class="buy-item-price">@if(app()->getLocale() == 'ru'){{ number_format($shopcase->price, 0, '.', '') }}@else{{ number_format($shopcase->price_usd, 0, '.', '') }}@endif</span> @if(app()->getLocale() == 'ru'){{__('₽')}}@else{{ 'USD' }}@endif
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                        @endif
                                </div>
                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>


    @else


    <div class="inner-header">{{ __('Магазин') }}</div>

    <div class="inner">
        <div class="container">
            <div class="shop-item">
                <div class="inner-nav">
                    <a href="{{ route('shop') }}"><i class="fa-solid fa-cart-shopping"></i> {{ __('Магазин') }}</a>
                    <a class="active">{{ $server->name }}</a>
                </div>

                <div class="shop-item-server"><p>{{ __('Магазин') }}: <span>{{ $server->name }}</span></p></div>

                <div class="shop-item-server">

                </div>

                <div class="shop-item-list">

                    @foreach($shopcategories as $shopcategory)
                        @foreach($shopitems[$shopcategory->id] as $shopitem)
                        <div class="item">
                            <div class="shop-item-buy">
                                <div class="shop-item-buy-name shop-item-buy-name--title">{{ $shopitem->$name }}</div>
                                <div class="shop-item-buy-name shop-item-image">
                                    <img src="{{ $shopitem->image_url }}" alt="{{ $shopitem->$name }}">
                                </div>
                                <div class="shop-item-buy-gift"><div class="shop-item-buy-gift-text"><span></span>{{ __('Купить в подарок') }}</div>
                                    <div class="shop-item-buy-gift-user gift-user">
                                        <input type="text" name="steam_id" class="steam-id" placeholder="Steam ID64" value="">
                                        <p>
                                            <span class="steam-id-error">*{{ __('Вы должны ввести только цифры SteamID') }}.</span>
                                            <span class="steam-id-notice">*{{ __('Вы сделаете подарок этому пользователю') }}.</span>
                                        </p>
                                    </div>
                                </div>

                                <div class="shop-item-buy__content">
                                    <div class="shop-item-buy__content--description">
                                        {{ __('Описание') }}: {{ Str::limit(strip_tags($shopitem->$description), 20) }}

                                    </div>
                                    <div class="shop-item-buy__content--days d-flex">
                                        @php $variations = json_decode($shopitem->variations) @endphp
                                        <div class="shop-item-buy__content--days__text">{{ __('Срок') }}:</div>
                                        <div class="shop-item-buy__content--days__days">
                                            <div class="select">
                                                <select class="variation">
                                                    @php $first_item_var_id = 0; @endphp
                                                    @foreach($variations as $variation)
                                                        @if(isset($variation->variation_id))
                                                            @if($loop->first)
                                                                @php $first_item_var_id = $variation->variation_id; @endphp
                                                            @endif

                                                            @if(app()->getLocale() == 'ru')
                                                                <option value="1" data-varprice="{{ $variation->variation_price }}" data-varid="{{ $variation->variation_id }}">{{ translate($variation->variation_name) }}</option>
                                                            @else
                                                                <option value="1" data-varprice="{{ $variation->variation_price_usd }}" data-varid="{{ $variation->variation_id }}">{{ translate($variation->variation_name) }}</option>
                                                            @endif
                                                        @elseif(isset($variation->quantity_id))
                                                            @if($loop->first)
                                                                @php $first_item_var_id = $variation->quantity_id; @endphp
                                                            @endif

                                                            @if(app()->getLocale() == 'ru')
                                                                <option value="1" data-varprice="{{ $variation->quantity_price }}" data-varid="{{ $variation->quantity_id }}">{{ translate($variation->variation_name) }}</option>
                                                            @else
                                                                <option value="1" data-varprice="{{ $variation->quantity_price_usd }}" data-varid="{{ $variation->quantity_id }}">{{ translate($variation->variation_name) }}</option>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="shop-item-buy__content--price">
                                        <div class="shop-item-buy__content--price--text">
                                            {{ __('Стоимость') }}:
                                        </div>
                                        <div class="shop-item-buy__content--price--cost">
                                            <span class="buy-item-price">@if(app()->getLocale() == 'ru'){{ $shopitem->price }}@else{{ $shopitem->price_usd }}@endif</span> @if(app()->getLocale() == 'ru'){{__('руб.')}}@else{{ 'USD' }}@endif
                                        </div>
                                    </div>

                                    @if(!isset(auth()->user()->id))
                                        <a class="shop-item-buy__content--btn" onclick="window.location='{{ route('login') }}';">{{ __('Купить') }}</a>
                                    @else
                                        <a class="shop-item-buy__content--btn buy buy-item-button" data-itemid="{{ $shopitem->id }}" data-itemname="{{ $shopitem->$name }}" data-itemprice="@if(app()->getLocale() == 'ru'){{ $shopitem->price }}@else{{ $shopitem->price_usd }}@endif" data-varid="{{ $first_item_var_id }}">{{ __('Купить') }}</a>
                                    @endif

                                </div>
                            </div>
                        </div>
                    @endforeach
                    @endforeach

                </div>
            </div>
        </div>
    </div>

    <div class="buying-item-modal balance-modal modal">
        <div class="modal-close"></div>

        <div class="modal-content buying-item">
            <div class="buying-item-close"><i class="fa-solid fa-xmark"></i></div>
            <div class="bi__content">
                <div class="bi__title">{{ __('Покупка') }}: <span id="item-name"></span></div>

                <form action="{{ route('shop.item.buy') }}" method="POST" id="topup-balance-form">
                    @csrf
                    <input type="hidden" id="server_id" name="server_id" value="{{ $server->id }}">
                    <input type="hidden" id="item_id" name="item_id" value="">
                    <input type="hidden" id="var_id" name="var_id" value="">
                    <input type="hidden" id="payment_id" name="payment_id" value="5">
                    <input type="hidden" id="steam_id" name="steam_id" value="">

                    <div class="bi__items">

                        <div class="styles__MethodSelect-sc-ena6u4-2 jIjorv">
                            <div class="styles__Method-sc-ena6u4-3 payment-item gcQxa-d fdLgLR" data-payment_id="5">
                                <div
                                        class="styles__Icon-sc-h5vgtd-0 gpvUmb styles__MethodIcon-sc-ena6u4-4 kDTdJM"
                                        fill="inherit"
                                >
                                    <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 239 120"
                                    >
                                        <path
                                                d="M206.665 34.254v29.338h-10.476v-20.58h-10.087v20.58h-10.476v-29.34h31.039z"
                                                style="fill: #fff"
                                        ></path>
                                        <path
                                                d="M154.11 64.64c9.379 0 16.343-5.75 16.343-14.466 0-8.437-5.138-13.915-13.725-13.915-3.963 0-7.233 1.395-9.696 3.802.588-4.975 4.795-8.607 9.427-8.607 1.069 0 9.117-.017 9.117-.017l4.55-8.71s-10.103.23-14.8.23c-10.732.188-17.981 9.943-17.981 21.79 0 13.804 7.07 19.894 16.766 19.894zm.058-20.667c3.482 0 5.896 2.288 5.896 6.2 0 3.52-2.145 6.422-5.896 6.43-3.588 0-6.002-2.688-6.002-6.37 0-3.913 2.414-6.26 6.002-6.26z"
                                                style="clip-rule: evenodd; fill: #fff; fill-rule: evenodd"
                                        ></path>
                                        <path
                                                d="M128.818 53.77s-2.474 1.426-6.17 1.696c-4.247.126-8.032-2.557-8.032-7.324 0-4.65 3.34-7.315 7.926-7.315 2.812 0 6.532 1.949 6.532 1.949s2.722-4.995 4.132-7.493c-2.582-1.957-6.021-3.03-10.021-3.03-10.095 0-17.914 6.582-17.914 15.83 0 9.366 7.349 15.795 17.914 15.6 2.953-.11 7.027-1.146 9.51-2.741z"
                                                style="fill: #fff"
                                        ></path>
                                        <path
                                                fill="#5B57A2"
                                                d="m0 26.12 14.532 25.975v15.844L.017 93.863 0 26.12z"
                                        ></path>
                                        <path
                                                fill="#D90751"
                                                d="m55.797 42.643 13.617-8.346 27.868-.026-41.485 25.414V42.643z"
                                        ></path>
                                        <path
                                                fill="#FAB718"
                                                d="m55.72 25.967.077 34.39-14.566-8.95V0l14.49 25.967z"
                                        ></path>
                                        <path
                                                fill="#ED6F26"
                                                d="m97.282 34.271-27.869.026-13.693-8.33L41.231 0l56.05 34.271z"
                                        ></path>
                                        <path
                                                fill="#63B22F"
                                                d="M55.797 94.007V77.322l-14.566-8.78.008 51.458 14.558-25.993z"
                                        ></path>
                                        <path
                                                fill="#1487C9"
                                                d="M69.38 85.737 14.531 52.095 0 26.12l97.223 59.583-27.844.034z"
                                        ></path>
                                        <path
                                                fill="#017F36"
                                                d="m41.24 120 14.556-25.993 13.583-8.27 27.843-.034L41.24 120z"
                                        ></path>
                                        <path
                                                fill="#984995"
                                                d="m.017 93.863 41.333-25.32-13.896-8.526-12.922 7.922L.017 93.863z"
                                        ></path>
                                        <path
                                                d="M114.619 83.337c-.32.408-.741.716-1.246.924a4.282 4.282 0 0 1-1.632.316 4.63 4.63 0 0 1-1.633-.274 3.623 3.623 0 0 1-1.262-.782 3.539 3.539 0 0 1-.824-1.224 4.173 4.173 0 0 1-.295-1.59c0-.54.093-1.04.286-1.513a3.58 3.58 0 0 1 .79-1.224 3.88 3.88 0 0 1 1.196-.823c.463-.2.968-.308 1.523-.308.598 0 1.153.075 1.658.224.505.15.934.4 1.287.75l-.614 1.04a2.886 2.886 0 0 0-1.027-.6 3.612 3.612 0 0 0-1.12-.174c-.335 0-.655.066-.966.19a2.591 2.591 0 0 0-.825.534 2.438 2.438 0 0 0-.564.832 2.64 2.64 0 0 0-.21 1.08c0 .409.075.775.218 1.09.144.326.337.6.58.833.245.234.53.408.859.525a2.9 2.9 0 0 0 1.044.183c.454 0 .867-.092 1.22-.267a3.05 3.05 0 0 0 .926-.69zm1.733 1.065v-7.405h1.33v5.517l4.325-5.517h1.33v7.406h-1.33v-5.517l-4.325 5.517h-1.33zm15.795-1.065a3.01 3.01 0 0 1-1.245.924 4.291 4.291 0 0 1-1.633.316 4.628 4.628 0 0 1-1.632-.274 3.627 3.627 0 0 1-1.263-.782 3.539 3.539 0 0 1-.824-1.224 4.173 4.173 0 0 1-.295-1.59c0-.54.093-1.04.286-1.513.186-.475.455-.882.79-1.224a3.88 3.88 0 0 1 1.196-.823 3.637 3.637 0 0 1 1.523-.308c.598 0 1.153.075 1.658.224.505.15.934.4 1.288.75l-.615 1.04a2.874 2.874 0 0 0-1.026-.6 3.616 3.616 0 0 0-1.12-.174 2.603 2.603 0 0 0-1.792.724 2.536 2.536 0 0 0-.564.832 2.64 2.64 0 0 0-.21 1.08c0 .409.076.775.219 1.09.143.326.336.6.58.833.244.234.53.408.859.525.328.116.673.183 1.043.183.455 0 .859-.092 1.22-.267a3.05 3.05 0 0 0 .926-.69zm.682-6.34h6.824v1.315h-2.743v6.09h-1.33v-6.09h-2.743v-1.315z"
                                                style="fill: #fff"
                                        ></path>
                                        <path
                                                d="M145.864 84.328c.505-.158.934-.4 1.304-.732l-.404-1.032c-.244.217-.572.4-.967.55-.396.15-.842.224-1.33.224-.732 0-1.338-.191-1.818-.582-.48-.392-.74-.94-.8-1.64h5.757a4 4 0 0 0 .084-.89 3.35 3.35 0 0 0-.278-1.381 3.277 3.277 0 0 0-.74-1.074 3.295 3.295 0 0 0-1.111-.69 3.692 3.692 0 0 0-1.355-.25c-.623 0-1.17.108-1.65.308a3.637 3.637 0 0 0-1.203.824 3.477 3.477 0 0 0-.748 1.223 4.272 4.272 0 0 0-.261 1.514c0 .583.1 1.115.286 1.59a3.5 3.5 0 0 0 .808 1.223c.345.34.765.599 1.262.782a4.772 4.772 0 0 0 1.658.274 5.04 5.04 0 0 0 1.506-.24zm-3.366-5.7c.412-.374.959-.557 1.649-.557.648 0 1.17.175 1.549.525.379.349.58.832.614 1.447h-4.544a2.26 2.26 0 0 1 .732-1.414z"
                                                style="clip-rule: evenodd; fill: #fff; fill-rule: evenodd"
                                        ></path>
                                        <path
                                                d="M149.499 76.997h1.212l2.878 3.67 2.726-3.67h1.178v7.406h-1.33v-5.4l-2.591 3.436h-.051l-2.692-3.437v5.4h-1.33z"
                                                style="fill: #fff"
                                        ></path>
                                        <path
                                                d="M161.11 77.022a4.187 4.187 0 0 0-1.143.49l.353 1.05c.303-.15.606-.275.909-.383.303-.11.69-.16 1.153-.16.37 0 .665.06.892.176.227.108.395.275.513.475.118.2.194.449.236.74.042.29.059.607.059.949a2.28 2.28 0 0 0-.91-.375 5.229 5.229 0 0 0-.958-.1c-.396 0-.766.059-1.12.158-.353.1-.656.25-.9.45a2.14 2.14 0 0 0-.59.74 2.3 2.3 0 0 0-.218 1.007c0 .724.21 1.29.63 1.69.422.399.977.598 1.667.598.623 0 1.12-.108 1.498-.324.379-.217.682-.466.9-.75v.958h1.246v-4.543c0-.966-.21-1.714-.64-2.24-.42-.523-1.17-.79-2.23-.79-.471 0-.917.067-1.346.184zm2.224 6c-.303.216-.7.315-1.187.315-.43 0-.766-.108-1.001-.316-.236-.216-.354-.5-.354-.85 0-.206.042-.381.135-.54a1.23 1.23 0 0 1 .362-.382 1.6 1.6 0 0 1 .513-.225c.194-.05.387-.075.589-.075.657 0 1.22.158 1.683.49v.94a3.824 3.824 0 0 1-.74.642zm13.733-8.63c.269-.124.52-.332.757-.64l-.724-1.04c-.185.25-.395.424-.631.516a4.007 4.007 0 0 1-.774.216l-.252.045c-.22.038-.454.08-.707.122a5.224 5.224 0 0 0-1.178.366c-.598.258-1.077.607-1.44 1.048-.361.44-.647.94-.841 1.49a7.793 7.793 0 0 0-.395 1.722c-.068.6-.101 1.173-.101 1.722 0 .708.092 1.348.277 1.922.186.575.446 1.057.79 1.448.346.4.759.7 1.247.916.488.216 1.035.324 1.649.324.564 0 1.086-.108 1.557-.308a3.758 3.758 0 0 0 1.212-.823c.336-.342.606-.75.79-1.207a3.74 3.74 0 0 0 .287-1.465c0-.557-.084-1.064-.244-1.514a3.271 3.271 0 0 0-.69-1.156 3.072 3.072 0 0 0-1.103-.75 3.827 3.827 0 0 0-1.447-.266c-.32 0-.631.042-.934.125a3.604 3.604 0 0 0-.842.358 3.2 3.2 0 0 0-.698.54c-.202.209-.37.442-.488.708h-.034a8.07 8.07 0 0 1 .143-1.048c.076-.358.185-.7.329-1.024.143-.324.328-.616.555-.882a2.52 2.52 0 0 1 .875-.624 4.646 4.646 0 0 1 1.153-.374l.264-.048c.263-.048.514-.094.754-.144.32-.066.614-.15.884-.274zm-4.68 7.115a4.146 4.146 0 0 1-.184-1.298 2.09 2.09 0 0 1 .303-.707 2.48 2.48 0 0 1 .547-.6 2.773 2.773 0 0 1 1.733-.59c.783 0 1.372.24 1.784.716.413.482.615 1.08.615 1.805 0 .358-.068.683-.185.982a2.36 2.36 0 0 1-1.28 1.323 2.45 2.45 0 0 1-1.001.2c-.37 0-.707-.075-1.018-.225a2.34 2.34 0 0 1-.8-.624 3.129 3.129 0 0 1-.513-.982zm16.402-4.51h-1.33v7.406h1.33zm-8.281 0h1.33v2.222h1.229c.58 0 1.05.075 1.43.216.379.142.682.333.909.566.227.233.387.508.48.815.092.308.143.624.143.957 0 .333-.051.65-.16.966-.101.316-.278.59-.514.84-.235.25-.555.45-.96.6-.403.149-.891.232-1.48.232H180.5v-7.414zm1.33 3.46v2.714h.968c.665 0 1.136-.116 1.405-.35.27-.233.404-.565.404-1.006 0-.45-.143-.79-.412-1.024-.278-.233-.741-.35-1.38-.35h-.985z"
                                                style="clip-rule: evenodd; fill: #fff; fill-rule: evenodd"
                                        ></path>
                                        <path
                                                d="M197.583 83.337c-.32.408-.741.716-1.246.924a4.286 4.286 0 0 1-1.632.316 4.63 4.63 0 0 1-1.633-.274 3.613 3.613 0 0 1-1.262-.782 3.555 3.555 0 0 1-.825-1.224 4.192 4.192 0 0 1-.294-1.59c0-.54.092-1.04.286-1.513a3.58 3.58 0 0 1 .79-1.224c.338-.35.74-.615 1.196-.823a3.634 3.634 0 0 1 1.523-.308c.597 0 1.153.075 1.658.224.505.15.934.4 1.287.75l-.614 1.04a2.886 2.886 0 0 0-1.027-.6 3.612 3.612 0 0 0-1.12-.174c-.335 0-.655.066-.967.19a2.587 2.587 0 0 0-.824.534 2.52 2.52 0 0 0-.564.832 2.64 2.64 0 0 0-.21 1.08c0 .409.075.775.218 1.09.143.326.337.6.58.833.245.234.53.408.859.525.329.116.674.183 1.044.183a2.76 2.76 0 0 0 1.22-.267 3.05 3.05 0 0 0 .926-.69zm.68-6.34h6.826v1.315h-2.744v6.09h-1.33v-6.09h-2.742v-1.315z"
                                                style="fill: #fff"
                                        ></path>
                                        <path
                                                d="M207.866 76.998h-1.33v10.7h1.33v-3.636c.269.175.589.3.959.383.37.083.757.124 1.153.124.589 0 1.119-.108 1.599-.307a3.825 3.825 0 0 0 1.228-.84 3.65 3.65 0 0 0 .79-1.266c.186-.49.279-1.015.279-1.572 0-.55-.084-1.057-.244-1.515a3.524 3.524 0 0 0-.69-1.181 2.996 2.996 0 0 0-1.086-.774 3.49 3.49 0 0 0-1.44-.283c-.52 0-1.008.1-1.471.3-.463.2-.825.44-1.077.74zm.926 1.373c.379-.2.79-.3 1.237-.3.387 0 .732.058 1.044.183.303.125.555.3.766.533.21.233.37.5.47.815.11.316.16.658.16 1.032 0 .4-.067.757-.184 1.09a2.4 2.4 0 0 1-.514.849 2.36 2.36 0 0 1-.816.557 2.685 2.685 0 0 1-1.086.208c-.361 0-.698-.041-1.018-.124a3.328 3.328 0 0 1-.984-.45v-3.67c.235-.282.547-.523.925-.723zm15.28-1.374h-1.33v7.406h1.33zm-8.288 0h1.33v2.222h1.228c.58 0 1.052.075 1.43.216.38.142.683.333.91.566.227.233.387.508.479.815.093.308.143.624.143.957 0 .333-.05.65-.16.966-.1.316-.277.59-.513.84-.235.25-.555.45-.96.6-.403.149-.891.232-1.48.232h-2.415v-7.414zm1.339 3.46v2.714h.968c.664 0 1.136-.116 1.405-.35.269-.233.404-.565.404-1.006 0-.45-.143-.79-.412-1.024-.278-.233-.741-.35-1.38-.35h-.985z"
                                                style="clip-rule: evenodd; fill: #fff; fill-rule: evenodd"
                                        ></path>
                                        <path
                                                d="m230.452 80.567 2.844 3.836h-1.632l-2.197-2.996-2.23 2.996h-1.548l2.827-3.77-2.66-3.636h1.634l2.028 2.796 2.06-2.796h1.55zM113.635 93.556h-4.081v6.174h-1.33v-7.405h6.74v7.405h-1.33zm8.877.083h-2.625l-.085 1.248c-.092 1.057-.218 1.906-.395 2.554-.177.65-.396 1.149-.648 1.498-.252.35-.547.59-.884.708a3.06 3.06 0 0 1-1.085.183l-.101-1.282a1.08 1.08 0 0 0 .496-.108c.186-.083.37-.266.547-.54.177-.284.337-.683.48-1.2.143-.523.236-1.205.286-2.063l.135-2.305h5.209v7.406h-1.33v-6.1z"
                                                style="fill: #fff"
                                        ></path>
                                        <path
                                                d="M127.427 92.342c-.43.116-.817.282-1.145.49l.354 1.049c.303-.15.606-.275.908-.383.303-.108.69-.158 1.153-.158.37 0 .665.058.892.174.228.109.396.275.514.475.117.2.193.45.235.74.042.292.059.608.059.949a2.277 2.277 0 0 0-.91-.374 5.22 5.22 0 0 0-.958-.1c-.395 0-.766.058-1.12.158-.353.1-.656.25-.9.45a2.14 2.14 0 0 0-.59.74 2.3 2.3 0 0 0-.217 1.007c0 .723.21 1.29.63 1.689.42.399.977.599 1.667.599.623 0 1.119-.108 1.498-.325.378-.216.68-.466.9-.75v.958h1.246v-4.543c0-.965-.211-1.714-.64-2.238-.421-.525-1.17-.79-2.23-.79a5.1 5.1 0 0 0-1.346.182zm2.23 5.998c-.302.217-.698.317-1.186.317-.438 0-.766-.108-1.001-.317-.236-.216-.354-.5-.354-.848 0-.208.042-.383.135-.541.092-.158.219-.283.362-.383.143-.1.319-.175.513-.225a2.33 2.33 0 0 1 .589-.074c.656 0 1.22.158 1.683.49v.94a3.83 3.83 0 0 1-.741.64z"
                                                style="clip-rule: evenodd; fill: #fff; fill-rule: evenodd"
                                        ></path>
                                        <path
                                                d="M132.946 92.325h6.825v1.314h-2.744v6.09h-1.33v-6.09h-2.742v-1.314z"
                                                style="fill: #fff"
                                        ></path>
                                        <path
                                                d="M145.982 99.655c.505-.158.934-.4 1.304-.732l-.404-1.032c-.244.217-.572.4-.968.55-.395.15-.841.224-1.33.224-.731 0-1.337-.191-1.817-.582-.48-.391-.74-.94-.8-1.64h5.757c.058-.257.084-.55.084-.89 0-.507-.093-.965-.278-1.381a3.264 3.264 0 0 0-.741-1.073 3.28 3.28 0 0 0-1.11-.691 3.696 3.696 0 0 0-1.355-.25c-.623 0-1.17.108-1.65.308a3.618 3.618 0 0 0-1.203.824 3.494 3.494 0 0 0-.75 1.223 4.294 4.294 0 0 0-.26 1.514c0 .583.1 1.115.286 1.59.186.474.463.882.808 1.223a3.47 3.47 0 0 0 1.263.782 4.766 4.766 0 0 0 1.657.275c.497 0 1.002-.084 1.507-.242zm-3.374-5.708c.412-.374.959-.557 1.649-.557.657 0 1.17.183 1.549.524.378.35.58.832.614 1.448h-4.544a2.26 2.26 0 0 1 .732-1.415zm5.848-1.622h1.683l2.566 3.57-2.743 3.835h-1.632l2.877-3.836-2.751-3.57zm4.577 0h1.33v7.405h-1.33zm6.084 7.405-2.928-3.836 2.735-3.57h-1.65l-2.583 3.57 2.777 3.836zm6.076-.075c.505-.158.934-.4 1.304-.732l-.404-1.032c-.244.217-.572.4-.967.55-.396.15-.842.224-1.33.224-.732 0-1.338-.191-1.818-.582-.48-.391-.74-.94-.8-1.64h5.757c.059-.257.084-.55.084-.89 0-.507-.092-.965-.278-1.381a3.276 3.276 0 0 0-.74-1.073 3.284 3.284 0 0 0-1.111-.691 3.692 3.692 0 0 0-1.355-.25c-.622 0-1.17.108-1.65.308a3.624 3.624 0 0 0-1.202.824 3.465 3.465 0 0 0-.75 1.223 4.272 4.272 0 0 0-.26 1.514c0 .583.1 1.115.286 1.59.185.474.463.882.808 1.223.345.34.765.599 1.262.782a4.772 4.772 0 0 0 1.658.275 5.04 5.04 0 0 0 1.506-.242zm-3.374-5.708c.413-.374.96-.557 1.65-.557.648 0 1.169.183 1.548.524.379.35.58.832.614 1.448h-4.544a2.26 2.26 0 0 1 .732-1.415zm10.595-4.11c-.783 0-1.212-.433-1.28-1.298h-1.253c0 .332.059.632.168.915.11.283.27.524.48.724.21.2.48.358.79.482.312.125.674.183 1.086.183a2.64 2.64 0 0 0 1.019-.183c.303-.116.555-.282.774-.482a2.1 2.1 0 0 0 .496-.724c.118-.283.177-.583.177-.915h-1.245c-.06.865-.472 1.298-1.212 1.298zm-3.594 2.487v7.406h1.33l4.325-5.517v5.517h1.33v-7.406h-1.33l-4.325 5.517v-5.517z"
                                                style="clip-rule: evenodd; fill: #fff; fill-rule: evenodd"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="styles__Method-sc-ena6u4-3 payment-item gcQxa-d" data-payment_id="5">
                                <div
                                        class="styles__Icon-sc-h5vgtd-0 gpvUmb styles__MethodIcon-sc-ena6u4-4 kDTdJM"
                                        fill="inherit"
                                >
                                    <svg
                                            viewBox="0 0 97 64"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                                d="M53.4313 28.7061H42.8158V9.59956H53.4313V28.7061Z"
                                                fill="#FF5F00"
                                        ></path>
                                        <path
                                                d="M43.4958 19.1514C43.4958 15.2755 45.3078 11.823 48.1295 9.59807C46.066 7.97109 43.4618 7 40.6316 7C33.9313 7 28.5 12.4403 28.5 19.1514C28.5 25.8624 33.9313 31.3027 40.6316 31.3027C43.4618 31.3027 46.066 30.3316 48.1295 28.7046C45.3078 26.4797 43.4958 23.0272 43.4958 19.1514Z"
                                                fill="#EB001B"
                                        ></path>
                                        <path
                                                d="M67.7486 19.1514C67.7486 25.8624 62.3172 31.3027 55.6169 31.3027C52.7867 31.3027 50.1825 30.3316 48.1183 28.7046C50.9408 26.4797 52.7528 23.0272 52.7528 19.1514C52.7528 15.2755 50.9408 11.823 48.1183 9.59807C50.1825 7.97109 52.7867 7 55.6169 7C62.3172 7 67.7486 12.4403 67.7486 19.1514Z"
                                                fill="#F79E1B"
                                        ></path>
                                        <path
                                                d="M37.6287 19.2284C37.6287 18.6977 37.976 18.2616 38.5435 18.2616C39.0857 18.2616 39.4518 18.6789 39.4518 19.2284C39.4518 19.778 39.0857 20.1953 38.5435 20.1953C37.976 20.1953 37.6287 19.7592 37.6287 19.2284ZM40.0699 19.2284V17.7186H39.4143V18.0852C39.2063 17.8133 38.8908 17.6426 38.4619 17.6426C37.6164 17.6426 36.9536 18.3065 36.9536 19.2284C36.9536 20.1511 37.6164 20.8142 38.4619 20.8142C38.8908 20.8142 39.2063 20.6436 39.4143 20.3717V20.7383H40.0699V19.2284ZM62.2248 19.2284C62.2248 18.6977 62.5721 18.2616 63.1396 18.2616C63.6826 18.2616 64.0479 18.6789 64.0479 19.2284C64.0479 19.778 63.6826 20.1953 63.1396 20.1953C62.5721 20.1953 62.2248 19.7592 62.2248 19.2284ZM64.6667 19.2284V16.5059H64.0104V18.0852C63.8024 17.8133 63.4869 17.6426 63.058 17.6426C62.2125 17.6426 61.5497 18.3065 61.5497 19.2284C61.5497 20.1511 62.2125 20.8142 63.058 20.8142C63.4869 20.8142 63.8024 20.6436 64.0104 20.3717V20.7383H64.6667V19.2284ZM48.2079 18.2305C48.6303 18.2305 48.9018 18.4959 48.9711 18.9631H47.4065C47.4765 18.527 47.7408 18.2305 48.2079 18.2305ZM48.2209 17.6426C47.3371 17.6426 46.7191 18.2869 46.7191 19.2284C46.7191 20.1887 47.3624 20.8142 48.265 20.8142C48.7191 20.8142 49.135 20.7007 49.5011 20.3912L49.1798 19.9046C48.9271 20.1063 48.605 20.2206 48.3025 20.2206C47.8801 20.2206 47.4953 20.0246 47.4007 19.4808H49.6397C49.6462 19.3991 49.6527 19.3167 49.6527 19.2284C49.6462 18.2869 49.0657 17.6426 48.2209 17.6426ZM56.1373 19.2284C56.1373 18.6977 56.4846 18.2616 57.0521 18.2616C57.5944 18.2616 57.9604 18.6789 57.9604 19.2284C57.9604 19.778 57.5944 20.1953 57.0521 20.1953C56.4846 20.1953 56.1373 19.7592 56.1373 19.2284ZM58.5785 19.2284V17.7186H57.9229V18.0852C57.7142 17.8133 57.3994 17.6426 56.9705 17.6426C56.125 17.6426 55.4622 18.3065 55.4622 19.2284C55.4622 20.1511 56.125 20.8142 56.9705 20.8142C57.3994 20.8142 57.7142 20.6436 57.9229 20.3717V20.7383H58.5785V19.2284ZM52.4347 19.2284C52.4347 20.1446 53.0715 20.8142 54.0434 20.8142C54.4976 20.8142 54.8001 20.713 55.1279 20.4541L54.8131 19.9234C54.5669 20.1005 54.3084 20.1953 54.0239 20.1953C53.5004 20.1887 53.1156 19.8098 53.1156 19.2284C53.1156 18.6471 53.5004 18.2681 54.0239 18.2616C54.3084 18.2616 54.5669 18.3564 54.8131 18.5335L55.1279 18.0028C54.8001 17.7439 54.4976 17.6426 54.0434 17.6426C53.0715 17.6426 52.4347 18.3123 52.4347 19.2284ZM60.8876 17.6426C60.5092 17.6426 60.263 17.8198 60.0926 18.0852V17.7186H59.4428V20.7383H60.0991V19.0455C60.0991 18.5458 60.3135 18.2681 60.7424 18.2681C60.8753 18.2681 61.0139 18.2869 61.1525 18.3441L61.3547 17.7251C61.2096 17.668 61.0204 17.6426 60.8876 17.6426ZM43.3125 17.9587C42.997 17.7504 42.5623 17.6426 42.0829 17.6426C41.319 17.6426 40.8273 18.0093 40.8273 18.6095C40.8273 19.1019 41.1933 19.4056 41.8677 19.5003L42.1775 19.5444C42.5371 19.5951 42.7067 19.6898 42.7067 19.8604C42.7067 20.094 42.467 20.2271 42.0194 20.2271C41.5652 20.2271 41.2374 20.0817 41.0164 19.9111L40.7074 20.4223C41.067 20.6877 41.5212 20.8142 42.0129 20.8142C42.8836 20.8142 43.3883 20.4035 43.3883 19.8286C43.3883 19.2979 42.9912 19.0202 42.3349 18.9255L42.0259 18.8806C41.7421 18.843 41.5147 18.7866 41.5147 18.5841C41.5147 18.3629 41.7291 18.2305 42.0887 18.2305C42.4735 18.2305 42.8461 18.3759 43.0288 18.4894L43.3125 17.9587ZM51.7719 17.6426C51.3935 17.6426 51.1473 17.8198 50.9776 18.0852V17.7186H50.3278V20.7383H50.9834V19.0455C50.9834 18.5458 51.1979 18.2681 51.6267 18.2681C51.7596 18.2681 51.8982 18.2869 52.0369 18.3441L52.239 17.7251C52.0939 17.668 51.9047 17.6426 51.7719 17.6426ZM46.1768 17.7186H45.1046V16.8024H44.4418V17.7186H43.8302V18.3188H44.4418V19.6963C44.4418 20.397 44.7133 20.8142 45.4887 20.8142C45.7732 20.8142 46.101 20.726 46.309 20.5807L46.1198 20.0181C45.9241 20.1316 45.7097 20.1887 45.5393 20.1887C45.2115 20.1887 45.1046 19.9863 45.1046 19.6833V18.3188H46.1768V17.7186ZM36.3738 20.7383V18.843C36.3738 18.1293 35.9196 17.6492 35.1875 17.6426C34.8026 17.6361 34.4055 17.7562 34.1275 18.1799C33.9196 17.8451 33.5918 17.6426 33.1311 17.6426C32.8091 17.6426 32.4943 17.7374 32.2481 18.091V17.7186H31.5917V20.7383H32.2538V19.0643C32.2538 18.54 32.5441 18.2616 32.9925 18.2616C33.4279 18.2616 33.6481 18.5458 33.6481 19.0578V20.7383H34.3109V19.0643C34.3109 18.54 34.6134 18.2616 35.0488 18.2616C35.4965 18.2616 35.7109 18.5458 35.7109 19.0578V20.7383H36.3738Z"
                                                fill="white"
                                        ></path>
                                        <path
                                                d="M45.2428 56.2244H40.8043L43.5805 40.2887H48.0187L45.2428 56.2244Z"
                                                fill="white"
                                        ></path>
                                        <path
                                                d="M61.3322 40.6784C60.4567 40.356 59.0682 40 57.3513 40C52.9681 40 49.8815 42.1699 49.8626 45.2721C49.8262 47.5609 52.0725 48.8322 53.7526 49.5953C55.4697 50.3752 56.0535 50.8842 56.0535 51.5793C56.036 52.6469 54.6659 53.139 53.388 53.139C51.616 53.139 50.6665 52.8853 49.2236 52.2913L48.6391 52.0367L48.018 55.6139C49.059 56.0542 50.9769 56.4449 52.9681 56.462C57.6253 56.462 60.6572 54.3257 60.6932 51.0198C60.7109 49.2058 59.5247 47.8157 56.9675 46.6799C55.4151 45.9508 54.4644 45.4591 54.4644 44.7131C54.4826 44.0348 55.2685 43.3402 57.0209 43.3402C58.4638 43.3061 59.524 43.6281 60.3271 43.9503L60.7286 44.1196L61.3322 40.6784Z"
                                                fill="white"
                                        ></path>
                                        <path
                                                d="M67.2316 50.579C67.5971 49.6635 69.0036 46.1203 69.0036 46.1203C68.9851 46.1543 69.3684 45.1879 69.5876 44.5946L69.8977 45.9677C69.8977 45.9677 70.7383 49.7822 70.9208 50.579C70.2271 50.579 68.1082 50.579 67.2316 50.579ZM72.7103 40.2887H69.2771C68.2184 40.2887 67.4141 40.5767 66.9573 41.6109L60.3645 56.2242H65.0217C65.0217 56.2242 65.7884 54.2573 65.9532 53.8337C66.4641 53.8337 70.9946 53.8337 71.6518 53.8337C71.7792 54.3932 72.1814 56.2242 72.1814 56.2242H76.291L72.7103 40.2887Z"
                                                fill="white"
                                        ></path>
                                        <path
                                                d="M37.0972 40.2887L32.7505 51.1553L32.2755 48.9515C31.4718 46.4085 28.9515 43.6456 26.139 42.2718L30.1204 56.2075H34.814L41.7905 40.2887H37.0972Z"
                                                fill="white"
                                        ></path>
                                        <path
                                                d="M28.7141 40.2887H21.5731L21.5 40.6107C27.0705 41.9331 30.7597 45.1208 32.2754 48.9521L30.7231 41.6283C30.4675 40.6105 29.6821 40.3223 28.7141 40.2887Z"
                                                fill="#FAA61A"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="styles__Method-sc-ena6u4-3 payment-item gcQxa-d" data-payment_id="3">
                                <div
                                        class="styles__Icon-sc-h5vgtd-0 gpvUmb styles__MethodIcon-sc-ena6u4-4 kDTdJM"
                                        fill="inherit"
                                >
                                    <svg
                                            viewBox="0 0 93 20"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <g clip-path="url(#payment_io_money)">
                                            <path
                                                    d="M90.1173 5.72948L87.9638 10.9047C87.902 10.965 87.902 11.0857 87.902 11.146L87.8402 11.2666L87.7784 11.146C87.7784 11.0857 87.7166 10.965 87.7166 10.9047L85.56 5.7325H82.7885L86.422 14.572L84.8834 17.9408H87.6548L92.7683 5.7325H90.1173V5.72948ZM69.6666 6.21202C69.1722 5.85012 68.5605 5.60885 67.8189 5.54853C66.5861 5.42789 65.4182 5.85012 64.6149 6.63424V5.66916H62.0906V14.5087H64.6149V9.87932C64.6149 8.85694 64.8003 8.55837 64.9857 8.3171C65.2946 7.83456 65.8508 7.59329 66.5274 7.59329C67.2041 7.59329 67.8189 7.89488 68.1279 8.37742C68.3133 8.73932 68.3751 9.21885 68.3751 9.46012V14.5087H70.8994V9.15853C70.8994 7.77424 70.5286 6.81218 69.6666 6.21202ZM57.3479 5.7898C56.0986 5.36805 54.7307 5.43287 53.5291 5.97075C52.6984 6.33177 52.0077 6.94256 51.5578 7.71392C51.1253 8.37742 50.9399 9.21885 50.9399 10.1206C50.9399 11.0223 51.1253 11.8638 51.5578 12.5242C51.9904 13.1877 52.5435 13.7879 53.2819 14.1498C54.0266 14.524 54.8483 14.7291 55.6857 14.75C56.1182 14.75 56.5477 14.6896 56.9772 14.569C57.3479 14.4484 57.7187 14.3277 58.0895 14.1468C58.8279 13.7276 59.4428 13.1877 59.8135 12.5242C60.2461 11.8638 60.4284 11.0223 60.4284 10.1206C60.4284 8.13615 59.2574 6.45329 57.3479 5.7898ZM58.0277 10.2412C57.9659 11.0223 57.7187 11.6828 57.2244 12.105C56.8536 12.4066 56.3005 12.5876 55.6239 12.6479C54.9442 12.6479 54.4529 12.4669 54.0203 12.105C53.5291 11.6828 53.2819 11.0193 53.2201 10.2412C53.1583 9.33646 53.5291 8.61567 54.2088 8.13615C54.5765 7.89488 55.0677 7.71392 55.5621 7.71392C56.1182 7.71392 56.5477 7.89488 56.9772 8.13615C57.7187 8.55837 58.0895 9.33948 58.0277 10.2412ZM47.9831 6.21202C47.4918 5.85012 46.8769 5.60885 46.1385 5.54853H45.7059C44.7203 5.54853 43.8583 5.97075 43.1199 6.81519L42.9963 6.99615H42.7491C42.5637 6.69456 42.3165 6.45329 42.0694 6.27234C41.5781 5.91043 40.9633 5.66916 40.2248 5.60885C39.0507 5.48821 38.13 5.85012 37.3298 6.69456V5.7898H34.8024V14.6293H37.3298V9.87932C37.3298 8.91726 37.4534 8.67599 37.6387 8.37742C37.9446 7.89488 38.4977 7.59329 39.1156 7.65361C39.7336 7.65361 40.2866 7.95519 40.5925 8.43773C40.8397 8.79964 40.8397 9.21885 40.8397 9.63805V14.569H43.364V9.819C43.364 8.91726 43.4875 8.61567 43.6729 8.3171C43.9819 7.83456 44.538 7.59329 45.1529 7.59329C45.7677 7.59329 46.259 7.89488 46.568 8.37742C46.7534 8.679 46.8152 9.03789 46.8152 9.27916V14.5087H49.3394V9.09821C49.3394 8.61567 49.2776 8.13615 49.1541 7.71392C48.9069 7.11377 48.5392 6.57392 47.9861 6.21202H47.9831ZM80.3848 11.8638C79.7428 12.4931 78.8911 12.8772 77.984 12.9465C76.9953 13.0068 76.26 12.7052 75.7039 12.1623C75.4567 11.9241 75.2713 11.5622 75.1477 11.2033H82.1119V11.143C82.1737 10.7811 82.2355 10.4795 82.2355 10.1176C82.2355 9.819 82.1737 9.3998 82.0501 8.85694C81.7411 7.83456 81.1232 7.05345 80.323 6.45329C79.3991 5.7898 78.5371 5.60885 78.2899 5.60885C76.5041 5.30726 74.4711 6.09139 73.4267 7.71392C72.6852 8.85996 72.747 9.99996 72.747 10.3619C72.747 10.7841 72.8088 12.105 73.918 13.3084C75.2126 14.6896 76.9984 14.75 77.5515 14.8103C80.0171 14.8706 81.6793 13.3657 82.0501 13.0671L80.3848 11.8638ZM76.0746 8.13615C76.4454 7.89488 76.8749 7.71392 77.4279 7.71392C77.9841 7.71392 78.4166 7.89488 78.8461 8.13615C79.2168 8.43773 79.5258 8.79964 79.7081 9.21885H75.2744C75.398 8.79662 75.6451 8.3744 76.0746 8.13615ZM17.6793 0.499956C16.4005 0.497572 15.1338 0.741669 13.9519 1.21824C12.77 1.69482 11.6961 2.39449 10.7919 3.27712C9.88763 4.15976 9.17082 5.20798 8.68258 6.36164C8.19434 7.51531 7.94427 8.75173 7.94671 9.99996C7.94427 11.2482 8.19434 12.4846 8.68258 13.6383C9.17082 14.7919 9.88763 15.8402 10.7919 16.7228C11.6961 17.6054 12.77 18.3051 13.9519 18.7817C15.1338 19.2582 16.4005 19.5023 17.6793 19.5C20.2587 19.4944 22.7309 18.4917 24.5549 16.7113C26.3789 14.9309 27.4061 12.5178 27.4118 9.99996C27.4061 7.48211 26.3789 5.06899 24.5549 3.2886C22.7309 1.50821 20.2587 0.505533 17.6793 0.499956ZM17.6793 13.4863C16.7196 13.4738 15.8028 13.0961 15.1242 12.4337C14.4455 11.7713 14.0586 10.8764 14.0458 9.93964C14.0458 8.01551 15.708 6.39297 17.6793 6.39297C19.6505 6.39297 21.3128 8.01551 21.3128 9.93964C21.251 11.9241 19.6505 13.4863 17.6793 13.4863ZM7.88492 3.20519V17.036H4.43372L0 3.20519H7.88492Z"
                                                    fill="white"
                                            ></path>
                                        </g>
                                        <defs>
                                            <clippath id="payment_io_money">
                                                <rect
                                                        width="93"
                                                        height="19"
                                                        fill="white"
                                                        transform="translate(0 0.5)"
                                                ></rect>
                                            </clippath>
                                        </defs>
                                    </svg>
                                </div>
                            </div>
                            <div class="styles__Method-sc-ena6u4-3 payment-item gcQxa-d" data-payment_id="5">
                                <div
                                        class="styles__Icon-sc-h5vgtd-0 gpvUmb styles__MethodIcon-sc-ena6u4-4 kDTdJM"
                                        fill="inherit"
                                >
                                    <svg
                                            viewBox="0 0 69 32"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                                d="M33.7406 19.1587H35.4806C36.3606 19.1587 37.0006 19.6305 37.0006 20.451C37.0006 20.9228 36.7806 21.3125 36.4006 21.5176V21.5381C36.9406 21.7023 37.2006 22.2151 37.2006 22.7279C37.2006 23.733 36.4206 24.1843 35.5206 24.1843H33.7406V19.1587ZM35.5006 21.2099C35.8806 21.2099 36.1006 20.9228 36.1006 20.574C36.1006 20.2253 35.8806 19.9587 35.4806 19.9587H34.6406V21.2305H35.5006V21.2099ZM35.5806 23.4048C36.0406 23.4048 36.3006 23.0971 36.3006 22.6664C36.3006 22.2561 36.0406 21.9484 35.5806 21.9484H34.6206V23.4048H35.5806ZM39.6406 20.492C40.1206 20.492 40.6606 20.6561 41.0006 21.0253L40.6006 21.6407C40.3806 21.4151 40.0406 21.251 39.6806 21.251C39.0406 21.251 38.6206 21.7433 38.6206 22.3587C38.6206 22.974 39.0406 23.4869 39.7206 23.4869C40.1006 23.4869 40.5006 23.2817 40.7606 23.0356L41.1006 23.6715C40.7606 24.0407 40.2206 24.2664 39.6606 24.2664C38.5006 24.2664 37.7406 23.4253 37.7406 22.3792C37.7406 21.3535 38.4806 20.492 39.6406 20.492ZM43.2006 20.492C44.2006 20.492 44.7606 21.251 44.7606 22.1946C44.7606 22.3176 44.7406 22.4202 44.7406 22.5433H42.3206C42.3806 23.1792 42.8406 23.5074 43.3806 23.5074C43.7406 23.5074 44.1006 23.3433 44.3806 23.1176L44.7206 23.7535C44.3606 24.0817 43.8006 24.2664 43.3006 24.2664C42.1406 24.2664 41.4006 23.4048 41.4006 22.3792C41.4206 21.2715 42.1606 20.492 43.2006 20.492ZM42.2006 19.1587H42.8806V19.9792H42.2006V19.1587ZM43.8806 21.9279C43.8806 21.4561 43.5606 21.1689 43.1806 21.1689C42.7406 21.1689 42.4206 21.4766 42.3406 21.9279H43.8806ZM43.4606 19.1587H44.1206V19.9792H43.4606V19.1587ZM47.2206 20.574H50.4206V24.1843H49.5406V21.333H48.0806V24.1843H47.2006V20.574H47.2206ZM51.3606 20.574H52.1606V20.8407C52.1606 20.9843 52.1406 21.0869 52.1406 21.0869H52.1606C52.3806 20.6971 52.8206 20.4715 53.3006 20.4715C54.2606 20.4715 54.8606 21.251 54.8606 22.3587C54.8606 23.5074 54.1806 24.2458 53.2606 24.2458C52.8606 24.2458 52.5006 24.0612 52.2406 23.733H52.2206C52.2206 23.733 52.2406 23.8561 52.2406 24.0407V25.5587H51.3606V20.574ZM53.0806 23.5074C53.5606 23.5074 53.9606 23.0971 53.9606 22.3997C53.9606 21.7228 53.6006 21.2715 53.1006 21.2715C52.6406 21.2715 52.2206 21.6202 52.2206 22.4202C52.2006 22.9535 52.5206 23.5074 53.0806 23.5074ZM57.3006 20.492C58.3806 20.492 59.2406 21.2715 59.2406 22.3792C59.2406 23.4869 58.3806 24.2664 57.3006 24.2664C56.2206 24.2664 55.3606 23.4869 55.3606 22.3792C55.3606 21.2715 56.2406 20.492 57.3006 20.492ZM57.3006 23.5074C57.8806 23.5074 58.3406 23.0561 58.3406 22.3792C58.3406 21.7228 57.8606 21.251 57.3006 21.251C56.7406 21.251 56.2606 21.7023 56.2606 22.3792C56.2606 23.0561 56.7206 23.5074 57.3006 23.5074ZM59.9406 20.574H60.7806V23.4253H61.8806V20.574H62.7206V23.4253H63.8206V20.574H64.6606V23.4253H65.1606V25.0458H64.3806V24.2048H59.9806V20.574H59.9406ZM67.3006 20.492C68.3006 20.492 68.8606 21.251 68.8606 22.1946C68.8606 22.3176 68.8406 22.4202 68.8406 22.5433H66.4206C66.4806 23.1792 66.9406 23.5074 67.4806 23.5074C67.8406 23.5074 68.2006 23.3433 68.4806 23.1176L68.8206 23.7535C68.4606 24.0817 67.9006 24.2664 67.4006 24.2664C66.2406 24.2664 65.5006 23.4048 65.5006 22.3792C65.5006 21.2715 66.2406 20.492 67.3006 20.492ZM67.9806 21.9279C67.9806 21.4561 67.6606 21.1689 67.2806 21.1689C66.8406 21.1689 66.5206 21.4766 66.4406 21.9279H67.9806Z"
                                                fill="white"
                                        ></path>
                                        <path
                                                d="M48.8003 4.75898V15.118C48.8003 15.2821 48.6803 15.4051 48.5203 15.4051H46.4603C46.3003 15.4051 46.1803 15.2821 46.1803 15.118V4.75898C46.1803 4.59488 46.3003 4.4718 46.4603 4.4718H48.5203C48.6803 4.4718 48.8003 4.61539 48.8003 4.75898ZM64.8803 4.4718H62.5803C62.4603 4.4718 62.3603 4.55385 62.3203 4.67693L60.5003 10.8103L58.5003 4.67693C58.4603 4.55385 58.3603 4.49231 58.2403 4.49231H56.6003C56.4803 4.49231 56.3803 4.57436 56.3403 4.67693L54.3403 10.8103L52.5203 4.67693C52.4803 4.55385 52.3803 4.4718 52.2603 4.4718H49.9603C49.8803 4.4718 49.7803 4.51283 49.7403 4.59488C49.6803 4.67693 49.6803 4.75898 49.7003 4.84103L53.1003 15.2205C53.1403 15.3436 53.2403 15.4051 53.3603 15.4051H55.2003C55.3203 15.4051 55.4203 15.3231 55.4603 15.2205L57.4403 9.16924L59.4203 15.2205C59.4603 15.3436 59.5603 15.4051 59.6803 15.4051H61.5203C61.6403 15.4051 61.7403 15.3231 61.7803 15.2205L65.1803 4.84103C65.2003 4.75898 65.2003 4.65641 65.1403 4.59488C65.0603 4.53334 64.9803 4.4718 64.8803 4.4718ZM68.3603 4.4718H66.3003C66.1403 4.4718 66.0203 4.59488 66.0203 4.75898V15.118C66.0203 15.2821 66.1403 15.4051 66.3003 15.4051H68.3603C68.5203 15.4051 68.6403 15.2821 68.6403 15.118V4.75898C68.6403 4.61539 68.5203 4.4718 68.3603 4.4718ZM45.0003 15.0564C45.1203 15.2 45.0203 15.4051 44.8403 15.4051H42.3203C42.2203 15.4051 42.1203 15.3641 42.0603 15.2821L41.6403 14.7487C40.7603 15.3231 39.7203 15.6718 38.6203 15.6718C35.5403 15.6718 33.0403 13.1077 33.0403 9.94872C33.0403 6.78975 35.5403 4.22565 38.6203 4.22565C41.7003 4.22565 44.2003 6.78975 44.2003 9.94872C44.2003 11.0769 43.8803 12.1436 43.3203 13.0462L45.0003 15.0564ZM39.9803 12.7385L38.8803 11.3641C38.7603 11.2205 38.8603 10.9949 39.0403 10.9949H41.3603C41.4803 10.6667 41.5403 10.2974 41.5403 9.92821C41.5403 8.22565 40.3003 6.74872 38.6003 6.74872C36.9003 6.74872 35.6603 8.22565 35.6603 9.92821C35.6603 11.6308 36.9003 13.0872 38.6003 13.0872C39.1003 13.1077 39.5603 12.9846 39.9803 12.7385ZM24.7003 21.4564C24.7803 22.0718 24.6003 22.3385 24.4003 22.3385C24.2003 22.3385 23.9203 22.0923 23.6203 21.6C23.3203 21.1077 23.2003 20.5539 23.3603 20.2667C23.4603 20.0821 23.6603 20 23.9403 20.1026C24.4403 20.2667 24.6403 21.0872 24.7003 21.4564ZM21.8803 22.7898C22.5003 23.3231 22.6803 23.959 22.3603 24.3897C22.1803 24.6359 21.8803 24.759 21.5403 24.759C21.2003 24.759 20.8403 24.6359 20.6003 24.4103C20.0403 23.918 19.8803 23.0974 20.2403 22.6462C20.3803 22.4615 20.6203 22.359 20.8803 22.359C21.2203 22.3795 21.5603 22.5436 21.8803 22.7898ZM20.8403 27.918C23.4803 27.918 26.3403 28.841 29.5003 31.7333C29.8203 32.0205 30.2403 31.6718 29.9603 31.3026C26.8403 27.2615 23.9603 26.5026 21.1003 25.8462C17.5803 25.0462 15.7803 23.0154 14.5203 20.8C14.2603 20.3487 14.1603 20.4308 14.1403 21.0051C14.1203 21.7026 14.1603 22.6256 14.3203 23.5282H13.9003C8.8803 23.5282 4.8003 19.3436 4.8003 14.1949C4.8003 9.04616 8.8803 4.86155 13.9003 4.86155C18.9203 4.86155 23.0003 9.04616 23.0003 14.1949C23.0003 14.5641 22.9803 14.9333 22.9403 15.2821C22.2803 15.159 20.9603 15.1385 20.0403 15.2205C19.7003 15.241 19.7403 15.4256 20.0003 15.4667C23.0003 16.0205 25.0803 17.9282 25.5403 21.4154C25.5603 21.4974 25.6603 21.518 25.7003 21.4564C26.9403 19.3436 27.6403 16.8615 27.6403 14.2154C27.6403 6.42052 21.4803 0.10257 13.8803 0.10257C6.2803 0.10257 0.1203 6.42052 0.1203 14.2154C0.1203 22.0103 6.2803 28.3282 13.8803 28.3282C15.9003 28.3282 17.9003 27.918 20.8403 27.918Z"
                                                fill="#EB9327"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="styles__Method-sc-ena6u4-3 payment-item gcQxa-d" data-payment_id="3">
                                <div
                                        class="styles__Icon-sc-h5vgtd-0 gpvUmb styles__MethodIcon-sc-ena6u4-4 kDTdJM"
                                        fill="inherit"
                                >
                                    <svg
                                            viewBox="0 0 103 18"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <g clip-path="url(#payment_sber)">
                                            <path
                                                    d="M46.05 5.36L48.2 3.78H41.01V13.27H48.2V11.68H43.07V9.26H47.45V7.68H43.07V5.36H46.05ZM35.58 7.5H33.09V5.36H37.09L39.23 3.78H31V13.27H35.28C37.67 13.27 39.06 12.18 39.06 10.27C39.06 8.36 37.85 7.5 35.58 7.5ZM35.24 11.71H33.09V9.06H35.24C36.55 9.06 37.15 9.49 37.15 10.38C37.15 11.27 36.5 11.68 35.24 11.68V11.71ZM54 3.78H50.1V13.27H52.15V10.58H54C56.49 10.58 58 9.28 58 7.18C58 5.08 56.53 3.78 54 3.78ZM54 9H52.15V5.36H54C55.33 5.36 56 6.01 56 7.18C56 8.35 55.3 9 54 9ZM27.52 11.22C26.9761 11.5136 26.3681 11.6682 25.75 11.67C25.3279 11.6882 24.9066 11.6189 24.5127 11.4663C24.1187 11.3137 23.7607 11.0812 23.461 10.7834C23.1613 10.4856 22.9266 10.129 22.7715 9.736C22.6164 9.34301 22.5444 8.92218 22.56 8.5C22.5486 8.08029 22.6238 7.66274 22.7807 7.27332C22.9377 6.8839 23.1731 6.53095 23.4723 6.23642C23.7715 5.94189 24.1281 5.71208 24.52 5.56131C24.9118 5.41053 25.3305 5.34201 25.75 5.36C26.4195 5.35747 27.0732 5.5637 27.62 5.95L29.1 4.9L29 4.8C28.0771 4.0201 26.8977 3.61033 25.69 3.65C24.3493 3.62141 23.0479 4.10407 22.05 5C21.5788 5.44994 21.2079 5.99426 20.9615 6.59738C20.715 7.2005 20.5987 7.84883 20.62 8.5C20.6095 9.15421 20.7307 9.80384 20.9764 10.4103C21.2221 11.0167 21.5872 11.5675 22.05 12.03C23.0306 12.9454 24.3287 13.4439 25.67 13.42C26.3432 13.4436 27.014 13.3279 27.6404 13.0801C28.2668 12.8323 28.8352 12.4578 29.31 11.98L27.99 10.98L27.52 11.22ZM82.77 3.79V13.28H84.83V9.4H89.2V13.28H91.2V3.79H89.2V7.69H84.83V3.79H82.77ZM79.24 13.28H81.4L77.4 3.79H75.31L71.21 13.28H73.28L74.11 11.37H78.46L79.24 13.28ZM74.73 9.79L76.32 5.98L77.81 9.79H74.73ZM95.86 9.5H97.13L100.28 13.32H102.92L98.72 8.41L102.39 3.83H100L97 7.85H95.86V3.78H93.8V13.27H95.86V9.5ZM64.43 7.5V5.38H69.8V3.79H62.38V13.28H66.65C69.05 13.28 70.44 12.2 70.44 10.28C70.44 8.36 69.19 7.5 66.92 7.5H64.43ZM64.43 11.71V9.07H66.58C67.88 9.07 68.49 9.51 68.49 10.4C68.49 11.29 67.85 11.72 66.58 11.72H64.43V11.71ZM14.19 3.72C14.5753 4.20482 14.9103 4.7275 15.19 5.28L8 10.58L5 8.69V6.42L8 8.3L14.19 3.72ZM1.87 8.5V8.19L0.0600021 8.13V8.52C0.0588895 9.55486 0.262407 10.5797 0.658847 11.5356C1.05529 12.4916 1.63682 13.3597 2.37 14.09L3.67 12.8C3.09458 12.2424 2.63835 11.5738 2.32895 10.8346C2.01955 10.0955 1.86341 9.30124 1.87 8.5ZM7.94 2.5C8.04311 2.49038 8.14689 2.49038 8.25 2.5L8.34 0.669996H8C6.95448 0.661068 5.91758 0.859772 4.94943 1.25459C3.98127 1.6494 3.10112 2.23247 2.36 2.97L3.66 4.26C4.22137 3.6979 4.88878 3.25288 5.62349 2.95075C6.35821 2.64863 7.14561 2.49541 7.94 2.5ZM7.94 14.59C7.83357 14.5999 7.72644 14.5999 7.62 14.59L7.53 16.41H7.92C10.0172 16.4083 12.0279 15.5738 13.51 14.09L12.21 12.8C11.6557 13.3709 10.9917 13.8237 10.2579 14.1314C9.52411 14.439 8.73566 14.595 7.94 14.59Z"
                                                    fill="#229F37"
                                            ></path>
                                            <path
                                                    d="M11.36 3.5L12.9 2.4C11.4853 1.25574 9.71947 0.634147 7.89999 0.640002V2.5C9.12785 2.47625 10.3341 2.82488 11.36 3.5ZM15.84 8.5C15.8373 8.02714 15.7938 7.55539 15.71 7.09L14 8.38V8.5C14.0025 9.34719 13.8256 10.1853 13.4809 10.9592C13.1362 11.7331 12.6315 12.4252 12 12.99L13.23 14.34C14.0511 13.6047 14.708 12.7046 15.1577 11.6983C15.6074 10.692 15.8399 9.60221 15.84 8.5ZM7.93999 14.59C7.09161 14.5908 6.25255 14.4131 5.47729 14.0686C4.70203 13.724 4.00791 13.2202 3.43999 12.59L2.07999 13.81C2.82041 14.6293 3.72458 15.2841 4.73401 15.732C5.74345 16.1799 6.83566 16.4109 7.93999 16.41V14.59ZM3.86999 4.04L2.64999 2.69C1.83297 3.4216 1.1792 4.31707 0.731234 5.31811C0.283266 6.31914 0.0511464 7.4033 0.0499878 8.5H1.86999C1.8754 7.65847 2.05604 6.82729 2.40038 6.05941C2.74471 5.29154 3.24521 4.6038 3.86999 4.04Z"
                                                    fill="#229F37"
                                            ></path>
                                        </g>
                                        <defs>
                                            <clippath id="payment_sber">
                                                <rect
                                                        width="103"
                                                        height="17"
                                                        fill="white"
                                                        transform="translate(0 0.5)"
                                                ></rect>
                                            </clippath>
                                        </defs>
                                    </svg>
                                </div>
                            </div>
                            <div class="styles__Method-sc-ena6u4-3 payment-item gcQxa-d" data-payment_id="3">
                                <div
                                        class="styles__Icon-sc-h5vgtd-0 gpvUmb styles__MethodIcon-sc-ena6u4-4 kDTdJM"
                                        fill="inherit"
                                >
                                    <svg
                                            viewBox="0 0 107 24"
                                            fill="none"
                                            xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <g clip-path="url(#payment_alfa)">
                                            <path
                                                    d="M103.279 5.19406L99.6311 9.34083V5.19406H96.9925V15.6624H99.6311V11.2568L103.536 15.6624H106.945L102.142 10.1771L106.597 5.19406H103.279ZM92.3379 9.0994H88.6903V5.19406H86.0518V15.6624H88.6903V11.5349H92.3379V15.6624H94.9764V5.19406H92.3379V9.0994ZM15.7261 10.8647C15.6633 12.8174 15.4139 13.2095 14.3326 13.2095V15.6624H14.7324C17.43 15.6624 18.0791 14.2505 18.1781 11.1081L18.2885 7.63152H20.8243V15.6643H23.4648V5.196H15.9089L15.7261 10.8647ZM29.8194 13.3582H28.1061V10.6986H29.8194C30.7351 10.6986 31.2853 11.1081 31.2853 11.9811C31.2853 12.9294 30.7541 13.3582 29.8194 13.3582ZM30.0212 8.37318H28.1175V5.19406H25.4751V15.6624H30.0574C32.9339 15.6624 33.96 13.7657 33.96 11.9811C33.96 9.67496 32.5322 8.37318 30.0212 8.37318ZM42.5573 13.3756V7.46348C44.0231 7.66822 44.9407 8.78264 44.9407 10.4186C44.9407 12.0545 44.0231 13.1708 42.5573 13.3756ZM39.9187 13.3756C38.4509 13.1708 37.5352 12.0564 37.5352 10.4186C37.5352 8.78071 38.4509 7.66822 39.9187 7.46348V13.3756ZM42.5573 4.95263V1.06661H39.9187V4.95263C36.8937 5.20565 34.8586 7.38816 34.8586 10.4205C34.8586 13.4528 36.8937 15.6469 39.9187 15.9077V19.8497H42.5573V15.9057C45.5823 15.6624 47.6155 13.4683 47.6155 10.4186C47.6155 7.36884 45.5823 5.19406 42.5573 4.95263ZM54.8744 11.759C54.8744 12.9661 54.031 13.6556 52.9307 13.6556C51.9788 13.6556 51.2268 13.3022 51.2268 12.2611C51.2268 11.2201 52.0512 11.0694 52.767 11.0694H54.8744V11.759ZM57.513 12.4466V8.93137C57.513 6.36644 55.9005 4.7479 53.1877 4.7479C50.3835 4.7479 48.8986 6.45915 48.771 8.24378H51.4458C51.5391 7.8575 51.9407 7.18343 53.1877 7.18343C54.2138 7.18343 54.8744 7.66822 54.8744 8.93137H52.253C49.9076 8.93137 48.514 10.1771 48.514 12.2611C48.514 14.4359 50.037 15.9308 52.2339 15.9308C53.8464 15.9308 54.7392 15.1583 55.1314 14.5788C55.4798 15.2857 56.2489 15.6759 57.256 15.6759H58.1735V13.2462C57.6976 13.2462 57.513 13.0221 57.513 12.4466ZM68.6936 13.0221H65.8722V9.2288H68.6936C70.1594 9.2288 70.978 9.91832 70.978 11.1255C70.978 12.3712 70.1518 13.0221 68.6936 13.0221ZM68.823 6.68126H65.8722V3.72425H72.7085V1.06661H63.1042V15.6624H68.8154C71.9851 15.6624 73.7994 14.0632 73.7994 11.1255C73.807 8.3558 71.9927 6.68126 68.823 6.68126ZM81.2677 11.759C81.2677 12.9661 80.4243 13.6556 79.324 13.6556C78.3721 13.6556 77.6106 13.3022 77.6106 12.2611C77.6106 11.2201 78.4349 11.0694 79.1507 11.0694H81.2677V11.759ZM83.9062 12.4466V8.93137C83.9062 6.36644 82.2938 4.7479 79.581 4.7479C76.7768 4.7479 75.2919 6.45915 75.1643 8.24378H77.841C77.9324 7.8575 78.334 7.18343 79.581 7.18343C80.6071 7.18343 81.2677 7.66822 81.2677 8.93137H78.6462C76.3008 8.93137 74.9073 10.1771 74.9073 12.2611C74.9073 14.4359 76.4303 15.9308 78.6291 15.9308C80.2397 15.9308 81.1344 15.1583 81.5247 14.5788C81.8731 15.2857 82.6422 15.6759 83.6511 15.6759H84.5668V13.2462C84.0909 13.2462 83.9062 13.0221 83.9062 12.4466Z"
                                                    fill="#D64A2E"
                                            ></path>
                                            <path
                                                    d="M0.0546265 20.126H14.167V23.1004H0.0546265V20.126Z"
                                                    fill="#D64A2E"
                                            ></path>
                                            <path
                                                    d="M5.09601 10.1405L7.07399 4.17046H7.14823L9.0215 10.1405H5.09601ZM9.67638 3.06955C9.2747 1.85275 8.81019 0.8909 7.22057 0.8909C5.63096 0.8909 5.1379 1.85661 4.71527 3.06955L0.348117 15.6624H3.24369L4.25266 12.6687H9.82487L10.7577 15.6624H13.8379L9.67638 3.06955Z"
                                                    fill="#D64A2E"
                                            ></path>
                                        </g>
                                        <defs>
                                            <clippath id="payment_alfa">
                                                <rect
                                                        width="107"
                                                        height="23"
                                                        fill="white"
                                                        transform="translate(0 0.5)"
                                                ></rect>
                                            </clippath>
                                        </defs>
                                    </svg>
                                </div>
                            </div>
                            <div class="styles__Method-sc-ena6u4-3 payment-item gcQxa-d" data-payment_id="5">
                                <div
                                        class="styles__Icon-sc-h5vgtd-0 gpvUmb styles__MethodIcon-sc-ena6u4-4 kDTdJM"
                                        fill="inherit"
                                >
                                    <img src="/images/payment-logos/steam-logo3.png" alt="steam logo" style="width: 75%;">
                                </div>
                            </div>
                            <div class="styles__Method-sc-ena6u4-3 payment-item gcQxa-d" data-payment_id="5">
                                <div
                                        class="styles__Icon-sc-h5vgtd-0 gpvUmb styles__MethodIcon-sc-ena6u4-4 kDTdJM"
                                        fill="inherit"
                                >
                                    <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 306.5 64.001"
                                    >
                                        <path
                                                fill="#f7931a"
                                                d="M63.033 39.745C58.759 56.888 41.396 67.321 24.25 63.046 7.113 58.772-3.32 41.408.956 24.266 5.228 7.121 22.59-3.313 39.73.961c17.144 4.274 27.576 21.64 23.302 38.784z"
                                        ></path>
                                        <path
                                                fill="#FFF"
                                                d="M46.103 27.445c.637-4.258-2.605-6.547-7.038-8.074l1.438-5.768-3.511-.875-1.4 5.616c-.923-.23-1.871-.447-2.813-.662l1.41-5.653-3.51-.875-1.438 5.766c-.764-.174-1.514-.346-2.242-.527l.004-.018-4.842-1.209-.934 3.75s2.605.597 2.55.634c1.422.355 1.679 1.296 1.636 2.042l-1.638 6.571c.098.025.225.061.365.117l-.371-.092-2.296 9.205c-.174.432-.615 1.08-1.61.834.036.051-2.551-.637-2.551-.637l-1.743 4.02 4.569 1.138c.85.213 1.683.436 2.503.646l-1.453 5.834 3.507.875 1.439-5.772c.958.26 1.888.5 2.798.726l-1.434 5.745 3.51.875 1.454-5.823c5.987 1.133 10.489.676 12.384-4.739 1.527-4.36-.076-6.875-3.226-8.515 2.294-.529 4.022-2.038 4.483-5.155zm-8.022 11.25c-1.085 4.36-8.426 2.002-10.806 1.411l1.928-7.729c2.38.594 10.012 1.77 8.878 6.317zm1.086-11.313c-.99 3.966-7.1 1.951-9.082 1.457l1.748-7.01c1.982.494 8.365 1.416 7.334 5.553z"
                                        ></path>
                                        <path
                                                fill="#ffffff"
                                                d="M93.773 19.365c2.595 0 4.837.465 6.72 1.378 1.894.922 3.456 2.164 4.709 3.726 1.236 1.57 2.156 3.405 2.75 5.508.59 2.11.886 4.376.886 6.803 0 3.728-.683 7.25-2.062 10.57-1.38 3.325-3.25 6.21-5.63 8.67-2.378 2.456-5.186 4.393-8.424 5.824-3.233 1.432-6.748 2.148-10.522 2.148-.488 0-1.346-.014-2.558-.039s-2.605-.15-4.165-.36a48.879 48.879 0 0 1-4.983-.978 24.966 24.966 0 0 1-4.983-1.78L79.523 1.957 92.073.013l-5.017 20.893a19.51 19.51 0 0 1 3.236-1.132 14.375 14.375 0 0 1 3.48-.409zM83.246 54.036c1.89 0 3.67-.465 5.344-1.378a14.067 14.067 0 0 0 4.339-3.685c1.213-1.544 2.173-3.283 2.873-5.226s1.054-3.97 1.054-6.079c0-2.59-.433-4.612-1.296-6.073-.863-1.455-2.46-2.187-4.78-2.187-.76 0-1.738.145-2.952.404-1.218.275-2.308.846-3.285 1.705L79.2 53.705c.322.057.607.111.85.162.238.055.5.094.763.121.277.031.594.047.977.047l1.455.001zM121.853 62.865h-11.987l10.123-42.597h12.069l-10.205 42.597zm5.833-47.787a7.438 7.438 0 0 1-4.536-1.496c-1.357-.992-2.03-2.519-2.03-4.577 0-1.132.23-2.194.687-3.196a8.55 8.55 0 0 1 1.826-2.593 8.967 8.967 0 0 1 2.63-1.743 8.031 8.031 0 0 1 3.204-.645c1.672 0 3.18.498 4.532 1.496 1.346 1.003 2.023 2.53 2.023 4.577a7.565 7.565 0 0 1-.69 3.202 8.43 8.43 0 0 1-1.82 2.593 8.805 8.805 0 0 1-2.63 1.738c-1.002.437-2.064.644-3.196.644zM142.563 9.655l12.555-1.945-3.083 12.556h13.446l-2.428 9.878h-13.365l-3.56 14.9c-.328 1.242-.514 2.402-.566 3.48-.06 1.083.078 2.013.402 2.796.322.785.9 1.39 1.74 1.818.837.435 2.034.654 3.604.654 1.293 0 2.553-.123 3.77-.367a26.739 26.739 0 0 0 3.68-1.01l.895 9.235a43.015 43.015 0 0 1-5.264 1.535c-1.893.436-4.134.646-6.724.646-3.724 0-6.611-.553-8.668-1.654-2.054-1.109-3.506-2.624-4.375-4.542-.857-1.91-1.24-4.114-1.133-6.596.11-2.488.486-5.103 1.133-7.857l7.94-33.527zM164.953 45.855c0-3.669.594-7.129 1.78-10.368 1.186-3.242 2.893-6.077 5.108-8.51 2.207-2.42 4.896-4.339 8.06-5.747 3.15-1.4 6.678-2.106 10.565-2.106 2.433 0 4.606.23 6.518.691 1.92.465 3.657 1.066 5.228 1.82l-4.134 9.4a38.71 38.71 0 0 0-3.36-1.174c-1.16-.357-2.576-.529-4.251-.529-4.001 0-7.164 1.38-9.518 4.128-2.345 2.751-3.526 6.454-3.526 11.1 0 2.752.594 4.978 1.786 6.681 1.186 1.703 3.377 2.55 6.558 2.55 1.57 0 3.085-.164 4.536-.484 1.462-.324 2.753-.732 3.89-1.214l.895 9.636a43.381 43.381 0 0 1-5.022 1.584c-1.838.45-4.026.682-6.563.682-3.35 0-6.184-.49-8.503-1.455-2.32-.98-4.237-2.28-5.747-3.929-1.518-1.652-2.608-3.58-3.282-5.795a23.772 23.772 0 0 1-1.018-6.96zM218.203 63.995c-2.861 0-5.346-.436-7.454-1.299-2.102-.863-3.843-2.074-5.22-3.644-1.38-1.562-2.411-3.413-3.118-5.546-.707-2.132-1.047-4.493-1.047-7.08 0-3.245.52-6.489 1.574-9.724 1.048-3.242 2.603-6.155 4.66-8.744 2.043-2.593 4.562-4.713 7.528-6.366 2.963-1.642 6.37-2.468 10.199-2.468 2.809 0 5.28.437 7.418 1.3 2.127.861 3.879 2.082 5.264 3.644 1.37 1.57 2.41 3.413 3.11 5.55.706 2.127 1.054 4.494 1.054 7.083 0 3.235-.514 6.48-1.534 9.724-1.021 3.23-2.536 6.15-4.536 8.744-1.996 2.59-4.492 4.708-7.49 6.354-2.994 1.646-6.466 2.472-10.408 2.472zm5.99-34.662c-1.776 0-3.347.516-4.692 1.535-1.35 1.031-2.484 2.327-3.398 3.89-.924 1.57-1.61 3.282-2.072 5.143-.46 1.865-.684 3.628-.684 5.303 0 2.703.436 4.808 1.293 6.323.869 1.507 2.43 2.265 4.699 2.265 1.783 0 3.346-.512 4.699-1.542 1.342-1.023 2.477-2.32 3.398-3.886.918-1.562 1.609-3.279 2.072-5.143.453-1.859.684-3.632.684-5.304 0-2.696-.434-4.806-1.3-6.319-.863-1.507-2.431-2.265-4.698-2.265zM255.233 62.865h-11.997l10.123-42.597h12.075l-10.201 42.597zm5.824-47.787a7.424 7.424 0 0 1-4.532-1.496c-1.35-.992-2.028-2.519-2.028-4.577 0-1.132.233-2.194.69-3.196a8.442 8.442 0 0 1 1.824-2.593 8.95 8.95 0 0 1 2.632-1.743 7.976 7.976 0 0 1 3.194-.645c1.676 0 3.19.498 4.538 1.496 1.349 1.003 2.03 2.53 2.03 4.577 0 1.136-.242 2.202-.695 3.202s-1.062 1.861-1.817 2.593a8.894 8.894 0 0 1-2.63 1.738c-1.004.437-2.068.644-3.206.644zM274.073 22.205c.91-.266 1.926-.586 3.03-.934a40.19 40.19 0 0 1 3.733-.964c1.369-.3 2.914-.545 4.613-.734 1.699-.193 3.635-.287 5.786-.287 6.322 0 10.68 1.841 13.086 5.512 2.404 3.671 2.82 8.695 1.26 15.063l-5.514 23H288l5.344-22.516c.326-1.406.582-2.765.77-4.093.192-1.316.18-2.476-.042-3.48a4.168 4.168 0 0 0-1.494-2.433c-.791-.619-1.986-.93-3.607-.93a24.36 24.36 0 0 0-4.776.492l-7.857 32.96h-12.071l9.805-40.656z"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="styles__Method-sc-ena6u4-3 payment-item gcQxa-d" data-payment_id="5">
                                <div
                                        class="styles__Icon-sc-h5vgtd-0 gpvUmb styles__MethodIcon-sc-ena6u4-4 kDTdJM"
                                        fill="inherit"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1080 270.6">
                                        <g data-name="Layer 2">
                                            <g data-name="Layer 1">
                                                <path
                                                        d="M306.2 155.7a3.56 3.56 0 0 1-3.6 3.5h-66.3c1.7 16.4 14 31.4 31.4 31.4 11.9 0 20.7-4.5 27.3-14a3.58 3.58 0 0 1 2.9-1.7 3.21 3.21 0 0 1 3.3 3.3 3.1 3.1 0 0 1-.5 1.7c-6.7 11.6-20 17.3-33 17.3-22.3 0-38.3-20-38.3-41.3s15.9-41.3 38.3-41.3 38.4 19.8 38.5 41.1Zm-7.1-3.1c-1.4-16.4-14-31.4-31.4-31.4s-29.7 15-31.4 31.4ZM386.8 116.2a3.4 3.4 0 0 1 3.3 3.3 3.21 3.21 0 0 1-3.3 3.3H369v69.9a3.33 3.33 0 0 1-3.3 3.3 3.4 3.4 0 0 1-3.3-3.3v-69.9h-17.1a3.21 3.21 0 0 1-3.3-3.3 3.33 3.33 0 0 1 3.3-3.3h17.1V90.7a3.55 3.55 0 0 1 3-3.5 3.27 3.27 0 0 1 3.7 3.3v25.7ZM495.3 150v42.3a3.4 3.4 0 0 1-3.3 3.3 3.21 3.21 0 0 1-3.3-3.3V150c0-14.3-8.1-28.5-24-28.5-20.4 0-29.2 17.8-28 36.1 0 .5.2 2.6.2 2.9v31.7a3.55 3.55 0 0 1-3 3.5 3.27 3.27 0 0 1-3.7-3.3V53.3a3.33 3.33 0 0 1 3.3-3.3 3.4 3.4 0 0 1 3.3 3.3v78.6c5.7-10.2 15.9-17.1 27.8-17.1 19.6 0 30.7 17.1 30.7 35.2ZM614.4 155.7a3.56 3.56 0 0 1-3.6 3.5h-66.3c1.7 16.4 14 31.4 31.4 31.4 11.9 0 20.7-4.5 27.3-14a3.58 3.58 0 0 1 2.9-1.7 3.21 3.21 0 0 1 3.3 3.3 3.1 3.1 0 0 1-.5 1.7c-6.7 11.6-20 17.3-33 17.3-22.3 0-38.3-20-38.3-41.3s15.9-41.3 38.3-41.3c22.2 0 38.4 19.8 38.5 41.1Zm-7.2-3.1c-1.4-16.4-14-31.4-31.4-31.4s-29.7 15-31.4 31.4ZM695.9 119.3a3.37 3.37 0 0 1-3.1 3.6c-19.5 2.9-28.3 18.8-28.3 37.3v31.7a3.55 3.55 0 0 1-3 3.5 3.27 3.27 0 0 1-3.7-3.3v-72.3a3.55 3.55 0 0 1 3-3.5 3.27 3.27 0 0 1 3.7 3.3v14.7c5.5-9.3 16.4-18.1 27.8-18.1 1.7 0 3.6 1.2 3.6 3.1ZM804.9 155.7a3.56 3.56 0 0 1-3.6 3.5H735c1.7 16.4 14 31.4 31.4 31.4 11.9 0 20.7-4.5 27.3-14a3.58 3.58 0 0 1 2.9-1.7 3.21 3.21 0 0 1 3.3 3.3 3.1 3.1 0 0 1-.5 1.7c-6.7 11.6-20 17.3-33 17.3-22.3 0-38.3-20-38.3-41.3s15.9-41.3 38.3-41.3 38.4 19.8 38.5 41.1Zm-7.1-3.1c-1.4-16.4-14-31.4-31.4-31.4s-29.7 15-31.4 31.4ZM912.1 120.1v72.6a3.4 3.4 0 0 1-3.3 3.3 3.21 3.21 0 0 1-3.3-3.3v-13.8c-5.5 10.9-15.2 18.8-27.6 18.8-19.7 0-30.6-17.1-30.6-35.2V120a3.33 3.33 0 0 1 3.3-3.3 3.4 3.4 0 0 1 3.3 3.3v42.5c0 14.3 8.1 28.5 24 28.5 22.3 0 27.6-20.9 27.6-44v-27.1a3.35 3.35 0 0 1 4.5-3.1 3.63 3.63 0 0 1 2.1 3.3ZM1080 149.7v42.5a3.4 3.4 0 0 1-3.3 3.3 3.21 3.21 0 0 1-3.3-3.3v-42.5c0-14.3-8.1-28.3-24-28.3-20 0-27.6 21.4-27.6 38v32.8a3.4 3.4 0 0 1-3.3 3.3 3.21 3.21 0 0 1-3.3-3.3v-42.5c0-14.3-8.1-28.3-24-28.3-20.2 0-28.5 15.9-27.8 37.1 0 .5.2 1.4 0 1.7v31.9a3.55 3.55 0 0 1-3 3.5 3.27 3.27 0 0 1-3.7-3.3v-72.5a3.55 3.55 0 0 1 3-3.5 3.27 3.27 0 0 1 3.7 3.3v12.1c5.7-10.2 15.9-16.9 27.8-16.9 13.5 0 24 8.6 28.3 21.1 5.5-12.4 16.2-21.1 29.9-21.1 19.5 0 30.6 16.9 30.6 34.9Z"
                                                        fill="#ffffff"
                                                ></path>
                                                <path
                                                        d="M83 100.1 0 137.8l83 49.1 83.1-49.1Z"
                                                        style="opacity: 0.6000000238418579; isolation: isolate"
                                                ></path>
                                                <path
                                                        d="m0 137.8 83 49.1V0Z"
                                                        style="opacity: 0.44999998807907104; isolation: isolate"
                                                ></path>
                                                <path
                                                        d="M83 0v186.9l83.1-49.1Z"
                                                        style="opacity: 0.800000011920929; isolation: isolate"
                                                ></path>
                                                <path
                                                        d="m0 153.6 83 117v-68Z"
                                                        style="opacity: 0.44999998807907104; isolation: isolate"
                                                ></path>
                                                <path
                                                        d="M83 202.6v68l83.1-117Z"
                                                        style="opacity: 0.800000011920929; isolation: isolate"
                                                ></path>
                                            </g>
                                        </g>
                                    </svg>
                                </div>
                            </div>
                            <div class="styles__Method-sc-ena6u4-3 payment-item gcQxa-d" data-payment_id="5">
                                <div
                                        class="styles__Icon-sc-h5vgtd-0 gpvUmb styles__MethodIcon-sc-ena6u4-4 kDTdJM"
                                        fill="inherit"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" height="800" width="1200" id="svg20" version="1.1" viewBox="-76.3875 -25.59 662.025 153.54"><defs id="defs14"><style id="style12"/></defs><g transform="translate(-39.87 -50.56)" id="g18"><path id="path16" d="M63 101.74L51.43 113.3l-11.56-11.56 11.56-11.56zm28.05-28.07l19.81 19.82 11.56-11.56-31.37-31.37-31.37 31.37 11.56 11.56zm39.63 16.51l-11.56 11.56 11.56 11.56 11.55-11.56zm-39.63 39.63L71.24 110l-11.56 11.55 31.37 31.37 31.37-31.37L110.86 110zm0-16.51l11.56-11.56-11.56-11.56-11.56 11.56zm122 1.11v-.16c0-7.54-4-11.31-10.51-13.79 4-2.25 7.38-5.78 7.38-12.11v-.16c0-8.82-7.06-14.52-18.53-14.52h-26.04v56.14h26.7c12.67 0 21.02-5.13 21.02-15.4zm-15.4-24c0 4.17-3.45 5.94-8.9 5.94h-11.37V84.5h12.19c5.21 0 8.1 2.08 8.1 5.77zm3.13 22.46c0 4.17-3.29 6.09-8.75 6.09h-14.65v-12.33h14.27c6.34 0 9.15 2.33 9.15 6.1zM239 129.81V73.67h-12.39v56.14zm66.39 0V73.67h-12.23v34.57l-26.3-34.57h-11.39v56.14h12.19V94.12l27.18 35.69zm68.41 0l-24.1-56.54h-11.39l-24.05 56.54h12.59l5.15-12.59h23.74l5.13 12.59zm-22.45-23.5h-14.96l7.46-18.2zm81.32 23.5V73.67h-12.23v34.57l-26.31-34.57h-11.38v56.14h12.18V94.12l27.19 35.69zm63.75-9.06l-7.85-7.94c-4.41 4-8.34 6.57-14.76 6.57-9.62 0-16.28-8-16.28-17.64v-.16c0-9.62 6.82-17.48 16.28-17.48 5.61 0 10 2.4 14.36 6.33l7.83-9.06c-5.21-5.13-11.54-8.66-22.13-8.66-17.24 0-29.27 13.07-29.27 29v.16c0 16.12 12.27 28.87 28.79 28.87 10.81.03 17.22-3.82 22.99-9.99zm52.7 9.06v-11H518.6V107h26.47V96H518.6V84.66h30.08v-11h-42.35v56.14z" fill="#f0b90b"/></g></svg>
                                </div>
                            </div>
                            <div class="styles__Method-sc-ena6u4-3 payment-item gcQxa-d" data-payment_id="5">
                                <div
                                        class="styles__Icon-sc-h5vgtd-0 gpvUmb styles__MethodIcon-sc-ena6u4-4 kDTdJM"
                                        fill="inherit"
                                >
                                    <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            fill="none"
                                            viewBox="0 0 458 165"
                                    >
                                        <path
                                                fill="#26A17B"
                                                d="M62.665 125.093c34.352 0 62.2-27.847 62.2-62.2 0-34.352-27.848-62.2-62.2-62.2-34.352 0-62.2 27.848-62.2 62.2 0 34.353 27.848 62.2 62.2 62.2Z"
                                        ></path>
                                        <path
                                                fill="#fff"
                                                d="M69.295 68.633v-.01c-.43.03-2.65.16-7.59.16-3.95 0-6.72-.11-7.7-.16v.01c-15.19-.67-26.52-3.31-26.52-6.48 0-3.16 11.34-5.81 26.52-6.48v10.33c.99.07 3.84.24 7.77.24 4.72 0 7.08-.2 7.52-.24v-10.32c15.16.68 26.46 3.32 26.46 6.48 0 3.16-11.31 5.8-26.46 6.48m0-14.03v-9.24h21.15v-14.09h-57.58v14.09h21.15v9.24c-17.19.79-30.11 4.19-30.11 8.27 0 4.08 12.93 7.48 30.11 8.28v29.62h15.29v-29.62c17.16-.79 30.06-4.19 30.06-8.27 0-4.07-12.9-7.48-30.06-8.27"
                                        ></path>
                                        <path
                                                fill="#ffffff"
                                                d="M177.7 35.133v14.94h14.52v11.21H177.6v22.73c0 5.02 2.77 7.47 6.83 7.47 2.03 0 4.38-.64 6.3-1.6l3.63 11.1c-3.73 1.49-6.83 2.13-10.78 2.24-11.42.43-18.89-6.08-18.89-19.21v-22.73h-9.82v-11.21h9.82v-13.55l13.01-1.39Zm250.33 14.24.96 6.08c4.06-6.51 9.5-7.47 14.84-7.47 5.44 0 10.67 2.13 13.55 5.01l-5.87 11.31c-2.67-2.24-5.12-3.42-9.39-3.42-6.83 0-13.13 3.63-13.13 13.34v27.75h-13.02v-52.62l12.06.02Zm-31.91 20.17c-.85-6.83-6.19-10.25-13.66-10.25-7.04 0-12.81 3.42-14.73 10.25h28.39Zm-28.6 10.78c.85 6.51 6.51 11.21 15.69 11.21 4.8 0 11.1-1.82 14.09-4.91l8.32 8.22c-5.55 5.76-14.62 8.54-22.63 8.54-18.14 0-28.93-11.21-28.93-28.07 0-16.01 10.89-27.54 27.97-27.54 17.61 0 28.61 10.89 26.58 32.55h-41.09Zm-57.42-53.05v29.24c4.7-6.08 10.46-7.9 16.44-7.9 14.94 0 21.56 10.14 21.56 25.62v27.75h-13.02v-27.64c0-9.61-5.02-13.66-11.96-13.66-7.69 0-13.02 6.51-13.02 14.41v26.9h-13.02v-74.71l13.02-.01Zm-34.58 7.26v14.94h14.52v11.21h-14.62v22.73c0 5.02 2.78 7.47 6.83 7.47 2.03 0 4.38-.64 6.3-1.6l3.63 11.1c-3.73 1.49-6.83 2.13-10.78 2.24-11.42.43-18.89-6.09-18.89-19.21v-22.73h-9.82v-11.21h9.82v-13.55l13.01-1.39Zm-35.97 35.01c-.85-6.83-6.19-10.25-13.66-10.25-7.04 0-12.81 3.42-14.73 10.25h28.39Zm-28.6 10.78c.85 6.51 6.51 11.21 15.69 11.21 4.8 0 11.1-1.82 14.09-4.91l8.32 8.22c-5.55 5.76-14.62 8.54-22.63 8.54-18.15 0-28.93-11.21-28.93-28.07 0-16.01 10.89-27.54 27.96-27.54 17.61 0 28.6 10.89 26.58 32.55h-41.08ZM333.018 153v-22.72h-8.96V125h24.4v5.28h-8.96V153h-6.48Zm18.927 0v-28h12.12c2.507 0 4.667.413 6.48 1.24 1.813.8 3.213 1.96 4.2 3.48.987 1.52 1.48 3.333 1.48 5.44 0 2.08-.493 3.88-1.48 5.4-.987 1.493-2.387 2.64-4.2 3.44-1.813.8-3.973 1.2-6.48 1.2h-8.52l2.88-2.84V153h-6.48Zm17.8 0-7-10.16h6.92l7.08 10.16h-7Zm-11.32-9.92-2.88-3.04h8.16c2 0 3.493-.427 4.48-1.28.987-.88 1.48-2.08 1.48-3.6 0-1.547-.493-2.747-1.48-3.6-.987-.853-2.48-1.28-4.48-1.28h-8.16l2.88-3.08v15.88Zm36.334 10.4c-2.16 0-4.173-.347-6.04-1.04-1.84-.72-3.44-1.733-4.8-3.04a14.272 14.272 0 0 1-3.2-4.6c-.747-1.76-1.12-3.693-1.12-5.8 0-2.107.373-4.04 1.12-5.8a14.272 14.272 0 0 1 3.2-4.6c1.387-1.307 3-2.307 4.84-3 1.84-.72 3.853-1.08 6.04-1.08 2.427 0 4.613.427 6.56 1.28 1.973.827 3.627 2.053 4.96 3.68l-4.16 3.84c-.96-1.093-2.027-1.907-3.2-2.44-1.173-.56-2.453-.84-3.84-.84-1.307 0-2.507.213-3.6.64a8.169 8.169 0 0 0-2.84 1.84c-.8.8-1.427 1.747-1.88 2.84-.427 1.093-.64 2.307-.64 3.64 0 1.333.213 2.547.64 3.64a8.655 8.655 0 0 0 1.88 2.84c.8.8 1.747 1.413 2.84 1.84 1.093.427 2.293.64 3.6.64 1.387 0 2.667-.267 3.84-.8 1.173-.56 2.24-1.4 3.2-2.52l4.16 3.84c-1.333 1.627-2.987 2.867-4.96 3.72-1.947.853-4.147 1.28-6.6 1.28Zm13.819-.48v-4.2l10.8-10.2c.853-.773 1.48-1.467 1.88-2.08.4-.613.666-1.173.8-1.68.16-.507.24-.973.24-1.4 0-1.12-.387-1.973-1.16-2.56-.747-.613-1.854-.92-3.32-.92a7.941 7.941 0 0 0-3.28.68c-.987.453-1.827 1.16-2.52 2.12l-4.72-3.04c1.066-1.6 2.56-2.867 4.48-3.8 1.92-.933 4.133-1.4 6.64-1.4 2.08 0 3.893.347 5.44 1.04 1.573.667 2.786 1.613 3.64 2.84.88 1.227 1.32 2.693 1.32 4.4 0 .907-.12 1.813-.36 2.72-.214.88-.667 1.813-1.36 2.8-.667.987-1.654 2.093-2.96 3.32l-8.96 8.44-1.24-2.36h15.8V153h-21.16Zm35.834.48c-2.294 0-4.347-.56-6.16-1.68-1.814-1.147-3.24-2.8-4.28-4.96s-1.56-4.773-1.56-7.84.52-5.68 1.56-7.84 2.466-3.8 4.28-4.92c1.813-1.147 3.866-1.72 6.16-1.72 2.32 0 4.373.573 6.16 1.72 1.813 1.12 3.24 2.76 4.28 4.92s1.56 4.773 1.56 7.84-.52 5.68-1.56 7.84-2.467 3.813-4.28 4.96c-1.787 1.12-3.84 1.68-6.16 1.68Zm0-5.48c1.093 0 2.04-.307 2.84-.92.826-.613 1.466-1.587 1.92-2.92.48-1.333.72-3.053.72-5.16s-.24-3.827-.72-5.16c-.454-1.333-1.094-2.307-1.92-2.92-.8-.613-1.747-.92-2.84-.92-1.067 0-2.014.307-2.84.92-.8.613-1.44 1.587-1.92 2.92-.454 1.333-.68 3.053-.68 5.16s.226 3.827.68 5.16c.48 1.333 1.12 2.307 1.92 2.92.826.613 1.773.92 2.84.92Z"
                                        ></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="styles__Method-sc-ena6u4-3 payment-item gcQxa-d" data-payment_id="5">
                                <div
                                        class="styles__Icon-sc-h5vgtd-0 gpvUmb styles__MethodIcon-sc-ena6u4-4 kDTdJM"
                                        fill="inherit"
                                >
                                    <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            xml:space="preserve"
                                            id="图层_1"
                                            x="0"
                                            y="0"
                                            style="enable-background: new 0 0 3000 1131.5"
                                            version="1.1"
                                            viewBox="0 0 3000 1131.5"
                                    >
                    <path
                            d="M1198.6 497.4h44.4v289.9h-44.4zM1080 374.6h361.9V419H1080zM1278.8 497.4h44.4v289.9h-44.4zM2549.8 787.6h44.8V613l-44.8-49.8z"
                            class="st0"
                            fill="#ffffff"
                    ></path>
                                        <path
                                                d="M2785.7 374.6v323.7l-316.9-352.2v441.5h44.7V463.8l317.1 352.3V374.6zM2178.7 374.4c-113.9 0-206.5 92.6-206.5 206.5s92.6 206.5 206.5 206.5 206.5-92.6 206.5-206.5c0-113.8-92.6-206.5-206.5-206.5zm0 368.5c-89.3 0-162-72.6-162-162s72.6-162 162-162c89.3 0 162 72.6 162 162 0 89.3-72.7 162-162 162z"
                                                class="st0"
                                                fill="#ffffff"
                                        ></path>
                                        <path
                                                d="M2178.7 551.2c-16.4 0-29.7 13.3-29.7 29.7s13.3 29.7 29.7 29.7 29.7-13.3 29.7-29.7-13.3-29.7-29.7-29.7zM1894.5 501.3c0-69.8-56.4-126.6-125.7-126.6h-236.2v413h44.1V419.5h192.1c44.5 0 80.7 36.7 80.7 81.8 0 44.9-35.7 81.4-79.8 81.9l-156.7-.1v204.6h44.1V627.9h103.3l84.4 159.7h51.3l-88.1-166c50.9-16.9 86.5-66.1 86.5-120.3z"
                                                class="st0"
                                                fill="#ffffff"
                                        ></path>
                                        <path
                                                d="M774.7 292.8 172.9 182.1l316.7 796.8 441.2-537.6-156.1-148.5zm-9.6 48.8 92.1 87.5-251.8 45.6 159.7-133.1zm-214.4 124L285.3 245.5 719 325.3 550.7 465.6zm-19 39-43.2 357.7-233.3-587.2 276.5 229.5zm40 18.9L850.5 473 530.8 862.6l40.9-339.1z"
                                                fill="#eb0029"
                                        ></path>
                  </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ow__balance">
                        <div class="ow__balance--or">{{ __('Или') }}</div>
                        <div class="form-group">
                          <input type="checkbox" id="from-balance"/>
                          <label for="from-balance">
                            <div class="ow-balance__box">
                              <img src="/images/rustresort_icon.png" alt="icon" />
                              <div class="ow-balance__text">
                                <div class="ow-balance-text__top">
                                  {{ __('Оплатить с помощью личного баланса') }}
                                </div>
                                <div class="ow-balance-text__bottom">
                                  {{ __('У вас на счету') }}:
                                  <span>{{ getCurrentUserBalance() }}</span>
                                </div>
                              </div>
                            </div>
                          </label>
                        </div>

                    </div>
                    <div class="buying-item-terms">
                        <div>
                            <input type="checkbox" name="term" autocomplete="off"><label>{{ __('Я прочитал и согласен с') }} <a href="{{ route('term') }}" target="_blank">{{ __('Условия Обслуживания') }}</a></label>
                        </div>
                        <div>
                            <input type="checkbox" name="policy" autocomplete="off"><label>{{ __('Я прочитал и согласен с') }} <a href="{{ route('policy') }}" target="_blank">{{ __('Соглашение о Политике') }}</a></label>
                        </div>
                    </div>
                    <div class="styles__BalanceBlock-sc-n3x62l-0 yqtHU">
                        <div class="styles__MoneyBlock-sc-n3x62l-10 bgUbWn">
                            <div class="styles__Money-sc-n3x62l-11 bSqOVl">
                                <div class="styles__Label-sc-n3x62l-14 ehTfkQ">
                                    {{ __('Сумма пополнения') }}
                                </div>
                                <div class="styles__PaymentInfo-sc-n3x62l-12 bERcWv" style="width: 100px;">
                                    <input
                                            tabindex="0"
                                            type="text"
                                            id="item-price"
                                            class="styles__PaymentInput-sc-n3x62l-13 DGJNK price"
                                            value="1"
                                            readonly
                                    />
                                    <div
                                            class="styles__Icon-sc-h5vgtd-0 emYpJf styles__Rouble-sc-qhub7g-0 kozvEy"
                                    >
                                        @if(app()->getLocale() == 'ru'){{ '₽' }}@else{{ '$' }}@endif
                                    </div>
                                </div>
                            </div>
                            <div id="btn-buy" class="styles__ApplyButton-sc-ena6u4-10 fyAHtA">
                                {{ __('Оплатить') }}
                            </div>
                        </div>

                    </div>

                </form>
            </div>
        </div>
    </div>


    {{--
    <div class="buying-item-modal shop-modal modal">
        <div class="modal-close"></div>

        <div class="modal-content buying-item">
            <div class="buying-item-close"><i class="fa-solid fa-xmark"></i></div>

            <div class="buying-item-name">
                <p>{{ __('Покупка') }}: <span id="item-name">Premium</span></p>
            </div>

            <form action="{{ route('shop.item.buy') }}" method="POST">
                @csrf
                <input type="hidden" id="server_id" name="server_id" value="{{ $server->id }}">
                <input type="hidden" id="item_id" name="item_id" value="">
                <input type="hidden" id="var_id" name="var_id" value="">
                <input type="hidden" id="payment_id" name="payment_id" value="">
                <input type="hidden" id="steam_id" name="steam_id" value="">
                <div class="buying-item-terms">
                    <div>
                        <input type="checkbox" name="term" autocomplete="off" required><label>{{ __('Я прочитал и согласен с') }} <a href="{{ route('term') }}" target="_blank">{{ __('Условия Обслуживания') }}</a></label>
                    </div>
                    <div>
                        <input type="checkbox" name="policy" autocomplete="off" required><label>{{ __('Я прочитал и согласен с') }} <a href="{{ route('policy') }}" target="_blank">{{ __('Соглашение о Политике') }}</a></label>
                    </div>
                </div>

                <div class="buying-item-price">
                    <p>{{ __('Всего') }}: <span id="item-price" class="price"></span><span> @if(app()->getLocale() == 'ru'){{__('руб.')}}@else{{ 'USD' }}@endif</span></p>
                </div>

                <ul class="buying-item-payment-option">
                    @if(config('options.paymentwall_public_key', '') != '') <li class="payment-checkbox" data-paymentid="1"><span></span> Paymentwall</li> @endif
                    @if(config('options.qiwi_secret_key', '') != '') <li class="payment-checkbox" data-paymentid="2"><span></span> Qiwi</li> @endif
                    @if(config('options.enot_merchant_id', '') != '') <li class="payment-checkbox" data-paymentid="3"><span></span> Enot (Master card/ Visa, YMoney and Crypt) </li> @endif
                    @if(config('options.freekassa_merchant_id', '') != '') <li class="payment-checkbox" data-paymentid="5"><span></span> Free-Kassa (Master card/Visa, Qiwi, and others)</li> @endif
                    @if(config('options.cent_authorization', '') != '') <li class="payment-checkbox" data-paymentid="4"><span></span> Cent (Master card/ Visa)</li> @endif
                    @if(isset(auth()->user()->role) && auth()->user()->role == 'admin')<li class="payment-checkbox" data-paymentid="20"><span></span> {{ __('Оплатить с Внутреннего Баланса') }}</li>@endif
                    @if(isset(auth()->user()->role) && auth()->user()->role == 'admin')<li class="payment-checkbox" data-paymentid="6"><span></span> Test payment</li>@endif
                    <div class="buying-item-button">
                        <button class="btn-buy">{{ __('Оплатить') }}</button>
                    </div>
</ul>
</form>

</div>
</div>
--}}

    @endif

@endsection


@push('scripts')
    <script src="/js/stats.js"></script>
    <script src="/js/rolling.js"></script>

    <script>

        let controller;

        $('.payment-item').on('click', function () {
            $('#from-balance').prop('checked', false);
            $('#payment_id').val($(this).data('payment_id'));
        });
        $('#btn-buy').on('click', function () {

            $('input[name="term"]').removeClass('check-error');
            $('input[name="policy"]').removeClass('check-error');

            if(!$('input[name="term"]').is(':checked'))
            {
                $('input[name="term"]').addClass('check-error');
                return false;
            }
            if(!$('input[name="policy"]').is(':checked'))
            {
                $('input[name="policy"]').addClass('check-error');
                return false;
            }
            $('#topup-balance-form').submit();
        });

        $('#from-balance').on('click', function () {
            if($(this).is(':checked')) {
                $('#payment_id').val('20');
                $('.payment-item').removeClass('fdLgLR');
            } else {
                $('#payment_id').val('3');
                $('.payment-item:first').addClass('fdLgLR');
            }
        });


        $(document).on('click', '.shopcase-buy', function(e){
            let id = $(this).data('id');

            if ($('#sb__popup-case-'+id+' .spc__info').text() == '') {
                $.ajax({
                    type: "GET",
                    url: "/cases/show_shop/"+id+"?server={{ $server->id }}",
                }).done(function (html) {
                    $('#sb__popup-case-' + id).show();
                    $('#sb__popup-case-' + id + ' .spc__info').html(html);
                });
            } else {
                $('#sb__popup-case-' + id).show();
            }
        });

        $(document).on('click', '.shopitem-buy', function(e){
            let id = $(this).data('id');
            const element = $('#sb__popup-item-' + id);
            element.show();

            setTimeout(() => {
                const scrollHeight = Math.max(
                    $(document).height(), $(window).height(), element[0].scrollHeight
                );
                console.log('scrollHeight:' + scrollHeight);
                $('#sb__popup-item-' + id).find('.sb-popup_back').height(scrollHeight);
            }, 200);
        });

        //Кол-во товара
        $(document).on('click', '.amount-plus', function(e){
            let amount = parseInt($(this).parent().parent().parent().find('.item-amount-value').val());
            let qty = parseInt($(this).parent().parent().parent().find('.item-qty-value').val());
            let price = parseFloat($(this).parent().parent().parent().find('.item-price-value').val());

            qty = qty + 1;
            amount = amount * qty;
            price = price * qty;

            $(this).parent().parent().parent().find('.item-qty-value').val(qty);
            $(this).parent().find('.item-amount').text(amount);
            $(this).parent().parent().parent().find('.buy-item-price').text(price);
        });
        $(document).on('click', '.amount-minus', function(e){
            let amount = parseInt($(this).parent().parent().parent().find('.item-amount-value').val());
            let qty = parseInt($(this).parent().parent().parent().find('.item-qty-value').val());
            let price = parseFloat($(this).parent().parent().parent().find('.item-price-value').val());

            qty = qty - 1;
            if (qty < 1) qty = 1;
            amount = amount * qty;
            price = price * qty;

            $(this).parent().parent().parent().find('.item-qty-value').val(qty);
            $(this).parent().find('.item-amount').text(amount);
            $(this).parent().parent().parent().find('.buy-item-price').text(price);
        });

        $(document).ready(function() {
            $(".steam-id").on("input", function() {
                var inputValue = $(this).val();

                if (/[^0-9]/.test(inputValue)) {
                    $(this).addClass("input-error");
                    $('.steam-id-error').show();
                    $('.steam-id-notice').hide();
                } else {
                    $(this).removeClass("input-error");
                    $('.steam-id-error').hide();
                    $('.steam-id-notice').show();
                }
            });
        });


        $(document).ready(function() {

            @foreach($shopcases as $shopcase)
                $.ajax({
                    type: "GET",
                    url: "/cases/show_shop/{{ $shopcase->id }}?server={{ $server->id }}",
                }).done(function (html) {
                    $('#sb__popup-case-{{ $shopcase->id }} .spc__info').html(html);
                });
            @endforeach

        });

        $(document).on('click', '.btn-buy-item', function(e){
            if (!$(this).hasClass('disabled')) {
                $('#topup-balance-form-'+$(this).data('id')).submit();
                $(this).addClass('disabled');
            }
            return false;
        });

    </script>

@endpush