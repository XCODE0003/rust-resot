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

                        <div class="open_cases-link-block flex-sc">
                            <div class="line-left">
                                <div class="quotes"></div>
                            </div>
                            <div class="flex-sbc open_cases-links">

                                <div @if(isset(auth()->user()->steam_trade_url) && strlen(auth()->user()->steam_trade_url) > 10) id="opencase" @else id="set-tradeurl" @endif class="opencase-items flex-sbc open_cases-link active">
                                    <div class="open_cases-link-image"><img src="/images/icons/open-case_icon.png" alt=""></div>
                                    <div class="flex-cs">
                                        {{ __('Открыть кейс') }}
                                    </div>
                                    <span>{{ __('Бесплатно') }}</span>
                                    <div class="open_cases-link-shadow"></div>
                                </div>

                                <div class="open_cases__rolling flex-sbc">
                                    <div class="flex-cs">{{ __('ОТКРЫТИЕ') }}...</div>
                                </div>
                                <div class="open_cases-link-bar flex-cc">
                                    <div class="flex-sbc open_cases-link-take">
                                        <div id="take-bonus" class="flex-cs">{{ __('ЗАБРАТЬ ПРЕДМЕТ') }}</div>
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
                if (data.status == 'success') {
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
                url: "{{ route('account.cases.open') }}",
                dataType: "json",
                data: { inventory_id: '{{ $inventory->id }}', _token: $('input[name="_token"]').val() }
            }).done(function( data ) {
                if (data.status == 'success') {
                    controller.run(data.win_index);

                    $('#win_item-img').attr('src', data.win_item.image_url);
                    $('#sellitem').attr('data-id', data.win_item.inventory_id);
                    $('#win-item-price').text(data.win_item.price);

                    $('.user_balance').text('$ '+data.balance);

                } else {

                }
            });

        });

        $(document).on('click', '#take-bonus', function(e){
            location.href='{{ route('account.profile') }}';
        });

        $(document).on('click', '#set-tradeurl', function(e){
            $('#trade-url-block').show();
        });
    </script>
@endpush