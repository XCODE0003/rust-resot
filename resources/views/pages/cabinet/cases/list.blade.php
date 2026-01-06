@extends('layouts.main')
@php
    $title = "title_" . app()->getLocale();
    $subtitle = "subtitle_" . app()->getLocale();
    $description = "description_" . app()->getLocale();
@endphp
@section('title', __('Кейсы') )

@section('content')

    <div class="inner-header">{{ __('Кейсы') }}</div>

    <div class="inner">
        <div class="container">

            <section class="unbox_receive">
                <div class="content-area">
                    <div class="title-big title-big-type-1 flex-sc">
                        <div class="line-left"></div>
                        <h2>{{ __('Откройте') }} & <span>{{ __('Получите') }}</span></h2>
                        <div class="line-right"></div>
                    </div>

                        <div class="unbox_receive__items flex-sc active">

                            @foreach($cases as $case)
                                <a href="{{ route('cases.show', $case) }}" class="unbox_receive__item flex-cs" data-route="{{ route('cases.show', $case) }}" data-name="cases_show">
                                    <div class="unbox_receive__item-content">
                                        <div class="unbox_receive__item-image white-shadow  flex-cs">
                                            <img src="{{ $case->image_url }}" alt="{{ $case->$title }}"></div>
                                        <div class="unbox_receive__item-title">{!! $case->$title !!}</div>
                                        <div class="unbox_receive__item-text">{!! $case->$subtitle !!}</div>
                                    </div>
                                    <div class="flex-sbc open-cases__item-link">
                                        <div class="flex-cs"><img src="/images/icons/open-case_icon.png" alt="{{ __('Открыть') }}">{{ __('Открыть') }}</div>
                                        <span>
                                        @if(app()->getLocale() == 'ru'){{ number_format($case->price, 0) }} {{ __('₽') }}@else${{ number_format($case->price_usd, 0) }}@endif
                                        </span>
                                    </div>
                                </a>
                            @endforeach

                        </div>


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
                url: "{{ route('bonus.getBonusItemsForRoll') }}",
                dataType: "json",
                data: { _token: $('input[name="_token"]').val() }
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
                url: "{{ route('bonus.open') }}",
                dataType: "json",
                data: { _token: $('input[name="_token"]').val() }
            }).done(function( data ) {
                console.log(data.win_index);
                console.log(data.win_item);
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

    </script>
@endpush