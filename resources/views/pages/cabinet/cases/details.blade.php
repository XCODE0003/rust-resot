@extends('layouts.main')

@php
    $title = "title_" . app()->getLocale();
    $subtitle = "subtitle_" . app()->getLocale();
    $description = "description_" . app()->getLocale();
@endphp
@section('title', __('Кейс') . ' - ' . $case->$title )

@section('content')

    <div class="inner-header">{{ __('Кейс') . ' - ' . $case->$title }}</div>

    <div class="inner">
        <div class="container">

                <section class="open_cases-area">
                    <div class="content-area">
                        <div class="title-big title-big-type-1 flex-sc">
                            <div class="line-left"></div>
                            <h2 id="win_item-title"><span>{{ $case->$title }}</span></h2>
                            <div class="line-right"></div>
                        </div>

                        <div id="roll" class="open_cases-all-case-scroll">
                            <div class="open_cases-all-case" data-case-items>

                            </div>
                            <div class="open_cases-item-case main_case active flex-cs">
                                <img src="{{ $case->image_url }}" alt="{{ $case->$title }}">
                            </div>
                            <div class="open_cases-item-case win_case flex-cs">
                                <div class="win_case_img flex-cs"><img src="" alt=""></div>
                                <div class="win_case_name"></div>
                            </div>
                        </div>

                        <div class="open_cases-link-block flex-sc @if($case->kind == 1) bonus-case-btns @endif @if(in_array(app()->getLocale(), ['ru', 'ua'])) bonus-case-ru @endif">
                            <div class="line-left">
                                <div class="quotes"></div>
                            </div>
                            <div class="flex-sbc open_cases-links">

                                @if(in_array($case->kind, [0,2]))

                                    <div @if(isset(auth()->user()->steam_trade_url) && strlen(auth()->user()->steam_trade_url) > 10) id="opencase" @else id="set-tradeurl" @endif
                                    class="opencase-items flex-sbc open_cases-link active
                                        @if(auth()->user()->balance < $case->price) disable @endif">
                                        <div class="open_cases-link-image"><img src="/images/icons/open-case_icon.png" alt=""></div>
                                        <div class="flex-cs">
                                            @if(auth()->user()->balance >= $case->price) {{ __('Открыть кейс') }} @else {{ __('Не доступно') }} @endif
                                        </div>
                                        <span>
                                            @if(app()->getLocale() == 'ru'){{ $case->price }} {{ __('₽') }}@else${{ $case->price_usd }}@endif
                                        </span>
                                        <div class="open_cases-link-shadow"></div>
                                    </div>

                                @else

                                    @if(1==1)
                                        <div @if(isset(auth()->user()->steam_trade_url) && strlen(auth()->user()->steam_trade_url) > 10) id="opencase-free" @else id="set-tradeurl-free" @endif
                                        class="opencase-items flex-sbc open_cases-link active @if(getHoursAmount(getOnlineTimeCase($case->id)) >= $case->online_amount) @else disable @endif" data-caseid="{{ $case->id }}" style="margin-right: 0;">
                                            <div class="open_cases-link-image">
                                                @if(getHoursAmount(getOnlineTimeCase($case->id)) >= $case->online_amount)
                                                    <img src="/images/icons/open-case_icon.png" alt="open case icon">
                                                @else
                                                    <img src="/images/icons/open-case_icon_close.png" alt="close case icon">
                                                @endif
                                            </div>
                                            <div class="flex-cs">
                                                {{ __('Открыть даром') }}
                                            </div>
                                            <div class="open_cases-link-shadow"></div>
                                        </div>

                                        {{--
                                        <div @if(isset(auth()->user()->steam_trade_url) && strlen(auth()->user()->steam_trade_url) > 10) @if(auth()->user()->balance >= $case->price) id="opencase-pay" @else id="to-donate" @endif @else id="set-tradeurl-pay" @endif
                                        class="opencase-items flex-sbc open_cases-link active">
                                            <div class="flex-cs">
                                                {{ __('Открыть кейс') }}
                                            </div>
                                            <span>
                                                @if(app()->getLocale() == 'ru'){{ $case->price }} {{ __('₽') }}@else${{ $case->price_usd }}@endif
                                            </span>
                                            <div class="open_cases-link-shadow"></div>
                                        </div>
                                        --}}
                                    @else
                                        <div @if(isset(auth()->user()->steam_trade_url) && strlen(auth()->user()->steam_trade_url) > 10) id="opencase" @else id="set-tradeurl" @endif
                                        class="opencase-items flex-sbc open_cases-link active @if(getHoursAmount(getOnlineTimeCase($case->id)) >= $case->online_amount) @else disable @endif">
                                            <div class="open_cases-link-image"><img src="/images/icons/open-case_icon.png" alt=""></div>
                                            <div class="flex-cs">
                                                @if(getHoursAmount(getOnlineTimeCase($case->id)) >= $case->online_amount) {{ __('Выиграть подарок') }} @else {{ __('Не доступно') }} @endif
                                            </div>
                                            <div class="open_cases-link-shadow"></div>
                                        </div>
                                    @endif



                                @endif

                                <div class="open_cases__rolling flex-sbc">
                                    <div class="flex-cs">{{ __('ОТКРЫТИЕ') }}...</div>
                                </div>
                                <div class="open_cases-link-bar flex-cc">
                                    <div class="flex-sbc open_cases-link-take">
                                        <div @if(auth()->user()->balance >= $case->price) id="take-bonus" @else id="disable" @endif class="flex-cs">{{ __('ЗАБРАТЬ ПРЕДМЕТ') }}</div>
                                        <div class="open_cases-link-shadow"></div>
                                    </div>
                                </div>

                            </div>
                            <div class="line-right">
                                <div class="quotes"></div>
                            </div>
                        </div>

                    </div>
                    <form>
                        @csrf
                    </form>
                </section>

            <section id="trade-url-block" class="page-bonus-block" style="margin-bottom: 85px;display: none;">
                <div class="page-bonus-title">
                    <span>{{ __('STEAM TRADE OFFER URL') }}</span>
                </div>
                <div class="page-bonus-info">
                    <p>{{ __('Для получения подарка Вы должны указать ссылку для обмена STEAM') }}</p>
                </div>

                    <form action="{{ route('account.profile.setTradeUrl') }}" method="POST">
                        @csrf
                        <div class="page-bonus-tradeurl">
                            <div class="page-bonus-tradeurl-href">
                                <a href="https://steamcommunity.com/id/gbgi/tradeoffers/privacy#trade_offer_access_url" target="_blank">{{ __('Где взять ссылку для обмена?') }}</a>
                            </div>
                            <input type="text" placeholder="Steam tarde offer url" name="steam_trade_url" value="" class="search" autocomplete="off" required/>
                            <button class="modal-bonus-btn red modal-accept active" type="submit">{{ __('Сохранить') }}</button>
                        </div>
                    </form>

            </section>


                <section class="page-bonus-block">
                    <div class="page-bonus-title">
                        <span>{{ __('Предметы в кейсе') }}</span>
                    </div>

                    <div class="page-bonus-prizes">

                        @foreach($items as $item)
                            <div class="page-bonus-prize type-{{ $item->context->quality_type }}">
                                <div class="quality-type"></div>
                                <img src="{{ $item->context->icon }}" alt="{{ $item->context->name }}">
                                <span>{{ $item->context->name }}</span>
                                <span class="bonus-price">
                                    @if(app()->getLocale() == 'ru'){{ $item->context->price }} {{ __('₽') }}@else${{ $item->context->price_usd }}@endif
                                </span>

                                @if($case->kind === 1)
                                    <span class="wipe-block tooltip" style="right: 15px;top: 38px;">
                                        <span class="tooltiptext">{{ __('Шанс дропа') }}</span>
                                        <i class="case-drop-chance">{{ number_format($item->context->chance, 2, '.', '') }} %</i>
                                    </span>
                                @else($case->kind !== 1)
                                    <span class="wipe-block tooltip" style="right: 15px;top: 38px;">
                                        <span class="tooltiptext">{{ __('Блокировка после вайпа:') }} {{ $item->context->wipe_block }}</span>
                                        <img src="/images/info_white.svg" class="icon ni ni-info">
                                    </span>
                                @endif
                            </div>
                        @endforeach

                    </div>

                </section>


        </div>
    </div>

@endsection
@push('scripts')
    <script src="/js/rolling.js"></script>

    <script>

        let controller;

        $(document).ready(function() {

            controller = new CaseController('roll');

            //Открытие кейса
            let widthLink = $('.open_cases-link').width();
            $('.open_cases-links').width(widthLink + 100);

            // Добавление шаблона итемов
            controller.setItemsTemplate(`
                        <div class="open_cases-item-case flex-cs">
                            <img src="{icon}" alt="">
                        </div>
                    `);

            $.ajax({
                type: "POST",
                url: "{{ route('cases.getCaseItemsForRoll') }}",
                dataType: "json",
                data: { case_id: '{{ $case->id }}', _token: $('input[name="_token"]').val() }
            }).done(function( data ) {
                console.log(data.status);
                if (data.status == 'success') {
                    console.log(data.result);
                    // Добавление итемов
                    controller.setItems(data.result);
                }
            });

        });

        $(document).on('click', '#opencase', function(e){

            // Событие на старте анимации вращения
            controller.onAnimationStart(function (event) {
                $('.open_cases-link').removeClass('active');
                $('.main_case').removeClass('active');
                $('.open_cases__rolling').addClass('active');
                let widthLink = $('.open_cases__rolling').width();
                $('.open_cases-links').width(widthLink + 100);
                $('.open_cases-all-case-scroll').addClass('rolling');

                setTimeout(function () {
                    $('.open_cases-all-case').addClass('roll');
                }, 500);
            });

            // Событие в конце анимации вращения
            controller.onAnimationEnd(function (event) {
                $('.open_cases-all-case').addClass('disable');
                $('.open_cases__rolling').removeClass('active');
                let widthLink = $('.open_cases-link-bar').width();
                $('.open_cases-links').width(widthLink + 100);
                $('.open_cases-all-case-scroll').removeClass('rolling');
                $('.win_case_img').html('<img src="' + event.item.context.icon + '" alt="">');
                $('.win_case_name').html(event.item.context.name);

                $('.win_case, .open_cases-link-bar > div').addClass('active');
            });

            $.ajax({
                type: "POST",
                url: "{{ route('cases.open') }}",
                dataType: "json",
                data: { case_id: '{{ $case->id }}', _token: $('input[name="_token"]').val() }
            }).done(function( data ) {
                console.log(data.win_index);
                console.log(data.win_item);
                if (data.status == 'success') {
                    controller.run(data.win_index);
                } else {

                }
            });

        });

        $(document).on('click', '#opencase-free', function(e){

            if ($(this).hasClass('disable')) {
                let case_id = $(this).data('caseid');
                $('#modal-bonus-content-'+case_id).show();
                return false;
            }

            // Событие на старте анимации вращения
            controller.onAnimationStart(function (event) {
                $('.open_cases-link').removeClass('active');
                $('.main_case').removeClass('active');
                $('.open_cases__rolling').addClass('active');
                let widthLink = $('.open_cases__rolling').width();
                $('.open_cases-links').width(widthLink + 100);
                $('.open_cases-all-case-scroll').addClass('rolling');

                setTimeout(function () {
                    $('.open_cases-all-case').addClass('roll');
                }, 500);
            });

            // Событие в конце анимации вращения
            controller.onAnimationEnd(function (event) {
                $('.open_cases-all-case').addClass('disable');
                $('.open_cases__rolling').removeClass('active');
                let widthLink = $('.open_cases-link-bar').width();
                $('.open_cases-links').width(widthLink + 100);
                $('.open_cases-all-case-scroll').removeClass('rolling');
                $('.win_case_img').html('<img src="' + event.item.context.icon + '" alt="">');
                $('.win_case_name').html(event.item.context.name);

                $('.win_case, .open_cases-link-bar > div').addClass('active');
            });

            $.ajax({
                type: "POST",
                url: "{{ route('cases.open') }}",
                dataType: "json",
                data: { case_id: '{{ $case->id }}', _token: $('input[name="_token"]').val() }
            }).done(function( data ) {
                console.log(data.win_index);
                console.log(data.win_item);
                if (data.status == 'success') {
                    controller.run(data.win_index);
                } else {

                }
            });

        });

        $(document).on('click', '#opencase-pay', function(e) {

            // Событие на старте анимации вращения
            controller.onAnimationStart(function (event) {
                $('.open_cases-link').removeClass('active');
                $('.open_cases-link').removeClass('disable');
                $('.main_case').removeClass('active');
                $('.open_cases__rolling').addClass('active');
                let widthLink = $('.open_cases__rolling').width();
                $('.open_cases-links').width(widthLink + 100);
                $('.open_cases-all-case-scroll').addClass('rolling');

                setTimeout(function () {
                    $('.open_cases-all-case').addClass('roll');
                }, 500);
            });

            // Событие в конце анимации вращения
            controller.onAnimationEnd(function (event) {
                $('.open_cases-all-case').addClass('disable');
                $('.open_cases__rolling').removeClass('active');
                let widthLink = $('.open_cases-link-bar').width();
                $('.open_cases-links').width(widthLink + 100);
                $('.open_cases-all-case-scroll').removeClass('rolling');
                $('.win_case_img').html('<img src="' + event.item.context.icon + '" alt="">');
                $('.win_case_name').html(event.item.context.name);

                $('.win_case, .open_cases-link-bar > div').addClass('active');
            });

            $.ajax({
                type: "POST",
                url: "{{ route('cases.open_pay') }}",
                dataType: "json",
                data: { case_id: '{{ $case->id }}', _token: $('input[name="_token"]').val() }
            }).done(function( data ) {
                console.log(data.win_index);
                console.log(data.win_item);
                if (data.status == 'success') {
                    controller.run(data.win_index);
                } else {

                }
            });

        });

        $(document).on('click', '#take-bonus', function(e){
            location.href="{{ route('account.profile') }}";
        });
        $(document).on('click', '#to-donate', function(e){
            location.href="{{ route('account.profile', 'topup=1') }}";
        });

        $(document).on('click', '#set-tradeurl, #set-tradeurl-free, #set-tradeurl-pay', function(e){
            $('#trade-url-block').show();
        });
    </script>
@endpush